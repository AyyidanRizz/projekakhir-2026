<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\OrdersItemsResource\Pages;
use App\Filament\Admin\Resources\OrdersItemsResource\RelationManagers;
use App\Models\OrdersItems;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrdersItemsResource extends Resource
{
    protected static ?string $model = OrdersItems::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'Manajemen Pesanan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            // TAMBAHKAN INI: Agar saat input manual di menu Orders Items, bisa pilih Order ID-nya
            Forms\Components\Select::make('order_id')
                ->relationship('order', 'id')
                ->required(),
            Forms\Components\Select::make('product_variant_id')
                ->relationship('variant', 'id', fn ($query) => $query->with('product'))
                ->getOptionLabelFromRecordUsing(fn ($record) => $record->product->name . ' - ' . $record->size . ' ' . $record->material)
                ->required(),

            Forms\Components\TextInput::make('quantity')
                ->required()
                ->numeric()
                ->minValue(1)
                ->reactive()
                ->afterStateUpdated(function ($set, $get) {
                    $unitPrice = (float) ($get('unit_price') ?? 0);
                    $quantity = (int) ($get('quantity') ?? 1);
                    $set('subtotal', $unitPrice * $quantity);
                }),

            Forms\Components\TextInput::make('unit_price')
                ->required()
                ->numeric()
                ->prefix('Rp')
                ->reactive()
                ->afterStateUpdated(function ($set, $get) {
                    $unitPrice = (float) ($get('unit_price') ?? 0);
                    $quantity = (int) ($get('quantity') ?? 1);
                    $set('subtotal', $unitPrice * $quantity);
                }),

            Forms\Components\TextInput::make('subtotal')
                ->required()
                ->numeric()
                ->prefix('Rp')
                ->readOnly()
                ->dehydrated(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order.id')
                    ->label('Order ID'),
                Tables\Columns\TextColumn::make('order.order_number')
                    ->label('Order'),
                Tables\Columns\TextColumn::make('variant.product.name')->label('Produk'),
                Tables\Columns\TextColumn::make('variant.size')->label('Ukuran'),
                Tables\Columns\TextColumn::make('variant.material')->label('Material'),
                Tables\Columns\TextColumn::make('quantity')->label('Jumlah'),
                Tables\Columns\TextColumn::make('unit_price')->label('Harga Satuan')->money('IDR'),
                Tables\Columns\TextColumn::make('subtotal')->money('IDR'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('order_id')
                    ->label('Filter Berdasarkan Order')
                    ->relationship('order', 'id') // Menampilkan nomor order di opsi pilihan
                    ->searchable() // Opsional: Agar user bisa mengetik/mencari jika data order sangat banyak
                    ->preload(),
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
            'index' => Pages\ListOrdersItems::route('/'),
            'create' => Pages\CreateOrdersItems::route('/create'),
            'edit' => Pages\EditOrdersItems::route('/{record}/edit'),
        ];
    }
}
