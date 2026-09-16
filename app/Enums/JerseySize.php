<?php

namespace App\Enums;

enum JerseySize: string
{
    case S    = 'S';
    case M    = 'M';
    case L    = 'L';
    case XL   = 'XL';
    case XXL  = 'XXL';
    case XXXL = '3XL';

    /**
     * Mengembalikan seluruh daftar ukuran jersey
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
