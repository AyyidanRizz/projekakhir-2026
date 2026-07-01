<?php

namespace App\Enums;

enum UserRole: string
{
    case PEMBELI = 'pembeli';
    case ADMIN = 'admin';
    case PRODUKSI = 'produksi';
    case KEUANGAN = 'keuangan';
}