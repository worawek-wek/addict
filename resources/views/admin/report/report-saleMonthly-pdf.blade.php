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

    tr.group-header {
        background-color: #d9d9d9;
        font-weight: bold;
    }

    tr.group-subtotal {
        background-color: #f0f0f0;
        font-weight: bold;
    }
</style>
<div class="text-center">
    <span class="text-center">รายงานยอดขายรวม วันที่ {{ $report_start_date }} - {{ $report_end_date }} , พิมพ์เมื่อ
        {{ date('d/m/Y H:i') }}</span>
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>ห้อง</th>
            <th>วันที่</th>
            <th>เวลา</th>
            <th>ชม.</th>
            <th>ชำระเงิน</th>
            <th>ค่านวด</th>
            <th>คูปอง</th>
            <th>รับจริงของร้าน</th>
            <th>สถานะ</th>
        </tr>
    </thead>
    <tbody>
        @if ($orderRooms->isEmpty())
            <tr>
                <td colspan="10" class="text-center">ไม่มีข้อมูล</td>
            </tr>
        @else
            @php
                $roomGroups = $orderRooms->groupBy(function ($order) {
                    return $order->room_type->name ?? '-';
                });
                $globalIndex = 0;
                $grandNetSum = 0;
                $grandCoursePriceSum = 0;
                $grandCouponSum = 0;
            @endphp

            @foreach ($roomGroups as $roomName => $orders)
                {{-- Room Group Header --}}
                <tr style="background-color: #d9d9d9; font-weight: bold;">
                    <td colspan="10" style="text-align: left; padding-left: 10px;">
                        ห้อง: {{ $roomName }} ({{ $orders->count() }} รายการ)
                    </td>
                </tr>

                {{-- Orders in this room group --}}
                @php
                    $groupAddonSum = 0;
                    $groupProductSum = 0;
                    $groupTotalSum = 0;
                    $groupCouponSum = 0;
                    $groupCommissionSum = 0;
                    $groupCoursePriceSum = 0;
                    $groupNetSum = 0;
                @endphp

                @foreach ($orders as $order)
                    @php
                        $globalIndex++;
                        $groupAddonSum += $order->addons_sum_price ?? 0;
                        $groupProductSum += $order->products_sum_price ?? 0;
                        $groupTotalSum += $order->total_price;

                        // Calculate actual shop revenue: รวมเงิน - (คูปอง + ค่ามือ)
                        $actualRevenue = 0;
                        $usedCoupon = 0;
                        $usedCommission = 0;
                        $coursePrice = 0; // ค่านวด = raw course price
                        $isCancelled = $order->ref_status_id == 4; // Status 4 = ยกเลิก

                        // ค่านวด = what customer actually paid (negative if cancelled)
                        $coursePrice = $order->total_price ?? 0;
                        if ($isCancelled) {
                            $coursePrice = -$coursePrice;
                        }

                        // Still need roomTypeCourse for commission/coupon fallback
                        $rtcKey = "{$order->ref_room_type_id}_{$order->service_laundry_cost}";
                        $roomTypeCourse = $roomTypeCourseMap->get($rtcKey);

                        if (!$isCancelled) {
                            // 1. Try personal commission first (user_has_room_type_commissions)
                            $ucKey = "{$order->ref_user_id}_{$order->ref_room_type_id}_{$order->service_laundry_cost}";
                            $userCommission = $userCommissionMap->get($ucKey);

                            if ($userCommission && ($userCommission->price > 0 || $userCommission->coupon > 0)) {
                                // price = ค่ามือ, coupon = คูปอง
                                $usedCommission = $userCommission->price;
                                $usedCoupon = $userCommission->coupon;
                            } else {
                                // 2. Fallback to room_type_has_courses (commission = ค่ามือ, coupon = คูปอง)
                                if ($roomTypeCourse) {
                                    $usedCommission = $roomTypeCourse->commission;
                                    $usedCoupon = $roomTypeCourse->coupon;
                                }
                            }

                            // ยอดรับจริงของร้าน = ค่านวด - (คูปอง + ค่ามือ)
                            $actualRevenue = $coursePrice - ($usedCoupon + $usedCommission);
                            $groupNetSum += $actualRevenue;
                        }

                        $groupCouponSum += $usedCoupon;
                        $groupCommissionSum += $usedCommission;
                        // Cancelled orders don't count towards totals (excluded like payment channels)
                        $groupCoursePriceSum += $isCancelled ? 0 : $coursePrice;
                    @endphp
                    <tr>
                        <td>{{ $globalIndex }}</td>
                        <td>{{ $order->room_type->name ?? '-' }}</td>
                        <td>{{ date('d/m/Y', strtotime($order->created_at)) }}</td>
                        <td>{{ date('h:i', strtotime($order->created_at)) }}</td>
                        <td>
                            @php
                                $start = \Carbon\Carbon::parse($order->start_time);
                                $end = \Carbon\Carbon::parse($order->end_time);
                                $diff = $start->diff($end);
                            @endphp
                            @if ($diff->h > 0)
                                {{ $diff->h }} ชม.
                            @endif
                            @if ($diff->i > 0)
                                {{ $diff->i }} นาที
                            @endif
                        </td>
                        <td>{{ $order->payment_method }}</td>
                        <td>{{ number_format($coursePrice) }}</td>
                        <td>{{ $isCancelled ? '-' : number_format($usedCoupon) }}</td>
                        <td>{{ $isCancelled ? '-' : number_format($actualRevenue) }}</td>
                        <td>{{ $order->status->name }}</td>
                    </tr>
                @endforeach

                {{-- Subtotal for this room group --}}
                @php
                    $grandNetSum += $groupNetSum;
                    $grandCoursePriceSum += $groupCoursePriceSum;
                    $grandCouponSum += $groupCouponSum;
                @endphp
                <tr style="background-color: #f0f0f0; font-weight: bold;">
                    <td colspan="6" style="text-align: right;">รวม {{ $roomName }}</td>
                    <td>{{ number_format($groupCoursePriceSum) }}</td>
                    <td>{{ number_format($groupCouponSum) }}</td>
                    <td>{{ number_format($groupNetSum) }}</td>
                    <td></td>
                </tr>

                {{-- Spacing row between groups --}}
                <tr>
                    <td colspan="10" style="border: none; padding: 2px;"></td>
                </tr>
            @endforeach

            {{-- Grand Total --}}
            <tr style="font-weight: bold; background: #e0e0e0;">
                <td colspan="6" style="text-align: right;">รวมยอดทั้งหมด</td>
                <td>{{ number_format($grandCoursePriceSum) }}</td>
                <td>{{ number_format($grandCouponSum) }}</td>
                <td>{{ number_format($grandNetSum) }}</td>
                <td></td>
            </tr>
        @endif

    </tbody>
</table>

<!-- สรุปยอดรวมทั้งหมด (Summary Box) -->
<div style="margin-top:30px; width:100%;">
    <table style="width: 60%; margin: 0 auto; border: 2px solid #333; font-size: 12px;">
        <tr style="background: #f7f7f7; font-weight: bold;">
            <td colspan="2" style="text-align:center; border-bottom:2px solid #333;">สรุปยอดรวม</td>
        </tr>
        <tr>
            <td style="text-align:right; padding-right: 20px;">ยอดค่านวดรวม</td>
            <td style="text-align:right; padding-right: 20px;">{{ number_format($grandCoursePriceSum ?? 0, 2) }} บาท
            </td>
        </tr>
        <tr>
            <td style="text-align:right; padding-right: 20px;">ยอดคูปองรวม</td>
            <td style="text-align:right; padding-right: 20px;">{{ number_format($grandCouponSum ?? 0, 2) }} บาท</td>
        </tr>
        <tr>
            <td style="text-align:right; padding-right: 20px;">ยอดรับจริงของร้าน</td>
            <td style="text-align:right; padding-right: 20px; font-weight:bold; color:#1a8917;">
                {{ number_format($grandNetSum ?? 0, 2) }} บาท</td>
        </tr>
    </table>
</div>


<div style="margin-top:30px; width:100%;">
    <table style="width: 60%; margin: 0 auto; border: 2px solid #333; font-size: 12px;">
        <tr style="background: #f7f7f7; font-weight: bold;">
            <td colspan="2" style="text-align:center; border-bottom:2px solid #333;">การรับเงินจากช่องทางต่างๆ</td>
        </tr>
        <tr>
            <td style="text-align:right; padding-right: 20px;">เงินสด</td>
            <td style="text-align:right; padding-right: 20px;">{{ number_format($summary_type_payment_cash ?? 0, 2) }}
                บาท</td>
        </tr>
        <tr>
            <td style="text-align:right; padding-right: 20px;">QR Code</td>
            <td style="text-align:right; padding-right: 20px;">
                {{ number_format($summary_type_payment_transfer ?? 0, 2) }} บาท</td>
        </tr>
        <tr>
            <td style="text-align:right; padding-right: 20px;">บัตรเครดิต</td>
            <td style="text-align:right; padding-right: 20px;">
                {{ number_format($summary_type_payment_credit ?? 0, 2) }} บาท
            </td>
        </tr>
        <tr>
            <td style="text-align:right; padding-right: 20px;">Alipay</td>
            <td style="text-align:right; padding-right: 20px;">
                {{ number_format($summary_type_payment_al ?? 0, 2) }} บาท
            </td>
        </tr>
        <tr>
            <td style="text-align:right; padding-right: 20px;">รวมทั้งหมด</td>
            <td style="text-align:right; padding-right: 20px; font-weight:bold; color:#1a8917;">
                {{ number_format(($summary_type_payment_cash ?? 0) + ($summary_type_payment_credit ?? 0) + ($summary_type_payment_transfer ?? 0) + ($summary_type_payment_al ?? 0), 2) }}
                บาท</td>
        </tr>
    </table>
</div>
