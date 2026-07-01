<?php

namespace App\Filament\Admin\Resources\OrdersResource\Pages;

use App\Filament\Admin\Resources\OrdersResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateOrders extends CreateRecord
{
    protected static string $resource = OrdersResource::class;
}
