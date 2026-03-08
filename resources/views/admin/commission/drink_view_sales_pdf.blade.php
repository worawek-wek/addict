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

    tr.subtotal-row td {
        border-top: 2px solid #555;
        background-color: #fafafa;
        font-weight: bold;
    }

    tr.grand-row td {
        border-top: 2px solid #000;
        border-left: none;
        border-right: none;
        border-bottom: none;
        font-weight: bold;
    }

    .summary-section {
        margin-top: 24px;
        padding: 12px;
        background-color: #f9f9f9;
        border: 2px solid #333;
        border-radius: 4px;
    }

    .summary-title {
        font-size: 14px;
        font-weight: bold;
        text-align: center;
        margin-bottom: 10px;
        color: #333;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .summary-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 11px;
    }

    .summary-table th {
        background-color: #333;
        color: #fff;
        padding: 8px;
        font-weight: bold;
        border: 1px solid #000;
    }

    .summary-table td {
        padding: 6px 8px;
        border: 1px solid #666;
        background-color: #fff;
    }

    .summary-table tr:hover td {
        background-color: #f0f0f0;
    }
</style>

<div style="text-align:center; font-size:16px; font-weight:bold; margin-bottom:10px;">
    รายงานค่าคอม (ดื่ม)
</div>
<div style="font-size:11px; margin-bottom:8px;">
    วันที่ {{ date('d/m/Y', strtotime($start_date)) }} - {{ date('d/m/Y', strtotime($end_date)) }} , พิมพ์เมื่อ {{ date('d/m/Y H:i') }} น.
</div>

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
            <td align="right" style="padding-right: 12%;">{{ number_format(@$sale_commission->commission_price ?? 0, 2) }} บาท</td>
            <td class="text-center">{{ $staff->branch->name }}</td>
            <td class="text-center">{{ $staff->position->position_name }}</td>
        </tr>
        @endforeach
    </tbody>
</table>