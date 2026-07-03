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
                Forms\Components\Select::make('courier')
                    ->options([
                        'jne' => 'JNE',
                        'jnt' => 'J&T Express',
                        'grab_express' => 'GrabExpress',
                        'go_send' => 'GoSend',
                    ])
                    ->required(),
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
            'index' => Pages\ListShippings::route('/'),
            'create' => Pages\CreateShippings::route('/create'),
            'edit' => Pages\EditShippings::route('/{record}/edit'),
        ];
    }
}
