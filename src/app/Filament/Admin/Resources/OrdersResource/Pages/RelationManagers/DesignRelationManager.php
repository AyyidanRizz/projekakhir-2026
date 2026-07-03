<?php

namespace App\Filament\Admin\Resources\OrdersResource\RelationManagers;

use App\Enums\DesignStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class DesignRelationManager extends RelationManager
{
    protected static string $relationship = 'design';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('file_path')
                    ->required()
                    ->directory('designs')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'application/pdf', 'image/vnd.adobe.photoshop', 'application/postscript']),
                Forms\Components\Select::make('status')
                    ->options(DesignStatus::class)
                    ->required()
                    ->default(DesignStatus::PENDING),
                Forms\Components\Textarea::make('rejection_reason')
                    ->maxLength(65535),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('file_path') // Sesuaikan 'file_path' dengan nama kolom di databasemu (di gambar tertulis File path)
                ->label('File')
                ->disk('public') // Sangat penting agar Filament mencari di storage/app/public
                ->square()
                ->size(60),
                //Tables\Columns\TextColumn::make('file_path')->label('File'),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('uploaded_at')->dateTime(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\Action::make('approve')
                    ->action(fn ($record) => $record->update(['status' => DesignStatus::APPROVED]))
                    ->requiresConfirmation()
                    ->color('success'),
                Tables\Actions\Action::make('reject')
                    ->action(fn ($record) => $record->update(['status' => DesignStatus::REJECTED]))
                    ->requiresConfirmation()
                    ->color('danger'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }
}