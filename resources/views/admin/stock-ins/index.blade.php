@extends('layouts.admin')

@section('title', 'Mutasi Stok Masuk · NGIZAN APPAREL')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-hairline-soft pb-5">
        <div>
            <span class="text-[11px] font-bold uppercase tracking-[0.2em] text-mute block mb-1">Gudang & Inventori</span>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-ink">Mutasi Stok Masuk</h1>
            <p class="text-xs text-mute mt-1">Catat pengadaan jersey baru ke gudang fisik.</p>
        </div>
        <a href="{{ route('admin.stock-ins.create') }}" class="px-5 py-2.5 bg-ink hover:opacity-90 text-white rounded-full text-xs font-medium transition flex items-center gap-1.5 self-start sm:self-auto cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Catat Restock Masuk</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-hairline-soft overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-ink">
                <thead class="bg-soft-cloud uppercase font-medium text-mute border-b border-hairline-soft">
                    <tr>
                        <th class="p-4">Tanggal Diterima</th>
                        <th class="p-4">Jersey & Varian</th>
                        <th class="p-4">No. Invoice</th>
                        <th class="p-4">Harga Beli / Pcs</th>
                        <th class="p-4 text-right">Qty Masuk</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-hairline-soft">
                    @forelse($stockIns as $in)
                        <tr class="hover:bg-soft-cloud/50 transition">
                            <td class="p-4 font-mono text-ink">
                                {{ $in->received_date->format('d/m/Y') }}
                            </td>
                            <td class="p-4">
                                <span class="font-medium text-ink block">{{ $in->variant?->product?->name }}</span>
                                <span class="text-[11px] text-mute">Ukuran: <strong class="text-ink">{{ $in->variant?->size }}</strong> ({{ $in->variant?->type }})</span>
                            </td>
                            <td class="p-4 font-mono text-mute">
                                {{ $in->invoice_number ?: '-' }}
                            </td>
                            <td class="p-4 font-medium text-ink tabular-nums">
                                Rp {{ number_format($in->purchase_price, 0, ',', '.') }}
                            </td>
                            <td class="p-4 text-right font-medium text-sm text-emerald-700">
                                +{{ $in->quantity }} pcs
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-mute">Belum ada catatan mutasi stok masuk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-hairline-soft">
            {{ $stockIns->links() }}
        </div>
    </div>
</div>
@endsection
