<?php

namespace App\Filament\Admin\Resources;

use App\Enums\PaymentMethod;
use App\Filament\Admin\Resources\PaymentsResource\Pages;
use App\Filament\Admin\Resources\PaymentsResource\RelationManagers;
use App\Models\Payments;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentsResource extends Resource
{
    protected static ?string $model = Payments::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('order_id')
                    ->relationship('order', 'id')
                    ->required()
                    ->searchable()
                    ->disabledOn('edit'),

                Forms\Components\Select::make('type')
                    ->options(\App\Enums\PaymentType::class)
                    ->required(),

                Forms\Components\Select::make('payment_method')
                    ->label('Metode Pembayaran')
                    ->options(\App\Enums\PaymentMethod::class)
                    ->required(),

                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),

                Forms\Components\Select::make('status')
                    ->options(\App\Enums\PaymentStatus::class)
                    ->required(),

                Forms\Components\FileUpload::make('proof_file')
                    ->directory('payment_proofs'),

                Forms\Components\Textarea::make('notes')
                    ->maxLength(65535),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_id')->label('Order ID')->sortable(),
                Tables\Columns\TextColumn::make('amount')->label('Jumlah Bayar')->money('IDR'),
                Tables\Columns\TextColumn::make('payment_method')->label('Metode Pembayaran')->badge(),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Waktu Bayar'),
            ])
            ->filters([
                //
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayments::route('/create'),
            'edit' => Pages\EditPayments::route('/{record}/edit'),
        ];
    }
}
