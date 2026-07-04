<?php

namespace App\Filament\Admin\Resources\RefundsResource\Pages;

use App\Filament\Admin\Resources\RefundsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRefunds extends ListRecords
{
    protected static string $resource = RefundsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\CreateAction::make(),
        ];
    }
}
