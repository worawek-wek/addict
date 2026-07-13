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
            @if ($canViewAllBranches ?? false)
                <th class="text-center">
                    สาขา
                </th>
            @endif
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
        @forelse ($stock_history as $key => $row)
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
            <td class="text-center">
                {{ $stock_history->firstItem()+$key }}
            </td>
            <td class="text-center">
                {{ $row->name }}
            </td>
            @if ($canViewAllBranches ?? false)
                <td class="text-center">
                    {{ $row->branch->name ?? '-' }}
                </td>
            @endif
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
        @empty
            <tr>
                <td class="text-center" colspan="{{ ($canViewAllBranches ?? false) ? 11 : 10 }}">ไม่มีข้อมูล</td>
            </tr>
        @endforelse
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
