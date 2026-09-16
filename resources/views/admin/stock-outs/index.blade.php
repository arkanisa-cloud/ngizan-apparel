@extends('layouts.admin')

@section('title', 'Mutasi Stok Keluar · NGIZAN APPAREL')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-hairline-soft pb-5">
        <div>
            <h1 class="text-2xl font-medium tracking-tight text-ink">Mutasi Stok Keluar</h1>
            <p class="text-xs text-mute mt-1">Catat pengurangan stok akibat jersey rusak (cacat sablon), display sampel, atau promosi influencer.</p>
        </div>
        <a href="{{ route('admin.stock-outs.create') }}" class="px-5 py-2.5 bg-ink hover:opacity-90 text-white rounded-full text-xs font-medium transition flex items-center gap-1.5 self-start sm:self-auto cursor-pointer">
            <span>+ Catat Stok Keluar</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-hairline-soft overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-ink">
                <thead class="bg-soft-cloud uppercase font-medium text-mute border-b border-hairline-soft">
                    <tr>
                        <th class="p-4">Tanggal Keluar</th>
                        <th class="p-4">Jersey & Varian</th>
                        <th class="p-4">Alasan Pengurangan</th>
                        <th class="p-4">Catatan</th>
                        <th class="p-4 text-right">Qty Keluar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-hairline-soft">
                    @forelse($stockOuts as $out)
                        <tr class="hover:bg-soft-cloud/50 transition">
                            <td class="p-4 font-mono text-ink">
                                {{ $out->out_date->format('d/m/Y') }}
                            </td>
                            <td class="p-4">
                                <span class="font-medium text-ink block">{{ $out->variant?->product?->name }}</span>
                                <span class="text-[11px] text-mute">Ukuran: <strong class="text-ink">{{ $out->variant?->size }}</strong> ({{ $out->variant?->type }})</span>
                            </td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-medium bg-soft-cloud text-ink border border-hairline uppercase">
                                    {{ $out->reason }}
                                </span>
                            </td>
                            <td class="p-4 text-mute">
                                {{ $out->notes ?: '-' }}
                            </td>
                            <td class="p-4 text-right font-medium text-sm text-sale">
                                -{{ $out->quantity }} pcs
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-mute">Belum ada catatan mutasi stok keluar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-hairline-soft">
            {{ $stockOuts->links() }}
        </div>
    </div>
</div>
@endsection
