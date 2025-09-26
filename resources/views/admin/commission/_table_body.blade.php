@forelse($staffData as $index => $staff)
<tr>
    <td class="text-center">{{ $index + 1 }}</td>
    <td class="text-center">
        {{ $staff['name'] }}
        @if(!empty($staff['nickname']))
            ({{ $staff['nickname'] }})
        @endif
    </td>
    <td class="text-center">{{ $staff['branch'] ?? '-' }}</td>
    <td class="text-center">{{ $staff['position'] ?? '-' }}</td>
    <td class="text-center">{{ number_format($staff['commission'], 2) }} บาท</td>
    <td class="text-center">{{ isset($staff['cheer_charge']) ? number_format($staff['cheer_charge'], 2) . ' บาท' : '0.00 บาท' }}</td>
    <td class="text-center">
        @php
            $isMassage = request()->routeIs('commission.view_massage');
            $orderRoute = $isMassage ? route('commission.massage_orders') : route('commission.sales_orders');
        @endphp
        <a href="#" class="btn btn-sm btn-outline-info order-link-btn" data-base-url="{{ $orderRoute }}" data-user-id="{{ $staff['id'] }}" target="_blank">
            ดู Order
        </a>
    </td>
</tr>
@empty
<tr>
    <td colspan="5" class="text-center">- ไม่มีข้อมูล -</td>
</tr>
@endforelse
