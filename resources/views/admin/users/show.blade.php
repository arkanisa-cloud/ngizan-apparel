@extends('layouts.admin')

@section('title', 'Detail Pengguna: ' . $user->name . ' · NGIZAN APPAREL')

@section('content')
<div class="space-y-6">

    {{-- Header & Navigation --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-hairline-soft pb-5">
        <div>
            <span class="text-[11px] font-bold uppercase tracking-[0.2em] text-mute block mb-1">Pengguna & Akses</span>
            <div class="flex items-center gap-3 flex-wrap">
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-ink">{{ $user->name }}</h1>
                @if($user->isAdmin())
                    <span class="px-3 py-1 bg-ink text-white rounded-full text-[10px] font-bold uppercase tracking-wider">
                        Administrator
                    </span>
                @else
                    <span class="px-3 py-1 bg-soft-cloud text-ink border border-hairline-soft rounded-full text-[10px] font-semibold uppercase tracking-wider">
                        Customer
                    </span>
                @endif

                @if($user->isPremiumActive())
                    <span class="px-3 py-1 bg-amber-50 text-amber-950 border border-amber-200 rounded-full text-[10px] font-bold uppercase tracking-wider">
                        ⭐ Member VIP (5% OFF)
                    </span>
                @endif
            </div>
            <p class="text-xs text-mute mt-1 font-mono">{{ $user->email }} @if($user->phone)&bull; {{ $user->phone }}@endif</p>
        </div>

        <div class="flex items-center gap-2.5 self-start sm:self-auto flex-wrap">
            <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 bg-soft-cloud hover:bg-neutral-200 text-ink rounded-full text-xs font-semibold transition flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali ke Daftar</span>
            </a>
        </div>
    </div>

    {{-- 2-Column Grid Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        {{-- Left Column: User Summary & Stats (4 Cols) --}}
        <div class="lg:col-span-4 space-y-6">
            
            {{-- Profile Card --}}
            <div class="bg-white rounded-3xl border border-hairline-soft p-6 space-y-5 shadow-xs text-xs">
                <div class="flex items-center gap-4 border-b border-hairline-soft pb-5">
                    <div class="w-16 h-16 rounded-full bg-soft-cloud border border-hairline-soft flex items-center justify-center font-bold text-xl text-ink shrink-0 overflow-hidden">
                        @if($user->avatar)
                            <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                        @else
                            <span>{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                        @endif
                    </div>
                    <div>
                        <h3 class="font-bold text-base text-ink leading-snug">{{ $user->name }}</h3>
                        <p class="text-mute font-mono text-[11px]">{{ $user->email }}</p>
                        <div class="mt-1">
                            @if($user->google_id)
                                <span class="px-2 py-0.5 bg-sky-50 text-sky-950 border border-sky-200 rounded-md text-[9px] font-bold">
                                    Google OAuth
                                </span>
                            @else
                                <span class="px-2 py-0.5 bg-soft-cloud text-mute border border-hairline-soft rounded-md text-[9px] font-bold">
                                    Email & Password
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Contact & Status Information --}}
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-1 border-b border-hairline-soft">
                        <span class="text-mute font-medium">WhatsApp / Telepon</span>
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-bold text-ink">{{ $user->phone ?? '-' }}</span>
                            @if($user->phone)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $user->phone) }}" target="_blank"
                                   class="px-2 py-0.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-[10px] font-bold transition">
                                    WA
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="flex justify-between items-center py-1 border-b border-hairline-soft">
                        <span class="text-mute font-medium">Status VIP Premium</span>
                        <span class="font-bold text-ink">
                            @if($user->isPremiumActive())
                                <span class="text-amber-700">Aktif (5% OFF)</span>
                            @else
                                <span class="text-mute">Reguler</span>
                            @endif
                        </span>
                    </div>

                    <div class="flex justify-between items-center py-1 border-b border-hairline-soft">
                        <span class="text-mute font-medium">Masa Berlaku VIP</span>
                        <span class="font-medium text-ink">
                            {{ $user->premium_until ? $user->premium_until->format('d M Y') : ($user->is_premium ? 'Seumur Hidup' : '-') }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center py-1 border-b border-hairline-soft">
                        <span class="text-mute font-medium">Terdaftar Sejak</span>
                        <span class="font-medium text-ink">{{ $user->created_at->translatedFormat('d F Y') }}</span>
                    </div>

                    @if($latestSession)
                        <div class="flex justify-between items-center py-1">
                            <span class="text-mute font-medium">Aktivitas Terakhir</span>
                            <span class="font-medium text-ink">{{ \Carbon\Carbon::createFromTimestamp($latestSession->last_activity)->diffForHumans() }}</span>
                        </div>
                    @endif
                </div>

                {{-- Action: Change Role Form --}}
                @if($user->id !== auth()->id())
                    <div class="pt-3 border-t border-hairline-soft">
                        <form action="{{ route('admin.users.role', $user->id) }}" method="POST" class="space-y-3">
                            @csrf
                            @method('PATCH')
                            <label class="font-bold text-[11px] text-ink uppercase tracking-wider block">Ubah Hak Akses / Peran:</label>
                            <div class="flex items-center gap-2">
                                <select name="role" class="bg-soft-cloud border border-hairline px-3.5 py-2 rounded-full text-xs font-semibold text-ink focus:outline-none focus:border-ink flex-1">
                                    <option value="customer" {{ $user->role === 'customer' ? 'selected' : '' }}>Customer (Pelanggan)</option>
                                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Administrator</option>
                                </select>
                                <button type="submit" class="px-4 py-2 bg-ink hover:opacity-90 text-white rounded-full text-xs font-bold transition cursor-pointer shrink-0">
                                    Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>

            {{-- Saved Addresses --}}
            <div class="bg-white rounded-3xl border border-hairline-soft p-6 space-y-4 shadow-xs text-xs">
                <div class="flex items-center justify-between border-b border-hairline-soft pb-3">
                    <h3 class="font-bold text-sm text-ink uppercase tracking-wider">Buku Alamat ({{ $user->shippingAddresses->count() }})</h3>
                </div>

                <div class="space-y-3">
                    @forelse($user->shippingAddresses as $addr)
                        <div class="p-3.5 bg-soft-cloud rounded-2xl border border-hairline-soft space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-ink">{{ $addr->label ?? 'Alamat' }}</span>
                                @if($addr->is_primary)
                                    <span class="px-2 py-0.5 bg-ink text-white rounded-md text-[9px] font-bold uppercase tracking-wider">Utama</span>
                                @endif
                            </div>
                            <p class="font-semibold text-ink">{{ $addr->recipient_name }} ({{ $addr->phone_number }})</p>
                            <p class="text-mute text-[11px] leading-relaxed">{{ $addr->full_address }}</p>
                            @if($addr->benchmark_notes)
                                <p class="text-[10px] text-neutral-400 italic">Patokan: "{{ $addr->benchmark_notes }}"</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-mute text-center py-4">Belum ada alamat pengiriman tersimpan.</p>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- Right Column: Order History & Transactions (8 Cols) --}}
        <div class="lg:col-span-8 space-y-6">
            
            {{-- Metrics Banner --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-hairline-soft shadow-xs space-y-1 text-center">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-mute block">Total Pesanan</span>
                    <span class="text-2xl font-bold text-ink tabular-nums">{{ $user->orders_count }}</span>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-hairline-soft shadow-xs space-y-1 text-center">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-mute block">Total Belanja (Lifetime)</span>
                    <span class="text-2xl font-bold text-ink tabular-nums">Rp {{ number_format($user->total_spend ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-hairline-soft shadow-xs space-y-1 text-center">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-mute block">Ulasan Diterbitkan</span>
                    <span class="text-2xl font-bold text-ink tabular-nums">{{ $user->reviews_count }}</span>
                </div>
            </div>

            {{-- Recent Orders Table --}}
            <div class="bg-white rounded-3xl border border-hairline-soft p-6 space-y-4 shadow-xs">
                <div class="flex items-center justify-between border-b border-hairline-soft pb-3">
                    <h3 class="font-bold text-sm text-ink uppercase tracking-wider">Riwayat Pesanan Terbaru</h3>
                </div>

                <div class="divide-y divide-hairline-soft text-xs">
                    @forelse($user->orders as $ord)
                        <div class="py-4 first:pt-0 last:pb-0 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-bold text-ink text-sm">#{{ $ord->order_number }}</span>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold {{ $ord->status->badgeClass() }}">
                                        {{ $ord->status->label() }}
                                    </span>
                                </div>
                                <p class="text-mute text-[11px]">
                                    {{ $ord->created_at->translatedFormat('d F Y, H:i') }} WIB &bull; {{ $ord->items->count() }} Produk
                                </p>
                            </div>

                            <div class="flex items-center gap-4">
                                <div class="text-left sm:text-right">
                                    <span class="font-bold text-sm text-ink block tabular-nums">
                                        Rp {{ number_format($ord->grand_total, 0, ',', '.') }}
                                    </span>
                                    <span class="text-[10px] text-mute font-medium">{{ strtoupper($ord->courier_code ?? 'JNT') }} (Gratis Ongkir)</span>
                                </div>
                                <a href="{{ route('admin.orders.show', $ord->id) }}" class="px-3.5 py-1.5 bg-soft-cloud hover:bg-neutral-200 text-ink rounded-full text-xs font-semibold transition">
                                    Detail &rarr;
                                </a>
                            </div>
                        </div>
                    @empty
                        <p class="text-mute text-center py-8">Pengguna ini belum pernah melakukan pemesanan.</p>
                    @endforelse
                </div>
            </div>

            {{-- Reviews List --}}
            @if($user->reviews->isNotEmpty())
                <div class="bg-white rounded-3xl border border-hairline-soft p-6 space-y-4 shadow-xs text-xs">
                    <div class="flex items-center justify-between border-b border-hairline-soft pb-3">
                        <h3 class="font-bold text-sm text-ink uppercase tracking-wider">Ulasan Produk ({{ $user->reviews->count() }})</h3>
                    </div>

                    <div class="space-y-3">
                        @foreach($user->reviews as $rev)
                            <div class="p-4 bg-soft-cloud rounded-2xl border border-hairline-soft space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-ink">{{ $rev->product->name ?? 'Produk' }}</span>
                                    <div class="flex text-premium-gold text-xs">
                                        @for($i = 1; $i <= 5; $i++)
                                            <span>{{ $i <= $rev->rating ? '★' : '☆' }}</span>
                                        @endfor
                                    </div>
                                </div>
                                <p class="text-mute italic text-xs">"{{ $rev->comment }}"</p>
                                <span class="text-[10px] text-neutral-400 block">{{ $rev->created_at->translatedFormat('d M Y, H:i') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

    </div>

</div>
@endsection
