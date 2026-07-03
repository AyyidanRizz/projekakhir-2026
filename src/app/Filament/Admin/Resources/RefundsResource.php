<?php

namespace App\Filament\Admin\Resources;

use App\Enums\RefundStatus;
use App\Filament\Admin\Resources\RefundsResource\Pages;
use App\Filament\Admin\Resources\RefundsResource\RelationManagers;
use App\Models\Refunds;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RefundsResource extends Resource
{
    protected static ?string $model = Refunds::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-refund';
        protected static ?string $navigationGroup = 'Manajemen Keuangan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('order_id')
                    ->relationship('order', 'id')
                    ->required()
                    ->searchable(),
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_id')->label('Order ID')->sortable(),
                Tables\Columns\TextColumn::make('order.order_number')
                    ->label('Order')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')->label('Jumlah Refund')->money('IDR'),
                Tables\Columns\TextColumn::make('reason')->label('Alasan')->limit(30),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Tanggal'),
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
            'index' => Pages\ListRefunds::route('/'),
            'create' => Pages\CreateRefunds::route('/create'),
            'edit' => Pages\EditRefunds::route('/{record}/edit'),
        ];
    }
}
