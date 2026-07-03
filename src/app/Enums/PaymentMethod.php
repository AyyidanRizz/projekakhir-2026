<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PaymentMethod: string implements HasLabel
{
    case VIRTUAL_ACCOUNT = 'virtual_account';
    case QRIS = 'qris';

    public function getLabel(): ?string
    {
        return match($this) {
            self::VIRTUAL_ACCOUNT => 'Virtual Account',
            self::QRIS => 'QRIS',
        };
    }
}