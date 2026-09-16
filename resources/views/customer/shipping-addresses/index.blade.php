@extends('layouts.customer')

@section('title', 'Alamat Pengiriman · NGIZAN APPAREL')
@section('meta_description', 'Kelola buku alamat pengiriman tersimpan Anda untuk kemudahan checkout di Ngizan Apparel.')

@section('content')
<div class="py-8 bg-canvas">
    <div class="wrap">
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-hairline-soft pb-5">
            <div>
                <span class="text-xs font-medium uppercase tracking-widest text-mute block mb-1">Buku Alamat</span>
                <h1 class="text-2xl sm:text-3xl font-medium tracking-tight text-ink">
                    Alamat Pengiriman
                </h1>
            </div>
            <a href="{{ route('customer.addresses.create') }}" class="btn-primary py-2.5 px-6 text-xs rounded-full inline-flex items-center gap-2">
                <span>+ Tambah Alamat</span>
            </a>
        </div>

        @if($addresses->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($addresses as $address)
                    <div class="bg-white border border-hairline-soft rounded-2xl p-6 flex flex-col justify-between space-y-4">
                        <div>
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h3 class="font-medium text-ink text-base">{{ $address->recipient_name }}</h3>
                                    <p class="text-xs text-mute mt-0.5">{{ $address->phone }}</p>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <a href="{{ route('customer.addresses.edit', $address) }}"
                                       class="btn-secondary py-1.5 px-3.5 text-xs rounded-full"
                                       title="Edit Alamat">
                                        Edit
                                    </a>
                                    <form action="{{ route('customer.addresses.destroy', $address) }}"
                                          method="POST"
                                          onsubmit="return confirm('Hapus alamat ini?');"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="py-1.5 px-3 text-xs text-sale hover:underline font-medium"
                                                title="Hapus Alamat">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="text-xs text-mute space-y-1 border-t border-hairline-soft pt-3">
                                <p class="leading-relaxed text-ink">{{ $address->address }}</p>
                                <p>{{ $address->city }}, {{ $address->province }} {{ $address->postal_code }}</p>
                            </div>

                            @if($address->orders()->whereIn('status', ['pending', 'processed', 'shipped'])->count() > 0)
                                <div class="mt-3 flex items-center gap-1.5 text-[11px] text-mute">
                                    <span>Digunakan di pesanan aktif</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- No Addresses -->
            <div class="bg-soft-cloud border border-hairline-soft rounded-2xl p-16 text-center text-mute max-w-lg mx-auto space-y-3">
                <h3 class="text-lg font-medium text-ink">Belum Ada Alamat</h3>
                <p class="text-xs text-mute">Anda belum memiliki alamat pengiriman terdaftar.</p>
                <div class="pt-2">
                    <a href="{{ route('customer.addresses.create') }}" class="btn-primary py-3 px-8 text-xs rounded-full inline-block">
                        Tambah Alamat Baru
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
