@extends('layouts.admin')

@section('title', 'Dashboard Overview · NGIZAN APPAREL')

@section('content')
<div class="space-y-8">
    
    {{-- Header Title --}}
    <div class="border-b border-hairline-soft pb-5">
        <span class="text-xs font-medium uppercase tracking-widest text-mute block mb-1">Executive Overview</span>
        <h1 class="text-2xl sm:text-3xl font-medium tracking-tight text-ink">Executive Dashboard</h1>
        <p class="text-xs text-mute mt-1">Ringkasan performa operasional toko online, status stok gudang, dan logistik.</p>
    </div>

    {{-- ===== 1. STATISTIC CARDS ===== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        {{-- Card 1: Omset Lunas --}}
        <div class="bg-soft-cloud p-5 rounded-2xl border border-hairline-soft space-y-2">
            <div class="flex items-center justify-between text-mute">
                <span class="text-[11px] font-medium uppercase tracking-wider">Omset Bulan Ini</span>
                <span class="px-2.5 py-0.5 bg-white border border-hairline text-ink rounded-full text-[10px] font-medium">💰 Lunas</span>
            </div>
            <div class="text-2xl font-medium text-ink tabular-nums">
                Rp {{ number_format($monthlyRevenue, 0, ',', '.') }}
            </div>
            <p class="text-[11px] text-mute">Dari pesanan terverifikasi Midtrans</p>
        </div>

        {{-- Card 2: Pesanan Butuh Diproses --}}
        <div class="bg-soft-cloud p-5 rounded-2xl border border-hairline-soft space-y-2">
            <div class="flex items-center justify-between text-mute">
                <span class="text-[11px] font-medium uppercase tracking-wider">Perlu Diproses</span>
                <span class="px-2.5 py-0.5 bg-white border border-hairline text-ink rounded-full text-[10px] font-medium">⚡ Antrian</span>
            </div>
            <div class="text-2xl font-medium text-ink tabular-nums">
                {{ $actionRequiredOrders }} <span class="text-xs font-normal text-mute">Pesanan</span>
            </div>
            <p class="text-[11px] text-mute">Status Lunas & Masuk Produksi</p>
        </div>

        {{-- Card 3: Total Stok Gudang --}}
        <div class="bg-soft-cloud p-5 rounded-2xl border border-hairline-soft space-y-2">
            <div class="flex items-center justify-between text-mute">
                <span class="text-[11px] font-medium uppercase tracking-wider">Stok Jersey Gudang</span>
                <span class="px-2.5 py-0.5 bg-white border border-hairline text-ink rounded-full text-[10px] font-medium">📦 Fisik</span>
            </div>
            <div class="text-2xl font-medium text-ink tabular-nums">
                {{ number_format($totalStockWarehouse, 0, ',', '.') }} <span class="text-xs font-normal text-mute">Pcs</span>
            </div>
            <p class="text-[11px] text-mute">Akumulasi seluruh varian ukuran</p>
        </div>

        {{-- Card 4: Total Koleksi Jersey --}}
        <div class="bg-soft-cloud p-5 rounded-2xl border border-hairline-soft space-y-2">
            <div class="flex items-center justify-between text-mute">
                <span class="text-[11px] font-medium uppercase tracking-wider">Koleksi Kit & Edisi</span>
                <span class="px-2.5 py-0.5 bg-white border border-hairline text-ink rounded-full text-[10px] font-medium">👕 Master</span>
            </div>
            <div class="text-2xl font-medium text-ink tabular-nums">
                {{ $totalProductsCount }} <span class="text-xs font-normal text-mute">Kit ({{ $totalCategoriesCount }} Kategori)</span>
            </div>
            <p class="text-[11px] text-mute">Edisi Klub, Timnas, dan Retro</p>
        </div>

    </div>

    {{-- ===== 2. CHARTS & LOW STOCK ALERT (2 COLS) ===== --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        {{-- Chart Section (8 Cols) --}}
        <div class="lg:col-span-8 bg-white p-6 rounded-2xl border border-hairline-soft space-y-4">
            <div class="flex justify-between items-center border-b border-hairline-soft pb-4">
                <div>
                    <h2 class="font-medium text-sm text-ink">Tren Penjualan 7 Hari Terakhir</h2>
                    <p class="text-xs text-mute">Grafik omset harian pesanan terverifikasi</p>
                </div>
                <span class="text-xs font-medium text-ink bg-soft-cloud border border-hairline px-3 py-1 rounded-full">Midtrans Verified</span>
            </div>

            <div class="h-64 relative">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        {{-- Low Stock Warning (4 Cols) --}}
        <div class="lg:col-span-4 bg-white p-6 rounded-2xl border border-hairline-soft space-y-4">
            <div class="flex justify-between items-center border-b border-hairline-soft pb-3">
                <h2 class="font-medium text-sm text-ink flex items-center gap-1.5">
                    <span>⚠️ Stok Menipis (&le; 3 pcs)</span>
                </h2>
                <a href="{{ route('admin.stock-ins.create') }}" class="text-xs font-medium text-ink underline">+ Restock</a>
            </div>

            @if($lowStockVariants->isEmpty())
                <div class="py-8 text-center text-xs text-mute">
                    Semua stok varian jersey aman di atas 3 pcs.
                </div>
            @else
                <div class="space-y-2.5">
                    @foreach($lowStockVariants as $variant)
                        <div class="flex items-center justify-between p-3 bg-soft-cloud rounded-xl text-xs">
                            <div class="space-y-0.5">
                                <span class="font-medium text-ink block line-clamp-1">{{ $variant->product?->name }}</span>
                                <span class="text-[11px] text-mute">Ukuran: <strong>{{ $variant->size }}</strong> ({{ $variant->type }})</span>
                            </div>
                            <div class="text-right">
                                <span class="px-2.5 py-0.5 bg-sale text-white rounded-full text-[10px] font-medium font-jersey">
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
    <div class="bg-white rounded-2xl border border-hairline-soft overflow-hidden space-y-4 p-6">
        <div class="flex justify-between items-center border-b border-hairline-soft pb-4">
            <div>
                <h2 class="font-medium text-sm text-ink">Pesanan Masuk Terbaru</h2>
                <p class="text-xs text-mute">Pantau transaksi dan proses pengiriman kurir</p>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="text-xs font-medium text-ink hover:text-mute underline">
                Lihat Semua Pesanan &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-ink">
                <thead class="bg-soft-cloud font-medium text-mute border-b border-hairline-soft">
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
                <tbody class="divide-y divide-hairline-soft">
                    @forelse($recentOrders as $order)
                        <tr class="hover:bg-soft-cloud transition">
                            <td class="p-3 font-mono font-medium text-ink">
                                #{{ $order->order_number }}
                                <div class="text-[10px] text-mute font-sans">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                            </td>
                            <td class="p-3">
                                <span class="font-medium text-ink block">{{ $order->customer_name }}</span>
                                <span class="text-[10px] text-mute">{{ $order->customer_phone }}</span>
                            </td>
                            <td class="p-3">
                                @foreach($order->items->take(2) as $item)
                                    <div class="line-clamp-1 font-medium text-ink">
                                        {{ $item->product_name }} ({{ $item->size }}) &times; {{ $item->quantity }}
                                    </div>
                                @endforeach
                                @if($order->items->count() > 2)
                                    <span class="text-[10px] text-mute">+{{ $order->items->count() - 2 }} item lainnya</span>
                                @endif
                            </td>
                            <td class="p-3 uppercase font-medium text-mute">
                                {{ $order->courier_code }} - {{ $order->courier_service_code }}
                            </td>
                            <td class="p-3 font-medium text-ink tabular-nums">
                                {{ $order->formatted_grand_total }}
                            </td>
                            <td class="p-3">
                                <span class="text-[10px] font-medium px-2.5 py-0.5 rounded-full {{ $order->status->badgeClass() }}">
                                    {{ $order->status->label() }}
                                </span>
                            </td>
                            <td class="p-3 text-right">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="btn-primary py-1.5 px-3.5 text-[11px] rounded-full inline-block">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-6 text-center text-mute">Belum ada pesanan masuk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@push('scripts')
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
                        borderColor: '#111111',
                        backgroundColor: 'rgba(17, 17, 17, 0.05)',
                        fill: true,
                        tension: 0.3,
                        borderWidth: 2,
                        pointBackgroundColor: '#111111',
                        pointRadius: 3
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
                                font: { family: 'Inter', size: 10 }
                            }
                        },
                        x: {
                            ticks: { font: { family: 'Inter', size: 10 } }
                        }
                    }
                }
            });
        });
    </script>
@endpush

@endsection
