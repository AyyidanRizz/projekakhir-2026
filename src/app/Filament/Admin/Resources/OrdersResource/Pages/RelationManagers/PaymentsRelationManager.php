<?php

namespace App\Filament\Admin\Resources\OrdersResource\RelationManagers;

use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->options(PaymentType::class)
                    ->required(),
                Forms\Components\Select::make('payment_method')
                    ->options([
                        'virtual_account' => 'Virtual Account (Transfer Bank)',
                        'qris' => 'QRIS (E-Wallet)',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
                Forms\Components\Select::make('status')
                    ->options(PaymentStatus::class)
                    ->required()
                    ->default(PaymentStatus::PENDING),
                Forms\Components\FileUpload::make('proof_file')
                    ->directory('payment_proofs')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'application/pdf']),
                Forms\Components\Select::make('verified_by')
                    ->relationship('verifier', 'name')
                    ->searchable(),
                Forms\Components\DateTimePicker::make('verified_at'),
                Forms\Components\Textarea::make('notes'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')->badge(),
                Tables\Columns\TextColumn::make('payment_method')->badge(),
                Tables\Columns\TextColumn::make('amount')->money('IDR'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (\App\Enums\PaymentStatus $state): string => match ($state) {
                        \App\Enums\PaymentStatus::PENDING => 'blue',
                        \App\Enums\PaymentStatus::VERIFIED => 'green',
                        \App\Enums\PaymentStatus::REJECTED => 'red',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('verified_at')->dateTime(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('verify')
                    ->button()
                    ->label('Verifikasi')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === PaymentStatus::PENDING)
                    ->action(function ($record) {
                        $record->update([
                            'status' => PaymentStatus::VERIFIED,
                            'verified_at' => now(),
                            'verified_by' => Auth::id()
                        ]);
                        // Update order status ke siap_produksi
                        $order = $record->order;
                        $order->update(['status' => 'siap_produksi']);
                        // Update paid_amount
                        $order->increment('paid_amount', $record->amount);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Verifikasi Pembayaran'),
                Tables\Actions\Action::make('reject_payment')
                    ->button()
                    ->label('Tolak')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status === PaymentStatus::PENDING)
                    ->action(function ($record) {
                        $record->update(['status' => PaymentStatus::REJECTED]);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Tolak Pembayaran'),
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