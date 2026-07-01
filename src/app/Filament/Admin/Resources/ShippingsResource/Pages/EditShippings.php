<?php

namespace App\Filament\Admin\Resources\ShippingsResource\Pages;

use App\Filament\Admin\Resources\ShippingsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditShippings extends EditRecord
{
    protected static string $resource = ShippingsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
