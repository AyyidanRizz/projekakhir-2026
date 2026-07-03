<?php

namespace App\Filament\Admin\Resources;

use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Enums\PaymentMethod;
use App\Filament\Admin\Resources\PaymentsResource\Pages;
use App\Models\Payments;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentsResource extends Resource
{
    protected static ?string $model = Payments::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Manajemen Keuangan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('order_id')
                    ->relationship('order', 'order_number')
                    ->required()
                    ->searchable(),
                Forms\Components\Select::make('type')
                    ->options(PaymentType::class)
                    ->required(),
                Forms\Components\Select::make('payment_method')
                    ->options(PaymentMethod::class)
                    ->required()
                    ->label('Metode Pembayaran'),
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
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'application/pdf']),
                Forms\Components\Select::make('verified_by')
                    ->relationship('verifier', 'name')
                    ->searchable(),
                Forms\Components\DateTimePicker::make('verified_at'),
                Forms\Components\Textarea::make('notes')
                    ->maxLength(65535),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order.order_number')
                    ->label('Order')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->badge()
                    ->label('Metode'),
                Tables\Columns\TextColumn::make('amount')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (PaymentStatus $state): string => match ($state) {
                        PaymentStatus::PENDING => 'blue',
                        PaymentStatus::VERIFIED => 'green',
                        PaymentStatus::REJECTED => 'red',
                    }),
                Tables\Columns\TextColumn::make('verified_at')
                    ->dateTime(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(PaymentStatus::class),
                Tables\Filters\SelectFilter::make('type')
                    ->options(PaymentType::class),
                Tables\Filters\SelectFilter::make('payment_method')
                    ->options(PaymentMethod::class),
            ])
            ->actions([
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayments::route('/create'),
            'edit' => Pages\EditPayments::route('/{record}/edit'),
        ];
    }
}