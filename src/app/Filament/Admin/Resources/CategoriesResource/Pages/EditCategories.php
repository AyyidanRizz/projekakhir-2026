<?php

namespace App\Filament\Admin\Resources\CategoriesResource\Pages;

use App\Filament\Admin\Resources\CategoriesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategories extends EditRecord
{
    protected static string $resource = CategoriesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
