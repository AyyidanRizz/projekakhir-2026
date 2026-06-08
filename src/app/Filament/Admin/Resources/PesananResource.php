<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PesananResource\Pages;
use App\Filament\Admin\Resources\PesananResource\RelationManagers;
use App\Models\Pesanan;
use App\Models\Produk; // Import model Produk untuk mengambil harga master
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PesananResource extends Resource
{
    protected static ?string $model = Pesanan::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Manajemen';
    protected static ?int $navigationSort = 3;
    protected static ?string $label = 'Pesanan';
    protected static ?string $pluralLabel = 'Pesanan';

    // Fungsi helper untuk menghitung Grand Total keseluruhan pesanan
    public static function updateGrandTotal(Get $get, Set $set): void
    {
        // Ambil semua item dari repeater
        $items = $get('items') ?? [];
        $subtotal = 0;

        // Jumlahkan jumlah_total dari masing-masing baris item
        foreach ($items as $item) {
            $subtotal += floatval($item['jumlah_total'] ?? 0);
        }

        // Ambil nilai ongkir (jika ada)
        $ongkir = floatval($get('ongkir') ?? 0);

        // Set field 'total' di luar repeater dengan (subtotal + ongkir)
        $set('total', $subtotal + $ongkir);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // SECTION 1: INFORMASI UTAMA & STATUS
                Forms\Components\Section::make('Informasi Pelanggan & Status')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        
                        Forms\Components\Select::make('status')
                            ->options([
                                'pesanan_baru' => 'Pesanan Baru',
                                'sedang_diproses' => 'Sedang Diproses',
                                'dikirim' => 'Dikirim',
                                'diterima' => 'Diterima',
                                'dibatalkan' => 'Dibatalkan',
                            ])
                            ->default('pesanan_baru')
                            ->required(),

                        Forms\Components\ToggleButtons::make('status_bayar')
                            ->options([
                                'Belum Bayar' => 'Belum Bayar',
                                'Lunas' => 'Lunas',
                            ])
                            ->colors([
                                'Belum Bayar' => 'warning',
                                'Lunas' => 'success',
                            ])
                            ->icons([
                                'Belum Bayar' => 'heroicon-o-clock',
                                'Lunas' => 'heroicon-o-check-circle',
                            ])
                            ->inline()
                            ->columnSpanFull(),
                            
                        Forms\Components\ToggleButtons::make('metode_bayar')
                            ->options([
                                'Transfer Bank' => 'Transfer Bank',
                                'E-Wallet' => 'E-Wallet',
                                'COD' => 'COD',
                            ])
                            ->inline()
                            ->columnSpanFull(),
                    ])->columns(2),

                // SECTION REPAETER: PRODUK YANG DIBELI
                Forms\Components\Section::make('Produk yang Dibeli')
                ->schema([
                    Forms\Components\Repeater::make('items')
                        ->relationship()
                        ->schema([
                            Forms\Components\Select::make('produk_id')
                                ->relationship('produk', 'name')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->live() // Membuat select produk reaktif langsung tanpa reload page
                                ->afterStateUpdated(function (string|null $state, Set $set, Get $get) {
                                    // Jika produk dipilih, cari harganya di DB master Produk
                                    if ($state) {
                                        $produk = Produk::find($state);
                                        $harga = $produk ? $produk->harga : 0;
                                        $kuantitas = intval($get('kuantitas') ?? 1);

                                        $set('jumlah_satuan', $harga);
                                        $set('jumlah_total', $harga * $kuantitas);
                                    }
                                })
                                ->columnSpan(4),

                            Forms\Components\TextInput::make('kuantitas')
                                ->numeric()
                                ->default(1)
                                ->required()
                                ->live() // Reaktif saat jumlah diotak-atik
                                ->afterStateUpdated(function (string|null $state, Set $set, Get $get) {
                                    $hargaSatuan = floatval($get('jumlah_satuan') ?? 0);
                                    $kuantitas = intval($state ?? 0);
                                    
                                    // Hitung total harga per baris
                                    $set('jumlah_total', $hargaSatuan * $kuantitas);
                                })
                                ->columnSpan(2),

                            Forms\Components\TextInput::make('jumlah_satuan')
                                ->numeric()
                                ->prefix('IDR')
                                ->required()
                                ->readOnly() // Biar admin tidak asal ubah harga, mengunci sesuai master produk
                                ->columnSpan(3),

                            Forms\Components\TextInput::make('jumlah_total')
                                ->numeric()
                                ->prefix('IDR')
                                ->required()
                                ->readOnly() // Dikunci karena hasil kalkulasi otomatis
                                ->columnSpan(3),
                        ])
                        ->columns(12)
                        ->live() // Memantau perubahan baris repeater (tambah/hapus item)
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            self::updateGrandTotal($get, $set);
                        })
                        ->addActionLabel('Tambah Produk'),
                ]),

                // SECTION 2: RINCIAN NOMINAL & KURIR
                Forms\Components\Section::make('Rincian Pembayaran & Pengiriman')
                    ->schema([
                        Forms\Components\TextInput::make('total')
                            ->numeric()
                            ->prefix('IDR')
                            ->placeholder('0.00')
                            ->readOnly() // Total keseluruhan otomatis terkunci dari kalkulasi di atas
                            ->dehydrated(), // Tetap mengirimkan nilai ke database walau berstatus readOnly

                        Forms\Components\TextInput::make('mata_uang')
                            ->default('IDR')
                            ->maxLength(255),

                        Forms\Components\ToggleButtons::make('ekspedisi')
                            ->options([
                                'JNE' => 'JNE',
                                'J&T' => 'J&T',
                                'SiCepat' => 'SiCepat',
                                'GoSend' => 'GoSend',
                            ])
                            ->inline()
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('ongkir')
                            ->numeric()
                            ->prefix('IDR')
                            ->placeholder('0.00')
                            ->live(onBlur: true) // Update grand total sesaat setelah admin selesai ketik ongkir
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                self::updateGrandTotal($get, $set);
                            }),
                    ])->columns(2),

                // SECTION 3: DATA ALAMAT
                Forms\Components\Section::make('Alamat Pengiriman')
                    ->relationship('alamat')
                    ->schema([
                        Forms\Components\TextInput::make('nama_penerima')
                            ->required(),
                        Forms\Components\TextInput::make('telepon')
                            ->tel()
                            ->required(),
                        Forms\Components\Textarea::make('detail_alamat')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('kota'),
                        Forms\Components\TextInput::make('kode_pos'),
                    ])->columns(2),

                // SECTION 4: CATATAN
                Forms\Components\Section::make('Catatan Tambahan')
                    ->schema([
                        Forms\Components\Textarea::make('catatan')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID Pesanan')
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pelanggan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('total')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('metode_bayar')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('status_bayar')
                    ->badge()
                    ->colors([
                        'warning' => 'Belum Bayar',
                        'success' => 'Lunas',
                    ])
                    ->searchable(),

                Tables\Columns\SelectColumn::make('status')
                    ->options([
                        'pesanan_baru' => 'Pesanan Baru',
                        'sedang_diproses' => 'Sedang Diproses',
                        'dikirim' => 'Dikirim',
                        'diterima' => 'Diterima',
                        'dibatalkan' => 'Dibatalkan',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('ekspedisi')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pesanan_baru' => 'Pesanan Baru',
                        'sedang_diproses' => 'Sedang Diproses',
                        'dikirim' => 'Dikirim',
                        'diterima' => 'Diterima',
                        'dibatalkan' => 'Dibatalkan',
                    ]),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
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