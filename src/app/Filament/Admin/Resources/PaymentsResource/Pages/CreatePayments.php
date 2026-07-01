<?php

namespace App\Filament\Admin\Resources\PaymentsResource\Pages;

use App\Filament\Admin\Resources\PaymentsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePayments extends CreateRecord
{
    protected static string $resource = PaymentsResource::class;
}
