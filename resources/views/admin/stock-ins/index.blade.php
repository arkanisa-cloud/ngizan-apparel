@extends('layouts.admin')

@section('title', 'Mutasi Stok Masuk · NGIZAN APPAREL')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold font-display text-slate-900">Mutasi Stok Masuk (Restock Supplier)</h1>
            <p class="text-xs text-slate-500 mt-0.5">Catat pengadaan jersey baru dari supplier konveksi ke gudang fisik.</p>
        </div>
        <a href="{{ route('admin.stock-ins.create') }}" class="px-4 py-2.5 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
            <span>+ Catat Restock Masuk</span>
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full text-xs text-left text-slate-600">
            <thead class="bg-slate-50 uppercase font-bold text-slate-700 border-b border-slate-200">
                <tr>
                    <th class="p-3.5">Tanggal Diterima</th>
                    <th class="p-3.5">Jersey & Varian</th>
                    <th class="p-3.5">Supplier</th>
                    <th class="p-3.5">No. Invoice</th>
                    <th class="p-3.5">Harga Beli / Pcs</th>
                    <th class="p-3.5 text-right">Qty Masuk</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($stockIns as $in)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="p-3.5 font-mono text-slate-900">
                            {{ $in->received_date->format('d/m/Y') }}
                        </td>
                        <td class="p-3.5">
                            <span class="font-bold text-slate-900 block">{{ $in->variant?->product?->name }}</span>
                            <span class="text-[10.5px] text-slate-500">Ukuran: <strong>{{ $in->variant?->size }}</strong> ({{ $in->variant?->type }})</span>
                        </td>
                        <td class="p-3.5 font-semibold text-slate-700">
                            {{ $in->supplier?->name ?? 'Supplier Direct' }}
                        </td>
                        <td class="p-3.5 font-mono text-slate-600">
                            {{ $in->invoice_number ?: '-' }}
                        </td>
                        <td class="p-3.5 font-semibold text-slate-900 tabular-nums">
                            Rp {{ number_format($in->purchase_price, 0, ',', '.') }}
                        </td>
                        <td class="p-3.5 text-right font-display font-black text-sm text-emerald-600">
                            +{{ $in->quantity }} pcs
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-400">Belum ada catatan mutasi stok masuk.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4 border-t border-slate-100">
            {{ $stockIns->links() }}
        </div>
    </div>
</div>
@endsection
