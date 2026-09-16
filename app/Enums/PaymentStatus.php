<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING    = 'pending';
    case SETTLEMENT = 'settlement';
    case EXPIRE     = 'expire';
    case CANCEL     = 'cancel';
    case DENY       = 'deny';

    /**
     * Label pembayaran dalam bahasa Indonesia
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING    => 'Menunggu Pembayaran',
            self::SETTLEMENT => 'Berhasil (Settlement)',
            self::EXPIRE     => 'Kadaluarsa',
            self::CANCEL     => 'Dibatalkan',
            self::DENY       => 'Ditolak',
        };
    }

    /**
     * Warna badge untuk UI Tailwind CSS
     */
    public function badgeColor(): string
    {
        return match ($this) {
            self::PENDING    => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
            self::SETTLEMENT => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
            self::EXPIRE     => 'bg-zinc-500/10 text-zinc-400 border-zinc-500/20',
            self::CANCEL     => 'bg-rose-500/10 text-rose-400 border-rose-500/20',
            self::DENY       => 'bg-red-500/10 text-red-400 border-red-500/20',
        };
    }
}
