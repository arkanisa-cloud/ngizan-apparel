@extends('layouts.customer')

@section('title', 'Edit Alamat - Toko Online')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <nav class="flex text-sm text-slate-500 gap-2">
        <a href="{{ route('customer.addresses.index') }}" class="hover:text-brand-600 transition-colors">Alamat Pengiriman</a>
        <span>/</span>
        <span class="text-slate-808 font-medium">Edit Alamat</span>
    </nav>
</div>

<div class="card max-w-2xl mx-auto">
    <div class="card-header border-b border-slate-200 bg-slate-50/50">
        <h3 class="text-base font-semibold text-slate-805">Edit Alamat</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('customer.addresses.update', $shippingAddress) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-5">
                <!-- Nama Penerima -->
                <div>
                    <label for="recipient_name" class="form-label text-xs">Nama Penerima <span class="text-red-500">*</span></label>
                    <input type="text"
                           name="recipient_name"
                           id="recipient_name"
                           class="form-input-custom"
                           value="{{ old('recipient_name', $shippingAddress->recipient_name) }}"
                           placeholder="Contoh: Budi Santoso"
                           required>
                    @error('recipient_name') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <!-- No. HP -->
                <div>
                    <label for="phone" class="form-label text-xs">No. HP / WhatsApp <span class="text-red-500">*</span></label>
                    <input type="text"
                           name="phone"
                           id="phone"
                           class="form-input-custom"
                           value="{{ old('phone', $shippingAddress->phone) }}"
                           placeholder="Contoh: 08123456789"
                           required>
                    @error('phone') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <!-- Alamat Lengkap -->
                <div>
                    <label for="address" class="form-label text-xs">Alamat Lengkap <span class="text-red-500">*</span></label>
                    <textarea name="address"
                              id="address"
                              class="form-input-custom"
                              rows="3"
                              placeholder="Nama jalan, nomor rumah, RT/RW..."
                              required>{{ old('address', $shippingAddress->address) }}</textarea>
                    @error('address') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <!-- Kota/Kabupaten -->
                <div>
                    <label for="city" class="form-label text-xs">Kota/Kabupaten <span class="text-red-500">*</span></label>
                    <input type="text"
                           name="city"
                           id="city"
                           class="form-input-custom"
                           value="{{ old('city', $shippingAddress->city) }}"
                           placeholder="Contoh: Jakarta Selatan"
                           required>
                    @error('city') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <!-- Provinsi -->
                <div>
                    <label for="province" class="form-label text-xs">Provinsi <span class="text-red-500">*</span></label>
                    <input type="text"
                           name="province"
                           id="province"
                           class="form-input-custom"
                           value="{{ old('province', $shippingAddress->province) }}"
                           placeholder="Contoh: DKI Jakarta"
                           required>
                    @error('province') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <!-- Kode Pos -->
                <div>
                    <label for="postal_code" class="form-label text-xs">Kode Pos <span class="text-red-500">*</span></label>
                    <input type="text"
                           name="postal_code"
                           id="postal_code"
                           class="form-input-custom"
                           value="{{ old('postal_code', $shippingAddress->postal_code) }}"
                           placeholder="Contoh: 12345"
                           required>
                    @error('postal_code') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <!-- Warning if used in active order -->
                @if($shippingAddress->orders()->whereIn('status', ['pending', 'processed', 'shipped'])->count() > 0)
                    <div class="alert alert-warning">
                        <svg class="w-5 h-5 flex-shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                        <span>Alamat ini sedang digunakan di pesanan aktif. Mohon hati-hati saat mengubah detail alamat.</span>
                    </div>
                @endif

                <div class="flex justify-end gap-3 pt-6 border-t border-slate-200">
                    <a href="{{ route('customer.addresses.index') }}" class="btn-secondary">Batal</a>
                    <button type="submit" class="btn-warning text-sm font-semibold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Update Alamat
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
