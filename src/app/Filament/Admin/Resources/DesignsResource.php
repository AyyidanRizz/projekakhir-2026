<?php

namespace App\Filament\Admin\Resources;

use App\Enums\DesignStatus;
use App\Enums\OrderStatus;
use App\Filament\Admin\Resources\DesignsResource\Pages;
use App\Filament\Admin\Resources\DesignsResource\RelationManagers;
use App\Models\Designs;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DesignsResource extends Resource
{
    protected static ?string $model = Designs::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';
    protected static ?string $navigationGroup = 'Manajemen Produk';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('order_id')
                    ->relationship('order', 'id')
                    ->required()
                    ->searchable(),
                Forms\Components\FileUpload::make('file_path')
                    ->required()
                    ->directory('designs')
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'application/pdf', 'image/vnd.adobe.photoshop', 'application/postscript']),
                Forms\Components\Select::make('status')
                    ->options(DesignStatus::class)
                    ->required()
                    ->default(DesignStatus::PENDING),
                Forms\Components\Textarea::make('rejection_reason')
                    ->maxLength(65535)
                    ->visible(fn ($get) => $get('status') === 'rejected'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order.order_number')
                    ->label('Order')
                    ->searchable(),
                Tables\Columns\TextColumn::make('order.id')->label('Order ID')->sortable(),
                Tables\Columns\ImageColumn::make('file_path')
                    ->label('File Desain')
                    ->disk('public'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (DesignStatus $state): string => match ($state) {
                        DesignStatus::PENDING => 'warning',
                        DesignStatus::APPROVED => 'success',
                        DesignStatus::REJECTED => 'danger',
                    }),
                Tables\Columns\TextColumn::make('uploaded_at')
                    ->dateTime(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(DesignStatus::class),
            ])
            ->actions([
                Action::make('approve')
                    ->button()
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === DesignStatus::PENDING)
                    ->action(function ($record) {
                        $record->update(['status' => DesignStatus::APPROVED]);
                        // Update status order menjadi menunggu_pembayaran
                        $record->order->update(['status' => 'menunggu_pembayaran']);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Setujui Desain')
                    ->modalDescription('Apakah Anda yakin ingin menyetujui desain ini?')
                    ->modalSubmitActionLabel('Ya, Setujui'),

                // ==================== DISINI TEMPAT PERUBAHANNYA ====================
                Action::make('reject')
                    ->button()
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status === DesignStatus::PENDING)
                    ->form([
                        Textarea::make('rejection_reason')
                            ->label('Alasan Penolakan')
                            ->placeholder('Masukkan alasan penolakan (opsional)')
                            ->maxLength(65535)
                            ->rows(4),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status' => DesignStatus::REJECTED,
                            'rejection_reason' => $data['rejection_reason'] ?? null,
                        ]);
                        
                        // Update status order menjadi ditolak
                        $record->order->update([
                            'status' => \App\Enums\OrderStatus::DIBATALKAN]);
                        
                        // Panggil method untuk mengembalikan stok
                        $record->order->restoreStock();
                        
                        // OTOMATIS MEMBUAT DATA REFUND KE MENU REFUNDS
// SINKRONISASI TYPO: ganti firts() menjadi first()
                        $refundAmount = $record->order->payment->first()?->amount ?? $record->order->total_price;                        
                        $record->order->refund()->create([
                            'user_id' => $record->order->user_id,
                            'amount' => $refundAmount,
                            'reason' => 'Desain ditolak admin: ' . ($data['rejection_reason'] ?? 'Tidak ada alasan spesifik.'),
                            'status' => 'pending', 
                        ]);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Tolak Desain')
                    ->modalDescription('Apakah Anda yakin ingin menolak desain ini?')
                    ->modalSubmitActionLabel('Ya, Tolak'),

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
                    Tables\Actions\BulkAction::make('bulk_approve')
                        ->label('Setujui Terpilih')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                if ($record->status === DesignStatus::PENDING) {
                                    $record->update(['status' => DesignStatus::APPROVED]);
                                    $record->order->update(['status' => 'menunggu_pembayaran']);
                                }
                            }
                        })
                        ->requiresConfirmation(),
                        
                    // ==================== SINKRONISASI JUGA PADA AKSI MASSAL ====================
                    Tables\Actions\BulkAction::make('bulk_reject')
                        ->label('Tolak Terpilih')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->form([
                            Textarea::make('rejection_reason')
                                ->label('Alasan Penolakan')
                                ->placeholder('Masukkan alasan penolakan (opsional)')
                                ->maxLength(65535)
                                ->rows(4),
                        ])
                        ->action(function ($records, array $data) {
                            foreach ($records as $record) {
                                if ($record->status === DesignStatus::PENDING) {
                                    $record->update([
                                        'status' => DesignStatus::REJECTED,
                                        'rejection_reason' => $data['rejection_reason'] ?? null,
                                    ]);
                                    $record->order->update(['status' => 'ditolak']);
                                    $record->order->restoreStock();

                                    // OTOMATIS REFUND MASSAL
                                    $refundAmount = $record->order->payment?->amount ?? $record->order->total_price;
                                    $record->order->refund()->create([
                                        'amount' => $refundAmount,
                                        'reason' => 'Desain ditolak massal oleh admin: ' . ($data['rejection_reason'] ?? 'Tidak ada alasan spesifik.'),
                                        'status' => 'pending',
                                    ]);
                                }
                            }
                        })
                        ->requiresConfirmation(),
                ]),
            ]);
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