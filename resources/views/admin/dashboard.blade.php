@extends('layouts.admin')

@section('title', 'Executive Dashboard · NGIZAN APPAREL')

@section('content')
    <div class="space-y-6">

        {{-- Header Section --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-hairline-soft pb-5">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-[0.2em] text-mute block mb-1">Executive Overview</span>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-ink">Dashboard Operasional</h1>
                <p class="text-xs text-mute mt-1">Ringkasan performa finansial toko, aktivitas pesanan, dan status inventori
                    gudang.</p>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('admin.stock-ins.create') }}"
                    class="px-5 py-2.5 bg-ink hover:opacity-90 text-white rounded-full text-xs font-medium transition flex items-center gap-1.5 self-start sm:self-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Restock Masuk</span>
                </a>
                <a href="{{ route('admin.orders.index') }}"
                    class="px-5 py-2.5 bg-soft-cloud hover:bg-neutral-200 text-ink rounded-full text-xs font-medium transition flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span>Kelola Pesanan</span>
                </a>
            </div>
        </div>

        {{-- ===== 1. STATISTIC CARDS ===== --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">

            {{-- Card 1: Omset Lunas --}}
            <div
                class="bg-white p-5 sm:p-6 rounded-2xl border border-hairline-soft hover:border-ink/20 transition-all flex flex-col justify-between space-y-4">
                <div class="flex items-center justify-between">
                    <div class="w-10 h-10 rounded-full bg-soft-cloud flex items-center justify-center text-ink">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span
                        class="px-2.5 py-1 bg-soft-cloud text-ink rounded-full text-[10px] font-bold uppercase tracking-wider border border-hairline-soft">
                        Lunas
                    </span>
                </div>
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-mute block mb-1">Omset Bulan Ini</span>
                    <div class="text-2xl sm:text-3xl font-extrabold text-ink tabular-nums tracking-tight">
                        Rp {{ number_format($monthlyRevenue, 0, ',', '.') }}
                    </div>
                    <p class="text-[11px] text-mute mt-1.5 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>
                        <span>{{ $totalOrdersThisMonth }} transaksi terverifikasi</span>
                    </p>
                </div>
            </div>

            {{-- Card 2: Pesanan Butuh Diproses --}}
            <div
                class="bg-white p-5 sm:p-6 rounded-2xl border border-hairline-soft hover:border-ink/20 transition-all flex flex-col justify-between space-y-4">
                <div class="flex items-center justify-between">
                    <div class="w-10 h-10 rounded-full bg-soft-cloud flex items-center justify-center text-ink">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span
                        class="px-2.5 py-1 bg-amber-50 text-amber-900 rounded-full text-[10px] font-bold uppercase tracking-wider border border-amber-200">
                        Antrian Aktif
                    </span>
                </div>
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-mute block mb-1">Perlu Diproses</span>
                    <div class="text-2xl sm:text-3xl font-extrabold text-ink tabular-nums tracking-tight">
                        {{ $actionRequiredOrders }} <span
                            class="text-sm font-semibold text-mute tracking-normal">Pesanan</span>
                    </div>
                    <p class="text-[11px] text-mute mt-1.5">Menunggu sablon atau kirim resi</p>
                </div>
            </div>

            {{-- Card 3: Total Stok Gudang --}}
            <div
                class="bg-white p-5 sm:p-6 rounded-2xl border border-hairline-soft hover:border-ink/20 transition-all flex flex-col justify-between space-y-4">
                <div class="flex items-center justify-between">
                    <div class="w-10 h-10 rounded-full bg-soft-cloud flex items-center justify-center text-ink">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <span
                        class="px-2.5 py-1 bg-soft-cloud text-ink rounded-full text-[10px] font-bold uppercase tracking-wider border border-hairline-soft">
                        Fisik Gudang
                    </span>
                </div>
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-mute block mb-1">Stok Jersey
                        Tersedia</span>
                    <div class="text-2xl sm:text-3xl font-extrabold text-ink tabular-nums tracking-tight">
                        {{ number_format($totalStockWarehouse, 0, ',', '.') }} <span
                            class="text-sm font-semibold text-mute tracking-normal">Pcs</span>
                    </div>
                    <p class="text-[11px] text-mute mt-1.5">Akumulasi seluruh varian & size</p>
                </div>
            </div>

            {{-- Card 4: Total Koleksi Jersey --}}
            <div
                class="bg-white p-5 sm:p-6 rounded-2xl border border-hairline-soft hover:border-ink/20 transition-all flex flex-col justify-between space-y-4">
                <div class="flex items-center justify-between">
                    <div class="w-10 h-10 rounded-full bg-soft-cloud flex items-center justify-center text-ink">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </div>
                    <span
                        class="px-2.5 py-1 bg-soft-cloud text-ink rounded-full text-[10px] font-bold uppercase tracking-wider border border-hairline-soft">
                        Master Katalog
                    </span>
                </div>
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-mute block mb-1">Koleksi Kit &
                        Edisi</span>
                    <div class="text-2xl sm:text-3xl font-extrabold text-ink tabular-nums tracking-tight">
                        {{ $totalProductsCount }} <span class="text-sm font-semibold text-mute tracking-normal">Kit</span>
                    </div>
                    <p class="text-[11px] text-mute mt-1.5">Tersebar di {{ $totalCategoriesCount }} kategori edisi</p>
                </div>
            </div>

        </div>

        {{-- ===== 2. CHARTS & LOW STOCK ALERT (2 COLS) ===== --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8 items-start">

            {{-- Chart Section (8 Cols) --}}
            <div class="lg:col-span-8 bg-white p-6 sm:p-7 rounded-2xl border border-hairline-soft space-y-5">
                <div
                    class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-hairline-soft pb-4">
                    <div>
                        <h2 class="font-bold text-sm uppercase tracking-wider text-ink">Tren Penjualan 7 Hari Terakhir</h2>
                        <p class="text-xs text-mute mt-0.5">Grafik omset harian dari pesanan berstatus terverifikasi</p>
                    </div>
                </div>

                <div class="h-64 relative">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            {{-- Low Stock Warning (4 Cols) --}}
            <div class="lg:col-span-4 bg-white p-6 sm:p-7 rounded-2xl border border-hairline-soft space-y-5">
                <div class="flex justify-between items-center border-b border-hairline-soft pb-4">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full bg-rose-50 text-sale flex items-center justify-center">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <h2 class="font-bold text-sm uppercase tracking-wider text-ink">Stok Menipis (&le; 3 pcs)</h2>
                    </div>
                    <a href="{{ route('admin.stock-ins.create') }}"
                        class="text-xs font-bold text-ink hover:underline uppercase tracking-wider">
                        + Restock
                    </a>
                </div>

                @if ($lowStockVariants->isEmpty())
                    <div class="py-10 text-center space-y-2">
                        <div
                            class="w-10 h-10 rounded-full bg-soft-cloud mx-auto flex items-center justify-center text-mute">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <p class="text-xs text-mute font-medium">Seluruh stok varian jersey dalam kondisi aman.</p>
                    </div>
                @else
                    <div class="space-y-2.5">
                        @foreach ($lowStockVariants as $variant)
                            <div
                                class="flex items-center justify-between p-3 bg-soft-cloud rounded-xl text-xs hover:bg-neutral-100 transition">
                                <div class="space-y-0.5 pr-3 min-w-0">
                                    <span
                                        class="font-semibold text-ink block truncate">{{ $variant->product?->name }}</span>
                                    <span class="text-[11px] text-mute">Ukuran <strong>{{ $variant->size }}</strong>
                                        &bull; {{ $variant->type }}</span>
                                </div>
                                <div class="shrink-0">
                                    <span
                                        class="px-2.5 py-0.5 bg-sale text-white rounded-full text-[10px] font-bold uppercase tracking-wider">
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
        <div class="bg-white rounded-2xl border border-hairline-soft overflow-hidden p-6 sm:p-7 space-y-5">
            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-hairline-soft pb-4">
                <div>
                    <h2 class="font-bold text-sm uppercase tracking-wider text-ink">Pesanan Masuk Terbaru</h2>
                    <p class="text-xs text-mute mt-0.5">Pantau transaksi dan proses logistik secara real-time</p>
                </div>
                <a href="{{ route('admin.orders.index') }}"
                    class="text-xs font-bold text-ink hover:text-mute uppercase tracking-wider inline-flex items-center gap-1.5">
                    <span>Lihat Semua Pesanan</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
            </div>

            <div class="overflow-x-auto -mx-6 sm:-mx-7 px-6 sm:px-7">
                <table class="w-full text-xs text-left text-ink whitespace-nowrap">
                    <thead
                        class="bg-soft-cloud/70 text-mute uppercase text-[10px] font-bold tracking-wider border-b border-hairline-soft">
                        <tr>
                            <th class="py-3.5 px-4 rounded-l-lg">No. Pesanan</th>
                            <th class="py-3.5 px-4">Pelanggan</th>
                            <th class="py-3.5 px-4">Jersey & Varian</th>
                            <th class="py-3.5 px-4">Kurir</th>
                            <th class="py-3.5 px-4">Total</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4 text-right rounded-r-lg">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-hairline-soft">
                        @forelse($recentOrders as $order)
                            <tr class="hover:bg-soft-cloud/50 transition">
                                <td class="py-3.5 px-4 font-mono font-bold text-ink">
                                    #{{ $order->order_number }}
                                    <div class="text-[10px] text-mute font-sans font-normal">
                                        {{ $order->created_at->format('d M Y, H:i') }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="font-semibold text-ink block">{{ $order->customer_name }}</span>
                                    <span
                                        class="text-[11px] text-mute font-mono">{{ $order->customer_phone ?? '-' }}</span>
                                </td>
                                <td class="py-3.5 px-4">
                                    @foreach ($order->items->take(2) as $item)
                                        <div class="truncate max-w-xs font-medium text-ink">
                                            {{ $item->product_name }} <span
                                                class="text-mute">({{ $item->size }})</span> &times;
                                            {{ $item->quantity }}
                                        </div>
                                    @endforeach
                                    @if ($order->items->count() > 2)
                                        <span class="text-[10px] text-mute font-medium">+{{ $order->items->count() - 2 }}
                                            item lainnya</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded bg-soft-cloud text-[11px] font-semibold text-ink uppercase tracking-wider">
                                        {{ $order->courier_code }} - {{ $order->courier_service_code }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 font-bold text-ink tabular-nums">
                                    {{ $order->formatted_grand_total }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <span
                                        class="text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full {{ $order->status->badgeClass() }}">
                                        {{ $order->status->label() }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <a href="{{ route('admin.orders.show', $order->id) }}"
                                        class="px-3.5 py-1.5 bg-soft-cloud hover:bg-neutral-200 text-ink rounded-full text-xs font-medium transition inline-block">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-mute font-medium">Belum ada pesanan masuk.
                                </td>
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
                const chartCanvas = document.getElementById('salesChart');
                if (!chartCanvas) return;

                const ctx = chartCanvas.getContext('2d');

                // Create sleek gradient fill
                const gradient = ctx.createLinearGradient(0, 0, 0, 260);
                gradient.addColorStop(0, 'rgba(17, 17, 17, 0.12)');
                gradient.addColorStop(1, 'rgba(17, 17, 17, 0.00)');

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($chartLabels) !!},
                        datasets: [{
                            label: 'Omset Penjualan (Rp)',
                            data: {!! json_encode($chartData) !!},
                            borderColor: '#111111',
                            backgroundColor: gradient,
                            fill: true,
                            tension: 0.35,
                            borderWidth: 2,
                            pointBackgroundColor: '#111111',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: '#111111',
                                titleFont: {
                                    family: 'Inter',
                                    size: 11,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    family: 'Inter',
                                    size: 12
                                },
                                padding: 10,
                                cornerRadius: 8,
                                displayColors: false,
                                callbacks: {
                                    label: function(context) {
                                        return 'Rp ' + Number(context.parsed.y).toLocaleString('id-ID');
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)',
                                    drawBorder: false
                                },
                                ticks: {
                                    callback: function(value) {
                                        if (value >= 1000000) {
                                            return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                                        } else if (value >= 1000) {
                                            return 'Rp ' + (value / 1000).toFixed(0) + 'k';
                                        }
                                        return 'Rp ' + value.toLocaleString('id-ID');
                                    },
                                    font: {
                                        family: 'Inter',
                                        size: 10
                                    },
                                    color: '#707072'
                                }
                            },
                            x: {
                                grid: {
                                    display: false,
                                    drawBorder: false
                                },
                                ticks: {
                                    font: {
                                        family: 'Inter',
                                        size: 10,
                                        weight: '500'
                                    },
                                    color: '#707072'
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endpush
@endsection
