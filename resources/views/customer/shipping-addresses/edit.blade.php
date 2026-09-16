@extends('layouts.customer')

@section('title', 'Edit Alamat · NGIZAN APPAREL')
@section('meta_description', 'Perbarui detail alamat pengiriman tersimpan Anda di Ngizan Apparel.')

@section('content')
<div class="py-8 bg-canvas">
    <div class="wrap max-w-2xl">
        <div class="mb-6 flex items-center justify-between border-b border-hairline-soft pb-4">
            <nav class="flex text-xs text-mute gap-2">
                <a href="{{ route('customer.addresses.index') }}" class="hover:text-ink">Alamat Pengiriman</a>
                <span>/</span>
                <span class="text-ink font-medium">Edit Alamat</span>
            </nav>
        </div>

        <div class="bg-white border border-hairline-soft rounded-2xl p-6 sm:p-8 space-y-6">
            <h2 class="text-lg font-medium text-ink">Edit Alamat</h2>

            <form action="{{ route('customer.addresses.update', $shippingAddress) }}" method="POST" class="space-y-4 text-xs">
                @csrf
                @method('PUT')

                <!-- Nama Penerima -->
                <div>
                    <label for="recipient_name" class="block font-medium text-ink mb-1.5">Nama Penerima *</label>
                    <input type="text"
                           name="recipient_name"
                           id="recipient_name"
                           class="w-full bg-soft-cloud border border-hairline rounded-full px-4 py-2.5 text-xs text-ink focus:border-ink focus:ring-0"
                           value="{{ old('recipient_name', $shippingAddress->recipient_name) }}"
                           placeholder="Contoh: Budi Santoso"
                           required>
                    @error('recipient_name') <p class="text-sale text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- No. HP -->
                <div>
                    <label for="phone" class="block font-medium text-ink mb-1.5">No. HP / WhatsApp *</label>
                    <input type="text"
                           name="phone"
                           id="phone"
                           class="w-full bg-soft-cloud border border-hairline rounded-full px-4 py-2.5 text-xs text-ink focus:border-ink focus:ring-0"
                           value="{{ old('phone', $shippingAddress->phone) }}"
                           placeholder="Contoh: 08123456789"
                           required>
                    @error('phone') <p class="text-sale text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Alamat Lengkap -->
                <div>
                    <label for="address" class="block font-medium text-ink mb-1.5">Alamat Lengkap *</label>
                    <textarea name="address"
                              id="address"
                              class="w-full bg-soft-cloud border border-hairline rounded-xl p-3 text-xs text-ink focus:border-ink focus:ring-0"
                              rows="3"
                              placeholder="Nama jalan, nomor rumah, RT/RW..."
                              required>{{ old('address', $shippingAddress->address) }}</textarea>
                    @error('address') <p class="text-sale text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Kota/Kabupaten -->
                <div>
                    <label for="city" class="block font-medium text-ink mb-1.5">Kota/Kabupaten *</label>
                    <input type="text"
                           name="city"
                           id="city"
                           class="w-full bg-soft-cloud border border-hairline rounded-full px-4 py-2.5 text-xs text-ink focus:border-ink focus:ring-0"
                           value="{{ old('city', $shippingAddress->city) }}"
                           placeholder="Contoh: Jakarta Selatan"
                           required>
                    @error('city') <p class="text-sale text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Provinsi -->
                <div>
                    <label for="province" class="block font-medium text-ink mb-1.5">Provinsi *</label>
                    <input type="text"
                           name="province"
                           id="province"
                           class="w-full bg-soft-cloud border border-hairline rounded-full px-4 py-2.5 text-xs text-ink focus:border-ink focus:ring-0"
                           value="{{ old('province', $shippingAddress->province) }}"
                           placeholder="Contoh: DKI Jakarta"
                           required>
                    @error('province') <p class="text-sale text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Kode Pos -->
                <div>
                    <label for="postal_code" class="block font-medium text-ink mb-1.5">Kode Pos *</label>
                    <input type="text"
                           name="postal_code"
                           id="postal_code"
                           class="w-full bg-soft-cloud border border-hairline rounded-full px-4 py-2.5 text-xs text-ink focus:border-ink focus:ring-0"
                           value="{{ old('postal_code', $shippingAddress->postal_code) }}"
                           placeholder="Contoh: 12345"
                           required>
                    @error('postal_code') <p class="text-sale text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Warning if used in active order -->
                @if($shippingAddress->orders()->whereIn('status', ['pending', 'processed', 'shipped'])->count() > 0)
                    <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-900">
                        Alamat ini sedang digunakan di pesanan aktif. Mohon hati-hati saat mengubah detail alamat.
                    </div>
                @endif

                <div class="flex justify-end gap-3 pt-6 border-t border-hairline-soft">
                    <a href="{{ route('customer.addresses.index') }}" class="btn-secondary py-2.5 px-6 rounded-full text-xs font-medium">Batal</a>
                    <button type="submit" class="btn-primary py-2.5 px-6 rounded-full text-xs font-medium">
                        Update Alamat
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
