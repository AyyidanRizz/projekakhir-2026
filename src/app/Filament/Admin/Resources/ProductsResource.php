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
    protected static ?string $navigationGroup = 'Manajemen Produk';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Utama Produk')
                    ->schema([
                        Forms\Components\Select::make('category_id')
                            ->label('Kategori') // Memberikan label yang rapi di form
                            ->relationship('category', 'name') // Mengambil data relasi dari model 'category' kolom 'name'
                            ->searchable() // Mengaktifkan fitur pencarian di dalam dropdown
                            ->preload() // 🔥 Trik Utama: Memuat data di awal agar langsung muncul saat diklik tanpa harus mengetik dulu
                            ->placeholder('Pilih atau cari kategori...') // Teks bantuan di dalam select
                            ->required(),
                        
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->reactive()
                            ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),
                        
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->disabled()
                            ->dehydrated(),
                        
                        Forms\Components\TextInput::make('base_price')
                            ->label('Harga Dasar')
                            ->required()
                            ->numeric()
                            ->prefix('Rp'),
                        
                        Forms\Components\FileUpload::make('image')
                            ->image()
                            ->directory('products')
                            ->imageEditor()
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('1:1')
                            ->imageResizeTargetWidth('800')
                            ->imageResizeTargetHeight('800'),
                        
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
                                Forms\Components\Select::make('size')
                                    ->label('Ukuran')
                                    ->options([
                                        'S' => 'S',
                                        'M' => 'M',
                                        'L' => 'L',
                                        'XL' => 'XL',
                                        'XXL' => 'XXL',
                                        '3XL' => '3XL',
                                        'All Size' => 'All Size',
                                    ])
                                    ->placeholder('Pilih Ukuran')
                                    ->searchable(),
                                
                                Forms\Components\Select::make('material')
                                    ->label('Bahan')
                                    ->options([
                                        'Cotton Combed 30s' => 'Cotton Combed 30s',
                                        'Cotton Combed 24s' => 'Cotton Combed 24s',
                                        'Canvas' => 'Canvas',
                                        'Denim' => 'Denim',
                                        'Fleece' => 'Fleece',
                                    ])
                                    ->placeholder('Pilih Bahan')
                                    ->searchable(),
                                
                                Forms\Components\TextInput::make('price')
                                    ->label('Harga Final Varian')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp'),
                                
                                Forms\Components\TextInput::make('stock')
                                    ->label('Stok')
                                    ->numeric()
                                    ->required()
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
                Tables\Columns\ImageColumn::make('image')->square(),
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