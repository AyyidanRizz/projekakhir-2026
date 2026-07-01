<?php

namespace App\Filament\Admin\Resources\ProductsVariantsResource\Pages;

use App\Filament\Admin\Resources\ProductsVariantsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProductsVariants extends ListRecords
{
    protected static string $resource = ProductsVariantsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
