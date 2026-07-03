<?php

namespace App\Filament\Admin\Resources\OrdersResource\RelationManagers;

use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Opsi pilihan metode pembayaran untuk pembeli/admin
                Forms\Components\Select::make('payment_method')
                    ->label('Metode Pembayaran')
                    ->options(\App\Enums\PaymentMethod::class)
                    ->required(),

                Forms\Components\Select::make('status')
                    ->options(PaymentStatus::class)
                    ->required()
                    ->default(PaymentStatus::PENDING),

                Forms\Components\FileUpload::make('proof_file')
                    ->directory('payment_proofs')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'application/pdf']),

                Forms\Components\Select::make('verified_by')
                    ->relationship('verifier', 'name'),

                Forms\Components\DateTimePicker::make('verified_at'),

                Forms\Components\Textarea::make('notes')
                    ->maxLength(65535),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')->badge(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Metode Pembayaran')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')->money('IDR'),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('verified_at')->dateTime(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tombol Tambah Pembayaran Baru di Atas Tabel
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data, $livewire): array {
                        $order = $livewire->getOwnerRecord();

                        // Otomatis mengisi data yang disembunyikan dari form menggunakan Enum yang benar
                        $data['amount'] = $order->dp_amount > 0 ? $order->dp_amount : $order->total_price;
                        $data['type'] = $order->dp_amount > 0 ? PaymentType::DP : PaymentType::FULL; 

                        return $data;
                    }),
            ])
            ->actions([
                // Tombol Verifikasi Cepat
                Tables\Actions\Action::make('verify')
                    ->button()
                    ->action(fn ($record) => $record->update([
                        'status' => PaymentStatus::VERIFIED, 
                        'verified_at' => now(),
                        'verified_by' => Auth::id()
                    ]))
                    ->requiresConfirmation()
                    ->color('success'),
                    
                // Tombol Tolak Cepat
                Tables\Actions\Action::make('reject')
                    ->button()
                    ->action(fn ($record) => $record->update(['status' => PaymentStatus::REJECTED]))
                    ->requiresConfirmation()
                    ->color('danger'),

                // Menu Dropdown Pilihan Tambahan
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->mutateFormDataUsing(function (array $data, $livewire): array {
                            $order = $livewire->getOwnerRecord();
                            
                            $data['amount'] = $order->dp_amount > 0 ? $order->dp_amount : $order->total_price;
                            $data['type'] = $order->dp_amount > 0 ? PaymentType::DP : PaymentType::FULL; 
                            
                            return $data;
                        }),
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
}