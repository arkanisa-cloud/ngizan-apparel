<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING_PAYMENT = 'pending_payment';
    case PAID            = 'paid';
    case IN_PRODUCTION   = 'in_production'; // Khusus proses sablon / nameset
    case SHIPPED         = 'shipped';
    case COMPLETED       = 'completed';
    case CANCELLED       = 'cancelled';
    case EXPIRED         = 'expired';

    /**
     * Label representasi status pesanan dalam bahasa Indonesia
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING_PAYMENT => 'Menunggu Pembayaran',
            self::PAID            => 'Lunas',
            self::IN_PRODUCTION   => 'Dalam Produksi (Sablon)',
            self::SHIPPED         => 'Sedang Dikirim',
            self::COMPLETED       => 'Selesai',
            self::CANCELLED       => 'Dibatalkan',
            self::EXPIRED         => 'Kadaluarsa',
        };
    }

    /**
     * Warna badge untuk UI Tailwind CSS
     */
    public function badgeColor(): string
    {
        return match ($this) {
            self::PENDING_PAYMENT => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
            self::PAID            => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
            self::IN_PRODUCTION   => 'bg-purple-500/10 text-purple-400 border-purple-500/20',
            self::SHIPPED         => 'bg-indigo-500/10 text-indigo-400 border-indigo-500/20',
            self::COMPLETED       => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
            self::CANCELLED       => 'bg-rose-500/10 text-rose-400 border-rose-500/20',
            self::EXPIRED         => 'bg-zinc-500/10 text-zinc-400 border-zinc-500/20',
        };
    }

    /**
     * Apakah pesanan masih aktif dan butuh penanganan
     */
    public function isActive(): bool
    {
        return in_array($this, [self::PENDING_PAYMENT, self::PAID, self::IN_PRODUCTION, self::SHIPPED]);
    }

    public function isPaid(): bool
    {
        return in_array($this, [self::PAID, self::IN_PRODUCTION, self::SHIPPED, self::COMPLETED]);
    }

    public function isInProduction(): bool
    {
        return $this === self::IN_PRODUCTION;
    }

    public function isShipped(): bool
    {
        return $this === self::SHIPPED;
    }

    public function isCompleted(): bool
    {
        return $this === self::COMPLETED;
    }

    public function isCancelled(): bool
    {
        return in_array($this, [self::CANCELLED, self::EXPIRED]);
    }

    public function isExpired(): bool
    {
        return $this === self::EXPIRED;
    }

    public function isPendingPayment(): bool
    {
        return $this === self::PENDING_PAYMENT;
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::PENDING_PAYMENT => 'bg-amber-100 text-amber-800',
            self::PAID            => 'bg-blue-100 text-blue-800',
            self::IN_PRODUCTION   => 'bg-purple-100 text-purple-800',
            self::SHIPPED         => 'bg-indigo-100 text-indigo-800',
            self::COMPLETED       => 'bg-emerald-100 text-emerald-800',
            self::CANCELLED       => 'bg-rose-100 text-rose-800',
            self::EXPIRED         => 'bg-slate-100 text-slate-800',
        };
    }
}
