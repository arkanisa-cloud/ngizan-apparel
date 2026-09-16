<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipping Label #{{ $order->order_number }}</title>
    <style>
        @page {
            size: 100mm 150mm;
            margin: 0;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 8mm;
            font-size: 11px;
            color: #000;
            background: #fff;
            box-sizing: border-box;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #000;
            padding-bottom: 6px;
            margin-bottom: 8px;
        }
        .brand {
            font-size: 15px;
            font-weight: 900;
            letter-spacing: 2px;
        }
        .courier-badge {
            font-size: 14px;
            font-weight: bold;
            background: #000;
            color: #fff;
            padding: 4px 8px;
            text-transform: uppercase;
            border-radius: 2px;
        }
        .barcode-box {
            text-align: center;
            border: 1px dashed #000;
            padding: 6px;
            margin-bottom: 10px;
        }
        .barcode-text {
            font-family: monospace;
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 2px;
            margin-top: 4px;
        }
        .section {
            margin-bottom: 8px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 6px;
        }
        .lbl {
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            color: #555;
            margin-bottom: 2px;
        }
        .recipient-name {
            font-size: 13px;
            font-weight: bold;
        }
        .address-text {
            font-size: 11px;
            line-height: 1.35;
            margin-top: 2px;
        }
        .benchmark-box {
            margin-top: 4px;
            padding: 4px 6px;
            background: #f0f0f0;
            font-size: 10px;
            font-weight: bold;
            border-left: 3px solid #000;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-top: 4px;
        }
        .items-table th {
            text-align: left;
            border-bottom: 1px solid #000;
            padding: 3px 0;
            font-size: 9px;
            text-transform: uppercase;
        }
        .items-table td {
            padding: 4px 0;
            border-bottom: 1px dotted #ddd;
        }
        .nameset-tag {
            font-weight: bold;
            font-size: 9.5px;
            display: block;
        }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 15px; text-align: center;">
        <button onclick="window.print()" style="padding: 8px 16px; font-weight: bold; cursor: pointer;">🖨️ Cetak Ulang Label</button>
    </div>

    {{-- Header --}}
    <div class="header">
        <div>
            <div class="brand">NGIZAN APPAREL</div>
            <div style="font-size: 8.5px; color: #555;">Bespoke Football Kits & Archive Store</div>
        </div>
        <div class="courier-badge">
            {{ strtoupper($order->courier_code) }} {{ strtoupper($order->courier_service_code) }}
        </div>
    </div>

    {{-- Barcode / Resi Box --}}
    <div class="barcode-box">
        <svg style="height: 36px; width: 85%;" viewBox="0 0 100 24" preserveAspectRatio="none">
            <rect x="0" y="0" width="2" height="24" fill="#000"/>
            <rect x="4" y="0" width="3" height="24" fill="#000"/>
            <rect x="9" y="0" width="1" height="24" fill="#000"/>
            <rect x="12" y="0" width="4" height="24" fill="#000"/>
            <rect x="18" y="0" width="2" height="24" fill="#000"/>
            <rect x="22" y="0" width="5" height="24" fill="#000"/>
            <rect x="29" y="0" width="1" height="24" fill="#000"/>
            <rect x="32" y="0" width="3" height="24" fill="#000"/>
            <rect x="37" y="0" width="2" height="24" fill="#000"/>
            <rect x="41" y="0" width="4" height="24" fill="#000"/>
            <rect x="47" y="0" width="2" height="24" fill="#000"/>
            <rect x="51" y="0" width="5" height="24" fill="#000"/>
            <rect x="58" y="0" width="1" height="24" fill="#000"/>
            <rect x="61" y="0" width="3" height="24" fill="#000"/>
            <rect x="66" y="0" width="4" height="24" fill="#000"/>
            <rect x="72" y="0" width="2" height="24" fill="#000"/>
            <rect x="76" y="0" width="5" height="24" fill="#000"/>
            <rect x="83" y="0" width="1" height="24" fill="#000"/>
            <rect x="86" y="0" width="4" height="24" fill="#000"/>
            <rect x="92" y="0" width="2" height="24" fill="#000"/>
            <rect x="96" y="0" width="4" height="24" fill="#000"/>
        </svg>
        <div class="barcode-text">{{ $order->tracking_number ?: $order->order_number }}</div>
    </div>

    {{-- Info Penerima --}}
    @php
        $addr = $order->shipping_address_snapshot ?? [];
    @endphp
    <div class="section">
        <div class="lbl">PENERIMA:</div>
        <div class="recipient-name">{{ $addr['recipient_name'] ?? $order->customer_name }}</div>
        <div style="font-weight: bold; font-size: 11px;">{{ $addr['phone_number'] ?? $order->customer_phone }}</div>
        <div class="address-text">
            {{ $addr['full_address'] ?? '-' }}<br>
            <strong>{{ $addr['district_name'] ?? '' }}, {{ $addr['city_name'] ?? '' }} - {{ $addr['postal_code'] ?? '' }}</strong>
        </div>

        @if(!empty($addr['benchmark_notes']))
            <div class="benchmark-box">
                PATOKAN: {{ $addr['benchmark_notes'] }}
            </div>
        @endif
    </div>

    {{-- Info Pengirim --}}
    <div class="section" style="font-size: 10px;">
        <div class="lbl">PENGIRIM:</div>
        <strong>NGIZAN APPAREL WORKSHOP</strong> (0812-3456-7890)<br>
        DKI Jakarta / Bandung, Indonesia
    </div>

    {{-- Packing Checklist Items --}}
    <div style="margin-top: 6px;">
        <div class="lbl">CHECKLIST PACKING BARANG (ORDER #{{ $order->order_number }}):</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Item & Varian</th>
                    <th style="text-align: right;">Qty</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>
                            [ ] <strong>{{ $item->product_name }}</strong> (Size: {{ $item->size }}, {{ $item->type }})
                            @if($item->custom_name || $item->custom_number)
                                <span class="nameset-tag">⚡ SABLON: {{ $item->custom_name }} #{{ $item->custom_number }}</span>
                            @endif
                            @if($item->selected_patch)
                                <span style="font-size: 9px; color: #333;">★ {{ $item->selected_patch }}</span>
                            @endif
                        </td>
                        <td style="text-align: right; font-weight: bold; font-size: 12px;">{{ $item->quantity }} pcs</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top: 10px; text-align: center; font-size: 8.5px; color: #666; border-top: 1px solid #ddd; padding-top: 4px;">
        Terima kasih telah berbelanja di Ngizan Apparel · Garansi Autentik & Sablon DTF High-Density
    </div>

</body>
</html>
