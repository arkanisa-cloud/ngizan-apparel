@extends('layouts.customer')

@section('title', 'Buku Alamat Pengiriman · NGIZAN APPAREL')
@section('meta_description',
    'Kelola buku alamat pengiriman tersimpan Anda untuk kemudahan checkout presisi di Ngizan
    Apparel.')

@section('content')
    <div class="py-8 sm:py-12 bg-canvas">
        <div class="wrap">

            {{-- 1. Top Section Header --}}
            <div
                class="mb-8 sm:mb-10 flex flex-col md:flex-row md:items-end md:justify-between gap-5 border-b border-hairline-soft pb-6">
                <div class="space-y-1">
                    <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.2em] text-mute block">
                        Buku Alamat Pelanggan
                    </span>
                    <h1 class="font-display font-medium text-3xl sm:text-4xl text-ink tracking-tight uppercase">
                        Alamat Pengiriman
                    </h1>
                    <p class="text-xs text-mute max-w-xl leading-relaxed">
                        Kelola destinasi pengiriman resmi jersey Anda untuk kemudahan checkout otomatis dan akurasi
                        pelacakan kurir.
                    </p>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <a href="{{ route('customer.addresses.create') }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-ink hover:bg-neutral-800 text-white text-xs font-bold uppercase tracking-[0.12em] rounded-full shadow-2xs hover:shadow-xs transition active:scale-95 cursor-pointer">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Tambah Alamat</span>
                    </a>
                </div>
            </div>

            {{-- 2. Flash Messages --}}
            @if (session('success'))
                <div
                    class="mb-6 p-4 bg-emerald-50 border border-emerald-200/80 rounded-2xl text-xs text-emerald-900 font-medium flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div
                    class="mb-6 p-4 bg-rose-50 border border-rose-200/80 rounded-2xl text-xs text-rose-900 font-medium flex items-center gap-2">
                    <svg class="w-4 h-4 text-sale shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{-- 3. Addresses Cards Grid --}}
            @if ($addresses->count() > 0)
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 sm:gap-6 items-start">
                    @foreach ($addresses as $address)
                        <div
                            class="bg-white border rounded-3xl p-6 sm:p-7 flex flex-col justify-between space-y-5 transition duration-200 relative {{ $address->is_primary ? 'border-ink ring-1 ring-ink/10 shadow-xs' : 'border-hairline-soft hover:border-hairline hover:shadow-xs' }}">

                            {{-- Header Card: Badges & Actions --}}
                            <div>
                                <div
                                    class="flex flex-wrap items-center justify-between gap-3 pb-4 border-b border-hairline-soft">
                                    {{-- Left Badges --}}
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span
                                            class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider {{ $address->is_primary ? 'bg-ink text-white' : 'bg-soft-cloud text-ink border border-hairline-soft' }}">
                                            {{ $address->label ?? 'Alamat' }}
                                        </span>

                                        @if ($address->is_primary)
                                            <span
                                                class="px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200/60 flex items-center gap-1">
                                                <span>✓</span>
                                                <span>Utama</span>
                                            </span>
                                        @else
                                            <form action="{{ route('customer.addresses.primary', $address) }}"
                                                method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                    class="text-[10px] uppercase font-bold tracking-wider text-mute hover:text-ink hover:underline transition cursor-pointer">
                                                    Set Utama
                                                </button>
                                            </form>
                                        @endif
                                    </div>

                                    {{-- Right Action Buttons --}}
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('customer.addresses.edit', $address) }}"
                                            class="btn-secondary py-1.5 px-4 text-xs font-bold rounded-full transition cursor-pointer"
                                            title="Edit detail alamat">
                                            Edit
                                        </a>

                                        <form action="{{ route('customer.addresses.destroy', $address) }}" method="POST"
                                            data-confirm-title="Hapus Alamat Pengiriman?"
                                            data-confirm-text="Apakah Anda yakin ingin menghapus alamat '{{ $address->label }}'?"
                                            data-confirm-btn="Ya, Hapus"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="w-8 h-8 rounded-full bg-soft-cloud hover:bg-rose-50 text-mute hover:text-sale flex items-center justify-center border border-hairline-soft transition cursor-pointer"
                                                title="Hapus alamat">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                {{-- Body: Recipient & Address Info --}}
                                <div class="pt-4 space-y-3">
                                    <div>
                                        <h3 class="font-bold text-ink text-base tracking-tight leading-snug">
                                            {{ $address->recipient_name }}
                                        </h3>
                                        <p class="text-xs text-mute font-medium flex items-center gap-1.5 mt-0.5">
                                            <svg class="w-3.5 h-3.5 text-neutral-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                            </svg>
                                            <span>{{ $address->phone_number }}</span>
                                        </p>
                                    </div>

                                    <div class="text-xs text-neutral-600 leading-relaxed font-normal space-y-1">
                                        <p class="font-medium text-ink">{{ $address->full_address }}</p>
                                        <p class="text-neutral-500">
                                            {{ $address->district_name && $address->district_name !== '-' ? $address->district_name . ', ' : '' }}{{ $address->city_name }},
                                            {{ $address->province_name }}
                                            <strong
                                                class="text-ink font-semibold ml-1">[{{ $address->postal_code }}]</strong>
                                        </p>
                                    </div>

                                    @if ($address->benchmark_notes)
                                        <div
                                            class="p-3 bg-soft-cloud/70 rounded-2xl border border-hairline-soft text-[11px] text-neutral-500 font-medium flex items-start gap-2">
                                            <span class="text-xs shrink-0">📍</span>
                                            <div>
                                                <strong class="text-ink font-bold">Patokan Lokasi:</strong>
                                                <span>{{ $address->benchmark_notes }}</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Footer Card: Status & Google Maps Verification --}}
                            <div class="pt-3 border-t border-hairline-soft flex items-center justify-between text-xs gap-3">
                                <div>
                                    @if ($address->hasActiveOrders())
                                        <span
                                            class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-amber-800 bg-amber-50 px-2.5 py-1 rounded-full border border-amber-200/60">
                                            <span>Digunakan di Pesanan Aktif</span>
                                        </span>
                                    @else
                                        <span class="text-[11px] text-neutral-400">Siap digunakan saat checkout</span>
                                    @endif
                                </div>

                                @php
                                    $mapQuery =
                                        $address->latitude && $address->longitude
                                            ? "{$address->latitude},{$address->longitude}"
                                            : urlencode(
                                                $address->full_address .
                                                    ', ' .
                                                    $address->city_name .
                                                    ', ' .
                                                    $address->province_name,
                                            );
                                @endphp
                                <a href="https://www.google.com/maps/search/?api=1&query={{ $mapQuery }}"
                                    target="_blank" rel="noopener noreferrer"
                                    class="text-[11px] font-bold text-ink hover:underline inline-flex items-center gap-1.5 group">
                                    <svg class="w-3 h-3 text-sale group-hover:scale-110 transition shrink-0"
                                        fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
                                    </svg>
                                    <span>Lihat di Maps</span>
                                </a>
                            </div>

                        </div>
                    @endforeach
                </div>
            @else
                {{-- 4. Clean Minimalist Empty State --}}
                <div
                    class="bg-white border border-hairline-soft rounded-3xl p-12 sm:p-16 text-center max-w-lg mx-auto shadow-xs space-y-4">
                    <div
                        class="w-16 h-16 rounded-full bg-soft-cloud border border-hairline-soft flex items-center justify-center mx-auto text-neutral-400">
                        <svg class="w-8 h-8 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>

                    <div class="space-y-1.5">
                        <h3 class="font-display font-medium text-2xl text-ink uppercase tracking-wide">
                            Belum Ada Alamat Tersimpan
                        </h3>
                        <p class="text-xs text-mute leading-relaxed max-w-sm mx-auto">
                            Simpan alamat rumah atau kantor Anda untuk mempermudah perhitungan ongkir kurir dan checkout
                            satu-klik yang presisi.
                        </p>
                    </div>

                    <div class="pt-3">
                        <a href="{{ route('customer.addresses.create') }}"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-ink hover:bg-neutral-800 text-white text-xs font-bold uppercase tracking-[0.12em] rounded-full shadow-2xs hover:shadow-xs transition active:scale-95 cursor-pointer">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Tambah Alamat Baru</span>
                        </a>
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection
