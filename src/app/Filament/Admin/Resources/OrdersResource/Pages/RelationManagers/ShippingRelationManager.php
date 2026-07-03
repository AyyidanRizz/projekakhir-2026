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
                    ->options([
                        'jne' => 'JNE',
                        'jnt' => 'J&T Express',
                        'grab_express' => 'GrabExpress',
                        'go_send' => 'GoSend',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('tracking_number')
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('shipped_at'),
                Forms\Components\DateTimePicker::make('delivered_at'),
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
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->tooltip('Aksi'),
            ])
            ->bulkActions([]);
    }
}