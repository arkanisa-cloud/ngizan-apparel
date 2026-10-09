@extends('layouts.customer')

@section('title', 'Hubungi Kami · Contact Us — NGIZAN APPAREL')
@section('meta_description', 'Hubungi tim layanan pelanggan Ngizan Apparel untuk pertanyaan pesanan, konsultasi kustom jersey nameset & patch, serta bantuan operasional.')

@section('content')
<div class="min-h-screen bg-white text-ink pt-28 pb-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12 sm:space-y-16">

        {{-- Breadcrumbs & Header --}}
        <div class="space-y-3">
            <nav class="flex items-center gap-2 text-xs text-mute font-medium">
                <a href="{{ route('home') }}" class="hover:text-ink transition">Beranda</a>
                <span>/</span>
                <span class="text-ink font-semibold">Hubungi Kami</span>
            </nav>

            <div class="max-w-3xl space-y-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-soft-cloud text-[11px] font-bold uppercase tracking-wider text-ink">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Customer Support & Studio
                </span>
                <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-ink uppercase">
                    Ada Pertanyaan? <br class="hidden sm:inline" />Kami Siap Membantu.
                </h1>
                <p class="text-sm sm:text-base text-mute leading-relaxed">
                    Konsultasi kustom jersey nameset & patch, informasi ketersediaan stok, pengecekan resi pengiriman, atau penawaran pesanan tim.
                </p>
            </div>
        </div>

        {{-- 4 Direct Channel Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            {{-- 1. WhatsApp CS --}}
            <a href="https://wa.me/6281234567890?text=Halo%20Admin%20Ngizan%20Apparel,%20saya%20ingin%20bertanya%20seputar%20produk/pesanan." 
               target="_blank" rel="noopener noreferrer"
               class="p-6 rounded-2xl bg-soft-cloud hover:bg-neutral-200/80 border border-hairline-soft transition group flex flex-col justify-between space-y-4">
                <div class="space-y-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-500/10 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-5.805 1.554zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-ink">WhatsApp CS Resmi</h3>
                        <p class="text-xs text-mute mt-0.5">Respon cepat & konsultasi langsung</p>
                    </div>
                </div>
                <div class="text-xs font-semibold text-ink flex items-center gap-1 group-hover:underline">
                    <span>+62 812-3456-7890</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </div>
            </a>

            {{-- 2. Email Support --}}
            <a href="mailto:support@ngizanapparel.com"
               class="p-6 rounded-2xl bg-soft-cloud hover:bg-neutral-200/80 border border-hairline-soft transition group flex flex-col justify-between space-y-4">
                <div class="space-y-3">
                    <div class="w-10 h-10 rounded-full bg-ink/10 text-ink flex items-center justify-center group-hover:scale-110 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-ink">Email Support</h3>
                        <p class="text-xs text-mute mt-0.5">Untuk inquiry bisnis & kemitraan</p>
                    </div>
                </div>
                <div class="text-xs font-semibold text-ink flex items-center gap-1 group-hover:underline">
                    <span>support@ngizanapparel.com</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </div>
            </a>

            {{-- 3. Jam Operasional --}}
            <div class="p-6 rounded-2xl bg-soft-cloud border border-hairline-soft flex flex-col justify-between space-y-4">
                <div class="space-y-3">
                    <div class="w-10 h-10 rounded-full bg-amber-500/10 text-amber-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-ink">Jam Operasional CS</h3>
                        <p class="text-xs text-mute mt-0.5">Layanan bantuan pelanggan</p>
                    </div>
                </div>
                <div class="text-xs font-semibold text-ink">
                    <p>Senin – Sabtu: 09:00 - 21:00 WIB</p>
                    <p class="text-[11px] text-mute font-normal">Minggu & Hari Libur: Slow Response</p>
                </div>
            </div>

            {{-- 4. Workshop Studio --}}
            <div class="p-6 rounded-2xl bg-soft-cloud border border-hairline-soft flex flex-col justify-between space-y-4">
                <div class="space-y-3">
                    <div class="w-10 h-10 rounded-full bg-blue-500/10 text-blue-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-ink">Studio & Workshop</h3>
                        <p class="text-xs text-mute mt-0.5">Pusat produksi & logistik</p>
                    </div>
                </div>
                <div class="text-xs font-semibold text-ink">
                    <p>Yogyakarta, Indonesia</p>
                    <p class="text-[11px] text-mute font-normal">Kawasan Pengiriman Cepat</p>
                </div>
            </div>
        </div>

        {{-- Main 2-Column: Form & FAQ --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start" x-data="{
            name: '',
            contact: '',
            category: 'Tanya Produk / Stok',
            orderNumber: '',
            message: '',
            categories: [
                'Tanya Produk / Stok',
                'Kustom Nameset & Patch',
                'Status & Resi Pesanan',
                'Kemitraan / Pesanan Tim',
                'Lainnya'
            ],
            sendViaWhatsApp() {
                if (!this.name || !this.message) {
                    if (window.toastr) {
                        toastr.warning('Mohon lengkapi Nama dan Pesan terlebih dahulu.');
                    } else {
                        alert('Mohon lengkapi Nama dan Pesan terlebih dahulu.');
                    }
                    return;
                }
                let text = `*HALO CS NGIZAN APPAREL*%0A%0A`;
                text += `*Nama:* ${encodeURIComponent(this.name)}%0A`;
                if (this.contact) text += `*Kontak:* ${encodeURIComponent(this.contact)}%0A`;
                text += `*Kategori:* ${encodeURIComponent(this.category)}%0A`;
                if (this.orderNumber) text += `*No. Pesanan:* ${encodeURIComponent(this.orderNumber)}%0A`;
                text += `%0A*Pesan / Pertanyaan:*%0A${encodeURIComponent(this.message)}`;

                const waUrl = `https://wa.me/6281234567890?text=${text}`;
                window.open(waUrl, '_blank');
            },
            sendOnlineForm() {
                if (!this.name || !this.message) {
                    if (window.toastr) {
                        toastr.warning('Mohon lengkapi Nama dan Pesan Anda.');
                    } else {
                        alert('Mohon lengkapi Nama dan Pesan Anda.');
                    }
                    return;
                }
                if (window.Swal) {
                    Swal.fire({
                        title: 'Pesan Terkirim!',
                        text: 'Terima kasih, tim Customer Care Ngizan Apparel akan segera menghubungi Anda.',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#111111',
                        customClass: {
                            popup: 'rounded-3xl',
                            confirmButton: 'rounded-full px-6 py-2.5 text-xs font-bold uppercase'
                        }
                    });
                } else if (window.toastr) {
                    toastr.success('Pesan Anda telah berhasil dikirim!');
                }
                this.name = '';
                this.contact = '';
                this.orderNumber = '';
                this.message = '';
            }
        }">
            {{-- Form Section (7 cols) --}}
            <div class="lg:col-span-7 bg-white p-6 sm:p-8 rounded-3xl border border-hairline-soft shadow-xs space-y-6">
                <div class="space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-mute">Formulir Pesan</span>
                    <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-ink">Kirimkan Pertanyaan Anda</h2>
                    <p class="text-xs text-mute">Tim kami biasanya merespon dalam waktu kurang dari 1 jam pada jam kerja.</p>
                </div>

                <form @submit.prevent="sendViaWhatsApp" class="space-y-4 text-xs">
                    {{-- Nama Lengkap & Kontak --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-ink mb-1.5 uppercase tracking-wider text-[10px]">
                                Nama Lengkap <span class="text-rose-600">*</span>
                            </label>
                            <input type="text" x-model="name" placeholder="Nama lengkap Anda" required
                                class="w-full bg-soft-cloud border border-hairline px-4 py-3 rounded-xl text-xs text-ink focus:outline-none focus:border-ink transition font-medium">
                        </div>
                        <div>
                            <label class="block font-semibold text-ink mb-1.5 uppercase tracking-wider text-[10px]">
                                Email / WhatsApp <span class="text-rose-600">*</span>
                            </label>
                            <input type="text" x-model="contact" placeholder="0812xxxx atau email@domain.com"
                                class="w-full bg-soft-cloud border border-hairline px-4 py-3 rounded-xl text-xs text-ink focus:outline-none focus:border-ink transition font-medium">
                        </div>
                    </div>

                    {{-- Kategori Topik --}}
                    <div>
                        <label class="block font-semibold text-ink mb-2 uppercase tracking-wider text-[10px]">
                            Kategori Pertanyaan
                        </label>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="cat in categories" :key="cat">
                                <button type="button" @click="category = cat"
                                    :class="category === cat ? 'bg-ink text-white font-bold' : 'bg-soft-cloud text-mute hover:text-ink font-medium'"
                                    class="px-3.5 py-1.5 rounded-full text-[11px] transition cursor-pointer border border-hairline-soft">
                                    <span x-text="cat"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    {{-- No. Pesanan (Opsional) --}}
                    <div>
                        <label class="block font-semibold text-ink mb-1.5 uppercase tracking-wider text-[10px]">
                            Nomor Pesanan <span class="text-mute font-normal">(Opsional, jika sudah order)</span>
                        </label>
                        <input type="text" x-model="orderNumber" placeholder="NGZ-20261009-XXXX"
                            class="w-full bg-soft-cloud border border-hairline px-4 py-3 rounded-xl text-xs text-ink focus:outline-none focus:border-ink transition font-mono">
                    </div>

                    {{-- Isi Pesan --}}
                    <div>
                        <label class="block font-semibold text-ink mb-1.5 uppercase tracking-wider text-[10px]">
                            Detail Pesan / Pertanyaan <span class="text-rose-600">*</span>
                        </label>
                        <textarea x-model="message" rows="4" placeholder="Tuliskan pertanyaan atau detail kebutuhan Anda di sini..." required
                            class="w-full bg-soft-cloud border border-hairline p-4 rounded-xl text-xs text-ink focus:outline-none focus:border-ink transition resize-none font-medium"></textarea>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="pt-2 flex flex-col sm:flex-row gap-3">
                        <button type="button" @click="sendViaWhatsApp"
                            class="w-full sm:flex-1 py-3.5 px-6 rounded-full bg-ink hover:opacity-90 text-white font-bold text-xs uppercase tracking-wider transition flex items-center justify-center gap-2 cursor-pointer shadow-sm">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-5.805 1.554zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                            </svg>
                            <span>Chat CS via WhatsApp</span>
                        </button>
                        <button type="button" @click="sendOnlineForm"
                            class="w-full sm:w-auto py-3.5 px-6 rounded-full bg-soft-cloud hover:bg-neutral-200 text-ink font-semibold text-xs transition cursor-pointer border border-hairline-soft">
                            Kirim Formulir
                        </button>
                    </div>
                </form>
            </div>

            {{-- FAQ Accordion Section (5 cols) --}}
            <div class="lg:col-span-5 space-y-4" x-data="{ activeAccordion: 1 }">
                <div class="space-y-1 mb-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-mute">Bantuan Cepat</span>
                    <h2 class="text-xl font-bold tracking-tight text-ink">Pertanyaan Umum (FAQ)</h2>
                </div>

                {{-- Item 1 --}}
                <div class="bg-soft-cloud rounded-2xl border border-hairline-soft overflow-hidden transition">
                    <button type="button" @click="activeAccordion = (activeAccordion === 1 ? null : 1)"
                        class="w-full p-4 text-left flex items-center justify-between text-xs font-bold text-ink cursor-pointer">
                        <span>Berapa lama proses sablon nameset & patch?</span>
                        <svg class="w-4 h-4 text-mute transition-transform duration-200"
                            :class="activeAccordion === 1 ? 'rotate-180 text-ink' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="activeAccordion === 1" x-cloak class="px-4 pb-4 text-xs text-mute leading-relaxed border-t border-hairline-soft/60 pt-3">
                        Proses sablon polyflex premium dan pemasangan patch turnamen memakan waktu <strong class="text-ink">1–2 hari kerja</strong> sebelum pesanan diteruskan ke kurir logistik.
                    </div>
                </div>

                {{-- Item 2 --}}
                <div class="bg-soft-cloud rounded-2xl border border-hairline-soft overflow-hidden transition">
                    <button type="button" @click="activeAccordion = (activeAccordion === 2 ? null : 2)"
                        class="w-full p-4 text-left flex items-center justify-between text-xs font-bold text-ink cursor-pointer">
                        <span>Bagaimana cara cek resi dan status kirim?</span>
                        <svg class="w-4 h-4 text-mute transition-transform duration-200"
                            :class="activeAccordion === 2 ? 'rotate-180 text-ink' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="activeAccordion === 2" x-cloak class="px-4 pb-4 text-xs text-mute leading-relaxed border-t border-hairline-soft/60 pt-3">
                        Setelah pesanan di-pickup oleh kurir via sistem Biteship, Anda akan menerima nomor resi resmi yang dapat dipantau di halaman akun profil atau website resmi kurir terkait.
                    </div>
                </div>

                {{-- Item 3 --}}
                <div class="bg-soft-cloud rounded-2xl border border-hairline-soft overflow-hidden transition">
                    <button type="button" @click="activeAccordion = (activeAccordion === 3 ? null : 3)"
                        class="w-full p-4 text-left flex items-center justify-between text-xs font-bold text-ink cursor-pointer">
                        <span>Apakah bisa memesan jersey polos (tanpa sablon)?</span>
                        <svg class="w-4 h-4 text-mute transition-transform duration-200"
                            :class="activeAccordion === 3 ? 'rotate-180 text-ink' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="activeAccordion === 3" x-cloak class="px-4 pb-4 text-xs text-mute leading-relaxed border-t border-hairline-soft/60 pt-3">
                        Tentu saja! Jika tidak mengisi opsi nameset di halaman detail produk, jersey akan dikirim dalam kondisi original kit polos (ready to ship).
                    </div>
                </div>

                {{-- Item 4 --}}
                <div class="bg-soft-cloud rounded-2xl border border-hairline-soft overflow-hidden transition">
                    <button type="button" @click="activeAccordion = (activeAccordion === 4 ? null : 4)"
                        class="w-full p-4 text-left flex items-center justify-between text-xs font-bold text-ink cursor-pointer">
                        <span>Bagaimana kebijakan retur dan garansi?</span>
                        <svg class="w-4 h-4 text-mute transition-transform duration-200"
                            :class="activeAccordion === 4 ? 'rotate-180 text-ink' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="activeAccordion === 4" x-cloak class="px-4 pb-4 text-xs text-mute leading-relaxed border-t border-hairline-soft/60 pt-3">
                        Kami memberikan garansi 100% ganti baru jika terdapat cacat produksi pada kain atau kesalahan cetak nama/nomor yang disebabkan oleh tim kami (wajib menyertakan video unboxing).
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
