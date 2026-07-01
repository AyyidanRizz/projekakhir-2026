<?php

namespace App\Filament\Admin\Resources\RefundsResource\Pages;

use App\Filament\Admin\Resources\RefundsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRefunds extends EditRecord
{
    protected static string $resource = RefundsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
