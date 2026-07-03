<?php

namespace App\Filament\Admin\Resources;

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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_id')->label('Order ID')->sortable(),
                Tables\Columns\TextColumn::make('amount')->label('Jumlah Refund')->money('IDR'),
                Tables\Columns\TextColumn::make('reason')->label('Alasan')->limit(30),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Tanggal'),
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
            'index' => Pages\ListRefunds::route('/'),
            'create' => Pages\CreateRefunds::route('/create'),
            'edit' => Pages\EditRefunds::route('/{record}/edit'),
        ];
    }
}
