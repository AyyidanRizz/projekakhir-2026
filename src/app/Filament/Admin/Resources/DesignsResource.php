<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\DesignsResource\Pages;
use App\Filament\Admin\Resources\DesignsResource\RelationManagers;
use App\Models\Designs;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DesignsResource extends Resource
{
    protected static ?string $model = Designs::class;

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
                Tables\Columns\ImageColumn::make('file_path') 
                    ->label('Foto Design')
                    ->disk('public')
                    ->square()
                    ->size(60),
                Tables\Columns\TextColumn::make('notes')->label('Catatan')->limit(30),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Tanggal Dibuat'),
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
            'index' => Pages\ListDesigns::route('/'),
            'create' => Pages\CreateDesigns::route('/create'),
            'edit' => Pages\EditDesigns::route('/{record}/edit'),
        ];
    }
}
