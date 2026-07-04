<?php

namespace App\Enums;

enum Akad: string
{
    case SALAM = 'salam';
    case ISTISHNA = 'istishna';

    /**
     * Mendapatkan label untuk ditampilkan di UI
     */
    public function label(): string
    {
        return match ($this) {
            self::SALAM => 'Akad Salam (Bayar Full)',
            self::ISTISHNA => 'Akad Istishna (DP 50%)',
        };
    }

    /**
     * Mendapatkan persentase DP untuk akad tertentu
     */
    public function dpPercentage(): float
    {
        return match ($this) {
            self::SALAM => 0,
            self::ISTISHNA => 0.50,
        };
    }

    /**
     * Cek apakah akad ini memerlukan DP
     */
    public function requiresDownPayment(): bool
    {
        return $this === self::ISTISHNA;
    }
}