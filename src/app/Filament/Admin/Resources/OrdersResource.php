<?php

namespace App\Filament\Admin\Resources;

use App\Enums\PaymentMethod;
use App\Enums\Courier;
use App\Models\Products; // Tambahkan ini jika belum di-import
use App\Enums\Akad;
use App\Enums\OrderStatus;
use App\Filament\Admin\Resources\OrdersResource\Pages;
use App\Filament\Admin\Resources\OrdersResource\RelationManagers;
use App\Models\Orders;
use App\Models\ProductsVariants;
use App\Models\Designs;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class OrdersResource extends Resource
{
    protected static ?string $model = Orders::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Manajemen Pesanan';

public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // ==================== BARIS 1: INFO & DESAIN ====================
                Forms\Components\Section::make('Informasi Pesanan')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable()
                            ->label('Pembeli'),
                        Forms\Components\Select::make('akad')
                            ->options(Akad::class)
                            ->required()
                            ->default(Akad::SALAM)
                            ->disabled()
                            ->dehydrated(true)
                            ->label('Akad'),
                        Forms\Components\Select::make('status')
                            ->options(OrderStatus::class)
                            ->required()
                            ->default(OrderStatus::MENUNGGU_VALIDASI_DESAIN)
                            ->label('Status'),
                        Forms\Components\DateTimePicker::make('order_date')
                            ->required()
                            ->default(now())
                            ->label('Tanggal Order'),
                        Forms\Components\Textarea::make('shipping_address')
                            ->maxLength(65535)
                            ->columnSpanFull()
                            ->label('Alamat Pengiriman')
                            ->reactive()
                            ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('shipping.shipping_address', $state)),
                        Forms\Components\Textarea::make('note')
                            ->maxLength(65535)
                            ->columnSpanFull()
                            ->label('Catatan'),
                    ])
                    ->columns(2)
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Section::make('Desain Pesanan')
                    ->schema([
                        Forms\Components\FileUpload::make('design.file_path')
                            ->label('Unggah Desain')
                            ->directory('designs')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'application/pdf', 'image/vnd.adobe.photoshop', 'application/postscript'])
                            ->maxSize(51200)
                            ->required()
                            ->extraAttributes([
                                'class' => 'flex-1 [&>.fi-fo-file-upload]:h-full [&>.fi-fo-file-upload>div]:h-full [&_label]:h-full [&_label]:flex [&_label]:flex-col [&_label]:justify-center min-h-[250px]'
                            ]),
                        Forms\Components\Hidden::make('design.order_id')
                            ->default(fn ($livewire) => $livewire->record?->id),
                    ])
                    ->columnSpan(['lg' => 1])
                    ->extraAttributes(['class' => 'h-full flex flex-col']),

                // ==================== BARIS 2: REPEATER ITEMS ====================
                Forms\Components\Section::make('Detail Item Pesanan')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                // 1. Pilih Produk Terlebih Dahulu
                                Forms\Components\Select::make('product_id')
                                    ->label('Produk')
                                    ->options(Products::all()->pluck('name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function (Forms\Set $set) {
                                        // Reset varian, harga, dan subtotal jika produk diganti
                                        $set('product_variant_id', null);
                                        $set('unit_price', 0);
                                        $set('subtotal', 0);
                                    }),

                                // 2. Dropdown Varian Produk Bergantung dari Produk yang Dipilih
                                Forms\Components\Select::make('product_variant_id')
                                    ->label('Varian Produk')
                                    ->placeholder(fn (Forms\Get $get) => $get('product_id') ? 'Pilih varian...' : 'Pilih produk dulu')
                                    ->options(function (Forms\Get $get) {
                                        $productId = $get('product_id');
                                        if (! $productId) {
                                            return [];
                                        }
                                        return ProductsVariants::where('product_id', $productId)
                                            ->where('stock', '>', 0)
                                            ->get()
                                            ->mapWithKeys(function ($variant) {
                                                $label = ($variant->size ?? '') . ' ' . ($variant->material ?? '') . ' (Stok: ' . $variant->stock . ')';
                                                return [$variant->id => $label];
                                            });
                                    })
                                    ->required()
                                    ->searchable()
                                    ->reactive()
                                    ->disabled(fn (Forms\Get $get) => ! $get('product_id'))
                                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                        if ($state) {
                                            $variant = ProductsVariants::find($state);
                                            if ($variant) {
                                                $set('unit_price', $variant->price);
                                                $set('max_stock', $variant->stock);
                                                $qty = (int) ($get('quantity') ?? 1);
                                                $set('subtotal', $qty * $variant->price);
                                            }
                                        } else {
                                            $set('unit_price', 0);
                                            $set('subtotal', 0);
                                            $set('max_stock', 0);
                                        }
                                        static::updateAkadDanHarga($set, $get);
                                    }),

                                                                // 3. Jumlah Item
                                // 3. Jumlah Item dengan Validasi Keras Saat Simpan Form
                                Forms\Components\TextInput::make('quantity')
                                    ->label('Jumlah')
                                    ->required()
                                    ->numeric()
                                    ->minValue(1)
                                    ->default(1)
                                    ->reactive()
                                    // KUNCI UTAMA: Validasi saat form diklik SUBMIT/SAVE
                                    ->rules([
                                        fn (Forms\Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                                            $variantId = $get('product_variant_id');
                                            if ($variantId) {
                                                $variant = ProductsVariants::find($variantId);
                                                
                                                // Ambil info record saat ini untuk mode EDIT agar tidak salah hitung stok lama
                                                $livewire = $get('../../'); // Naik ke konteks form utama
                                                $isEditMode = isset($livewire->record);
                                                
                                                if ($variant) {
                                                    $availableStock = $variant->stock;

                                                    // Jika dalam mode EDIT, tambahkan stok lama yang sudah terlanjur dipotong sebelumnya
                                                    if ($isEditMode) {
                                                        // Cari baris item ini di database berdasarkan ID (jaging jika ada)
                                                        $itemId = $get('id');
                                                        if ($itemId) {
                                                            $oldItem = \App\Models\OrdersItems::find($itemId);
                                                            if ($oldItem) {
                                                                $availableStock += $oldItem->quantity;
                                                            }
                                                        }
                                                    }

                                                    if ((int)$value > $availableStock) {
                                                        $fail("Jumlah pesanan melebihi stok yang tersedia (Maksimal stok: {$availableStock}).");
                                                    }
                                                }
                                            }
                                        },
                                    ])
                                    // Validasi interaktif saat mengetik (opsional/real-time)
                                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                        $unitPrice = (float) ($get('unit_price') ?? 0);
                                        $set('subtotal', ((int)$state) * $unitPrice);
                                        static::updateAkadDanHarga($set, $get);
                                    }),
                                // 4. Harga Satuan (Otomatis Terkunci)
                                Forms\Components\TextInput::make('unit_price')
                                    ->label('Harga Satuan')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->disabled()
                                    ->dehydrated(true),

                                // 5. Subtotal (Otomatis Terkunci)
                                Forms\Components\TextInput::make('subtotal')
                                    ->label('Subtotal')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->disabled()
                                    ->dehydrated(true),

                                Forms\Components\Hidden::make('max_stock')
                                    ->default(0),
                            ])
                            ->columns(5) // Diubah ke 5 kolom agar muat sebaris mendatar
                            ->columnSpanFull()
                            ->reorderable(false)
                            ->defaultItems(0)
                            ->afterStateUpdated(fn (Forms\Set $set, Forms\Get $get) => static::updateAkadDanHarga($set, $get))
                            ->label('Item Pesanan'),
                    ])
                    ->columnSpanFull(),

                // ==================== BARIS 3: PEMBAYARAN & PENGIRIMAN ====================
                Forms\Components\Section::make('Informasi Pembayaran Awal')
                    ->schema([
                        Forms\Components\Select::make('payment.payment_method')
                            ->label('Metode Pembayaran')
                            ->options(PaymentMethod::class) // Menggunakan Enum PaymentMethod
                            ->required(),
                        Forms\Components\Select::make('payment.payment_status')
                            ->label('Status Pembayaran')
                            ->options([
                                'pending' => 'Menunggu Verifikasi',
                                'success' => 'Berhasil/Lunas',
                                'failed' => 'Gagal',
                            ])
                            ->default('pending')
                            ->required(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Forms\Components\Section::make('Informasi Pengiriman')
                    ->schema([
                        Forms\Components\Select::make('shipping.courier')
                            ->label('Kurir / Ekspedisi')
                            ->options(Courier::class) // Menggunakan Enum Courier
                            ->required(),
                        Forms\Components\TextInput::make('shipping.tracking_number')
                            ->label('Nomor Resi (Opsional)')
                            ->placeholder('Kosongkan jika belum dikirim'),
                        Forms\Components\Textarea::make('shipping.shipping_address')
                            ->label('Alamat Lengkap Pengiriman')
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                // ==================== BARIS 4: RINGKASAN TOTALS ====================
                Forms\Components\Section::make('Ringkasan Biaya')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('total_price')
                                    ->label('Total Harga')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->disabled()
                                    ->dehydrated(true),
                                Forms\Components\TextInput::make('dp_amount')
                                    ->label('DP (50%)')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->disabled()
                                    ->dehydrated(true),
                                Forms\Components\TextInput::make('refund_amount')
                                    ->label('Total Refund')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0)
                                    ->disabled()
                                    ->dehydrated(true),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->columns(3);
    }

    /**
     * Update akad, total harga, dp, dan jumlah pembayaran awal secara real-time
     */
    protected static function updateAkadDanHarga(Forms\Set $set, Forms\Get $get): void
    {
        $items = $get('items') ?? [];
        $totalQuantity = 0;
        $totalPrice = 0;

        foreach ($items as $item) {
            $qty = (int) ($item['quantity'] ?? 0);
            $price = (float) ($item['unit_price'] ?? 0);
            $totalQuantity += $qty;
            $totalPrice += $qty * $price;
        }

        $set('total_price', $totalPrice);
        
        if ($totalQuantity >= 12) {
            $set('akad', 'istishna');
            $dpAmount = $totalPrice * 0.5;
            $set('dp_amount', $dpAmount);
            // Akad Istishna: Pembayaran awal diset otomatis mengikuti harga DP (50%)
            $set('payment.amount', $dpAmount);
        } else {
            $set('akad', 'salam');
            $set('dp_amount', 0);
            // Akad Salam: Sesuai syariat wajib bayar tunai lunas 100% di awal pesanan
            $set('payment.amount', $totalPrice);
        }
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('akad')
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (OrderStatus $state): string => match ($state->value) {
                        'menunggu_validasi_desain' => 'gray',
                        'menunggu_pembayaran' => 'warning',
                        'menunggu_verifikasi_pembayaran' => 'warning',
                        'siap_produksi' => 'info',
                        'sedang_diproduksi' => 'info',
                        'selesai_produksi' => 'success',
                        'menunggu_pelunasan' => 'warning',
                        'menunggu_verifikasi_pelunasan' => 'warning',
                        'siap_kirim' => 'success',
                        'dikirim' => 'success',
                        'selesai' => 'success',
                        'ditolak' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('total_price')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(OrderStatus::class),
                Tables\Filters\SelectFilter::make('akad')
                    ->options(Akad::class),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->tooltip('Aksi'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                //
            ]);
    }

    /**
     * Relation Manager: Kita hanya tampilkan items, payments, shipping, design, refund sebagai view
     */
    public static function getRelations(): array
    {
        return [
            RelationManagers\ItemsRelationManager::class,
            RelationManagers\DesignRelationManager::class,
            RelationManagers\PaymentsRelationManager::class,
            RelationManagers\RefundsRelationManager::class,
            RelationManagers\ShippingRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrders::route('/create'),
            'edit' => Pages\EditOrders::route('/{record}/edit'),
        ];
    }
}