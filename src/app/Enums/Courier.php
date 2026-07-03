<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Courier: string implements HasLabel
{
    // Ubah nilainya (sebelah kanan tanda =) menjadi huruf kecil sesuai database
    case JNE = 'jne';
    case JNT = 'jnt';
    case GRAB_EXPRESS = 'grab_express';
    case GO_SEND = 'go_send';

    public function getLabel(): ?string
    {
        // Tetap tampilkan nama yang rapi untuk pembeli / admin di tampilan web
        return match($this) {
            self::JNE => 'JNE',
            self::JNT => 'J&T Express',
            self::GRAB_EXPRESS => 'Grab Express',
            self::GO_SEND => 'Go Send',
        };
    }
}