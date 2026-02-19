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
    <span class="text-center">รายงานยอดขายรวม วันที่ {{ date('d/m/Y') }} , เวลา {{ date('H:i') }}</span>
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
            <th>อาหาร</th>
            <th>เครื่องดื่มพนักงาน</th>
            <th>เครื่องดื่มลูกค้า</th>
            <th>รวมเงิน</th>
            <th>คูปอง</th>
            <th>รับจริงของร้าน</th>
            <th>สถานะ</th>
        </tr>
    </thead>
    <tbody>
        @if ($orderRooms->isEmpty())
            <tr>
                <td colspan="14" class="text-center">ไม่มีข้อมูล</td>
            </tr>
        @else
            @php
                $roomGroups = $orderRooms->groupBy(function ($order) {
                    return $order->room_type->name ?? '-';
                });
                $globalIndex = 0;
            @endphp

            @foreach ($roomGroups as $roomName => $orders)
                {{-- Room Group Header --}}
                <tr style="background-color: #d9d9d9; font-weight: bold;">
                    <td colspan="14" style="text-align: left; padding-left: 10px;">
                        ห้อง: {{ $roomName }} ({{ $orders->count() }} รายการ)
                    </td>
                </tr>

                {{-- Orders in this room group --}}
                @php
                    $groupAddonSum = 0;
                    $groupProductSum = 0;
                    $groupTotalSum = 0;
                    $groupCouponSum = 0;
                    $groupNetSum = 0;
                @endphp

                @foreach ($orders as $order)
                    @php
                        $globalIndex++;
                        $groupAddonSum += $order->addons_sum_price ?? 0;
                        $groupProductSum += $order->products_sum_price ?? 0;
                        $groupTotalSum += $order->total_price;
                        $groupCouponSum += $order->addons_sum_coupon ?? 0;

                        // Calculate actual shop revenue
                        $actualRevenue = 0;
                        $isCancelled = $order->ref_status_id == 4; // Status 4 = ยกเลิก

                        if (!$isCancelled) {
                            // Try to get user commission data first
                            $userCommission = \App\Models\UserHasRoomTypeCommission::where('ref_user_id', $order->ref_user_id)
                                ->where('ref_room_type_id', $order->ref_room_type_id)
                                ->where('ref_course_id', $order->service_laundry_cost)
                                ->first();

                            if ($userCommission && ($userCommission->price > 0 || $userCommission->coupon > 0)) {
                                // Use user commission: (price + coupon) - totalReceived
                                $actualRevenue = ($userCommission->price + $userCommission->coupon) - $order->total_price;
                            } else {
                                // Fallback to room_type_has_courses
                                $roomTypeCourse = \App\Models\RoomTypeHasCourse::where('ref_room_type_id', $order->ref_room_type_id)
                                    ->where('ref_course_id', $order->service_laundry_cost)
                                    ->first();

                                if ($roomTypeCourse) {
                                    $actualRevenue = ($roomTypeCourse->price + $roomTypeCourse->coupon) - $order->total_price;
                                } else {
                                    // Final fallback: total_price - coupon_used
                                    $actualRevenue = $order->total_price - ($order->addons_sum_coupon ?? 0);
                                }
                            }

                            $groupNetSum += $actualRevenue;
                        }
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
                        <td>{{ number_format($order->addons_sum_price ?? 0) }}</td>
                        <td> 0 </td>
                        <td> 0 </td>
                        <td>{{ number_format($order->products_sum_price ?? 0) }}</td>
                        <td>{{ number_format($order->total_price) }}</td>
                        <td>{{ number_format($order->addons_sum_coupon ?? 0) }}</td>
                        <td>{{ $isCancelled ? '-' : number_format($actualRevenue) }}</td>
                        <td>{{ $order->status->name }}</td>
                    </tr>
                @endforeach

                {{-- Subtotal for this room group --}}
                <tr style="background-color: #f0f0f0; font-weight: bold;">
                    <td colspan="6" style="text-align: right;">รวม {{ $roomName }}</td>
                    <td>{{ number_format($groupAddonSum) }}</td>
                    <td>0</td>
                    <td>0</td>
                    <td>{{ number_format($groupProductSum) }}</td>
                    <td>{{ number_format($groupTotalSum) }}</td>
                    <td>{{ number_format($groupCouponSum) }}</td>
                    <td>{{ number_format($groupNetSum) }}</td>
                    <td></td>
                </tr>

                {{-- Spacing row between groups --}}
                <tr>
                    <td colspan="14" style="border: none; padding: 2px;"></td>
                </tr>
            @endforeach

            {{-- Grand Total --}}
            <tr style="font-weight: bold; background: #e0e0e0;">
                <td colspan="6" style="text-align: right;">รวมยอดทั้งหมด</td>
                <td>{{ number_format($addons_sum_price ?? 0) }}</td>
                <td>0</td>
                <td>0</td>
                <td></td>
                <td>{{ number_format($summary_receive_price ?? 0) }}</td>
                <td></td>
                <td>{{ number_format($summary_receive_price_after_discount ?? 0) }}</td>
                <td></td>
            </tr>
        @endif

    </tbody>
</table>

<!-- สรุปยอดรวมทั้งหมด (Summary Box) -->
<div style="margin-top:30px; width:100%;">
    <table style="width: 60%; margin: 0 auto; border: 2px solid #333; font-size: 12px;">
        <tr style="background: #f7f7f7; font-weight: bold;">
            <td colspan="2" style="text-align:center; border-bottom:2px solid #333;">สรุปยอดรวมประจำเดือน</td>
        </tr>
        <tr>
            <td style="text-align:right; padding-right: 20px;">ยอดรวมส่วนลด (Discount)</td>
            <td style="text-align:right; padding-right: 20px;">{{ number_format($discounts_summary ?? 0, 2) }} บาท</td>
        </tr>
        <tr>
            <td style="text-align:right; padding-right: 20px;">ยอดรวม Addon</td>
            <td style="text-align:right; padding-right: 20px;">{{ number_format($addons_sum_price ?? 0, 2) }} บาท</td>
        </tr>
        <tr>
            <td style="text-align:right; padding-right: 20px;">ยอดรับจริงก่อนหักส่วนลด</td>
            <td style="text-align:right; padding-right: 20px;">{{ number_format($summary_receive_price ?? 0, 2) }} บาท
            </td>
        </tr>
        <tr>
            <td style="text-align:right; padding-right: 20px;">ยอดรับจริงหลังหักส่วนลด</td>
            <td style="text-align:right; padding-right: 20px; font-weight:bold; color:#1a8917;">
                {{ number_format($summary_receive_price_after_discount ?? 0, 2) }} บาท</td>
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
                {{ number_format(($summary_type_payment_cash ?? 0) + ($summary_type_payment_credit ?? 0) + ($summary_type_payment_transfer ?? 0), 2) }}
                บาท</td>
        </tr>
    </table>
</div>
