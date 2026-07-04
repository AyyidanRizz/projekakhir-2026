<?php

namespace App\Filament\Admin\Resources\RefundsResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class OrderRelationManager extends RelationManager
{
    protected static string $relationship = 'order';

    protected static ?string $recordTitleAttribute = 'order_number';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Nomor Order')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                Tables\Columns\TextColumn::make('total_price')
                    ->label('Total')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pembeli'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Order')
                    ->dateTime(),
            ])
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}