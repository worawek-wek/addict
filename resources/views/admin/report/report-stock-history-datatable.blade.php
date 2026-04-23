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
                เพิ่ม
            </th>
            <th class="text-center">
                ลด
            </th>
            <th class="text-center">
                จำนวนสิ้นสุด
            </th>
        </tr>
    </thead>
    <tbody style="font-size: small;">
        @foreach ($stock_history as $key => $row)
        <tr class="odd">
            <td class="text-center" onclick="view({{ $row->id }})" style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#insurance">
                {{ $stock_history->firstItem()+$key }}
            </td>
            <td class="text-center" onclick="view({{ $row->id }})" style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#insurance">
                {{ $row->name }}
            </td>
            <td class="text-center" onclick="view({{ $row->id }})" style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#insurance">
                {{ $row->firstOrderOfDay->stock_before_quantity ?? 0 }}
            </td>
            <td class="text-center" onclick="view({{ $row->id }})" style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#insurance">
                {{ $row->order_has_products_sum_quantity }}
            </td>
            <td class="text-center" onclick="view({{ $row->id }})" style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#insurance">
                {{-- {{ $row->position->position_name }} --}}
            </td>
            <td class="text-center" onclick="view({{ $row->id }})" style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#insurance">
                {{-- {{ $row->branch->name }} --}}
            </td>
            <td class="text-center" onclick="view({{ $row->id }})" style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#insurance">
                {{ $row->lastOrderOfDay->stock_after_quantity ?? 0 }}
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