<?php

namespace App\Enums;

enum JerseyType: string
{
    case FANS_ISSUE   = 'Fans Issue';
    case PLAYER_ISSUE = 'Player Issue';
    case RETRO        = 'Retro';

    /**
     * Mengembalikan seluruh daftar tipe jersey
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
