@php
    $collection = $orderRooms->getCollection();
    $globalIndex = ($orderRooms->currentPage() - 1) * $orderRooms->perPage();
    $grandNetSum = 0;
    $grandCoursePriceSum = 0;
    $grandCouponSum = 0;
    $grandDiscountSum = 0;
    $grandDrinkSum = 0;
@endphp

@if ($orderRooms->isEmpty())
    <div class="text-center py-4">ไม่มีข้อมูล</div>
@else
    <table class="table table-bordered table-sm mb-0">
        <thead class="table-light">
            <tr>
                <th style="width:4%;">#</th>
                <th style="width:6%;">ห้อง</th>
                <th style="width:8%;">วันที่</th>
                <th style="width:6%;">เวลา</th>
                <th style="width:6%;">ชม.</th>
                <th style="width:8%;">ชำระเงิน</th>
                <th style="width:9%; text-align:right;">ค่านวด</th>
                <th style="width:8%; text-align:right;">ส่วนลด</th>
                <th style="width:9%; text-align:right;">เครื่องดื่ม</th>
                <th style="width:8%; text-align:right;">คูปอง</th>
                <th style="width:9%; text-align:right;">รับจริงร้าน</th>
                <th style="width:8%;">สถานะ</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($collection as $order)
                @php
                    $globalIndex++;
                    $isCancelled = $order->ref_status_id == 4;
                    $coursePrice = $order->total_price ?? 0;
                    if ($isCancelled) {
                        $coursePrice = -$coursePrice;
                    }

                    $usedCoupon = 0;
                    $usedCommission = 0;
                    $actualRevenue = 0;

                    $rtcKey = "{$order->ref_room_type_id}_{$order->service_laundry_cost}";
                    $roomTypeCourse = $roomTypeCourseMap->get($rtcKey);

                    if (!$isCancelled) {
                        $ucKey = "{$order->ref_user_id}_{$order->ref_room_type_id}_{$order->service_laundry_cost}";
                        $uc = $userCommissionMap->get($ucKey);
                        if ($uc && ($uc->price > 0 || $uc->coupon > 0)) {
                            $usedCommission = $uc->price;
                            $usedCoupon = $uc->coupon;
                        } elseif ($roomTypeCourse) {
                            $usedCommission = $roomTypeCourse->commission;
                            $usedCoupon = $roomTypeCourse->coupon;
                        }
                        $actualRevenue = $coursePrice - ($usedCoupon + $usedCommission);
                        $grandNetSum += $actualRevenue;
                    }

                    $grandCouponSum += $usedCoupon;
                    $grandCoursePriceSum += $isCancelled ? 0 : $coursePrice;
                    $grandDiscountSum += $isCancelled ? 0 : $order->discount ?? 0;
                    $grandDrinkSum += $isCancelled ? 0 : $order->products_sum_price ?? 0;

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
                <tr @if ($isCancelled) class="text-muted" style="text-decoration:line-through;" @endif>
                    <td>{{ $globalIndex }}</td>
                    <td>{{ $order->room_type->name ?? '-' }}</td>
                    <td>{{ date('d/m/Y', strtotime($order->created_at)) }}</td>
                    <td>{{ date('H:i', strtotime($order->created_at)) }}</td>
                    <td>{{ $durStr }}</td>
                    <td>{{ $order->payment_method }}</td>
                    <td class="text-end">{{ number_format($coursePrice) }}</td>
                    <td class="text-end">{{ $isCancelled ? '-' : number_format($order->discount ?? 0) }}</td>
                    <td class="text-end">{{ $isCancelled ? '-' : number_format($order->products_sum_price ?? 0) }}</td>
                    <td class="text-end">{{ $isCancelled ? '-' : number_format($usedCoupon) }}</td>
                    <td class="text-end">{{ $isCancelled ? '-' : number_format($actualRevenue) }}</td>
                    <td>{{ $order->status->name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Grand Total (current page summary) --}}
    <div class="d-flex align-items-center px-3 py-2 mt-3 rounded fw-bold"
        style="background:#dee2e6; font-size:13px; gap:16px;">
        <span>รวมหน้านี้</span>
        <span>ค่านวด: {{ number_format($grandCoursePriceSum) }}</span>
        <span>คูปอง: {{ number_format($grandCouponSum) }}</span>
        <span class="ms-auto text-success">รับจริงหน้านี้: {{ number_format($grandNetSum) }} บาท</span>
    </div>

    {{-- All-data Total bar --}}
    <div class="d-flex align-items-center px-3 py-2 mt-1 rounded fw-bold"
        style="background:#c3e6cb; font-size:13px; gap:16px;">
        <span>รวมทั้งหมด (ตามช่วงวันที่)</span>
        <span class="ms-auto text-dark">รับจริงทั้งหมด: {{ number_format($totalNetSum) }} บาท</span>
    </div>

    {{-- Summary Boxes (totals over ALL filtered records, not just this page) --}}
    <div class="row mt-4 g-3">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header fw-bold">การรับเงินจากช่องทางต่างๆ (ทั้งหมด)</div>
                <div class="card-body p-0">
                    <table class="table table-bordered mb-0">
                        <tr>
                            <td>ยอดรับจริงร้าน</td>
                            <td class="text-end">{{ number_format($totalNetSum, 2) }} บาท</td>
                        </tr>
                        <tr>
                            <td>QR Code</td>
                            <td class="text-end">{{ number_format($totalNetTransfer, 2) }} บาท</td>
                        </tr>
                        <tr>
                            <td>บัตรเครดิต</td>
                            <td class="text-end">{{ number_format($totalNetCredit, 2) }} บาท</td>
                        </tr>
                        <tr>
                            <td>Alipay</td>
                            <td class="text-end">{{ number_format($totalNetAl, 2) }} บาท</td>
                        </tr>
                        <tr class="fw-bold">
                            <td>คงเหลือเงินสดรับจริง</td>
                            <td class="text-end">{{ number_format($totalNetCash, 2) }} บาท</td>
                        </tr>

                        <tr class="fw-bold table-primary">
                            <td>รับจริงสุทธิ</td>
                            <td class="text-end">{{ number_format($totalCashRaw - $grandCommission, 2) }} บาท</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endif

{{-- Pagination --}}
<div class="mt-3">
    {!! $orderRooms->links('vendor.pagination.custom') !!}
</div>
