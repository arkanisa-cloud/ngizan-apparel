@extends('layouts.customer')

@section('title', 'Tambah Alamat Baru · NGIZAN APPAREL')
@section('meta_description', 'Tambahkan alamat pengiriman baru dengan pin point peta Leaflet akurat dan pengisian otomatis dari GPS.')

@push('styles')
    <style>
        #map {
            height: 250px;
            width: 100%;
            border-radius: 1rem;
            z-index: 10;
        }

        .leaflet-container {
            font-family: inherit;
        }
    </style>
@endpush

@section('content')
    <div class="py-8 sm:py-12 bg-canvas" x-data="addressFormApp()" x-cloak>
        <div class="wrap max-w-2xl">

            {{-- 1. Back Navigation & Breadcrumb --}}
            <div class="mb-6 flex items-center justify-between border-b border-hairline-soft pb-4">
                <a href="{{ route('customer.addresses.index') }}"
                    class="inline-flex items-center gap-1.5 text-xs font-bold text-mute hover:text-ink transition">
                    <span>&larr;</span>
                    <span>Kembali ke Buku Alamat</span>
                </a>
                <span class="text-[10px] uppercase font-bold tracking-widest text-neutral-400">
                    Buku Alamat
                </span>
            </div>

            {{-- 2. Form Card --}}
            <div class="bg-white border border-hairline-soft rounded-3xl p-6 sm:p-9 shadow-xs space-y-7">

                {{-- Header --}}
                <div class="space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-mute block">
                        Formulir Destinasi
                    </span>
                    <h1 class="font-display font-medium text-2xl sm:text-3xl text-ink uppercase tracking-tight">
                        Tambah Alamat Baru
                    </h1>
                    <p class="text-xs text-mute leading-relaxed">
                        Gunakan tombol GPS atau geser pin di peta untuk mengisi kecamatan dan alamat lengkap secara instan.
                    </p>
                </div>

                <form action="{{ route('customer.addresses.store') }}" method="POST" class="space-y-6 text-xs">
                    @csrf

                    {{-- Hidden Inputs for Location Meta --}}
                    <input type="hidden" name="biteship_area_id" :value="form.biteship_area_id">
                    <input type="hidden" name="latitude" :value="form.latitude">
                    <input type="hidden" name="longitude" :value="form.longitude">

                    {{-- Penamaan / Label Alamat --}}
                    <div class="space-y-1.5">
                        <label for="label" class="block font-bold text-ink uppercase text-[11px] tracking-wider">
                            Nama / Label Alamat *
                        </label>
                        <div class="flex flex-wrap items-center gap-2 mb-2">
                            @foreach (['Rumah', 'Kantor', 'Kost', 'Apartemen'] as $preset)
                                <button type="button" @click="selectedLabel = '{{ $preset }}'"
                                    :class="selectedLabel === '{{ $preset }}' ?
                                        'bg-ink text-white border-ink shadow-2xs' :
                                        'bg-soft-cloud text-ink border-hairline hover:bg-neutral-200'"
                                    class="px-3.5 py-1 rounded-full border text-xs font-semibold transition cursor-pointer">
                                    {{ $preset }}
                                </button>
                            @endforeach
                        </div>
                        <input type="text" name="label" id="label" x-model="selectedLabel"
                            class="w-full bg-soft-cloud border border-hairline-soft px-4 py-3 rounded-full text-xs text-ink focus:border-ink focus:bg-white focus:ring-0 transition placeholder:text-neutral-400 font-medium"
                            placeholder="Contoh: Rumah, Kantor, Kosan..." required>
                        @error('label')
                            <p class="text-sale text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 2 Columns: Penerima & No HP --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label for="recipient_name"
                                class="block font-bold text-ink uppercase text-[11px] tracking-wider">
                                Nama Lengkap Penerima *
                            </label>
                            <input type="text" name="recipient_name" id="recipient_name" x-model="form.recipient_name"
                                class="w-full bg-soft-cloud border border-hairline-soft px-4 py-3 rounded-full text-xs text-ink focus:border-ink focus:bg-white focus:ring-0 transition placeholder:text-neutral-400 font-medium"
                                placeholder="Contoh: Muhammad Alvaro" required>
                            @error('recipient_name')
                                <p class="text-sale text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="phone" class="block font-bold text-ink uppercase text-[11px] tracking-wider">
                                Nomor WhatsApp Aktif *
                            </label>
                            <input type="tel" name="phone" id="phone" x-model="form.phone"
                                class="w-full bg-soft-cloud border border-hairline-soft px-4 py-3 rounded-full text-xs text-ink focus:border-ink focus:bg-white focus:ring-0 transition placeholder:text-neutral-400 font-medium"
                                placeholder="Contoh: 081234567890" required>
                            @error('phone')
                                <p class="text-sale text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- SECTION: GPS, PETA LEAFLET & AUTO-FILL WILAYAH --}}
                    <div class="p-4 sm:p-5 bg-soft-cloud rounded-3xl border border-hairline-soft space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-hairline-soft pb-3">
                            <div>
                                <h3 class="font-extrabold text-xs text-ink uppercase tracking-wider flex items-center gap-1.5">
                                    <span>📍</span>
                                    <span>Titik Presisi & Deteksi Otomatis</span>
                                </h3>
                                <p class="text-[11px] text-mute mt-0.5">
                                    Klik tombol di samping untuk mengisi alamat secara otomatis.
                                </p>
                            </div>

                            {{-- Tombol GPS Lokasi Saat Ini --}}
                            <button type="button" @click="getCurrentLocation()" :disabled="isGeocoding"
                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-ink hover:bg-neutral-800 text-white text-xs font-bold rounded-full transition shadow-xs cursor-pointer disabled:opacity-50 shrink-0">
                                <template x-if="!isGeocoding">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </template>
                                <template x-if="isGeocoding">
                                    <span class="inline-block w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                                </template>
                                <span x-text="isGeocoding ? 'Mendeteksi...' : 'Gunakan Lokasi Saat Ini'"></span>
                            </button>
                        </div>

                        {{-- Leaflet Interactive Map Container --}}
                        <div class="space-y-2">
                            <div class="border border-hairline-soft rounded-2xl overflow-hidden relative shadow-inner">
                                <div id="map"></div>
                            </div>

                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                                <div class="flex items-center gap-2 text-mute text-[11px]">
                                    <span class="w-2 h-2 rounded-full"
                                        :class="isGeocoding ? 'bg-amber-500 animate-spin' : 'bg-emerald-500 animate-pulse'"></span>
                                    <span>Koordinat: <strong class="text-ink tabular-nums font-mono"
                                            x-text="form.latitude.toFixed(5) + ', ' + form.longitude.toFixed(5)"></strong></span>
                                </div>

                                <a :href="googleMapsUrl" target="_blank" rel="noopener noreferrer"
                                    class="inline-flex items-center gap-1.5 px-3 py-1 bg-white hover:bg-neutral-100 text-ink text-[11px] font-bold rounded-full border border-hairline-soft transition">
                                    <svg class="w-3 h-3 text-sale shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
                                    </svg>
                                    <span>Lihat di Google Maps</span>
                                </a>
                            </div>

                            {{-- Live Detecting Alert --}}
                            <div x-show="isGeocoding" x-cloak
                                class="p-2.5 bg-neutral-200 border border-hairline rounded-xl text-xs flex items-center gap-2 text-ink">
                                <span class="w-3.5 h-3.5 border-2 border-ink border-t-transparent rounded-full animate-spin shrink-0"></span>
                                <span>Mengambil nama jalan, kecamatan, dan wilayah dari titik koordinat...</span>
                            </div>
                        </div>

                        {{-- Search Autocomplete Wilayah --}}
                        <div class="relative space-y-1.5 pt-1">
                            <label class="block font-bold text-ink uppercase text-[11px] tracking-wider">
                                Cari Kecamatan / Kota / Kelurahan
                            </label>
                            <div class="relative">
                                <input type="text" x-model="areaSearchQuery"
                                    @input.debounce.350ms="searchBiteshipAreas()"
                                    placeholder="Ketik kecamatan, kelurahan, atau kode pos (misal: Tebet atau 12810)..."
                                    class="w-full bg-white border border-hairline-soft pl-10 pr-4 py-2.5 rounded-full text-xs text-ink focus:border-ink focus:bg-white focus:ring-0 transition placeholder:text-neutral-400 font-medium">
                                <div class="absolute left-3.5 top-3 text-neutral-400 pointer-events-none">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                            </div>

                            {{-- Dropdown Hasil Area --}}
                            <div x-show="areaResults.length > 0" x-cloak @click.away="areaResults = []"
                                class="absolute left-0 right-0 top-full mt-1.5 bg-white border border-hairline-soft rounded-2xl shadow-xl max-h-52 overflow-y-auto z-40 divide-y divide-hairline-soft">
                                <template x-for="area in areaResults" :key="area.id">
                                    <div @click="selectArea(area)"
                                        class="p-3 hover:bg-soft-cloud cursor-pointer transition text-xs flex items-center justify-between">
                                        <div>
                                            <div class="font-bold text-ink" x-text="area.name"></div>
                                            <div class="text-[11px] text-mute"
                                                x-text="(area.district_name || '') + ', ' + (area.city_name || '') + ' - ' + (area.postal_code || '')">
                                            </div>
                                        </div>
                                        <span class="text-[10px] px-2.5 py-0.5 bg-soft-cloud border border-hairline-soft rounded-full text-mute font-bold">
                                            Pilih &rarr;
                                        </span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Alamat Lengkap --}}
                    <div class="space-y-1.5">
                        <label for="address" class="block font-bold text-ink uppercase text-[11px] tracking-wider">
                            Alamat Lengkap (Nama Jalan, No. Rumah, RT/RW, Blok) *
                        </label>
                        <textarea name="address" id="address" rows="3" x-model="form.address"
                            class="w-full bg-soft-cloud border border-hairline-soft p-3.5 rounded-2xl text-xs text-ink focus:border-ink focus:bg-white focus:ring-0 transition placeholder:text-neutral-400 font-medium"
                            placeholder="Contoh: Jl. Kemang Raya No. 12B, RT 02/RW 04, Kel. Bangka..." required></textarea>
                        @error('address')
                            <p class="text-sale text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 2 Columns: Wilayah & Lokasi --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label for="district" class="block font-bold text-ink uppercase text-[11px] tracking-wider">
                                Kecamatan / Kelurahan
                            </label>
                            <input type="text" name="district" id="district" x-model="form.district"
                                class="w-full bg-soft-cloud border border-hairline-soft px-4 py-3 rounded-full text-xs text-ink focus:border-ink focus:bg-white focus:ring-0 transition placeholder:text-neutral-400 font-medium"
                                placeholder="Contoh: Mampang Prapatan">
                            @error('district')
                                <p class="text-sale text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="city" class="block font-bold text-ink uppercase text-[11px] tracking-wider">
                                Kota / Kabupaten *
                            </label>
                            <input type="text" name="city" id="city" x-model="form.city"
                                class="w-full bg-soft-cloud border border-hairline-soft px-4 py-3 rounded-full text-xs text-ink focus:border-ink focus:bg-white focus:ring-0 transition placeholder:text-neutral-400 font-medium"
                                placeholder="Contoh: Jakarta Selatan" required>
                            @error('city')
                                <p class="text-sale text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="province" class="block font-bold text-ink uppercase text-[11px] tracking-wider">
                                Provinsi *
                            </label>
                            <input type="text" name="province" id="province" x-model="form.province"
                                class="w-full bg-soft-cloud border border-hairline-soft px-4 py-3 rounded-full text-xs text-ink focus:border-ink focus:bg-white focus:ring-0 transition placeholder:text-neutral-400 font-medium"
                                placeholder="Contoh: DKI Jakarta" required>
                            @error('province')
                                <p class="text-sale text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="postal_code" class="block font-bold text-ink uppercase text-[11px] tracking-wider">
                                Kode Pos *
                            </label>
                            <input type="text" name="postal_code" id="postal_code" x-model="form.postal_code"
                                class="w-full bg-soft-cloud border border-hairline-soft px-4 py-3 rounded-full text-xs text-ink focus:border-ink focus:bg-white focus:ring-0 transition placeholder:text-neutral-400 font-medium"
                                placeholder="Contoh: 12730" required>
                            @error('postal_code')
                                <p class="text-sale text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Patokan Rumah (Opsional) --}}
                    <div class="space-y-1.5">
                        <label for="benchmark_notes" class="block font-bold text-ink uppercase text-[11px] tracking-wider">
                            Patokan Rumah / Cat / Pagar (Opsional)
                        </label>
                        <input type="text" name="benchmark_notes" id="benchmark_notes" x-model="form.benchmark_notes"
                            class="w-full bg-soft-cloud border border-hairline-soft px-4 py-3 rounded-full text-xs text-ink focus:border-ink focus:bg-white focus:ring-0 transition placeholder:text-neutral-400 font-medium"
                            placeholder="Contoh: Rumah tingkat warna abu-abu pagar hitam samping minimarket">
                        @error('benchmark_notes')
                            <p class="text-sale text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Checkbox Jadikan Alamat Utama --}}
                    <div class="pt-2">
                        <label class="inline-flex items-center gap-2.5 cursor-pointer text-xs font-semibold text-ink select-none">
                            <input type="checkbox" name="is_primary" value="1"
                                {{ old('is_primary', true) ? 'checked' : '' }}
                                class="w-4 h-4 rounded border-neutral-300 text-ink focus:ring-ink">
                            <span>Jadikan alamat ini sebagai Alamat Utama</span>
                        </label>
                    </div>

                    {{-- Footer Buttons --}}
                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-hairline-soft">
                        <a href="{{ route('customer.addresses.index') }}"
                            class="px-5 py-2.5 rounded-full text-xs font-bold uppercase tracking-wider text-mute hover:text-ink transition active:scale-95">
                            Batal
                        </a>
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-ink hover:bg-neutral-800 text-white text-xs font-bold uppercase tracking-[0.12em] rounded-full shadow-2xs hover:shadow-xs transition active:scale-95 cursor-pointer">
                            <span>Simpan Alamat</span>
                            <span>&rarr;</span>
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function addressFormApp() {
            return {
                selectedLabel: '{{ old('label', 'Rumah') }}',
                areaSearchQuery: '',
                areaResults: [],
                isGeocoding: false,
                detectedLocationText: '',
                map: null,
                marker: null,
                form: {
                    recipient_name: '{{ old('recipient_name', Auth::user()->name ?? '') }}',
                    phone: '{{ old('phone', Auth::user()->phone ?? '') }}',
                    address: '{{ old('address', '') }}',
                    district: '{{ old('district', '') }}',
                    city: '{{ old('city', '') }}',
                    province: '{{ old('province', 'DKI Jakarta') }}',
                    postal_code: '{{ old('postal_code', '') }}',
                    benchmark_notes: '{{ old('benchmark_notes', '') }}',
                    biteship_area_id: '{{ old('biteship_area_id', '') }}',
                    latitude: {{ old('latitude', -6.229728) }},
                    longitude: {{ old('longitude', 106.855556) }},
                },

                get googleMapsUrl() {
                    if (this.form.latitude && this.form.longitude) {
                        return 'https://www.google.com/maps/search/?api=1&query=' + this.form.latitude + ',' + this.form.longitude;
                    }
                    return 'https://maps.google.com';
                },

                init() {
                    this.initMap();
                    if (this.form.district || this.form.city) {
                        this.areaSearchQuery = [this.form.district, this.form.city].filter(Boolean).join(', ') + (this.form.postal_code ? ' (' + this.form.postal_code + ')' : '');
                    }
                },

                initMap() {
                    this.$nextTick(() => {
                        const mapEl = document.getElementById('map');
                        if (!mapEl || !window.L) return;

                        try {
                            delete window.L.Icon.Default.prototype._getIconUrl;
                            window.L.Icon.Default.mergeOptions({
                                iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
                                iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
                                shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
                            });
                        } catch (e) {}

                        this.map = window.L.map('map', {
                            scrollWheelZoom: false
                        }).setView([this.form.latitude, this.form.longitude], 15);

                        window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; OpenStreetMap contributors',
                            maxZoom: 19
                        }).addTo(this.map);

                        this.marker = window.L.marker([this.form.latitude, this.form.longitude], {
                            draggable: true
                        }).addTo(this.map);

                        this.marker.on('dragend', (e) => {
                            const pos = e.target.getLatLng();
                            this.form.latitude = pos.lat;
                            this.form.longitude = pos.lng;
                            this.reverseGeocodeLocation(pos.lat, pos.lng);
                        });

                        this.map.on('click', (e) => {
                            this.marker.setLatLng(e.latlng);
                            this.form.latitude = e.latlng.lat;
                            this.form.longitude = e.latlng.lng;
                            this.reverseGeocodeLocation(e.latlng.lat, e.latlng.lng);
                        });
                    });
                },

                getCurrentLocation() {
                    if (navigator.geolocation) {
                        this.isGeocoding = true;
                        if (window.toastr) {
                            toastr.info('Mendeteksi lokasi GPS perangkat Anda...');
                        }
                        navigator.geolocation.getCurrentPosition((pos) => {
                            this.form.latitude = pos.coords.latitude;
                            this.form.longitude = pos.coords.longitude;
                            if (this.map && this.marker) {
                                this.map.setView([pos.coords.latitude, pos.coords.longitude], 16);
                                this.marker.setLatLng([pos.coords.latitude, pos.coords.longitude]);
                            }
                            this.reverseGeocodeLocation(pos.coords.latitude, pos.coords.longitude);
                        }, (err) => {
                            this.isGeocoding = false;
                            if (window.toastr) {
                                toastr.warning('Izin akses GPS ditolak atau tidak tersedia.');
                            }
                        }, { enableHighAccuracy: true, timeout: 10000 });
                    } else {
                        if (window.toastr) toastr.error('Perangkat Anda tidak mendukung fitur Geolocation.');
                    }
                },

                async reverseGeocodeLocation(lat, lng) {
                    this.isGeocoding = true;
                    try {
                        const res = await fetch(`/api/shipping/reverse-geocode?latitude=${encodeURIComponent(lat)}&longitude=${encodeURIComponent(lng)}`);
                        const data = await res.json();
                        if (data.success && data.geo) {
                            const geo = data.geo;
                            this.detectedLocationText = geo.street_address || geo.road || geo.display_name || '';

                            // Otomatis isi alamat lengkap
                            if (this.detectedLocationText) {
                                this.form.address = this.detectedLocationText;
                            }

                            if (geo.state) this.form.province = geo.state;
                            if (geo.city) this.form.city = geo.city;
                            if (geo.district) this.form.district = geo.district;
                            if (geo.postcode) this.form.postal_code = geo.postcode.toString();

                            if (data.matched_area) {
                                this.form.biteship_area_id = data.matched_area.id;
                                this.form.district = data.matched_area.administrative_division_level_3_name || data.matched_area.name;
                                this.form.city = data.matched_area.administrative_division_level_2_name || this.form.city;
                                this.form.province = data.matched_area.administrative_division_level_1_name || this.form.province;
                                if (data.matched_area.postal_code) {
                                    this.form.postal_code = data.matched_area.postal_code.toString();
                                }
                                this.areaSearchQuery = [this.form.district, this.form.city].filter(Boolean).join(', ') + ' (' + this.form.postal_code + ')';
                            } else if (geo.district || geo.city) {
                                this.areaSearchQuery = [geo.district, geo.city].filter(Boolean).join(', ') + (geo.postcode ? ' (' + geo.postcode + ')' : '');
                            }

                            if (window.toastr) {
                                toastr.success('Alamat & wilayah berhasil diisi otomatis dari lokasi GPS!');
                            }
                        }
                    } catch (e) {
                        console.error('Reverse geocode error:', e);
                    } finally {
                        this.isGeocoding = false;
                    }
                },

                async searchBiteshipAreas() {
                    if (this.areaSearchQuery.length < 3) {
                        this.areaResults = [];
                        return;
                    }
                    try {
                        const res = await fetch(`/api/shipping/areas?query=${encodeURIComponent(this.areaSearchQuery)}`);
                        const data = await res.json();
                        this.areaResults = data.areas || [];
                    } catch (e) {
                        console.error(e);
                    }
                },

                selectArea(area) {
                    this.form.biteship_area_id = area.id;
                    this.form.district = area.administrative_division_level_3_name || area.name;
                    this.form.city = area.administrative_division_level_2_name || '';
                    this.form.province = area.administrative_division_level_1_name || '';
                    this.form.postal_code = area.postal_code ? area.postal_code.toString() : '';
                    this.areaSearchQuery = `${area.name} (${this.form.postal_code})`;
                    this.areaResults = [];
                    if (window.toastr) {
                        toastr.success(`Wilayah ${this.form.district} terpilih.`);
                    }
                }
            };
        }
    </script>
@endpush
