@extends('layouts.customer')

@section('title', 'Tambah Alamat - Toko Online')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <nav class="flex text-sm text-slate-500 gap-2">
        <a href="{{ route('customer.addresses.index') }}" class="hover:text-brand-600 transition-colors">Alamat Pengiriman</a>
        <span>/</span>
        <span class="text-slate-808 font-medium">Tambah Alamat</span>
    </nav>
</div>

<div class="card max-w-2xl mx-auto">
    <div class="card-header border-b border-slate-200 bg-slate-50/50">
        <h3 class="text-base font-semibold text-slate-800">Tambah Alamat Baru</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('customer.addresses.store') }}" method="POST">
            @csrf

            <div class="space-y-5">
                <!-- Nama Penerima -->
                <div>
                    <label for="recipient_name" class="form-label text-xs">Nama Penerima <span class="text-red-500">*</span></label>
                    <input type="text"
                           name="recipient_name"
                           id="recipient_name"
                           class="form-input-custom"
                           value="{{ old('recipient_name') }}"
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
                           value="{{ old('phone') }}"
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
                              required>{{ old('address') }}</textarea>
                    @error('address') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <!-- Kota/Kabupaten -->
                <div>
                    <label for="city" class="form-label text-xs">Kota/Kabupaten <span class="text-red-500">*</span></label>
                    <input type="text"
                           name="city"
                           id="city"
                           class="form-input-custom"
                           value="{{ old('city') }}"
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
                           value="{{ old('province') }}"
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
                           value="{{ old('postal_code') }}"
                           placeholder="Contoh: 12345"
                           required>
                    @error('postal_code') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-slate-200">
                    <a href="{{ route('customer.addresses.index') }}" class="btn-secondary">Batal</a>
                    <button type="submit" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan Alamat
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
