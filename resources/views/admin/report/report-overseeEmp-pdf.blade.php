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
    วันที่ {{ date('d/m/Y') }} , เวลา {{ date('H:i') }}
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
            $sumTotal = 0;
            $sumCustomer = 0;
            $orders = $orderRooms->values();
        @endphp

        @foreach ($orders as $order)
            @php
                $sumTotal += $order->total_price;
                $sumCustomer++;
                $next = $loop->last ? null : $orders->get($loop->index + 1);

                $start = \Carbon\Carbon::parse($order->start_time);
                $end = \Carbon\Carbon::parse($order->end_time);
                $diff = $start->diff($end);
                $durStr = '';
                if ($diff->h > 0) {
                    $durStr .= $diff->h . ' ชม. ';
                }
                if ($diff->i > 0) {
                    $durStr .= $diff->i . ' นาที';
                }
                $durStr = trim($durStr) ?: '-';
            @endphp

            <tr>
                <td>{{ $order->id ?? $loop->index + 1 }}</td>
                <td>{{ date('d/m/Y', strtotime($order->created_at)) }}</td>
                <td>{{ date('H:i', strtotime($order->created_at)) }}</td>
                <td>{{ $order->seller->id ?? '-' }}</td>
                <td>{{ $order->seller->name ?? '-' }}</td>
                <td style="text-align:left;">{{ $order->user->name ?? '-' }} + {{ $order->course->name ?? '-' }}</td>
                <td>1</td>
                <td style="text-align:right;">{{ number_format($order->total_price) }}</td>
                <td style="text-align:right;">{{ number_format($order->total_price) }}</td>
            </tr>

            {{-- Subtotal when supervisor group changes --}}
            @if (!$next || $next->ref_user_id != $order->ref_user_id)
                <tr class="subtotal-row">
                    <td colspan="5" style=""></td>
                    <td style="text-align:right;">รวมต่อคน</td>
                    <td style="text-align:center;">{{ $sumCustomer }}</td>
                    <td style=""></td>
                    <td style="text-align:right; text-decoration:underline;">{{ number_format($sumTotal) }}</td>
                </tr>
                <tr>
                    <td colspan="9" style="border:none; padding:4px;"></td>
                </tr>
                @php
                    $sumTotal = 0;
                    $sumCustomer = 0;
                @endphp
            @endif
        @endforeach

        {{-- Grand Total --}}
        @if (!$orders->isEmpty())
            @php
                $grandTotal = $orders->sum('total_price');
                $grandCustomer = $orders->count();
            @endphp
        @endif

        @if ($orders->isEmpty())
            <tr>
                <td colspan="9" style="text-align:center; padding:12px;">ไม่มีข้อมูล</td>
            </tr>
        @endif

    </tbody>
</table>

{{-- Summary Section --}}
@php
    $summary = [];
    foreach ($orders as $order) {
        $supervisor = $order->seller->name ?? '-';
        $supervisorId = $order->seller->id ?? '-';
        $duration = 0;
        if ($order->start_time && $order->end_time) {
            $start = \Carbon\Carbon::parse($order->start_time);
            $end = \Carbon\Carbon::parse($order->end_time);
            $duration = $end->diffInMinutes($start) / 60; // use total minutes for fractional hours
        }
        if (!isset($summary[$supervisorId])) {
            $summary[$supervisorId] = [
                'name' => $supervisor,
                'hours' => 0,
                'total' => 0,
            ];
        }
        $summary[$supervisorId]['hours'] += $duration;
        $summary[$supervisorId]['total'] += $order->total_price;
    }
@endphp

<div class="summary-section">
    <div class="summary-title">สรุปยอดรวมตามผู้ดูแล</div>
    <table class="summary-table">
        <thead>
            <tr>
                <th style="width:15%; text-align:center; color:#333;">รหัสผู้ดูแล</th>
                <th style="width:45%; text-align:left; color:#333;">ชื่อผู้ดูแล</th>
                <th style="width:20%; text-align:right; color:#333;">รวมเงิน (บาท)</th>
            </tr>
        </thead>
        <tbody>
            @if (!empty($summary))
                @foreach ($summary as $supervisorId => $item)
                    <tr>
                        <td style="text-align:center;">{{ $supervisorId }}</td>
                        <td style="text-align:left; font-weight:500;">{{ $item['name'] }}</td>
                        <td style="text-align:right; font-weight:600;">{{ number_format($item['total']) }}</td>
                    </tr>
                @endforeach
                <tr style="background-color:#e8e8e8; font-weight:bold;">
                    <td colspan="2" style="text-align:right; padding-right:12px;">รวมทั้งสิ้น</td>
                    <td style="text-align:right; font-size:12px;">
                        {{ number_format(array_sum(array_column($summary, 'total'))) }}</td>
                </tr>
            @else
                <tr>
                    <td colspan="4" style="text-align:center; padding:16px;">ไม่มีข้อมูล</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
