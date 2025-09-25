<div>
    <h5>รายละเอียด Order #{{ $order->order_number }}</h5>
    <table class="table table-bordered mb-3">
        <tr>
            <th>วันที่จอง</th>
            <td>{{ $order->booking_date }}</td>
        </tr>
        <tr>
            <th>ลูกค้า</th>
            <td>{{ $order->customer ? $order->customer->name : '-' }}</td>
        </tr>
        <tr>
            <th>สาขา</th>
            <td>{{ $order->branch ? $order->branch->name : '-' }}</td>
        </tr>
        <tr>
            <th>พนักงานนวด</th>
            <td>{{ $order->user ? $order->user->name : '-' }}</td>
        </tr>
        <tr>
            <th>ยอดขาย</th>
            <td>{{ number_format($order->total_price, 2) }} บาท</td>
        </tr>
        @if($order->user)
        <tr>
            <th>คำนวณค่าคอมมิชชั่นของพนักงานนวด</th>
            <td>
                @php
                    $commission_value = 0;
                    $breakdown = [];
                    // 1. จาก AddonOption
                    if ($order->user && $order->addons && $order->addons->count()) {
                        foreach ($order->addons as $addonItem) {
                            $commission = \App\Models\MassageCommission::where('ref_user_id', $order->user->id)
                                ->where('addon_options_id', $addonItem->ref_option_id)
                                ->where('ref_branch_id', $order->ref_branch_id)
                                ->first();
                            if (!$commission) {
                                $commission = \App\Models\MassageCommission::whereNull('ref_user_id')
                                    ->where('addon_options_id', $addonItem->ref_option_id)
                                    ->where('ref_branch_id', $order->ref_branch_id)
                                    ->first();
                            }
                            if ($commission) {
                                if ($commission->commission_amount) {
                                    $commission_value += $commission->commission_amount;
                                    $breakdown[] = 'Addon ' . ($addonItem->option ? $addonItem->option->name : '-') . ': ' . number_format($commission->commission_amount, 2) . ' บาท';
                                } elseif ($commission->commission_percent) {
                                    $val = ($commission->commission_percent / 100) * $addonItem->price;
                                    $commission_value += $val;
                                    $breakdown[] = 'Addon ' . ($addonItem->option ? $addonItem->option->name : '-') . ': ' . $commission->commission_percent . '% x ' . number_format($addonItem->price, 2) . ' = ' . number_format($val, 2) . ' บาท';
                                }
                            }
                        }
                    }
                    // 2. จาก service_duration
                    if ($order->user && $order->service_laundry_cost) {
                        $duration = null;
                        switch ($order->service_laundry_cost) {
                            case 'forty_minutes': $duration = 40; break;
                            case 'sixty_minutes': $duration = 60; break;
                            case 'ninety_minutes': $duration = 90; break;
                        }
                        if ($duration) {
                            $commission = \App\Models\MassageCommission::where('ref_user_id', $order->user->id)
                                ->where('service_duration', $duration)
                                ->where('ref_branch_id', $order->ref_branch_id)
                                ->first();
                            if (!$commission) {
                                $commission = \App\Models\MassageCommission::whereNull('ref_user_id')
                                    ->where('service_duration', $duration)
                                    ->where('ref_branch_id', $order->ref_branch_id)
                                    ->first();
                            }
                            if ($commission) {
                                if ($commission->commission_amount) {
                                    $commission_value += $commission->commission_amount;
                                    $breakdown[] = 'บริการ ' . $duration . ' นาที: ' . number_format($commission->commission_amount, 2) . ' บาท';
                                } elseif ($commission->commission_percent) {
                                    $room_price = 0;
                                    if ($order->room) {
                                        if ($duration == 40) $room_price = $order->room->forty_minutes;
                                        if ($duration == 60) $room_price = $order->room->sixty_minutes;
                                        if ($duration == 90) $room_price = $order->room->ninety_minutes;
                                    }
                                    $staff_salary = $order->user->salary ?? 0;
                                    $commission_base = $room_price + $staff_salary;
                                    $val = ($commission->commission_percent / 100) * $commission_base;
                                    $commission_value += $val;
                                    $breakdown[] = 'บริการ ' . $duration . ' นาที: ' . $commission->commission_percent . '% x (' . number_format($room_price, 2) . ' + ' . number_format($staff_salary, 2) . ') = ' . number_format($val, 2) . ' บาท';
                                }
                            }
                        }
                    }
                @endphp
                <strong>{{ number_format($commission_value, 2) }} บาท</strong>
                @if(count($breakdown))
                    <ul class="mt-2 mb-0" style="font-size: 14px;">
                        @foreach($breakdown as $line)
                            <li>{{ $line }}</li>
                        @endforeach
                    </ul>
                @else
                    <span class="text-muted">ไม่มีข้อมูลรายละเอียดการคำนวณ</span>
                @endif
            </td>
        </tr>
        @endif
    </table>
    <h6 class="mt-3">Addon Options</h6>
    <table class="table table-sm table-striped">
        <thead>
            <tr>
                <th>ชื่อ Addon</th>
                <th>ราคา</th>
            </tr>
        </thead>
        <tbody>
            @forelse($order->addons as $addon)
            <tr>
                <td>{{ $addon->option ? $addon->option->name : '-' }}</td>
                <td>{{ $addon->option ? $addon->option->price : '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="2" class="text-center">ไม่มีข้อมูล</td></tr>
            @endforelse
        </tbody>
    </table>
    <h6 class="mt-3">Products</h6>
    <table class="table table-sm table-striped">
        <thead>
            <tr>
                <th>ชื่อสินค้า</th>
                <th>จำนวน</th>
                <th>ราคา</th>
            </tr>
        </thead>
        <tbody>
            @forelse($order->products as $product)
            <tr>
                <td>{{ $product->product ? $product->product->name : '-' }}</td>
                <td>{{ $product->quantity ?? '-' }}</td>
                <td>{{ number_format($product->price ?? 0, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="3" class="text-center">ไม่มีข้อมูล</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
