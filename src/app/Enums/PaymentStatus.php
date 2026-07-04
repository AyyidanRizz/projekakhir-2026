<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case VERIFIED = 'verified';
    case REJECTED = 'rejected';
    case PARTIAL = 'partial';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu Pembayaran',
            self::VERIFIED => 'Lunas',
            self::REJECTED => 'Gagal',
            self::PARTIAL => 'DP 50% Dibayar',

        };
    }

    public function isPaid(): bool
    {
        return in_array($this, [self::VERIFIED, self::PARTIAL]);
    }
}