<?php

namespace App\Enums;

enum PaymentType: string
{
    case FULL = 'full';
    case DP = 'dp';
    case PELUNASAN = 'pelunasan';
}