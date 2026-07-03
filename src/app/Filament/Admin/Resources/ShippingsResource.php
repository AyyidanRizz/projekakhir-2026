<?php

namespace App\Filament\Admin\Resources;

use App\Enums\Courier;
use App\Filament\Admin\Resources\ShippingsResource\Pages;
use App\Filament\Admin\Resources\ShippingsResource\RelationManagers;
use App\Models\Shippings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ShippingsResource extends Resource
{
    protected static ?string $model = Shippings::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('order_id')
                ->relationship('order', 'id')
                ->required()
                ->searchable()
                ->disabledOn('edit'), // Kunci ketika mode edit agar tidak bisa dipindahkan ke order lain

                Forms\Components\Select::make('courier')
                    ->label('Kurir')
                    ->options(\App\Enums\Courier::class) // Otomatis membaca enum kamu (JNE, JNT, dll)
                    ->required(),

                Forms\Components\TextInput::make('tracking_number')
                    ->label('No. Resi')
                    ->maxLength(255),

                // Tambahkan field text area alamat pengiriman jika ada di database kamu
                Forms\Components\Textarea::make('shipping_address')
                    ->label('Alamat Pengiriman')
                    ->rows(3),

                // Tambahkan field status pengiriman jika diperlukan
                Forms\Components\Select::make('status')
                    ->label('Status Pengiriman')
                    ->options([
                        'pending' => 'Pending',
                        'shipped' => 'Shipped',
                        'delivered' => 'Delivered',
                    ]),

                Forms\Components\DateTimePicker::make('shipped_at')
                    ->label('Waktu Dikirim'),
                    
                Forms\Components\DateTimePicker::make('delivered_at')
                    ->label('Waktu Diterima'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_id')->label('Order ID')->sortable(),
                Tables\Columns\TextColumn::make('courier')->label('Kurir')->searchable(),
                Tables\Columns\TextColumn::make('tracking_number')->label('No. Resi')->placeholder('Belum ada resi'),
                Tables\Columns\TextColumn::make('shipping_address')->label('Alamat Pengiriman')->limit(30),
                Tables\Columns\TextColumn::make('status')->label('Status Pengiriman')->badge(),
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
            'index' => Pages\ListShippings::route('/'),
            'create' => Pages\CreateShippings::route('/create'),
            'edit' => Pages\EditShippings::route('/{record}/edit'),
        ];
    }
}
