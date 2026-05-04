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
<span style="font-size: 12px; font-weight: bold;">รายงานสต็อกการ์ด(สินค้า) วันที่ {{ date('d/m/Y H:i น.', strtotime($startDate)) }} - {{ date('d/m/Y H:i น.', strtotime($endDate)) }} , พิมพ์เมื่อ {{ date('d/m/Y H:i น.') }}</span>

{{-- {{dd($list_data['to'])}} --}}
<table class="datatables-basic table dataTable no-footer dtr-column" id="DataTables_Table_0" aria-describedby="DataTables_Table_0_info">
    <thead class="border-top">
        <tr class=" table-info">
            <th class="text-center" tabindex="0" style="width: 40px;">
                ลำดับ
            </th>
            <th class="text-center">
                รายการ
            </th>
            <th class="text-center">
                จำนวนเริ่มต้น
            </th>
            <th class="text-center">
                ยอดขาย
            </th>
            <th class="text-center">
                นำเข้า
            </th>
            {{-- <th class="text-center">
                ลด
            </th> --}}
            <th class="text-center">
                จำนวนสิ้นสุด
            </th>
        </tr>
    </thead>
    <tbody style="font-size: small;">
        @foreach ($stock_history as $key => $row)
        <tr class="odd">
            <td class="text-center" onclick="view({{ $row->id }})" style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#insurance">
                {{ $key+1 }}
            </td>
            <td class="text-center" onclick="view({{ $row->id }})" style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#insurance">
                {{ $row->name }}
            </td>
            <td class="text-center" onclick="view({{ $row->id }})" style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#insurance">
                {{ $row->firstOrderOfDay->stock_before_quantity ?? $row->total_remain + $row->ready_for_sale_total_remain }}
            </td>
            <td class="text-center" onclick="view({{ $row->id }})" style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#insurance">
                {{ 0-$row->quantity_decrease ?? 0 }}
            </td>
            <td class="text-center" onclick="view({{ $row->id }})" style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#insurance">
                {{ $row->quantity_increase ?? 0 }}
            </td>
            {{-- <td class="text-center" onclick="view({{ $row->id }})" style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#insurance">
                 0
            </td> --}}
            <td class="text-center" onclick="view({{ $row->id }})" style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#insurance">
                {{ $row->lastOrderOfDay->stock_after_quantity ?? $row->total_remain + $row->ready_for_sale_total_remain }}
            </td>         
        </tr>
        @endforeach
    </tbody>
</table>