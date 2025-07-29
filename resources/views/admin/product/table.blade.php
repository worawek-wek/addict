    {{-- {{dd($list_data['to'])}} --}}
    <table class="datatables-basic table dataTable no-footer dtr-column" id="DataTables_Table_0" aria-describedby="DataTables_Table_0_info">
        <thead class="border-top">
            <tr class=" table-info">
                <th class="text-center" tabindex="0" style="width: 40px;">
                    ลำดับ
                </th>
                <th class="text-center">
                    คงเหลือ
                </th>
                <th class="text-center">
                    ชื่อสินค้า
                </th>
                <th class="text-center">
                    ราคาขาย
                </th>
                <th class="text-center">
                    ต้นทุน
                </th>
                <th class="text-center">
                    สาขา
                </th>
                <th class="text-center">
                    หมายเหตุ
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($list_data as $key => $row)
            <tr class="odd" style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#insurance" onclick="view({{ $row->id }})">
                <td class="text-center">
                    {{ $list_data->firstItem()+$key }}
                </td>
                <td class="text-center">
                    {{ \App\Models\CardStocks::where('ref_product_id', $row->id)->latest()->value('remain') ?? 0 }}

                </td>
                <td class="text-center">
                    {{ $row->name }}
                </td>
                <td class="text-center">
                    {{ $row->price }}
                </td>
                <td class="text-center">
                    {{ $row->cost }}
                </td>
                <td class="text-center">
                    {{ $row->branch->name }}
                </td>
                <td class="text-center">
                    {{ $row->remark }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
<!-- END: Data List -->
<!-- BEGIN: Pagination -->
@include('admin/layout/pagination')
        