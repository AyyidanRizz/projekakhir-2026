<?php

namespace App\Filament\Admin\Resources\OrdersItemsResource\Pages;

use App\Filament\Admin\Resources\OrdersItemsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrdersItems extends EditRecord
{
    protected static string $resource = OrdersItemsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
