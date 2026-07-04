<?php

namespace App\Enums;

enum DesignStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu',
            self::APPROVED => 'Diterima',
            self::REJECTED => 'Ditolak',
        };
    }
}