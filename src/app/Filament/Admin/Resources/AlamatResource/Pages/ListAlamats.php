<?php

namespace App\Filament\Admin\Resources\AlamatResource\Pages;

use App\Filament\Admin\Resources\AlamatResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAlamats extends ListRecords
{
    protected static string $resource = AlamatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
