<?php

namespace App\Filament\Admin\Resources\DesignsResource\Pages;

use App\Filament\Admin\Resources\DesignsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDesigns extends ListRecords
{
    protected static string $resource = DesignsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
