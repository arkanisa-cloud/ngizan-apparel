@extends('layouts.admin')

@section('title', 'Mutasi Stok Keluar · NGIZAN APPAREL')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold font-display text-slate-900">Mutasi Stok Keluar (Penyesuaian Non-Penjualan)</h1>
            <p class="text-xs text-slate-500 mt-0.5">Catat pengurangan stok akibat jersey rusak (cacat sablon), display sampel, atau promosi influencer.</p>
        </div>
        <a href="{{ route('admin.stock-outs.create') }}" class="px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
            <span>+ Catat Stok Keluar</span>
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full text-xs text-left text-slate-600">
            <thead class="bg-slate-50 uppercase font-bold text-slate-700 border-b border-slate-200">
                <tr>
                    <th class="p-3.5">Tanggal Keluar</th>
                    <th class="p-3.5">Jersey & Varian</th>
                    <th class="p-3.5">Alasan Pengurangan</th>
                    <th class="p-3.5">Catatan</th>
                    <th class="p-3.5 text-right">Qty Keluar</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($stockOuts as $out)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="p-3.5 font-mono text-slate-900">
                            {{ $out->out_date->format('d/m/Y') }}
                        </td>
                        <td class="p-3.5">
                            <span class="font-bold text-slate-900 block">{{ $out->variant?->product?->name }}</span>
                            <span class="text-[10.5px] text-slate-500">Ukuran: <strong>{{ $out->variant?->size }}</strong> ({{ $out->variant?->type }})</span>
                        </td>
                        <td class="p-3.5">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-800 uppercase">
                                {{ $out->reason }}
                            </span>
                        </td>
                        <td class="p-3.5 text-slate-500">
                            {{ $out->notes ?: '-' }}
                        </td>
                        <td class="p-3.5 text-right font-display font-black text-sm text-rose-600">
                            -{{ $out->quantity }} pcs
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-slate-400">Belum ada catatan mutasi stok keluar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4 border-t border-slate-100">
            {{ $stockOuts->links() }}
        </div>
    </div>
</div>
@endsection
