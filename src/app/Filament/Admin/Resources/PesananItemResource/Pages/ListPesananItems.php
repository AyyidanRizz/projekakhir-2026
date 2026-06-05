<?php

namespace App\Filament\Admin\Resources\PesananItemResource\Pages;

use App\Filament\Admin\Resources\PesananItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPesananItems extends ListRecords
{
    protected static string $resource = PesananItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
