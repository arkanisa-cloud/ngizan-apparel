@extends('layouts.admin')

@section('title', 'Dashboard Overview · NGIZAN APPAREL')

@section('content')
<div class="space-y-8">
    
    {{-- Header Title --}}
    <div>
        <h1 class="text-2xl font-bold font-display text-slate-900 tracking-tight">Executive Dashboard</h1>
        <p class="text-xs text-slate-500 mt-1">Ringkasan performa operasional toko online, status stok gudang, dan logistik.</p>
    </div>

    {{-- ===== 1. STATISTIC CARDS ===== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        {{-- Card 1: Omset Lunas --}}
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm space-y-2">
            <div class="flex items-center justify-between text-slate-500">
                <span class="text-[11px] font-bold uppercase tracking-wider">Omset Bulan Ini</span>
                <span class="p-2 bg-emerald-50 text-emerald-600 rounded-lg text-xs font-bold">💰 Lunas</span>
            </div>
            <div class="font-display font-black text-2xl text-slate-900">
                Rp {{ number_format($monthlyRevenue, 0, ',', '.') }}
            </div>
            <p class="text-[11px] text-slate-400">Dari pesanan terverifikasi Midtrans</p>
        </div>

        {{-- Card 2: Pesanan Butuh Diproses --}}
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm space-y-2">
            <div class="flex items-center justify-between text-slate-500">
                <span class="text-[11px] font-bold uppercase tracking-wider">Perlu Diproses</span>
                <span class="p-2 bg-cyan-50 text-cyan-600 rounded-lg text-xs font-bold">⚡ Antrian</span>
            </div>
            <div class="font-display font-black text-2xl text-cyan-700">
                {{ $actionRequiredOrders }} <span class="text-sm font-semibold text-slate-400">Pesanan</span>
            </div>
            <p class="text-[11px] text-slate-400">Status Lunas & Masuk Produksi</p>
        </div>

        {{-- Card 3: Total Stok Gudang --}}
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm space-y-2">
            <div class="flex items-center justify-between text-slate-500">
                <span class="text-[11px] font-bold uppercase tracking-wider">Stok Jersey Gudang</span>
                <span class="p-2 bg-indigo-50 text-indigo-600 rounded-lg text-xs font-bold">📦 Fisik</span>
            </div>
            <div class="font-display font-black text-2xl text-slate-900">
                {{ number_format($totalStockWarehouse, 0, ',', '.') }} <span class="text-sm font-semibold text-slate-400">Pcs</span>
            </div>
            <p class="text-[11px] text-slate-400">Akumulasi seluruh varian ukuran</p>
        </div>

        {{-- Card 4: Total Koleksi Jersey --}}
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm space-y-2">
            <div class="flex items-center justify-between text-slate-500">
                <span class="text-[11px] font-bold uppercase tracking-wider">Koleksi Kit & Edisi</span>
                <span class="p-2 bg-amber-50 text-amber-600 rounded-lg text-xs font-bold">👕 Master</span>
            </div>
            <div class="font-display font-black text-2xl text-slate-900">
                {{ $totalProductsCount }} <span class="text-sm font-semibold text-slate-400">Kit ({{ $totalCategoriesCount }} Kategori)</span>
            </div>
            <p class="text-[11px] text-slate-400">Edisi Klub, Timnas, dan Retro</p>
        </div>

    </div>

    {{-- ===== 2. CHARTS & LOW STOCK ALERT (2 COLS) ===== --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        {{-- Chart Section (8 Cols) --}}
        <div class="lg:col-span-8 bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
            <div class="flex justify-between items-center border-b border-slate-100 pb-4">
                <div>
                    <h2 class="font-bold text-sm text-slate-900">Tren Penjualan 7 Hari Terakhir</h2>
                    <p class="text-xs text-slate-400">Grafik omset harian pesanan lunas</p>
                </div>
                <span class="text-xs font-bold text-cyan-600">Midtrans Verified</span>
            </div>

            <div class="h-64 relative">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        {{-- Low Stock Warning (4 Cols) --}}
        <div class="lg:col-span-4 bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                <h2 class="font-bold text-sm text-slate-900 flex items-center gap-1.5">
                    <span>⚠️ Stok Menipis (&le; 3 pcs)</span>
                </h2>
                <a href="{{ route('admin.stock-ins.create') }}" class="text-[11px] font-bold text-cyan-600 hover:underline">+ Restock</a>
            </div>

            @if($lowStockVariants->isEmpty())
                <div class="py-8 text-center text-xs text-slate-400">
                    Semua stok varian jersey aman di atas 3 pcs.
                </div>
            @else
                <div class="space-y-3">
                    @foreach($lowStockVariants as $variant)
                        <div class="flex items-center justify-between p-2.5 bg-amber-50/60 border border-amber-100 rounded-lg text-xs">
                            <div class="space-y-0.5">
                                <span class="font-bold text-slate-900 block line-clamp-1">{{ $variant->product?->name }}</span>
                                <span class="text-[10.5px] text-slate-500">Ukuran: <strong>{{ $variant->size }}</strong> ({{ $variant->type }})</span>
                            </div>
                            <div class="text-right">
                                <span class="px-2 py-0.5 bg-rose-500 text-white rounded text-[10px] font-bold font-jersey">
                                    Sisa {{ $variant->stock }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    {{-- ===== 3. RECENT ORDERS TABLE ===== --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden space-y-4 p-6">
        <div class="flex justify-between items-center border-b border-slate-100 pb-4">
            <div>
                <h2 class="font-bold text-sm text-slate-900">Pesanan Masuk Terbaru</h2>
                <p class="text-xs text-slate-400">Pantau transaksi dan proses pengiriman kurir Biteship</p>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-cyan-600 hover:underline">
                Lihat Semua Pesanan &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-600">
                <thead class="bg-slate-50 uppercase font-bold text-slate-700 border-b border-slate-200">
                    <tr>
                        <th class="p-3">No. Pesanan</th>
                        <th class="p-3">Pelanggan</th>
                        <th class="p-3">Jersey & Varian</th>
                        <th class="p-3">Kurir</th>
                        <th class="p-3">Total</th>
                        <th class="p-3">Status</th>
                        <th class="p-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentOrders as $order)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-3 font-mono font-bold text-slate-900">
                                #{{ $order->order_number }}
                                <div class="text-[10px] text-slate-400 font-sans">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                            </td>
                            <td class="p-3">
                                <span class="font-semibold text-slate-900 block">{{ $order->customer_name }}</span>
                                <span class="text-[10px] text-slate-400">{{ $order->customer_phone }}</span>
                            </td>
                            <td class="p-3">
                                @foreach($order->items->take(2) as $item)
                                    <div class="line-clamp-1 font-semibold text-slate-800">
                                        {{ $item->product_name }} ({{ $item->size }}) &times; {{ $item->quantity }}
                                    </div>
                                @endforeach
                                @if($order->items->count() > 2)
                                    <span class="text-[10px] text-slate-400">+{{ $order->items->count() - 2 }} item lainnya</span>
                                @endif
                            </td>
                            <td class="p-3 uppercase font-semibold text-slate-700">
                                {{ $order->courier_code }} - {{ $order->courier_service_code }}
                            </td>
                            <td class="p-3 font-bold text-slate-900 tabular-nums">
                                {{ $order->formatted_grand_total }}
                            </td>
                            <td class="p-3">
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded {{ $order->status->badgeClass() }}">
                                    {{ $order->status->label() }}
                                </span>
                            </td>
                            <td class="p-3 text-right">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="px-3 py-1 bg-slate-900 hover:bg-slate-800 text-white rounded text-[11px] font-bold transition inline-block">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-6 text-center text-slate-400">Belum ada pesanan masuk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@push('scripts')
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('salesChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [{
                        label: 'Omset Penjualan (Rp)',
                        data: {!! json_encode($chartData) !!},
                        borderColor: '#0284C7',
                        backgroundColor: 'rgba(2, 132, 199, 0.08)',
                        fill: true,
                        tension: 0.3,
                        borderWidth: 2.5,
                        pointBackgroundColor: '#0284C7',
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                },
                                font: { size: 10 }
                            }
                        },
                        x: {
                            ticks: { font: { size: 10 } }
                        }
                    }
                }
            });
        });
    </script>
@endpush

@endsection
