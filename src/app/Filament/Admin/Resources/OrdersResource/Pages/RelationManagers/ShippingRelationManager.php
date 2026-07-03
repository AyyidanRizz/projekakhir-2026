<?php

namespace App\Filament\Admin\Resources\OrdersResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ShippingRelationManager extends RelationManager
{
    protected static string $relationship = 'shipping';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('courier')
                ->label('Kurir')
                ->options(\App\Enums\Courier::class) // Gunakan enum yang sama
                ->required(),
                
                Forms\Components\TextInput::make('tracking_number')
                    ->label('No. Resi')
                    ->maxLength(255),
                    
                Forms\Components\DateTimePicker::make('shipped_at')
                    ->label('Waktu Dikirim'),
                    
                Forms\Components\DateTimePicker::make('delivered_at')
                    ->label('Waktu Diterima'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('courier'),
                Tables\Columns\TextColumn::make('tracking_number'),
                Tables\Columns\TextColumn::make('shipped_at')->dateTime(),
                Tables\Columns\TextColumn::make('delivered_at')->dateTime(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}