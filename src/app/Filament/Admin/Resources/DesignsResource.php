<?php

namespace App\Filament\Admin\Resources;

use App\Enums\DesignStatus;
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
                Tables\Columns\TextColumn::make('order.order_number')->label('Order')->searchable(),
                
                // === SEKARANG MENGGUNAKAN IMAGE COLUMN ===
                Tables\Columns\ImageColumn::make('file_path')
                    ->label('Desain')
                    ->square()             // Membuat bentuk kotak (opsional, bisa diganti ->circular())
                    ->size(50)             // Mengatur ukuran gambar agar pas di tabel
                    ->defaultImageUrl(url('/images/default-placeholder.png')), // Gambar cadangan jika file kosong
                
                Tables\Columns\TextColumn::make('status')->badge()
                    ->colors([
                        'warning' => DesignStatus::PENDING,
                        'success' => DesignStatus::APPROVED,
                        'danger' => DesignStatus::REJECTED,
                    ]),
                Tables\Columns\TextColumn::make('uploaded_at')->dateTime(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(DesignStatus::class),
            ])
            ->actions([
                // Action Approve
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
                
                // Action Reject dengan Modal Form
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
                        $record->order->update(['status' => 'ditolak']);
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
                    // Bulk approve
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
                    // Bulk reject
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
                                }
                            }
                        })
                        ->requiresConfirmation(),
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