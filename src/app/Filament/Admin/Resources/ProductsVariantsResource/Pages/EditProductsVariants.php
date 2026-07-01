<?php

namespace App\Filament\Admin\Resources\ProductsVariantsResource\Pages;

use App\Filament\Admin\Resources\ProductsVariantsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProductsVariants extends EditRecord
{
    protected static string $resource = ProductsVariantsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
