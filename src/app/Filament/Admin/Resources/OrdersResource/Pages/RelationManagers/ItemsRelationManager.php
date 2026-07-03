<?php

namespace App\Filament\Admin\Resources\OrdersResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $recordTitleAttribute = 'id';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('variant.product.name')
                    ->label('Produk'),
                Tables\Columns\TextColumn::make('variant.size')
                    ->label('Ukuran'),
                Tables\Columns\TextColumn::make('variant.material')
                    ->label('Material'),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Jumlah'),
                Tables\Columns\TextColumn::make('unit_price')
                    ->label('Harga Satuan')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->money('IDR'),
            ])
            ->filters([
                //
            ])
            // Hapus aksi tambah/edit/hapus agar hanya sebagai viewer
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}