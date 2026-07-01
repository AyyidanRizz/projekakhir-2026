<?php

namespace App\Filament\Admin\Resources\ProductsResource\Pages;

use App\Filament\Admin\Resources\ProductsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProducts extends CreateRecord
{
    protected static string $resource = ProductsResource::class;
}
