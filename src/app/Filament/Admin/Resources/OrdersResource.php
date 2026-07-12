<?php

namespace App\Filament\Admin\Resources;

use App\Enums\PaymentMethod;
use App\Enums\Courier;
use App\Models\Products; // Tambahkan ini jika belum di-import
use App\Models\Refunds;
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
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

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
                            ->reactive() // Tambahkan reactive agar perubahan mendongkrak komponen lain
                            ->label('Pembeli')
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                // Jika pembeli dipilih, ambil data alamatnya dan set ke field shipping_address
                                if ($state) {
                                    $user = \App\Models\User::find($state);
                                    if ($user) {
                                        $fullAddress = $user->address . 
                                            ($user->city ? ', ' . $user->city : '') . 
                                            ($user->province ? ', ' . $user->province : '') . 
                                            ($user->postal_code ? ', ' . $user->postal_code : '');
                                        
                                        $set('shipping_address', $fullAddress);
                                        $set('shipping.shipping_address', $fullAddress);
                                    }
                                }
                            }),
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
                                    ->options(Products::where('is_active', true)->pluck('name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->reactive()
                                    ->dehydrated(false)
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
                                // 3. Jumlah Item dengan Validasi Keras Saat Simpan Form
                                Forms\Components\TextInput::make('quantity')
                                    ->label('Jumlah')
                                    ->required()
                                    ->numeric()
                                    ->minValue(1)
                                    ->default(1)
                                    ->reactive()
                                    // UBAH HANYA BAGIAN RULES INI:
                                    ->rules([
                                        fn (Forms\Get $get, $livewire): \Closure => function (string $attribute, $value, \Closure $fail) use ($get, $livewire) {
                                            $variantId = $get('product_variant_id');
                                            if (!$variantId) {
                                                return;
                                            }

                                            $variant = ProductsVariants::find($variantId);
                                            if (!$variant) {
                                                $fail("Varian produk tidak valid.");
                                                return;
                                            }

                                            // Memeriksa mode edit langsung menggunakan dependency injection $livewire
                                            $isEditMode = isset($livewire->record);
                                            $availableStock = (int) $variant->stock;

                                            if ($isEditMode) {
                                                $itemId = $get('id');
                                                if ($itemId) {
                                                    $oldItem = \App\Models\OrdersItems::find($itemId);
                                                    // Pastikan item yang ditemukan cocok dengan varian yang sedang divalidasi
                                                    if ($oldItem && $oldItem->product_variant_id == $variantId) {
                                                        $availableStock += (int) $oldItem->quantity;
                                                    }
                                                }
                                            }

                                            if ((int) $value > $availableStock) {
                                                $fail("Stok tidak mencukupi untuk melakukan checkout! (Maksimal tersedia: {$availableStock} pcs).");
                                            }
                                        },
                                    ])
                                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                        $unitPrice = (float) ($get('unit_price') ?? 0);
                                        $set('subtotal', ((int)$state) * $unitPrice);
                                        static::updateAkadDanHarga($set, $get);
                                    }),                                // 4. Harga Satuan (Otomatis Terkunci)
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
            //->disabled(fn ($record) => $record !== null);

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
                        'dibatalkan'=>'danger',
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
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\DeleteAction::make(),

                    // Aksi Batalkan Pesanan
                    Action::make('cancelOrder')
                        ->label('Batalkan Pesanan')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn ($record) => $record->canBeCancelled())
                        ->form(function ($record) {
                            $formFields = [];

                            // Jika Istishna dan sudah dalam tahap produksi, urai per item barang
                            if ($record->akad === Akad::ISTISHNA && $record->status === OrderStatus::DALAM_PRODUKSI) {
                                $formFields[] = Placeholder::make('info_produksi')
                                    ->label('Realisasi Produksi per Item')
                                    ->content('Silakan masukkan jumlah item yang TELANJUR diproduksi untuk masing-masing tipe barang di bawah ini:');

                                // Ambil detail item dari relasi (asumsi nama relasi: orderItems atau items)
                                // Sesuaikan 'orderItems' dengan nama fungsi relasi di model Orders Anda
                                foreach ($record->Items as $key => $item) {
                                    $itemNumber = $key + 1;
                                    $productName = $item->product?->name ?? 'Produk Unik';
                                    $orderedQty = $item->qty ?? 0;
                                    $formFields[] = TextInput::make("produced_qty_item_{$item->id}")
                                        ->label("Qty Terproduksi: " . ($item->product?->name ?? 'Produk Unik') . " (Pesan: {$item->qty} pcs)")
                                        ->numeric()
                                        ->default(0)
                                        ->required()
                                        ->minValue(0)
                                        ->maxValue($item->qty)
                                        ->helperText("Masukkan berapa pcs yang sudah selesai dibuat dari total {$item->qty} pcs.");
                                }
                            }

                            $formFields[] = Textarea::make('cancellation_note')
                                ->label('Alasan Pembatalan')
                                ->required();

                            return $formFields;
                        })
                        ->action(function ($record, array $data) {
                            $totalProducedQty = 0;
                            $itemProductionDetails = [];

                            // 1. Proses hitung kuantitas terproduksi per item jika ada
                            if ($record->akad === Akad::ISTISHNA && $record->status === OrderStatus::DALAM_PRODUKSI) {
                                $formFields[] = Forms\Components\Placeholder::make('info_produksi')
                                    ->label('Realisasi Produksi per Item')
                                    ->content('Silakan masukkan jumlah item yang TELANJUR diproduksi untuk masing-masing tipe barang di bawah ini:');
                                
                                foreach ($record->Items as $item) {
                                    
                                    $inputKey = "produced_qty_item_{$item->id}";
                                    $qtyTerproduksi = isset($data[$inputKey]) ? (int) $data[$inputKey] : 0;
                                    
                                    $totalProducedQty += $qtyTerproduksi;
                                    
                                    // Simpan log detail per item untuk keperluan kalkulasi/gudang jika dibutuhkan
                                    $itemProductionDetails[$item->id] = $qtyTerproduksi;
                                    
                                    // (Opsional) Jika model Anda mendukung restore stok per item secara langsung:
                                    // $item->restoreItemStock($qtyTerproduksi);
                                }
                            }

                            // 2. Hitung nominal kalkulasi refund via Model Orders (menggunakan total qty/logika internal Anda)
                            $refundAmount = $record->calculateRefundAmount($totalProducedQty);

                            // 3. Eksekusi pengembalian sisa stok ke gudang
                            $record->restoreStock($totalProducedQty);

                            // 4. Update data internal tabel Orders
                            $record->status = OrderStatus::DIBATALKAN;
                            $record->produced_qty_on_cancel = $totalProducedQty;
                            $record->cancellation_note = $data['cancellation_note'];
                            $record->refund_amount = $refundAmount;
                            $record->save();

                            // 5. MEMBUAT DATA REFUND BARU (Agar muncul di Dashboard Utama & Relation Manager)
                            // Sesuaikan nama kolom di bawah dengan struktur migrasi tabel refunds Anda
                            if ($refundAmount > 0) {
                                Refunds::create([
                                    'order_id' => $record->id,
                                    'user_id' => $record->user_id, // Pelanggan yang menerima refund
                                    'amount' => $refundAmount,
                                    'reason' => $data['cancellation_note'],
                                    'status' => 'pending', // atau status awal refund di sistem Anda
                                    // 'created_at' otomatis terisi oleh Eloquent
                                ]);
                            }

                            // 6. Kirim Notifikasi Sukses ke UI Filament dengan Nominal Angka
                            Notification::make()
                                ->title('Pesanan Berhasil Dibatalkan')
                                ->body("Status pesanan kini dibatalkan. Nominal Refund dikeluarkan: Rp " . number_format($refundAmount, 0, ',', '.'))
                                ->success()
                                ->persistent() // Agar notifikasi tidak langsung hilang dan bisa dibaca admin
                                ->send();
                        }),

                    // Aksi Ubah Status (Pastikan koma diletakkan setelah tutup kurung ) dari cancelOrder di atas)
                    Tables\Actions\Action::make('ubahStatus')
                        ->label('Ubah Status')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->mountUsing(fn (Forms\ComponentContainer $form, $record) => $form->fill([
                            'status' => $record->status,
                        ]))
                        ->form([
                            Forms\Components\Select::make('status')
                                ->label('Status Baru')
                                ->options(OrderStatus::class)
                                ->required(),
                        ])
                        ->action(function ($record, array $data): void {
                            $record->update([
                                'status' => $data['status'],
                            ]);
                            Notification::make()
                                ->title('Status berhasil diperbarui')
                                ->success()
                                ->send();
                        }),
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