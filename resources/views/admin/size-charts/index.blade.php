@extends('layouts.admin')

@section('title', 'Master Panduan Ukuran (Size Charts)')
@section('header_title', 'Master Panduan Ukuran')

@section('content')
<div class="space-y-6">

    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-hairline-soft pb-5">
        <div>
            <span class="text-[11px] font-bold uppercase tracking-[0.2em] text-mute block mb-1">Master Data</span>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-ink">Master Panduan Ukuran</h1>
            <p class="text-xs text-mute mt-1">Kelola template ukuran untuk Jersey, Celana, Trackpants, Dewasa, Wanita, & Anak-anak.</p>
        </div>

        <a href="{{ route('admin.size-charts.create') }}" 
           class="px-5 py-2.5 bg-ink hover:opacity-90 text-white rounded-full text-xs font-medium transition flex items-center gap-1.5 self-start sm:self-auto cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Tambah Template Baru</span>
        </a>
    </div>

    {{-- Cards Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($sizeCharts as $chart)
            <div class="bg-white rounded-2xl border border-hairline-soft p-5 space-y-4 shadow-xs hover:border-hairline transition flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="flex items-center justify-between gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-soft-cloud text-ink border border-hairline-soft">
                            {{ match($chart->category_type) {
                                'tops' => 'Atasan / Jersey',
                                'bottoms' => 'Bawahan / Celana',
                                'outerwear' => 'Jaket / Luaran',
                                default => $chart->category_type
                            } }}
                        </span>

                        @if($chart->is_default)
                            <span class="px-2 py-0.5 bg-amber-50 text-amber-800 border border-amber-200/60 rounded-full text-[10px] font-bold">
                                ★ Default
                            </span>
                        @endif
                    </div>

                    <div>
                        <h3 class="font-bold text-ink text-base">{{ $chart->name }}</h3>
                        <p class="text-xs text-mute line-clamp-2 mt-1">{{ $chart->description ?: 'Tidak ada catatan tambahan.' }}</p>
                    </div>

                    {{-- Mini Preview Table --}}
                    <div class="bg-soft-cloud rounded-xl p-3 border border-hairline-soft overflow-x-auto text-[11px]">
                        <div class="flex items-center justify-between text-mute font-bold mb-1.5 pb-1 border-b border-hairline-soft">
                            <span>Ukuran Terdaftar ({{ count($chart->rows ?? []) }})</span>
                            <span class="text-[10px] font-normal text-ink font-semibold">
                                {{ implode(', ', array_column($chart->rows ?? [], 'size')) }}
                            </span>
                        </div>
                        <p class="text-[10px] text-mute truncate">
                            Kolom: {{ implode(' · ', $chart->columns ?? []) }}
                        </p>
                    </div>
                </div>

                <div class="pt-4 border-t border-hairline-soft flex items-center justify-between gap-2">
                    <span class="text-[11px] text-mute font-medium">
                        Dipakai oleh: <strong class="text-ink font-bold">{{ $chart->products_count }} Produk</strong>
                    </span>

                    <div class="flex items-center gap-1.5">
                        <a href="{{ route('admin.size-charts.edit', $chart->id) }}" 
                           class="px-3 py-1.5 bg-soft-cloud hover:bg-neutral-200 text-ink text-xs font-semibold rounded-full transition">
                            Edit
                        </a>

                        @if(!$chart->is_default)
                            <form action="{{ route('admin.size-charts.destroy', $chart->id) }}" method="POST"
                                  data-confirm-title="Hapus Panduan Ukuran?"
                                  data-confirm-text="Apakah Anda yakin ingin menghapus template '{{ $chart->name }}'?"
                                  data-confirm-btn="Ya, Hapus Template">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-mute hover:text-sale hover:bg-rose-50 rounded-full transition cursor-pointer" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center bg-white rounded-2xl border border-hairline-soft p-6">
                <p class="text-sm font-semibold text-ink">Belum ada template panduan ukuran.</p>
                <a href="{{ route('admin.size-charts.create') }}" class="mt-3 inline-block text-xs font-bold underline text-ink">Tambah Sekarang</a>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($sizeCharts->hasPages())
        <div class="pt-4">
            {{ $sizeCharts->links() }}
        </div>
    @endif

</div>
@endsection
