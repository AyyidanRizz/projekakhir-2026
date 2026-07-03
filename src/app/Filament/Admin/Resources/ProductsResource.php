<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ProductsResource\Pages;
use App\Models\Products;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProductsResource extends Resource
{
    protected static ?string $model = Products::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Utama Produk')
                    ->schema([
                        Forms\Components\Select::make('category_id')
                            ->relationship('category', 'name')
                            ->required()
                            ->searchable(),
                        
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->reactive()
                            ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),
                        
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        
                        Forms\Components\TextInput::make('base_price')
                            ->label('Harga Dasar')
                            ->required()
                            ->numeric()
                            ->prefix('Rp'),
                        
                        Forms\Components\FileUpload::make('image')
                            ->image()
                            ->directory('products'),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->required()
                            ->default(true),
                    ])->columns(2),

                Forms\Components\Section::make('Detail Deskripsi')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ]),

                // SEKSI BARU: Mengelola Varian Langsung di Sini
                Forms\Components\Section::make('Opsi Varian & Ukuran Produk')
                    ->description('Jika produk tidak memiliki ukuran atau bahan khusus (misal: aksesoris), cukup buat 1 varian dengan mengosongkan kolom Ukuran & Bahan, lalu isi Harga Finalnya.')
                    ->schema([
                        Forms\Components\Repeater::make('variants')
                            ->relationship('variants') // Menghubungkan langsung ke relasi hasMany 'variants'
                            ->label('Daftar Varian')
                            ->schema([
                                Forms\Components\TextInput::make('size')
                                    ->label('Ukuran')
                                    ->placeholder('Contoh: S, M, L, XL atau All Size')
                                    ->maxLength(255),
                                
                                Forms\Components\TextInput::make('material')
                                    ->label('Bahan / Material')
                                    ->placeholder('Contoh: Cotton Combed, Canvas')
                                    ->maxLength(255),
                                
                                Forms\Components\TextInput::make('price')
                                    ->label('Harga Final Varian')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp'),
                                
                                Forms\Components\TextInput::make('stock')
                                    ->label('Stok')
                                    ->numeric()
                                    ->default(0),
                            ])
                            ->columns(4) // Membagi menjadi 4 kolom horizontal agar rapi dan hemat space
                            ->defaultItems(1) // Otomatis menyediakan 1 baris inputan awal
                            ->createItemButtonLabel('Tambah Varian Baru')
                            ->collapsible(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('category.name')->sortable(),
                Tables\Columns\TextColumn::make('base_price')->money('IDR'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->relationship('category', 'name'),
            ])
            ->actions([
                // Mengubah tombol aksi bawaan menjadi tombol titik tiga vertikal agar seragam dengan tabel lain
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
            // Kita bisa menonaktifkan atau menghapus baris ini karena pengisian varian sudah dipindah ke form utama
            // Pages\RelationManagers\VariantsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProducts::route('/create'),
            'edit' => Pages\EditProducts::route('/{record}/edit'),
        ];
    }
}