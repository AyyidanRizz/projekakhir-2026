<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ProductsVariantsResource\Pages;
use App\Filament\Admin\Resources\ProductsVariantsResource\RelationManagers;
use App\Models\ProductsVariants;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductsVariantsResource extends Resource
{
    protected static ?string $model = ProductsVariants::class;
    protected static string $relationship = 'variants';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('size')
                    ->maxLength(255),
                Forms\Components\TextInput::make('material')
                    ->maxLength(255),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
                Forms\Components\TextInput::make('stock')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')->label('Produk')->searchable(),
                Tables\Columns\TextColumn::make('product.category.name')->label('Kategori'),
                Tables\Columns\TextColumn::make('size'),
                Tables\Columns\TextColumn::make('material'),
                Tables\Columns\TextColumn::make('price')->money('IDR'),
                Tables\Columns\TextColumn::make('stock')
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        $state <= 0 => 'danger',
                        $state <= 5 => 'warning',
                        default => 'success',
                    }),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ])
            ->filters([
                // Filter Kategori
                SelectFilter::make('category_id')
                    ->label('Filter Kategori')
                    ->relationship('product.category', 'name')
                    ->preload()
                    ->searchable(),
                // Filter Stok (seperti di atas)
                SelectFilter::make('stock_status')
                    ->label('Status Stok')
                    ->options([
                        'available' => 'Tersedia (Stok > 0)',
                        'out_of_stock' => 'Habis (Stok = 0)',
                        'low_stock' => 'Stok Menipis (<= 5)',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['value'] === 'available') {
                            $query->where('stock', '>', 0);
                        } elseif ($data['value'] === 'out_of_stock') {
                            $query->where('stock', '=', 0);
                        } elseif ($data['value'] === 'low_stock') {
                            $query->where('stock', '>', 0)->where('stock', '<=', 5);
                        }
                    }),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductsVariants::route('/'),
            'create' => Pages\CreateProductsVariants::route('/create'),
            'edit' => Pages\EditProductsVariants::route('/{record}/edit'),
        ];
    }
}
