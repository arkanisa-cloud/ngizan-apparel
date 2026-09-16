@extends('layouts.customer')

@section('title', 'Alamat Pengiriman - Toko Online')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
        <svg class="w-7 h-7 text-slate-650" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        Alamat Pengiriman
    </h1>
    <a href="{{ route('customer.addresses.create') }}" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Alamat
    </a>
</div>

@if($addresses->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($addresses as $address)
            <div class="card flex flex-col justify-between">
                <div class="card-body">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="font-bold text-slate-800 text-base">{{ $address->recipient_name }}</h3>
                            <p class="text-xs font-semibold text-slate-500 mt-0.5">{{ $address->phone }}</p>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <a href="{{ route('customer.addresses.edit', $address) }}"
                               class="btn-outline btn-sm text-slate-550 border-slate-200 hover:bg-slate-50"
                               title="Edit Alamat">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form action="{{ route('customer.addresses.destroy', $address) }}"
                                  method="POST"
                                  onsubmit="return confirm('Hapus alamat ini?');"
                                  class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger btn-sm"
                                        title="Hapus Alamat">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="text-sm text-slate-700 space-y-1.5 border-t border-slate-100 pt-3">
                        <p class="leading-relaxed">{{ $address->address }}</p>
                        <p class="font-semibold text-slate-800">{{ $address->city }}, {{ $address->province }} {{ $address->postal_code }}</p>
                    </div>

                    <!-- Usage info in active orders -->
                    @if($address->orders()->whereIn('status', ['pending', 'processed', 'shipped'])->count() > 0)
                        <div class="mt-4 flex items-center gap-1.5 text-xs text-slate-400">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Digunakan di pesanan aktif</span>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@else
    <!-- No Addresses -->
    <div class="card p-12 text-center text-slate-500">
        <svg class="mx-auto h-16 w-16 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        <h3 class="mt-4 text-lg font-bold text-slate-800">Belum Ada Alamat</h3>
        <p class="mt-1 text-sm text-slate-500">Anda belum memiliki alamat pengiriman terdaftar.</p>
        <div class="mt-6">
            <a href="{{ route('customer.addresses.create') }}" class="btn-primary btn-lg">
                Tambah Alamat Baru
            </a>
        </div>
    </div>
@endif
@endsection
