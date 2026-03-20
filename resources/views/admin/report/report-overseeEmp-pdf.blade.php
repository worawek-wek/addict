<style>
    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 10px;
    }

    th,
    td {
        border: 1px solid #000;
        padding: 4px 6px;
        text-align: center;
    }

    thead th {
        background-color: #f0f0f0;
        font-weight: bold;
    }

    tr.subtotal-row td {
        border-top: 2px solid #555;
        background-color: #fafafa;
        font-weight: bold;
    }

    tr.grand-row td {
        border-top: 2px solid #000;
        border-left: none;
        border-right: none;
        border-bottom: none;
        font-weight: bold;
    }

    .summary-section {
        margin-top: 24px;
        padding: 12px;
        background-color: #f9f9f9;
        border: 2px solid #333;
        border-radius: 4px;
    }

    .summary-title {
        font-size: 14px;
        font-weight: bold;
        text-align: center;
        margin-bottom: 10px;
        color: #333;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .summary-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 11px;
    }

    .summary-table th {
        background-color: #333;
        color: #fff;
        padding: 8px;
        font-weight: bold;
        border: 1px solid #000;
    }

    .summary-table td {
        padding: 6px 8px;
        border: 1px solid #666;
        background-color: #fff;
    }

    .summary-table tr:hover td {
        background-color: #f0f0f0;
    }
</style>

<div style="text-align:center; font-size:16px; font-weight:bold; margin-bottom:10px;">
    รายงานผู้ดูแลพนักงาน
</div>
<div style="font-size:11px; margin-bottom:8px;">
    วันที่ {{ $report_start_date }} - {{ $report_end_date }} , พิมพ์เมื่อ {{ date('d/m/Y H:i') }}
</div>

<table>
    <thead>
        <tr>
            <th style="width:5%;">No</th>
            <th style="width:9%;">วันที่</th>
            <th style="width:7%;">เวลา</th>
            <th style="width:9%;">รหัสผู้ดูแล</th>
            <th style="width:11%;">ชื่อผู้ดูแล</th>
            <th style="width:26%;">ชื่อพนักงาน</th>
            <th style="width:8%;">ชม.</th>
            <th style="width:9%;">@ราคา</th>
            <th style="width:9%;">รวมเงิน</th>
        </tr>
    </thead>
    <tbody>

        @php
            $grouped = $orderRooms->groupBy('ref_seller_id');
            $globalIndex = 0;
        @endphp

        @if ($orderRooms->isEmpty())
            <tr>
                <td colspan="9" style="text-align:center; padding:12px;">ไม่มีข้อมูล</td>
            </tr>
        @else
            @foreach ($grouped as $sellerId => $groupOrders)
                @php
                    $firstOrder = $groupOrders->first();
                    $sellerCode = $firstOrder->seller->user_id ?? '-';
                    $sellerName = $firstOrder->seller->name ?? 'ไม่ระบุ';
                    $groupTotal = $groupOrders->where('ref_status_id', '!=', 4)->sum(function($o) {
                        return $o->total_price - ($o->addons_sum_price ?? 0);
                    });
                    $groupCount = $groupOrders->where('ref_status_id', '!=', 4)->count();
                @endphp

                {{-- Group Header --}}
                <tr style="background-color:#d9d9d9; font-weight:bold;">
                    <td colspan="9" style="text-align:left; padding-left:8px;">
                        ผู้ดูแล: [{{ $sellerCode }}] {{ $sellerName }} ({{ $groupCount }} รายการ)
                    </td>
                </tr>

                @foreach ($groupOrders as $order)
                    @php
                        $globalIndex++;
                        $isCancelled = $order->ref_status_id == 4;
                        $start = \Carbon\Carbon::parse($order->start_time);
                        $end = \Carbon\Carbon::parse($order->end_time);
                        $diff = $start->diff($end);
                        $durStr = '';
                        if ($diff->h > 0) $durStr .= $diff->h . ' ชม. ';
                        if ($diff->i > 0) $durStr .= $diff->i . ' นาที';
                        $durStr = trim($durStr) ?: '-';
                        $netPrice = $order->total_price - ($order->addons_sum_price ?? 0);
                    @endphp
                    <tr @if($isCancelled) style="color:#999; text-decoration:line-through;" @endif>
                        <td>{{ $globalIndex }}</td>
                        <td>{{ date('d/m/Y', strtotime($order->created_at)) }}</td>
                        <td>{{ date('H:i', strtotime($order->created_at)) }}</td>
                        <td>{{ $sellerCode }}</td>
                        <td>{{ $sellerName }}</td>
                        <td style="text-align:left;">{{ $order->user->name ?? '-' }} + {{ $order->course->name ?? '-' }}</td>
                        <td>{{ $durStr }}</td>
                        <td style="text-align:right;">{{ number_format($netPrice) }}</td>
                        <td style="text-align:right;">{{ $isCancelled ? '-' : number_format($netPrice) }}</td>
                    </tr>
                @endforeach

                {{-- Group Subtotal --}}
                <tr class="subtotal-row">
                    <td colspan="5"></td>
                    <td style="text-align:right;">รวม {{ $sellerName }}</td>
                    <td style="text-align:center;">{{ $groupCount }}</td>
                    <td></td>
                    <td style="text-align:right; text-decoration:underline;">{{ number_format($groupTotal) }}</td>
                </tr>
                <tr>
                    <td colspan="9" style="border:none; padding:4px;"></td>
                </tr>
            @endforeach

            {{-- Grand Total --}}
            @php
                $grandTotal = $orderRooms->where('ref_status_id', '!=', 4)->sum(function($o) {
                    return $o->total_price - ($o->addons_sum_price ?? 0);
                });
                $grandCount = $orderRooms->where('ref_status_id', '!=', 4)->count();
            @endphp
            <tr style="font-weight:bold; background:#e0e0e0;">
                <td colspan="5" style="text-align:right;">รวมยอดทั้งหมด</td>
                <td></td>
                <td style="text-align:center;">{{ $grandCount }}</td>
                <td></td>
                <td style="text-align:right;">{{ number_format($grandTotal) }}</td>
            </tr>
        @endif

    </tbody>
</table>

{{-- Summary Section --}}
<div class="summary-section">
    <div class="summary-title">สรุปยอดรวมตามผู้ดูแล</div>
    <table class="summary-table">
        <thead>
            <tr>
                <th style="width:10%; text-align:center; color:#000;">รหัสผู้ดูแล</th>
                <th style="width:35%; text-align:left; color:#000;">ชื่อผู้ดูแล</th>
                <th style="width:10%; text-align:center; color:#000;">จำนวน</th>
                <th style="width:15%; text-align:right; color:#000;">รวม (บาท)</th>
            </tr>
        </thead>
        <tbody>
            @if ($summary_data->isNotEmpty())
                @foreach ($summary_data as $item)
                    <tr>
                        <td style="text-align:center;">{{ $item['user_id'] }}</td>
                        <td style="text-align:left; font-weight:500;">{{ $item['name'] }}</td>
                        <td style="text-align:center;">{{ $item['count'] }}</td>
                        <td style="text-align:right; font-weight:600;">{{ number_format($item['total_price']) }}</td>
                    </tr>
                @endforeach
                <tr style="background-color:#e8e8e8; font-weight:bold;">
                    <td colspan="2" style="text-align:right; padding-right:12px;">รวมทั้งสิ้น</td>
                    <td style="text-align:center;">{{ $summary_data->sum('count') }}</td>
                    <td style="text-align:right; font-size:12px;">
                        {{ number_format($summary_data->sum('total_price')) }}</td>
                </tr>
            @else
                <tr>
                    <td colspan="4" style="text-align:center; padding:16px;">ไม่มีข้อมูล</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
