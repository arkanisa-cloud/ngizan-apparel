<?php

namespace App\Enums;

enum StockReferenceType: string
{
    case ORDER_PLACED      = 'ORDER_PLACED';
    case RESTOCK_EXPIRED   = 'RESTOCK_EXPIRED';
    case RESTOCK_CANCELLED = 'RESTOCK_CANCELLED';
    case MANUAL_IN         = 'MANUAL_IN';
    case MANUAL_OUT        = 'MANUAL_OUT';

    /**
     * Deskripsi tipe referensi mutasi stok
     */
    public function label(): string
    {
        return match ($this) {
            self::ORDER_PLACED      => 'Pengurangan Pesanan Baru',
            self::RESTOCK_EXPIRED   => 'Pengembalian Stok (Pesanan Kadaluarsa)',
            self::RESTOCK_CANCELLED => 'Pengembalian Stok (Pesanan Dibatalkan)',
            self::MANUAL_IN         => 'Stok Masuk Manual',
            self::MANUAL_OUT        => 'Stok Keluar Manual / Rusak / Sampel',
        };
    }
}
