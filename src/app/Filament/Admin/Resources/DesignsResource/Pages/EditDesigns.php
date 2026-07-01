<?php

namespace App\Filament\Admin\Resources\DesignsResource\Pages;

use App\Filament\Admin\Resources\DesignsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDesigns extends EditRecord
{
    protected static string $resource = DesignsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
