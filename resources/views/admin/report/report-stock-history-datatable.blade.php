{{-- {{dd($list_data['to'])}} --}}
@include('admin.report.partials.selected-date-range')

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
    <tbody style="font-size: small;">
        @foreach ($stock_history as $key => $row)
        <tr class="odd">
            <td class="text-center">
                {{ $stock_history->firstItem()+$key }}
            </td>
            <td class="text-center">
                {{ $row->name }}
            </td>
            <td class="text-center">
                {{-- {{ $row->firstOrderOfDay->stock_before_quantity ?? $row->total_remain + $row->ready_for_sale_total_remain }} --}}
                {{ optional($row->historyStocksOldest)->stock_before_quantity ?? $row->total_remain }}
            </td>
            <td class="text-center">
                {{ $row->quantity_increase ?? 0 }}
            </td>
            <td class="text-center">
                {{ $row->total_withdraw_quantity ?? 0 }}
            </td>
            <td class="text-center">
                {{ optional($row->historyStocksMaxReady)->stock_ready_for_sale_before_quantity ?? $row->ready_for_sale_total_remain }}
                {{-- {{ $row->total_withdraw_quantity ?? 0 }} --}}
            </td>
            <td class="text-center">
                {{ 0-$row->quantity_decrease ?? 0 }}
            </td>
            <td class="text-center">
                {{ optional($row->historyStocksLatest)->stock_ready_for_sale_after_quantity ?? $row->ready_for_sale_total_remain }}
            </td>      
            <td class="text-center">
                {{ 0-$row->quantity_export ?? 0 }}
            </td>
            <td class="text-center">
                {{ optional($row->historyStocksLatest)->stock_after_quantity ?? $row->total_remain }}
                {{-- {{ $row->lastOrderOfDay->stock_after_quantity ?? $row->total_remain + $row->ready_for_sale_total_remain }} --}}
            </td>     
        </tr>
        @endforeach
    </tbody>
</table>
<!-- END: Data List -->
<!-- BEGIN: Pagination -->

{{-- Pagination --}}
<div class="mt-3">
    {!! $stock_history->links('vendor.pagination.custom') !!}
</div>

<script>
document.querySelectorAll('.sort-item').forEach((el) => {
    new TomSelect(el, {
        create: false,
        maxItems: 1,
        allowEmptyOption: true,
        sortField: { field: "text", direction: "asc" }
    });
});
</script>
