<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PesananItemResource\Pages;
use App\Filament\Admin\Resources\PesananItemResource\RelationManagers;
use App\Models\PesananItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PesananItemResource extends Resource
{
    protected static ?string $model = PesananItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';
    protected static ?string $navigationGroup = 'Toko';
    protected static ?int $navigationSort=4;
    protected static ?string $label = 'Pesanan Item';
    protected static ?string $pluralLabel = 'Pesanan Item';
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
                //
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
            'index' => Pages\ListPesananItems::route('/'),
            'create' => Pages\CreatePesananItem::route('/create'),
            'edit' => Pages\EditPesananItem::route('/{record}/edit'),
        ];
    }
}
