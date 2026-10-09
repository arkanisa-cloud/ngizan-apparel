@extends('layouts.admin')

@section('title', 'Profil Administrator · NGIZAN APPAREL')

@section('content')
    <div class="space-y-6 max-w-5xl">

        {{-- Breadcrumb & Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs text-mute font-medium mb-1">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-ink transition">Dashboard</a>
                    <span>/</span>
                    <span class="text-ink font-semibold">Pengaturan Profil</span>
                </div>
                <h1 class="font-display font-medium text-2xl tracking-wide uppercase text-ink">Profil & Keamanan Akun</h1>
                <p class="text-mute text-xs mt-0.5">Kelola informasi identitas administrator dan pembaruan kredensial
                    keamanan.</p>
            </div>

            <a href="{{ route('admin.dashboard') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-soft-cloud hover:bg-neutral-200 text-ink rounded-full text-xs font-semibold transition self-start sm:self-auto shadow-2xs">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Kembali ke Dashboard</span>
            </a>
        </div>

        {{-- Identity Overview Card --}}
        <div
            class="bg-white rounded-2xl border border-hairline-soft p-6 shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div
                    class="w-16 h-16 rounded-full bg-ink text-white font-display text-2xl font-bold flex items-center justify-center uppercase shadow-md shrink-0">
                    {{ substr($user->name ?? 'A', 0, 1) }}
                </div>
                <div class="space-y-1">
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h2 class="text-lg font-bold text-ink leading-tight">{{ $user->name }}</h2>
                    </div>
                    <p class="text-xs text-mute font-medium">{{ $user->email }}</p>
                    <p class="text-[11px] text-neutral-400">
                        Terdaftar sejak {{ $user->created_at ? $user->created_at->translatedFormat('d F Y') : '-' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Grid Form Sections --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Form 1: Informasi Profil --}}
            <div class="bg-white rounded-2xl border border-hairline-soft p-6 shadow-xs space-y-5">
                <div class="border-b border-hairline-soft pb-4">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full bg-soft-cloud flex items-center justify-center text-ink shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-sm text-ink">Informasi Administrator</h3>
                            <p class="text-mute text-[11px]">Perbarui nama dan alamat email login Anda.</p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('admin.profile.update') }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block font-semibold text-ink mb-1.5 uppercase tracking-wider text-[11px]">
                            Nama Lengkap <span class="text-sale">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                            class="w-full bg-soft-cloud focus:bg-white border border-hairline focus:border-ink rounded-xl px-3.5 py-2.5 text-xs text-ink transition focus:outline-none"
                            placeholder="Nama Administrator">
                        @error('name')
                            <p class="text-sale text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block font-semibold text-ink mb-1.5 uppercase tracking-wider text-[11px]">
                            Alamat Email <span class="text-sale">*</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                            class="w-full bg-soft-cloud focus:bg-white border border-hairline focus:border-ink rounded-xl px-3.5 py-2.5 text-xs text-ink transition focus:outline-none"
                            placeholder="admin@ngizan.com">
                        @error('email')
                            <p class="text-sale text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-mute text-[10px] mt-1">Email ini digunakan untuk masuk ke Backoffice NGIZAN APPAREL.
                        </p>
                    </div>

                    <div>
                        <label class="block font-semibold text-ink mb-1.5 uppercase tracking-wider text-[11px]">
                            Nomor Telepon / WhatsApp
                        </label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                            class="w-full bg-soft-cloud focus:bg-white border border-hairline focus:border-ink rounded-xl px-3.5 py-2.5 text-xs text-ink transition focus:outline-none"
                            placeholder="08xxxxxxxxxx">
                        @error('phone')
                            <p class="text-sale text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-3 flex justify-end">
                        <button type="submit"
                            class="px-6 py-2.5 bg-ink hover:opacity-90 text-white rounded-full text-xs font-semibold uppercase tracking-wider transition shadow-xs cursor-pointer inline-flex items-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Simpan Profil</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Form 2: Ubah Password --}}
            <div class="bg-white rounded-2xl border border-hairline-soft p-6 shadow-xs space-y-5">
                <div class="border-b border-hairline-soft pb-4">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full bg-soft-cloud flex items-center justify-center text-ink shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-sm text-ink">Keamanan & Sandi</h3>
                            <p class="text-mute text-[11px]">Pastikan akun menggunakan kata sandi yang kuat dan aman.</p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('admin.profile.password') }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block font-semibold text-ink mb-1.5 uppercase tracking-wider text-[11px]">
                            Kata Sandi Saat Ini <span class="text-sale">*</span>
                        </label>
                        <input type="password" name="current_password" required
                            class="w-full bg-soft-cloud focus:bg-white border border-hairline focus:border-ink rounded-xl px-3.5 py-2.5 text-xs text-ink transition focus:outline-none"
                            placeholder="••••••••">
                        @error('current_password')
                            <p class="text-sale text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block font-semibold text-ink mb-1.5 uppercase tracking-wider text-[11px]">
                            Kata Sandi Baru <span class="text-sale">*</span>
                        </label>
                        <input type="password" name="password" required
                            class="w-full bg-soft-cloud focus:bg-white border border-hairline focus:border-ink rounded-xl px-3.5 py-2.5 text-xs text-ink transition focus:outline-none"
                            placeholder="Minimal 8 karakter">
                        @error('password')
                            <p class="text-sale text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block font-semibold text-ink mb-1.5 uppercase tracking-wider text-[11px]">
                            Konfirmasi Kata Sandi Baru <span class="text-sale">*</span>
                        </label>
                        <input type="password" name="password_confirmation" required
                            class="w-full bg-soft-cloud focus:bg-white border border-hairline focus:border-ink rounded-xl px-3.5 py-2.5 text-xs text-ink transition focus:outline-none"
                            placeholder="Ulangi kata sandi baru">
                    </div>

                    <div class="p-3 bg-soft-cloud rounded-xl text-[11px] text-mute flex items-start gap-2">
                        <svg class="w-4 h-4 text-ink shrink-0 mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Gunakan kombinasi minimal 8 karakter huruf besar, kecil, angka, dan simbol untuk keamanan
                            maksimal.</span>
                    </div>

                    <div class="pt-3 flex justify-end">
                        <button type="submit"
                            class="px-6 py-2.5 bg-ink hover:opacity-90 text-white rounded-full text-xs font-semibold uppercase tracking-wider transition shadow-xs cursor-pointer inline-flex items-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                            <span>Perbarui Kata Sandi</span>
                        </button>
                    </div>
                </form>
            </div>

        </div>

    </div>
@endsection
