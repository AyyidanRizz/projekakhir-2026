<?php

namespace App\Filament\Admin\Resources\OrdersResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\ProductsVariants;
use App\Models\Products;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';
    protected static ?string $recordTitleAttribute = 'id';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // 1. Pilih Produk Utama terlebih dahulu
                /*Forms\Components\Select::make('product_id')
                    ->label('Product')
                    ->options(\App\Models\Products::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($set) {
                        // Reset pilihan varian ketika produk utama diganti
                        $set('product_variant_id', null);
                        $set('unit_price', 0);
                        $set('subtotal', 0);
                    })
                    ->dehydrated(false), // Kolom bantuan, tidak disimpan ke database order_items

                // 2. Dropdown Varian yang opsional dan dinamis tergantung Product yang dipilih
                Forms\Components\Select::make('product_variant_id')
                    ->label('Pilih Varian / Ukuran')
                    ->required()
                    ->reactive()
                    ->options(function (callable $get) {
                        $productId = $get('product_id');
                        if (! $productId) {
                            return [];
                        }

                        // Ambil semua daftar varian dari produk yang dipilih
                        $variants = \App\Models\ProductsVariants::where('product_id', $productId)->get();

                        return $variants->mapWithKeys(function ($variant) {
                            // Cek jika varian tidak punya ukuran maupun bahan (Produk tanpa varian)
                            if (empty($variant->size) && empty($variant->material)) {
                                return [$variant->id => 'Standard / No Variant'];
                            }
                            
                            // Gabungkan teks varian yang ada
                            $labelParts = [];
                            if (!empty($variant->size)) $labelParts[] = "Ukuran: {$variant->size}";
                            if (!empty($variant->material)) $labelParts[] = "Bahan: {$variant->material}";
                            
                            return [$variant->id => implode(' - ', $labelParts)];
                        });
                    })
                    ->disabled(fn (callable $get) => !$get('product_id')) // Kunci jika produk belum dipilih
                    ->afterStateUpdated(function ($state, $set, $get) {
                        // Ambil harga dari varian yang dipilih dan update subtotal
                        $variant = \App\Models\ProductsVariants::find($state);
                        if ($variant) {
                            $price = $variant->price ?? 0; 
                            $set('unit_price', $price);
                            
                            $quantity = $get('quantity') ?? 1;
                            $set('subtotal', $price * $quantity);
                        }
                    }),*/
                
                Forms\Components\Select::make('product_variant_id')
                    ->label('Varian Produk')
                    ->relationship('variant', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => 
                        $record->product->name . ' - ' . $record->size . ' ' . $record->material . ' (Stok: ' . $record->stock . ')'
                    )
                    ->required()
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        if ($state) {
                            $variant = ProductsVariants::find($state);
                            if ($variant) {
                                $set('unit_price', $variant->price);
                            }
                        }
                    }),

                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->default(1)
                    ->reactive()
                    ->afterStateUpdated(function ($set, $get) {
                        $unitPrice = (float) ($get('unit_price') ?? 0);
                        $quantity = (int) ($get('quantity') ?? 1);
                        $set('subtotal', $unitPrice * $quantity);
                    }),

                Forms\Components\TextInput::make('unit_price')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->reactive()
                    ->afterStateUpdated(function ($set, $get) {
                        $unitPrice = (float) ($get('unit_price') ?? 0);
                        $quantity = (int) ($get('quantity') ?? 1);
                        $set('subtotal', $unitPrice * $quantity);
                    }),

                Forms\Components\TextInput::make('subtotal')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->disabled()
                    ->dehydrated(), // Tetap simpan nilai ke DB meskipun dalam kondisi disabled
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('variant.product.name')
                    ->label('Product')
                    ->searchable(),
                
                // Merapikan label tabel ganda dari gambar image_cd310e.png
                Tables\Columns\TextColumn::make('variant.size')
                    ->label('Ukuran')
                    ->placeholder('-'), // Menampilkan '-' jika produk tidak memiliki ukuran
                    
                Tables\Columns\TextColumn::make('variant.material')
                    ->label('Bahan')
                    ->placeholder('-'), // Menampilkan '-' jika produk tidak memiliki bahan
                    
                Tables\Columns\TextColumn::make('quantity')
                    ->alignCenter(),
                    
                Tables\Columns\TextColumn::make('unit_price')
                    ->money('IDR'),
                    
                Tables\Columns\TextColumn::make('subtotal')
                    ->money('IDR'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['subtotal'] = (int)($data['quantity'] ?? 1) * (float)($data['unit_price'] ?? 0);
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->mutateFormDataUsing(function (array $data): array {
                            $data['subtotal'] = (int)($data['quantity'] ?? 1) * (float)($data['unit_price'] ?? 0);
                            return $data;
                        })
                        // Saat edit, kita perlu mengisi field 'product_id' bantuan agar dropdown varian berfungsi kembali
                        ->mutateRecordDataUsing(function (array $data): array {
                            $variant = \App\Models\ProductsVariants::find($data['product_variant_id']);
                            if ($variant) {
                                $data['product_id'] = $variant->product_id;
                            }
                            return $data;
                        }),
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
}