<style>
    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 11px;
        table-layout: fixed;
    }

    th,
    td {
        border: 1px solid #000;
        padding: 5px 4px;
        text-align: center;
        word-wrap: break-word;
    }

    thead th {
        background-color: #f0f0f0;
        font-weight: bold;
    }

    tbody td {
        font-size: 12px;
        line-height: 1.35;
    }

    .col-order {
        width: 7%;
    }

    .col-product {
        width: 21%;
    }

    .col-number {
        width: 9%;
    }
</style>
<span style="font-size: 13px; font-weight: bold;">รายงานสต็อกการ์ด(สินค้า) วันที่ {{ date('d/m/Y H:i น.', strtotime($startDate)) }} - {{ date('d/m/Y H:i น.', strtotime($endDate)) }} , พิมพ์เมื่อ {{ date('d/m/Y H:i น.') }}</span>

{{-- {{dd($list_data['to'])}} --}}
<table class="datatables-basic table dataTable no-footer dtr-column" id="DataTables_Table_0" aria-describedby="DataTables_Table_0_info">
    <colgroup>
        <col class="col-order">
        <col class="col-product">
        <col class="col-number">
        <col class="col-number">
        <col class="col-number">
        <col class="col-number">
        <col class="col-number">
        <col class="col-number">
        <col class="col-number">
        <col class="col-number">
    </colgroup>
    <thead class="border-top">
        <tr class=" table-info">
            <th class="text-center" tabindex="0" style="width: 40px;">
                ลำดับ
            </th>
            <th class="text-center">
                รายการ
            </th>
            <th class="text-center">
                สต็อกหลัก
            </th>
            <th class="text-center">
                นำเข้า
            </th>
            <th class="text-center">
                เบิก
            </th>
            <th class="text-center">
                สต็อกขาย
            </th>
            <th class="text-center">
                ยอดขาย
            </th>
            <th class="text-center">
                คงเหลือ(สต็อกขาย)
            </th>
            <th class="text-center">
                นำออก(สต็อกหลัก)
            </th>
            <th class="text-center">
                คงเหลือ(สต็อกหลัก)
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($stock_history as $key => $row)
        @php
            $mainStockClosing = optional($row->historyStocksLatest)->stock_after_quantity ?? $row->total_remain;
            $readyStockClosing = optional($row->historyStocksLatest)->stock_ready_for_sale_after_quantity ?? $row->ready_for_sale_total_remain;
            $quantityIncrease = $row->quantity_increase ?? 0;
            $quantityExport = $row->quantity_export ?? 0;
            $quantityDecrease = $row->quantity_decrease ?? 0;
            $withdrawQuantity = $row->total_withdraw_quantity ?? 0;
            $mainStockOpening = $mainStockClosing - $quantityIncrease + $quantityExport + $withdrawQuantity;
            $readyStockOpening = $readyStockClosing - $withdrawQuantity + $quantityDecrease;
        @endphp
        <tr class="odd">
            <td class="text-center" onclick="view({{ $row->id }})" style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#insurance">
                {{ $key+1 }}
            </td>
            <td class="text-center">
                {{ $row->name }}
            </td>
            <td class="text-center">
                {{ $mainStockOpening }}
            </td>
            <td class="text-center">
                {{ $quantityIncrease }}
            </td>
            <td class="text-center">
                {{ $withdrawQuantity }}
            </td>
            <td class="text-center">
                {{ $readyStockOpening }}
            </td>
            <td class="text-center">
                {{ 0 - $quantityDecrease }}
            </td>
            <td class="text-center">
                {{ $readyStockClosing }}
            </td>      
            <td class="text-center">
                {{ 0 - $quantityExport }}
            </td>
            <td class="text-center">
                {{ $mainStockClosing }}
            </td>     
        </tr>
        @endforeach
    </tbody>
</table>
