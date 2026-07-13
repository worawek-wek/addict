<style>
    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 10px;
    }
    th, td {
        border: 1px solid #000;
        padding: 4px 6px;
        text-align: center;
    }
    thead th {
        background-color: #f0f0f0;
        font-weight: bold;
    }
</style>
    <table class="datatables-basic table dataTable no-footer dtr-column" id="DataTables_Table_0" aria-describedby="DataTables_Table_0_info">
        <thead class="border-top">
            <tr class=" table-info">
                <th class="text-center" tabindex="0" style="width: 40px;">
                    ลำดับ
                </th>
                <th class="text-center">
                    วันที่
                </th>
                <th class="text-center">
                    รายการ
                </th>
                <th class="text-center">
                    สินค้า
                </th>
                <th class="text-center">
                    จำนวนก่อนรับเข้า
                </th>
                <th class="text-center">
                    รับเข้า
                </th>
                <th class="text-center">
                    จำนวนหลังรับเข้า
                </th>
                {{-- <th class="text-center">
                    จ่ายออก
                </th> --}}
                <th class="text-center">
                    เบิกสินค้า
                </th>
                <th class="text-center">
                    นำสินค้าออก
                </th>
                <th class="text-center">
                    คงเหลือ
                </th>
                <th class="text-center">
                    สาขา
                </th>
                <th class="text-center">
                    ราคาต้นทุน
                </th>
                <th class="text-center">
                    หมายเหตุ
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($list_data as $key => $row)
            @php
                $importHistory = \App\Models\HistoryStock::where('ref_product_id', $row->ref_product_id)
                    ->where('quantity_type', 1)
                    ->where('quantity', $row->quantity)
                    ->whereBetween('created_at', [
                        \Carbon\Carbon::parse($row->created_at)->copy()->subSeconds(10),
                        \Carbon\Carbon::parse($row->created_at)->copy()->addSeconds(10),
                    ])
                    ->orderByRaw('ABS(TIMESTAMPDIFF(SECOND, created_at, ?))', [$row->created_at])
                    ->first();

                $stockBeforeQuantity = $importHistory->stock_before_quantity ?? $row->stock_before_quantity;
                $stockAfterQuantity = $importHistory->stock_after_quantity ?? $row->stock_after_quantity;
            @endphp
            <tr class="odd">
                <td class="text-center">
                    {{ $key+1 }}
                </td>
                <td class="text-center" onclick="view({{ $row->id }})" style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#insurance">
                    {{ date('d/m/Y',strtotime($row->created_at)) }}
                </td>
                <td class="text-center" onclick="view({{ $row->id }})" style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#insurance">
                    {{ $row->label }}
                </td>
                <td class="text-center" onclick="view({{ $row->id }})" style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#insurance">
                    {{ $row->product_name }}
                </td>
                <td class="text-center text-success" onclick="view({{ $row->id }})" style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#insurance">
                    {{ $stockBeforeQuantity }}
                </td>
                <td class="text-center text-success" onclick="view({{ $row->id }})" style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#insurance">
                    {{ $row->type == 1 ?$row->quantity:''; }}
                </td>
                <td class="text-center text-success" onclick="view({{ $row->id }})" style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#insurance">
                    {{ $stockAfterQuantity }}
                </td>
                {{-- <td class="text-center text-success" onclick="view({{ $row->id }})" style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#insurance">
                    {{ $row->type == 2 ?$row->quantity:''; }}
                </td> --}} 
                <td class="text-center text-danger" onclick="view({{ $row->id }})" style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#insurance">
                    {{ $row->stock_ready_for_sales()->sum('qty') }}
                </td>
                <td class="text-center text-danger" onclick="view({{ $row->id }})" style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#insurance">
                    {{ $row->export_stocks()->sum('quantity') }}
                </td>
                <td class="text-center text-warning" onclick="view({{ $row->id }})" style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#insurance">
                    {{ $row->remain }}
                </td>
                <td class="text-center" onclick="view({{ $row->id }})" style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#insurance">
                    {{ $row->branch_name }}
                </td>
                    <td class="text-center" onclick="view({{ $row->id }})" style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#insurance">
                        {{ number_format($row->cost_price, 2) }}
                    </td>
                <td class="text-center">
                    {{ $row->remark }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
