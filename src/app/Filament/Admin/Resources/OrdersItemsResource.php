<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\OrdersItemsResource\Pages;
use App\Filament\Admin\Resources\OrdersItemsResource\RelationManagers;
use App\Models\OrdersItems;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrdersItemsResource extends Resource
{
    protected static ?string $model = OrdersItems::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // 1. Dropdown untuk memilih Order ID
                Forms\Components\Select::make('order_id')
                    ->relationship('order', 'id') // sesuaikan nama relasi di model Anda
                    ->required()
                    ->searchable(),

                // 2. Dropdown untuk memilih Varian Produk
                Forms\Components\Select::make('products_variant_id')
                    ->relationship('productVariant', 'size') // sesuaikan nama relasi & kolom yang mau dipamerkan
                    ->label('Product Variant')
                    ->required()
                    ->searchable(),

                // 3. Input Jumlah (Quantity)
                Forms\Components\TextInput::make('quantity')
                    ->numeric()
                    ->default(1)
                    ->required(),

                // 4. Input Harga (Price)
                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListOrdersItems::route('/'),
            'create' => Pages\CreateOrdersItems::route('/create'),
            'edit' => Pages\EditOrdersItems::route('/{record}/edit'),
        ];
    }
}
