<?php

namespace App\Filament\Admin\Resources;

use App\Enums\Akad;
use App\Enums\OrderStatus;
use App\Filament\Admin\Resources\OrdersResource\Pages;
use App\Filament\Admin\Resources\OrdersResource\RelationManagers;
use App\Models\Orders;
use App\Models\ProductsVariants; // Model ini aman digunakan
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrdersResource extends Resource
{
    protected static ?string $model = Orders::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),

                Forms\Components\Select::make('status')
                    ->options(OrderStatus::class)
                    ->required()
                    ->default(OrderStatus::MENUNGGU_VALIDASI_DESAIN),

                // 1. INPUT REPEATER UNTUK PRODUK ITEMS
                Forms\Components\Repeater::make('items')
                    ->relationship('items') 
                    ->schema([

                        // A. FIELD SELEKSI JENIS PRODUK
                        Forms\Components\Select::make('product_id')
                            ->label('Jenis Produk')
                            ->options(\App\Models\Products::pluck('name', 'id')) 
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                // Ketika jenis produk diganti, reset pilihan varian dan harga di baris yang sama
                                $set('product_variant_id', null);
                                $set('unit_price', null);
                                $set('subtotal', 0);
                            }),

                        // B. FIELD SELEKSI VARIAN PRODUK (Sudah disesuaikan dengan kolom database kamu)
                        Forms\Components\Select::make('product_variant_id')
                            ->label('Varian Produk')
                            ->required()
                            ->disabled(fn (Get $get) => !$get('product_id'))
                            ->options(function (Get $get) {
                                $productId = $get('product_id');
                                
                                if (!$productId) {
                                    return [];
                                }

                                // Ambil data berdasarkan product_id, lalu buat label gabungan dari kolom 'size' dan 'material'
                                return ProductsVariants::where('product_id', $productId)
                                    ->get()
                                    ->mapWithKeys(function ($variant) {
                                        $label = trim(($variant->size ?? '') . ' - ' . ($variant->material ?? ''));
                                        // Jika size & material kosong, gunakan default ID agar tidak kosong text-nya
                                        $label = $label ?: "Varian ID: {$variant->id}"; 
                                        
                                        return [$variant->id => $label];
                                    });
                            })
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                if ($state) {
                                    $variant = ProductsVariants::find($state);
                                    if ($variant) {
                                        $set('unit_price', $variant->price);
                                    }
                                }
                                static::updateAkadDanHarga($set, $get);
                            }),

                        Forms\Components\TextInput::make('quantity')
                            ->label('Jumlah (Pcs)')
                            ->numeric()
                            ->default(1)
                            ->required()
                            ->live() 
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                // Update subtotal untuk baris ini
                                $price = (float) ($get('unit_price') ?? 0);
                                $set('subtotal', ((int) $state) * $price);

                                static::updateAkadDanHarga($set, $get, true);
                            }),

                        Forms\Components\TextInput::make('unit_price')
                            ->label('Harga Satuan')
                            ->numeric()
                            ->required()
                            ->prefix('Rp')
                            ->live() 
                        ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                // Update subtotal untuk baris ini
                                $qty = (int) ($get('quantity') ?? 0);
                                $set('subtotal', $qty * ((float) $state));

                                static::updateAkadDanHarga($set, $get, true);
                            }),
                        
                // TAMBAHKAN FIELD SUB TOTAL (Hidden/Readonly agar terkirim ke database)
                        Forms\Components\TextInput::make('subtotal')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->readOnly()
                            ->live(),
                    ])
                    ->live() 
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        static::updateAkadDanHarga($set, $get, false);
                    })
                    ->columnSpanFull(),

                // 2. FIELD AKAD DINAMIS
                Forms\Components\Select::make('akad')
                    ->required()
                    ->options([
                        'salam' => 'Akad Salam (Bayar 100%)',
                        'istishna' => 'Akad Istishna (DP 50%)',
                    ])
                    ->live()
                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                        $totalPrice = (float) $get('total_price') ?? 0;
                        if ($state === 'istishna') {
                            $set('dp_amount', $totalPrice * 0.5);
                        } else {
                            $set('dp_amount', 0);
                        }
                    }),

                Forms\Components\TextInput::make('total_price')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->live()
                    ->readOnly(),

                Forms\Components\TextInput::make('dp_amount')
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0),

                Forms\Components\TextInput::make('paid_amount')
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0),

                Forms\Components\TextInput::make('refund_amount')
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0),

                Forms\Components\Textarea::make('shipping_address')
                    ->maxLength(65535)
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('note')
                    ->maxLength(65535)
                    ->columnSpanFull(),

                Forms\Components\DateTimePicker::make('order_date')
                    ->required()
                    ->default(now()),
            ]);
    }

    protected static function updateAkadDanHarga(Set $set, Get $get)
    {
        $items = $get('items') ?? [];
        
        $totalQuantity = 0;
        $totalPrice = 0;

        foreach ($items as $item) {
            $qty = (int) ($item['quantity'] ?? 0);
            $price = (float) ($item['unit_price'] ?? 0);
            
            $totalQuantity += $qty;
            $totalPrice += ($qty * $price);
        }

        $set('total_price', $totalPrice);

        if ($totalQuantity < 12) {
            $set('akad', 'salam');
            $set('dp_amount', 0);
        } else {
            $set('akad', 'istishna');
            $set('dp_amount', $totalPrice * 0.5);
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')->searchable(),
                Tables\Columns\TextColumn::make('user.name')->searchable(),
                Tables\Columns\TextColumn::make('akad')->badge(),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('total_price')->money('IDR'),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
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
                    Tables\Actions\DeleteAction::make(),
                ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->tooltip('Aksi'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Admin\Resources\OrdersResource\RelationManagers\ItemsRelationManager::class,
            \App\Filament\Admin\Resources\OrdersResource\RelationManagers\DesignRelationManager::class,
            \App\Filament\Admin\Resources\OrdersResource\RelationManagers\PaymentsRelationManager::class,
            \App\Filament\Admin\Resources\OrdersResource\RelationManagers\RefundsRelationManager::class,
            \App\Filament\Admin\Resources\OrdersResource\RelationManagers\ShippingRelationManager::class,
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