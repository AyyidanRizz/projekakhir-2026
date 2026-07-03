<?php

namespace App\Filament\Admin\Resources\OrdersResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use App\Enums\DesignStatus;

class DesignRelationManager extends RelationManager
{
    protected static string $relationship = 'design';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('file_path')
                    ->label('File Desain')
                    ->disk('public') 
                    ->visibility('public')
                    ->square() 
                    ->size(80) 
                    ->defaultImageUrl(url('/images/default-preview.png'))
                    ->url(fn ($record) => $record->file_path ? asset('storage/' . $record->file_path) : null) 
                    ->openUrlInNewTab(), 

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    // Menambahkan \App\Enums\DesignStatus secara eksplisit agar aman
                    ->color(fn (\App\Enums\DesignStatus $state): string => match ($state) {
                        DesignStatus::PENDING => 'warning',
                        DesignStatus::APPROVED => 'success',
                        DesignStatus::REJECTED => 'danger',
                    }),

                Tables\Columns\TextColumn::make('rejection_reason')
                    ->label('Alasan Penolakan'),

                Tables\Columns\TextColumn::make('created_at') // Biasanya default bawaan Laravel menggunakan created_at, sesuaikan jika di migrasi Anda memakai uploaded_at
                    ->dateTime()
                    ->label('Diunggah'),
            ])
            ->headerActions([])
            ->actions([
                // Jika ingin admin bisa mengubah status desain langsung dari tabel ini:
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }
}