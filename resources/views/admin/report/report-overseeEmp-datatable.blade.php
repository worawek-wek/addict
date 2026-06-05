@php
    $grouped = $orderRooms->getCollection()->groupBy('ref_seller_id');
    $globalIndex = ($orderRooms->currentPage() - 1) * $orderRooms->perPage();
    $formatDuration = function ($order) {
        $minutes = (int) ($order->duration_minutes ?? 0);

        if ($minutes <= 0 && preg_match('/(\d+)/', $order->course->name ?? '', $matches)) {
            $minutes = (int) $matches[1];
        }

        if ($minutes <= 0 && $order->start_time && $order->end_time) {
            $start = \Carbon\Carbon::parse(($order->booking_date ?? now()->toDateString()) . ' ' . $order->start_time);
            $end = \Carbon\Carbon::parse(($order->booking_date ?? now()->toDateString()) . ' ' . $order->end_time);

            if ($end->lessThan($start)) {
                $end->addDay();
            }

            $minutes = $start->diffInMinutes($end);
        }

        if ($minutes <= 0) {
            return '-';
        }

        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;
        $duration = '';

        if ($hours > 0) {
            $duration .= $hours . ' ชม. ';
        }

        if ($remainingMinutes > 0) {
            $duration .= $remainingMinutes . ' นาที';
        }

        return trim($duration) ?: '-';
    };
@endphp

@if ($orderRooms->isEmpty())
    <div class="text-center py-4">ไม่มีข้อมูล</div>
@else
    @foreach ($grouped as $sellerId => $groupOrders)
        @php
            $firstOrder    = $groupOrders->first();
            $sellerCode    = $firstOrder->seller->user_id ?? '-';
            $sellerName    = $firstOrder->seller->name ?? 'ไม่ระบุ';
            $groupTotal    = $groupOrders->where('ref_status_id', '!=', 4)->sum(function ($o) {
                return $o->total_price - ($o->addons_sum_price ?? 0) - ($o->products_sum_price ?? 0);
            });
            $groupCount    = $groupOrders->where('ref_status_id', '!=', 4)->count();
        @endphp

        {{-- Group Header --}}
        <div class="d-flex align-items-center px-3 py-2 mb-1 mt-3 rounded"
             style="background:#e9ecef; font-weight:600; font-size:13px;">
            <i class="ti ti-user me-2"></i>
            ผู้ดูแล: [{{ $sellerCode }}] {{ $sellerName }}
            <span class="ms-2 badge bg-secondary">{{ $groupCount }} รายการ</span>
            <span class="ms-auto fw-bold">รวม: {{ number_format($groupTotal) }} บาท</span>
        </div>

        <table class="table table-bordered table-sm mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width:5%;">No</th>
                    <th style="width:9%;">วันที่</th>
                    <th style="width:7%;">เวลา</th>
                    <th style="width:9%;">รหัสผู้ดูแล</th>
                    <th style="width:11%;">ชื่อผู้ดูแล</th>
                    <th style="width:26%;">ชื่อพนักงาน</th>
                    <th style="width:8%;">ชม.</th>
                    <th style="width:9%; text-align:right;">@ราคา</th>
                    <th style="width:9%; text-align:right;">รวมเงิน</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($groupOrders as $order)
                    @php
                        $globalIndex++;
                        $isCancelled = $order->ref_status_id == 4;
                        $durStr = $formatDuration($order);
                        $netPrice = $order->total_price - ($order->addons_sum_price ?? 0) - ($order->products_sum_price ?? 0);
                    @endphp
                    <tr @if($isCancelled) class="text-muted" style="text-decoration:line-through;" @endif>
                        <td>{{ $globalIndex }}</td>
                        <td>{{ date('d/m/Y', strtotime($order->created_at)) }}</td>
                        <td>{{ date('H:i', strtotime($order->created_at)) }}</td>
                        <td>{{ $sellerCode }}</td>
                        <td>{{ $sellerName }}</td>
                        <td>{{ $order->user->name ?? '-' }} + {{ $order->course->name ?? '-' }}</td>
                        <td>{{ $durStr }}</td>
                        <td class="text-end">{{ number_format($netPrice) }}</td>
                        <td class="text-end">{{ $isCancelled ? '-' : number_format($netPrice) }}</td>
                    </tr>
                @endforeach
                {{-- Group Subtotal --}}
                <tr class="fw-bold" style="background:#f8f9fa; border-top:2px solid #adb5bd;">
                    <td colspan="5"></td>
                    <td class="text-end">รวม {{ $sellerName }}</td>
                    <td class="text-center">{{ $groupCount }}</td>
                    <td></td>
                    <td class="text-end" style="text-decoration:underline;">{{ number_format($groupTotal) }}</td>
                </tr>
            </tbody>
        </table>
    @endforeach

    {{-- Grand Total --}}
    @php
        $grandTotal = $orderRooms->getCollection()->where('ref_status_id', '!=', 4)->sum(function ($o) {
            return $o->total_price - ($o->addons_sum_price ?? 0) - ($o->products_sum_price ?? 0);
        });
        $grandCount = $orderRooms->getCollection()->where('ref_status_id', '!=', 4)->count();
    @endphp
    <div class="d-flex align-items-center px-3 py-2 mt-3 rounded fw-bold"
         style="background:#dee2e6; font-size:13px;">
        <span>รวมยอดทั้งหมด</span>
        <span class="ms-3">{{ $grandCount }} รายการ</span>
        <span class="ms-auto">{{ number_format($grandTotal) }} บาท</span>
    </div>

    {{-- Summary Section --}}
    <div class="card mt-4">
        <div class="card-header fw-bold">สรุปยอดรวมตามผู้ดูแล</div>
        <div class="card-body p-0">
            <table class="table table-bordered mb-0">
                <thead class="table-dark">
                    <tr>
                        <th style="width:12%;">รหัสผู้ดูแล</th>
                        <th style="width:40%;">ชื่อผู้ดูแล</th>
                        <th style="width:12%; text-align:center;">จำนวน</th>
                        <th style="width:18%; text-align:right;">รวม (บาท)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($grouped as $sellerId => $groupOrders)
                        @php
                            $fo     = $groupOrders->first();
                            $sCode  = $fo->seller->user_code ?? $fo->seller->user_id ?? '-';
                            $sName  = $fo->seller->name ?? 'ไม่ระบุ';
                            $sTotal = $groupOrders->where('ref_status_id', '!=', 4)->sum(function ($o) {
                                return $o->total_price - ($o->addons_sum_price ?? 0) - ($o->products_sum_price ?? 0);
                            });
                            $sCount = $groupOrders->where('ref_status_id', '!=', 4)->count();
                        @endphp
                        <tr>
                            <td>{{ $sCode }}</td>
                            <td>{{ $sName }}</td>
                            <td class="text-center">{{ $sCount }}</td>
                            <td class="text-end fw-semibold">{{ number_format($sTotal) }}</td>
                        </tr>
                    @endforeach
                    <tr class="fw-bold" style="background:#e9ecef;">
                        <td colspan="2" class="text-end">รวมทั้งสิ้น</td>
                        <td class="text-center">{{ $grandCount }}</td>
                        <td class="text-end">{{ number_format($grandTotal) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endif

{{-- Pagination --}}
<div class="mt-3">
    {!! $orderRooms->links('vendor.pagination.custom') !!}
</div>
