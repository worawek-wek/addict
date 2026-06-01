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
<div class="text-center ">
    <span class="text-center">รายงานยอดขายรวม วันที่ {{ $report_start_date }} {{ $report_start_time }} -
        {{ $report_end_date }} {{ $report_end_time }} , พิมพ์เมื่อ
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
            <th>ส่วนลด</th>
            <th>เครื่องดื่ม</th>
            <th>คูปอง</th>
            <th>รับจริงร้าน</th>
            <th>สถานะ</th>
        </tr>
    </thead>
    {{-- @php
        dd($orderRooms);
    @endphp --}}
    <tbody>

        @if ($orderRooms->isEmpty())
            <tr>
                <td colspan="12" class="text-center">ไม่มีข้อมูล</td>
            </tr>
        @else
            @php
                $globalIndex = 0;
                $grandNetSum = 0;
                $grandCoursePriceSum = 0;
                $grandCouponSum = 0;
                $grandDiscountSum = 0;
                $grandDrinkSum = 0;
            @endphp

            @foreach ($orderRooms as $order)
                @php
                    $globalIndex++;

                    $actualRevenue = 0;
                    $usedCoupon = 0;
                    $usedCommission = 0;
                    $isCancelled = $order->ref_status_id == 4;

                    $coursePrice = $order->total_price ?? 0;
                    if ($isCancelled) {
                        $coursePrice = 0;
                    }

                    $rtcKey = "{$order->ref_room_type_id}_{$order->service_laundry_cost}";
                    $roomTypeCourse = $roomTypeCourseMap->get($rtcKey);

                    if (!$isCancelled) {
                        $ucKey = "{$order->ref_user_id}_{$order->ref_room_type_id}_{$order->service_laundry_cost}";
                        $userCommission = $userCommissionMap->get($ucKey);

                        if ($userCommission && ($userCommission->price > 0 || $userCommission->coupon > 0)) {
                            $usedCommission = $userCommission->price;
                            $usedCoupon = $userCommission->coupon;
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
                @endphp
                <tr>
                    <td>{{ $globalIndex }}</td>
                    <td>{{ $order->user->name ?? '-' }}:{{ $order->room_type->name ?? '-' }}</td>
                    <td>{{ date('d/m/Y', strtotime($order->created_at)) }}</td>
                    <td>{{ date('H:i', strtotime($order->created_at)) }}</td>
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
                    <td>{{ number_format($isCancelled ? 0 : ($order->discount ?? 0)) }}</td>
                    <td>{{ number_format($isCancelled ? 0 : ($order->products_sum_price ?? 0)) }}</td>
                    <td>{{ number_format($isCancelled ? 0 : $usedCoupon) }}</td>
                    <td>{{ number_format($isCancelled ? 0 : $actualRevenue) }}</td>
                    <td>{{ $order->status->name }}</td>
                </tr>
            @endforeach

            {{-- Grand Total --}}
            <tr style="font-weight: bold; background: #e0e0e0;">
                <td colspan="6" style="text-align: right;">รวมยอดทั้งหมด</td>
                <td>{{ number_format($grandCoursePriceSum) }}</td>
                <td>{{ number_format($grandDiscountSum ?? 0) }}</td>
                <td>{{ number_format($grandDrinkSum ?? 0) }}</td>
                <td>{{ number_format($grandCouponSum) }}</td>
                <td>{{ number_format($grandNetSum) }}</td>
                <td></td>
            </tr>
        @endif

    </tbody>
</table>

<div style="margin-top:10px; width:100%;">
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
            <td style="font-weight:bold; background:#b8b8b8;">Total</td>
            <td style="text-align:right; padding-right: 20px; font-weight:bold; color:#1a8917;">
                {{ number_format(($summary_type_payment_cash ?? 0) + ($summary_type_payment_transfer ?? 0) + ($summary_type_payment_credit ?? 0) + ($summary_type_payment_al ?? 0), 2) }}
                บาท</td>
        </tr>

    </table>
    <hr style="bold ">
    <table style="width: 60%; margin: 10px auto 0 auto; border: 2px solid #333; font-size: 12px;">
        <tr style="background: #f7f7f7; font-weight: bold;">
            <td colspan="2" style="text-align:center; border-bottom:2px solid #333;">สรุป</td>
        </tr>
        <tr style="font-weight:bold; background:#ebebeb;">
            <td style="text-align:right; padding-right: 20px;">รับจริงร้าน</td>
            <td style="text-align:right; padding-right: 20px;">
                {{ number_format($grandNetSum ?? 0, 2) }}
                บาท
            </td>
        </tr>
        <tr style="font-weight:bold; background:#ebebeb;">
            <td style="text-align:right; padding-right: 20px;">QR Code , Credit Card , AliPay</td>
            <td style="text-align:right; padding-right: 20px;">
                {{ number_format(($totalNetTransfer ?? 0) + ($totalNetCredit ?? 0) + ($totalNetAl ?? 0), 2) }}
                บาท
            </td>
        </tr>
        <tr style="font-weight:bold; background:#ebebeb;">
            <td style="text-align:right; padding-right: 20px;">รับเงินสุทธิ</td>
            <td style="text-align:right; padding-right: 20px;">
                {{ number_format($totalNetCash ?? 0, 2) }} บาท
            </td>
        </tr>
    </table>
</div>
