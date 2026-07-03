<?php

namespace App\Filament\Admin\Resources\OrdersResource\RelationManagers;

use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

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

                // TAMBAHKAN INI: Opsi pilihan metode pembayaran untuk pembeli/admin
                Forms\Components\Select::make('payment_method')
                    ->label('Metode Pembayaran')
                    ->options(\App\Enums\PaymentMethod::class)
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
                    ->relationship('verifier', 'name'),

                Forms\Components\DateTimePicker::make('verified_at'),

                Forms\Components\Textarea::make('notes')
                    ->maxLength(65535),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')->badge(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Metode Pembayaran')
                    ->badge() // Opsional: Diberi badge agar tampilannya cantik seperti status
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')->money('IDR'),
                Tables\Columns\TextColumn::make('status')->badge(),
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
                    ->action(fn ($record) => $record->update(['status' => PaymentStatus::VERIFIED, 'verified_at' => now()]))
                    ->requiresConfirmation()
                    ->color('success'),
                Tables\Actions\Action::make('reject')
                    ->action(fn ($record) => $record->update(['status' => PaymentStatus::REJECTED]))
                    ->requiresConfirmation()
                    ->color('danger'),

                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
                ->icon('heroicon-m-ellipsis-vertical') // Ini yang membuat ikonnya jadi titik tiga vertikal
                ->tooltip('Aksi'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}