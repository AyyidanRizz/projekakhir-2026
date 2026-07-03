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
                Forms\Components\Select::make('payment_method')
                    ->options(PaymentMethod::class)
                    ->required(),
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayments::route('/create'),
            'edit' => Pages\EditPayments::route('/{record}/edit'),
        ];
    }
}
