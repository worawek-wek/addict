@php
    $grouped = $orderRooms->getCollection()->groupBy('ref_user_id');
    $globalIndex = ($orderRooms->currentPage() - 1) * $orderRooms->perPage();
    $grandCommission = 0;
    $grandCount = 0;
@endphp

@include('admin.report.partials.selected-date-range')

@if ($orderRooms->isEmpty())
    <div class="text-center py-4">ไม่มีข้อมูล</div>
@else
    @foreach ($grouped as $userId => $groupOrders)
        @php
            $firstOrder   = $groupOrders->first();
            $employeeName = $firstOrder->user->name ?? 'ไม่ระบุ';
            $groupCount   = $groupOrders->count();
            $groupCommission = 0;
        @endphp

        {{-- Group Header --}}
        <div class="d-flex align-items-center px-3 py-2 mb-1 mt-3 rounded"
             style="background:#e9ecef; font-weight:600; font-size:13px;">
            <i class="ti ti-user me-2"></i>
            พนักงาน: {{ $employeeName }}
            <span class="ms-2 badge bg-secondary">{{ $groupCount }} รายการ</span>
        </div>

        <table class="table table-bordered table-sm mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width:5%;">#</th>
                    <th style="width:9%;">วันที่</th>
                    <th style="width:7%;">เวลา</th>
                    <th style="width:30%;">ชื่อพนักงาน + ดื่ม</th>
                    <th style="width:10%; text-align:right;">คอมมิชชั่น</th>
                    <th style="width:20%;">ชื่อผู้ดูแล</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($groupOrders as $order)
                    @php
                        // $globalIndex++;

                        // $ucKey  = "{$order->ref_user_id}_{$order->ref_room_type_id}_{$order->service_laundry_cost}";
                        // $rtcKey = "{$order->ref_room_type_id}_{$order->service_laundry_cost}";
                        // $uc  = $userCommissionMap->get($ucKey);
                        // $rtc = $roomTypeCourseMap->get($rtcKey);

                        // if ($uc && ($uc->price > 0)) {
                        //     $commission = $uc->price;
                        // } elseif ($rtc) {
                        //     $commission = $rtc->commission;
                        // } else {
                        //     $commission = 0;
                        // }
                        $commission = 0;

                    @endphp
                    <tr>
                        <td>{{ $globalIndex }}</td>
                        <td>{{ date('d/m/Y', strtotime($order->created_at)) }}</td>
                        <td>{{ date('H:i', strtotime($order->created_at)) }}</td>
                        <td>{{ $order->user->name ?? '-' }} + 
                            @foreach ($order->drinks as $drinks)
                                {{ $drinks->drink->name ?? '-' }}
                                @php
                                    $commission += $drinks->drink->commission*$drinks->quantity;
                                    $groupCommission += $commission;
                                @endphp
                            @endforeach
                        </td>
                        <td class="text-end">{{ number_format($commission) }}</td>
                        <td>{{ $order->seller->name ?? '-' }}</td>
                    </tr>
                @endforeach

                @php
                    $grandCommission += $groupCommission;
                    $grandCount += $groupCount;
                @endphp

                {{-- Group Subtotal --}}
                <tr class="fw-bold" style="background:#f8f9fa; border-top:2px solid #adb5bd;">
                    <td colspan="4" class="text-end">รวม {{ $employeeName }}</td>
                    <td class="text-end" style="text-decoration:underline;">{{ number_format($groupCommission) }}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    @endforeach

    {{-- Grand Total --}}
    <div class="d-flex align-items-center px-3 py-2 mt-3 rounded fw-bold"
         style="background:#dee2e6; font-size:13px;">
        <span>รวมยอดทั้งหมด</span>
        <span class="ms-3">{{ $grandCount }} รายการ</span>
        <span class="ms-auto">ค่าดื่ม {{ number_format($grandCommission) }} บาท</span>
    </div>

    {{-- Summary Section --}}
    <div class="card mt-4">
        <div class="card-header fw-bold">สรุปยอดรวมตามพนักงาน</div>
        <div class="card-body p-0">
            <table class="table table-bordered mb-0">
                <thead class="table-dark">
                    <tr>
                        <th style="width:40%;">ชื่อพนักงาน</th>
                        <th style="width:12%; text-align:center;">จำนวน</th>
                        <th style="width:18%; text-align:right;">คอมมิชชั่น (บาท)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($grouped as $userId => $groupOrders)
                        @php
                            $fo   = $groupOrders->first();
                            $eName = $fo->user->name ?? 'ไม่ระบุ';
                            $eCount = $groupOrders->count();
                            $eCommission = $groupOrders->sum(function ($o) use ($userCommissionMap, $roomTypeCourseMap) {
                                $ucKey  = "{$o->ref_user_id}_{$o->ref_room_type_id}_{$o->service_laundry_cost}";
                                $rtcKey = "{$o->ref_room_type_id}_{$o->service_laundry_cost}";
                                $uc  = $userCommissionMap->get($ucKey);
                                $rtc = $roomTypeCourseMap->get($rtcKey);
                                if ($uc && ($uc->price > 0 || $uc->coupon > 0)) return $uc->price;
                                if ($rtc) return $rtc->commission;
                                return 0;
                            });
                        @endphp
                        <tr>
                            <td>{{ $eName }}</td>
                            <td class="text-center">{{ $eCount }}</td>
                            <td class="text-end fw-semibold">{{ number_format($eCommission) }}</td>
                        </tr>
                    @endforeach
                    <tr class="fw-bold" style="background:#e9ecef;">
                        <td class="text-end">รวมทั้งสิ้น</td>
                        <td class="text-center">{{ $grandCount }}</td>
                        <td class="text-end">{{ number_format($grandCommission) }}</td>
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
