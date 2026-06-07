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
</style>
<span style="font-size: 12px; font-weight: bold;">รายงานคูปองพนักงาน วันที่ {{ $report_start_date }} -
    {{ $report_end_date }} , พิมพ์เมื่อ {{ date('d/m/Y H:i') }}</span>

<table class="table table-striped">
    <thead>
        <tr>
            <th class="text-center">#</th>
            <th class="text-center">วันที่</th>
            <th class="text-center">เวลา</th>
            <th class="text-center">ชื่อพนักงาน + คอร์ส</th>
            <th class="text-center">เวลาคอร์ส</th>
            <th class="text-center">รวมเงิน</th>
            <th class="text-center">ชื่อผู้ดูแล</th>
        </tr>
    </thead>
    <tbody>
        @php
            $grouped = $orderRooms->groupBy('ref_user_id');
            $globalIndex = 0;
            $grandTotal = 0;
            $grandCommission = 0;
            $grandNet = 0;
            $formatCourseDuration = function ($course) {
                $minutes = (int) ($course->minute ?? 0);

                if ($minutes <= 0 && preg_match('/(\d+)/', $course->name ?? '', $matches)) {
                    $minutes = (int) $matches[1];
                }

                if ($minutes <= 0) {
                    return '-';
                }

                $hours = intdiv($minutes, 60);
                $remainingMinutes = $minutes % 60;
                $parts = [];

                if ($hours > 0) {
                    $parts[] = $hours . ' ชม.';
                }

                if ($remainingMinutes > 0) {
                    $parts[] = $remainingMinutes . ' นาที';
                }

                return implode(' ', $parts);
            };
        @endphp

        @if ($orderRooms->isEmpty())
            <tr>
                <td colspan="8" class="text-center">ไม่มีข้อมูล</td>
            </tr>
        @else
            @foreach ($grouped as $userId => $groupOrders)
                @php
                    $firstOrder = $groupOrders->first();
                    $employeeName = $firstOrder->user->name ?? 'ไม่ระบุ';
                    $groupTotal = 0;
                    $groupCommission = 0;
                    $groupCount = $groupOrders->count();
                @endphp

                {{-- Group Header --}}
                <tr style="background-color:#d9d9d9; font-weight:bold;">
                    <td colspan="7" style="text-align:left; padding-left:8px;">
                        พนักงาน: {{ $employeeName }} ({{ $groupCount }} รายการ)
                    </td>
                </tr>

                @foreach ($groupOrders as $order)
                    @php
                        $globalIndex++;

                        // Look up commission
                        $ucKey = "{$order->ref_user_id}_{$order->ref_room_type_id}_{$order->service_laundry_cost}";
                        $rtcKey = "{$order->ref_room_type_id}_{$order->service_laundry_cost}";
                        $uc = $userCommissionMap->get($ucKey);
                        $rtc = $roomTypeCourseMap->get($rtcKey);

                        if ($uc && ($uc->price > 0 || $uc->coupon > 0)) {
                            $commission = $uc->price;
                        } elseif ($rtc) {
                            $commission = $rtc->commission;
                        } else {
                            $commission = 0;
                        }

                        $net = $order->total_price - $commission;
                        $groupTotal += $order->total_price;
                        $groupCommission += $commission;

                        $durStr = $formatCourseDuration($order->course);
                    @endphp
                    <tr>
                        <td>{{ $globalIndex }}</td>
                        <td>{{ date('d/m/Y', strtotime($order->created_at)) }}</td>
                        <td>{{ date('H:i', strtotime($order->created_at)) }}</td>
                        <td style="text-align:left;">{{ $order->user->name ?? '-' }} + {{ $order->course->name ?? '-' }}
                        </td>
                        <td>{{ $durStr }}</td>
                        <td style="text-align:right;">{{ number_format($commission) }}</td>
                        <td style="text-align:left;">{{ $order->seller->name ?? '-' }}</td>
                    </tr>
                @endforeach

                @php
                    $groupNet = $groupTotal - $groupCommission;
                    $grandTotal += $groupTotal;
                    $grandCommission += $groupCommission;
                    $grandNet += $groupNet;
                @endphp

                {{-- Group Subtotal --}}
                <tr style="background-color:#f0f0f0; font-weight:bold;">
                    <td colspan="4" style="text-align:right;">รวม {{ $employeeName }}</td>
                    <td style="text-align:center;">{{ $groupCount }}</td>
                    <td style="text-align:right;">{{ number_format($groupCommission) }}</td>
                    <td colspan="1"></td>
                </tr>
                <tr>
                    <td colspan="10" style="border:none; padding:3px;"></td>
                </tr>
            @endforeach

            {{-- Grand Total --}}
            <tr style="font-weight:bold; background:#e0e0e0;">
                <td colspan="3" style="text-align:right;">รวมยอดทั้งหมด</td>
                <td style="text-align:center;">{{ $orderRooms->count() }}</td>
                <td style="text-align:right;">{{ number_format($grandCommission) }}</td>
                <td colspan="2"></td>
            </tr>
        @endif
    </tbody>
</table>
