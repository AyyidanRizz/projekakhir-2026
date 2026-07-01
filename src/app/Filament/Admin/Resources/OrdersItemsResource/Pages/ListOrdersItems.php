<?php

namespace App\Filament\Admin\Resources\OrdersItemsResource\Pages;

use App\Filament\Admin\Resources\OrdersItemsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrdersItems extends ListRecords
{
    protected static string $resource = OrdersItemsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
