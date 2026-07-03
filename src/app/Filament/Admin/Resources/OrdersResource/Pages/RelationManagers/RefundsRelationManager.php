<?php

namespace App\Filament\Admin\Resources\OrdersResource\RelationManagers;

use App\Enums\RefundStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class RefundsRelationManager extends RelationManager
{
    protected static string $relationship = 'refunds';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
                Forms\Components\Textarea::make('reason')
                    ->required()
                    ->maxLength(65535),
                Forms\Components\Select::make('status')
                    ->options(RefundStatus::class)
                    ->required()
                    ->default(RefundStatus::PENDING),
                Forms\Components\Select::make('processed_by')
                    ->relationship('processor', 'name')
                    ->searchable(),
                Forms\Components\DateTimePicker::make('processed_at'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('amount')->money('IDR'),
                Tables\Columns\TextColumn::make('reason')->limit(50),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('processed_at')->dateTime(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('process')
                    ->button()
                    ->label('Proses Refund')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === RefundStatus::PENDING)
                    ->action(function ($record) {
                        $record->update([
                            'status' => RefundStatus::PROCESSED,
                            'processed_at' => now(),
                            'processed_by' => Auth::id()
                        ]);
                        // Update order refund_amount
                        $record->order->increment('refund_amount', $record->amount);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Proses Refund'),

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