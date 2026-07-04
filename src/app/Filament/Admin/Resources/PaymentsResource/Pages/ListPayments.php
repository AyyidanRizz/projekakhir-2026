<?php

namespace App\Filament\Admin\Resources\PaymentsResource\Pages;

use App\Filament\Admin\Resources\PaymentsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\CreateAction::make(),
        ];
    }
}
