<table class="datatables-basic table dataTable no-footer dtr-column" id="commission-table-view" aria-describedby="commission-table-view_info">
    <thead class="border-top">
        <tr class="table-info">
            <th class="text-center" style="width: 10px;">#</th>
            <th class="text-center">ชื่อพนักงาน</th>
            <th class="text-center">จำนวนเงินคอมมิชชั่น</th>
            <th class="text-center">สาขา</th>
            <th class="text-center">ชื่อตำแหน่ง</th>
            {{-- <th class="text-center">ค่าเชียร์</th> --}}
            {{-- <th class="text-center">ดู Order</th> --}}
        </tr>
    </thead>
    <tbody id="commission-table-body">
        @foreach($list_data as $i => $staff)
        <tr>
            <td class="text-center">{{ $i + 1 }}</td>
            <td class="text-center">{{ $staff->name }}{{ $staff->nickname ? ' (' . $staff->nickname . ')' : '' }}</td>
            @php
                $total_price = \App\Models\OrderHasProduct::whereHas('order', function ($query) use ($staff) {
                                                            $query->where('ref_seller_id', $staff->id)
                                                                    ->whereIn('type', [3]);
                                                        })
                                                        ->whereBetween('created_at', [$start_date, $end_date])
                                                        ->sum('price') ?? 0;
                $sale_commission = \App\Models\SalesCommissionTier::where('type', 2)->where('min_sales_amount', '<=', $total_price)->where('max_sales_amount', '>=', $total_price)->first(); // ดึงการตั้งค่า คอมมิชชั่น ที่ตรงกับยอดขายรวม
                if($sale_commission && $sale_commission->commission_by == 1){ // ถ้าเป็น เปอร์เซ็นต์
                    $sale_commission->commission_price = $sale_commission->commission_rate*$total_price/100; // เปอร์เซ็นต์ * ยอดขายรวม / 100
                }
            @endphp
            <td class="text-end" style="padding-right: 12%;">{{ number_format(@$sale_commission->commission_price ?? 0, 2) }} บาท</td>
            <td class="text-center">{{ $staff->branch->name }}</td>
            <td class="text-center">{{ $staff->position->position_name }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@include('admin/layout/pagination')
