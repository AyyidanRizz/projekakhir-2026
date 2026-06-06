<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PesananResource\Pages;
use App\Filament\Admin\Resources\PesananResource\RelationManagers;
use App\Models\Pesanan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PesananResource extends Resource
{
    protected static ?string $model = Pesanan::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Toko';
    protected static ?int $navigationSort = 3;
    protected static ?string $label = 'Pesanan';
    protected static ?string $pluralLabel = 'Pesanan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // KELOMPOK KIRI (Lebar 2 Kolom)
                Forms\Components\Group::make()->schema([
                    
                    // Section 1: Informasi Pesanan
                    Forms\Components\Section::make('Informasi Pesanan')
                        ->schema([
                            // Menggunakan TextInput biasa dulu untuk testing
                            Forms\Components\TextInput::make('user_id')
                                ->numeric()
                                ->required(),
                                
                            Forms\Components\Select::make('status')
                                ->options([
                                    'pesanan_baru' => 'Baru',
                                    'sedang_diproses' => 'Diproses',
                                    'dikirim' => 'Dikirim',
                                    'diterima' => 'Selesai',
                                    'dibatalkan' => 'Dibatalkan',
                                ])
                                ->required(),
                        ]),
                        
                    // Section 2: Item Pesanan (Sekarang posisinya sudah sejajar dan rapi)
                    Forms\Components\Section::make('Item Pesanan')
                        ->schema([
                            Forms\Components\Repeater::make('items') 
                                // ->relationship('items') // Dikomentari sementara untuk testing
                                ->schema([
                                    Forms\Components\TextInput::make('produk_id')
                                        ->numeric()
                                        ->required(),
                                        
                                    Forms\Components\TextInput::make('kuantitas')->numeric()->required(),
                                    Forms\Components\TextInput::make('jumlah_satuan')->numeric()->required(),
                                    Forms\Components\TextInput::make('jumlah_total')->numeric()->required(),
                                ])
                        ]),
                ])->columnSpan(2),
                
                // KELOMPOK KANAN (Lebar 1 Kolom)
                Forms\Components\Group::make()->schema([
                    
                    // Section 3: Total Pembayaran
                    Forms\Components\Section::make('Total Pembayaran')
                        ->schema([
                            Forms\Components\TextInput::make('mata_uang')
                                ->default('IDR'),
                            Forms\Components\TextInput::make('ongkir')
                                ->numeric()
                                ->prefix('IDR'),
                            Forms\Components\TextInput::make('total')
                                ->numeric()
                                ->prefix('IDR'),
                        ]),
                        
                    // Section 4: Alamat Pengiriman
                    Forms\Components\Section::make('Alamat Pengiriman')
                        ->schema([
                            Forms\Components\Grid::make(1)
                                // ->relationship('alamat') // Dikomentari sementara untuk testing
                                ->schema([
                                    Forms\Components\TextInput::make('name')
                                        ->label('Nama Penerima'),
                                    Forms\Components\TextInput::make('no_telfon'),
                                    Forms\Components\TextInput::make('nama_jalan'),
                                    Forms\Components\TextInput::make('kota'),
                                    Forms\Components\TextInput::make('provinsi'),
                                    Forms\Components\TextInput::make('kode_pos'),
                                ]),
                        ]),
                ])->columnSpan(1),
            ])->columns(3);
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
            'index' => Pages\ListPesanans::route('/'),
            'create' => Pages\CreatePesanan::route('/create'),
            'edit' => Pages\EditPesanan::route('/{record}/edit'),
        ];
    }
}