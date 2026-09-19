@extends('layouts.customer')

@section('title', 'Checkout & Pengiriman · NGIZAN APPAREL')
@section('meta_description', 'Lengkapi alamat pengiriman dengan pin point peta akurat, pilih kurir Biteship, dan bayar aman dengan Midtrans Snap.')

@push('styles')
    <style>
        #map { height: 260px; width: 100%; border-radius: 16px; z-index: 10; }
    </style>
@endpush

@section('content')
<div class="py-8 bg-canvas" x-data="checkoutApp()">
    <div class="wrap">
        
        {{-- Breadcrumb & Title --}}
        <div class="border-b border-hairline-soft pb-5 mb-8">
            <span class="text-xs font-medium uppercase tracking-widest text-mute block mb-1">Langkah Terakhir</span>
            <h1 class="text-2xl sm:text-3xl font-medium tracking-tight text-ink">
                Checkout & Pengiriman
            </h1>
        </div>

        <form @submit.prevent="submitOrder" class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
            
            {{-- ===== 1. FORM DATA PENGIRIMAN & PINPOINT MAPS (7 COLS) ===== --}}
            <div class="lg:col-span-7 space-y-6">
                
                {{-- Bagian 1: Data Penerima --}}
                <div class="bg-white p-6 rounded-2xl border border-hairline-soft space-y-4">
                    <h2 class="font-medium text-sm text-ink uppercase tracking-wider border-b border-hairline-soft pb-3 flex items-center gap-2">
                        <span>1. Informasi Penerima</span>
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div>
                            <label class="block font-medium text-ink mb-1.5">Nama Lengkap Penerima *</label>
                            <input type="text" x-model="form.recipient_name" required placeholder="Contoh: Muhammad Alvaro"
                                   class="w-full bg-soft-cloud border border-hairline px-4 py-2.5 rounded-full text-xs text-ink focus:border-ink focus:ring-0">
                        </div>
                        <div>
                            <label class="block font-medium text-ink mb-1.5">Nomor WhatsApp Aktif *</label>
                            <input type="tel" x-model="form.phone_number" required placeholder="081234567890"
                                   class="w-full bg-soft-cloud border border-hairline px-4 py-2.5 rounded-full text-xs text-ink focus:border-ink focus:ring-0">
                        </div>
                    </div>
                </div>

                {{-- Bagian 2: Alamat Presisi & Autocomplete Biteship --}}
                <div class="bg-white p-6 rounded-2xl border border-hairline-soft space-y-4">
                    <div class="flex justify-between items-center border-b border-hairline-soft pb-3">
                        <h2 class="font-medium text-sm text-ink uppercase tracking-wider">
                            2. Lokasi & Alamat Pengiriman
                        </h2>
                        <button type="button" @click="getCurrentLocation()" class="text-xs font-medium text-ink hover:text-mute underline flex items-center gap-1">
                            <span>Gunakan GPS Saya</span>
                        </button>
                    </div>

                    {{-- Autocomplete Area Biteship --}}
                    <div class="relative text-xs">
                        <label class="block font-medium text-ink mb-1.5">Cari Kecamatan / Kota / Kelurahan *</label>
                        <input type="text" x-model="areaSearchQuery" @input.debounce.400ms="searchBiteshipAreas()" 
                               placeholder="Ketik kecamatan atau kota tujuan (misal: Tebet, Jakarta Selatan)..."
                               class="w-full bg-soft-cloud border border-hairline px-4 py-2.5 rounded-full text-xs text-ink focus:border-ink focus:ring-0">
                        
                        {{-- Dropdown Hasil Area --}}
                        <div x-show="areaResults.length > 0" x-cloak @click.away="areaResults = []"
                             class="absolute left-0 right-0 top-full mt-1 bg-white border border-hairline rounded-2xl shadow-xl max-h-48 overflow-y-auto z-30 divide-y divide-hairline-soft">
                            <template x-for="area in areaResults" :key="area.id">
                                <div @click="selectArea(area)" class="p-3 hover:bg-soft-cloud cursor-pointer text-xs">
                                    <div class="font-medium text-ink" x-text="area.name"></div>
                                    <div class="text-[11px] text-mute" x-text="(area.district_name || '') + ', ' + (area.city_name || '') + ' - ' + (area.postal_code || '')"></div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Area Terpilih Badge --}}
                    <template x-if="form.biteship_area_id">
                        <div class="p-3 bg-soft-cloud border border-hairline rounded-xl text-xs flex justify-between items-center">
                            <div>
                                <span class="text-[10px] uppercase font-medium text-mute block">Area Terpilih:</span>
                                <p class="font-medium text-ink" x-text="form.district_name + ', ' + form.city_name + ' (' + form.postal_code + ')'"></p>
                            </div>
                            <span class="text-xs text-ink font-medium">✓ Terverifikasi</span>
                        </div>
                    </template>

                    {{-- Leaflet.js Interactive Map --}}
                    <div>
                        <label class="block font-medium text-ink mb-1.5 text-xs">
                            Pin Point Lokasi Rumah (Geser Pin untuk Kurir Instan)
                        </label>
                        <div id="map" class="border border-hairline-soft"></div>
                        <div class="flex justify-between items-center text-[11px] text-mute mt-1.5">
                            <span>Koordinat: <strong x-text="form.latitude.toFixed(5) + ', ' + form.longitude.toFixed(5)"></strong></span>
                            <span>Leaflet.js Pinpoint</span>
                        </div>
                    </div>

                    {{-- Alamat Lengkap & Patokan --}}
                    <div class="space-y-3 text-xs">
                        <div>
                            <label class="block font-medium text-ink mb-1.5">Alamat Lengkap (Nama Jalan, No. Rumah, RT/RW) *</label>
                            <textarea x-model="form.full_address" required rows="2" placeholder="Jl. Kemang Raya No. 12B, RT 02/RW 04..."
                                      class="w-full bg-soft-cloud border border-hairline p-3 rounded-xl text-xs text-ink focus:border-ink focus:ring-0"></textarea>
                        </div>

                        <div>
                            <label class="block font-medium text-ink mb-1.5">Patokan Rumah / Cat / Pagar (Opsional)</label>
                            <input type="text" x-model="form.benchmark_notes" placeholder="Contoh: Rumah pagar hitam samping minimarket"
                                   class="w-full bg-soft-cloud border border-hairline px-4 py-2.5 rounded-full text-xs text-ink focus:border-ink focus:ring-0">
                        </div>
                    </div>
                </div>

                {{-- Bagian 3: Pilihan Kurir & Ongkos Kirim (Biteship Rates) --}}
                <div class="bg-white p-6 rounded-2xl border border-hairline-soft space-y-4">
                    <div class="flex justify-between items-center border-b border-hairline-soft pb-3">
                        <h2 class="font-medium text-sm text-ink uppercase tracking-wider">
                            3. Pilihan Layanan Kurir
                        </h2>
                        <button type="button" @click="fetchShippingRates()" :disabled="isLoadingRates" 
                                class="text-xs font-medium text-ink hover:text-mute underline">
                            <span x-show="!isLoadingRates">🔄 Hitung Ulang Tarif</span>
                            <span x-show="isLoadingRates" x-cloak>Menghitung...</span>
                        </button>
                    </div>

                    {{-- Loading State --}}
                    <div x-show="isLoadingRates" class="py-6 text-center text-xs text-mute space-y-2">
                        <div class="inline-block w-5 h-5 border-2 border-ink border-t-transparent rounded-full animate-spin"></div>
                        <p>Mengambil tarif kurir (J&T Express, SiCepat, JNE, Grab, Gojek)...</p>
                    </div>

                    {{-- Daftar Pilihan Kurir --}}
                    <div x-show="!isLoadingRates && shippingOptions.length > 0" class="space-y-2 max-h-64 overflow-y-auto pr-1">
                        <template x-for="(rate, idx) in shippingOptions" :key="idx">
                            <label class="flex items-center justify-between p-3.5 border rounded-2xl cursor-pointer transition text-xs"
                                   :class="form.courier_service_code === rate.service_code && form.courier_code === rate.courier_code ? 'border-ink bg-soft-cloud' : 'border-hairline hover:border-ink'">
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="selected_courier" 
                                           :checked="form.courier_service_code === rate.service_code && form.courier_code === rate.courier_code"
                                           @change="selectCourier(rate)" class="text-ink focus:ring-ink">
                                    <div>
                                        <div class="font-medium text-ink uppercase" x-text="rate.courier_name + ' - ' + rate.service_name"></div>
                                        <div class="text-[11px] text-mute" x-text="'Estimasi: ' + (rate.etd || '1-3 Hari')"></div>
                                    </div>
                                </div>
                                <div class="font-medium text-sm text-ink tabular-nums" x-text="'Rp ' + (rate.price).toLocaleString('id-ID')"></div>
                            </label>
                        </template>
                    </div>

                    <div x-show="!isLoadingRates && shippingOptions.length === 0" class="py-4 text-center text-xs text-mute bg-soft-cloud rounded-xl">
                        Pilih area Biteship di atas untuk melihat pilihan kurir pengiriman.
                    </div>
                </div>

            </div>

            {{-- ===== 2. ORDER SUMMARY & MIDTRANS SNAP BUTTON (5 COLS) ===== --}}
            <div class="lg:col-span-5 bg-soft-cloud p-6 rounded-2xl border border-hairline-soft space-y-5 sticky top-24">
                <h2 class="font-medium text-base text-ink uppercase tracking-tight border-b border-hairline-soft pb-3">
                    Ringkasan Pesanan
                </h2>

                {{-- Daftar Item Singkat --}}
                <div class="space-y-3 max-h-56 overflow-y-auto pr-1 border-b border-hairline-soft pb-4">
                    @foreach($cart->items as $item)
                        <div class="flex items-center justify-between text-xs gap-3">
                            <div class="space-y-0.5">
                                <div class="font-medium text-ink line-clamp-1">{{ $item->product->name }}</div>
                                <div class="text-[11px] text-mute">
                                    Ukuran: {{ $item->variant->size ?? 'M' }} &times; {{ $item->quantity }} pcs
                                    @if($item->hasCustomNameset())
                                        · <span class="text-ink font-medium">#{{ $item->custom_name }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="font-medium text-ink tabular-nums whitespace-nowrap">
                                Rp {{ number_format($item->total_price, 0, ',', '.') }}
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Kalkulasi Total --}}
                <div class="space-y-2 text-xs text-mute">
                    <div class="flex justify-between">
                        <span>Subtotal Jersey</span>
                        <span class="font-medium text-ink">{{ $cart->formatted_total_price }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Ongkos Kirim (<span x-text="form.courier_service_name || 'Belum Dipilih'"></span>)</span>
                        <span class="font-medium text-ink" x-text="'Rp ' + form.shipping_cost.toLocaleString('id-ID')">Rp 0</span>
                    </div>
                </div>

                <div class="border-t border-hairline-soft pt-4 flex justify-between items-center">
                    <span class="text-xs uppercase font-medium text-ink">Total Pembayaran</span>
                    <div class="text-2xl font-medium text-ink tabular-nums" x-text="'Rp ' + grandTotal.toLocaleString('id-ID')">
                        {{ $cart->formatted_total_price }}
                    </div>
                </div>

                {{-- Catatan Pesanan --}}
                <div>
                    <label class="block font-medium text-ink mb-1.5 text-xs">Catatan Khusus Pesanan</label>
                    <input type="text" x-model="form.notes" placeholder="Contoh: Titipkan di pos satpam jika tidak ada orang"
                           class="w-full bg-white border border-hairline px-4 py-2 rounded-full text-xs text-ink focus:border-ink focus:ring-0">
                </div>

                {{-- Tombol Bayar Sekarang --}}
                <button type="submit" :disabled="isSubmitting || form.shipping_cost <= 0" 
                        class="btn-primary w-full py-4 text-center block disabled:opacity-50 disabled:cursor-not-allowed rounded-full text-xs font-medium uppercase tracking-wider">
                    <span x-show="!isSubmitting">Bayar Sekarang (Midtrans Snap) &rarr;</span>
                    <span x-show="isSubmitting" x-cloak>Memproses Pesanan...</span>
                </button>

                <div class="p-3 bg-white border border-hairline-soft rounded-xl text-xs text-mute space-y-1">
                    <p class="font-medium text-ink">⏱️ Batas Waktu Pembayaran 2 Jam</p>
                    <p class="text-[11px] leading-relaxed">Stok jersey otomatis diamankan (*Anti-Overselling Guard*). Jika tidak diselesaikan dalam 2 jam, pesanan kedaluwarsa dan stok dilepas kembali.</p>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection

@push('scripts')
    <!-- Midtrans Snap JS (Sandbox / Production) -->
    <script src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" 
            data-client-key="{{ config('services.midtrans.client_key') }}"></script>

    <script>
        function checkoutApp() {
            return {
                cartSubtotal: {{ (float)$cart->total_price }},
                isSubmitting: false,
                isLoadingRates: false,
                areaSearchQuery: '',
                areaResults: [],
                shippingOptions: [],
                map: null,
                marker: null,

                form: {
                    recipient_name: '{{ Auth::user()->name }}',
                    phone_number: '{{ $primaryAddress?->phone_number ?? '' }}',
                    full_address: '{{ $primaryAddress?->full_address ?? '' }}',
                    biteship_area_id: '{{ $primaryAddress?->biteship_area_id ?? 'IDNP6IDNC148IDND840IDZ12730' }}',
                    province_name: '{{ $primaryAddress?->province_name ?? 'DKI Jakarta' }}',
                    city_name: '{{ $primaryAddress?->city_name ?? 'Jakarta Selatan' }}',
                    district_name: '{{ $primaryAddress?->district_name ?? 'Tebet' }}',
                    postal_code: '{{ $primaryAddress?->postal_code ?? '12810' }}',
                    latitude: {{ $primaryAddress?->latitude ?? -6.229728 }},
                    longitude: {{ $primaryAddress?->longitude ?? 106.855556 }},
                    benchmark_notes: '{{ $primaryAddress?->benchmark_notes ?? '' }}',
                    save_address: true,
                    courier_code: '',
                    courier_service_code: '',
                    courier_service_name: '',
                    shipping_cost: 0,
                    notes: ''
                },

                get grandTotal() {
                    return this.cartSubtotal + (parseFloat(this.form.shipping_cost) || 0);
                },

                init() {
                    this.initMap();
                    this.fetchShippingRates();
                },

                initMap() {
                    this.$nextTick(() => {
                        this.map = L.map('map').setView([this.form.latitude, this.form.longitude], 14);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; OpenStreetMap contributors'
                        }).addTo(this.map);

                        this.marker = L.marker([this.form.latitude, this.form.longitude], { draggable: true }).addTo(this.map);

                        this.marker.on('dragend', (e) => {
                            const pos = e.target.getLatLng();
                            this.form.latitude = pos.lat;
                            this.form.longitude = pos.lng;
                            this.fetchShippingRates();
                        });
                    });
                },

                getCurrentLocation() {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition((pos) => {
                            this.form.latitude = pos.coords.latitude;
                            this.form.longitude = pos.coords.longitude;
                            this.map.setView([pos.coords.latitude, pos.coords.longitude], 16);
                            this.marker.setLatLng([pos.coords.latitude, pos.coords.longitude]);
                            toastr.success('Lokasi GPS Anda berhasil dikunci!');
                            this.fetchShippingRates();
                        }, (err) => {
                            toastr.warning('Izin akses lokasi ditolak atau tidak tersedia.');
                        });
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
                    this.form.province_name = area.province_name || '';
                    this.form.city_name = area.city_name || '';
                    this.form.district_name = area.district_name || area.name;
                    this.form.postal_code = area.postal_code || '';
                    if (area.latitude && area.longitude) {
                        this.form.latitude = area.latitude;
                        this.form.longitude = area.longitude;
                        this.map.setView([area.latitude, area.longitude], 14);
                        this.marker.setLatLng([area.latitude, area.longitude]);
                    }
                    this.areaResults = [];
                    this.areaSearchQuery = '';
                    this.fetchShippingRates();
                },

                async fetchShippingRates() {
                    this.isLoadingRates = true;
                    try {
                        const res = await fetch('/api/shipping/rates', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                destination_area_id: this.form.biteship_area_id,
                                destination_latitude: this.form.latitude,
                                destination_longitude: this.form.longitude
                            })
                        });
                        const data = await res.json();
                        this.shippingOptions = data.pricing || [];
                        if (this.shippingOptions.length > 0) {
                            this.selectCourier(this.shippingOptions[0]);
                        }
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.isLoadingRates = false;
                    }
                },

                selectCourier(rate) {
                    this.form.courier_code = rate.courier_code;
                    this.form.courier_service_code = rate.service_code;
                    this.form.courier_service_name = rate.courier_name + ' - ' + rate.service_name;
                    this.form.shipping_cost = rate.price;
                },

                async submitOrder() {
                    if (this.form.shipping_cost <= 0) {
                        toastr.error('Pilih opsi pengiriman terlebih dahulu.');
                        return;
                    }

                    this.isSubmitting = true;
                    try {
                        const res = await fetch('{{ route('customer.checkout.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify(this.form)
                        });

                        const data = await res.json();

                        if (!data.success) {
                            toastr.error(data.message || 'Gagal membuat pesanan.');
                            this.isSubmitting = false;
                            return;
                        }

                        toastr.success(data.message);

                        // Trigger Midtrans Snap Popup
                        if (data.snap_token && typeof window.snap !== 'undefined') {
                            window.snap.pay(data.snap_token, {
                                onSuccess: function(result) {
                                    window.location.href = `/customer/orders/${data.order_id}`;
                                },
                                onPending: function(result) {
                                    window.location.href = `/customer/orders/${data.order_id}`;
                                },
                                onError: function(result) {
                                    window.location.href = `/customer/orders/${data.order_id}`;
                                },
                                onClose: function() {
                                    window.location.href = `/customer/orders/${data.order_id}`;
                                }
                            });
                        } else if (data.redirect_url) {
                            window.location.href = data.redirect_url;
                        } else {
                            window.location.href = `/customer/orders/${data.order_id}`;
                        }

                    } catch (e) {
                        toastr.error('Terjadi kesalahan koneksi server.');
                        this.isSubmitting = false;
                    }
                }
            }
        }
    </script>
@endpush
