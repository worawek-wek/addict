    {{-- {{dd($list_data['to'])}} --}}
    <table class="datatables-basic table dataTable no-footer dtr-column" id="DataTables_Table_0" aria-describedby="DataTables_Table_0_info">
        <thead class="border-top">
            <tr class=" table-info">
                <th class="text-center" tabindex="0" style="width: 10px;">
                    ลำดับ
                </th>
                <th class="text-center">
                    ชื่อลูกค้า
                </th>
                <th class="text-center" width='20%'>
                    จำนวนครั้ง
                </th>
                <th class="text-center" width='20%'>
                    สาขา
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($list_data as $key => $row)
            <tr class="odd">
                <td class="text-center">
                    {{ $list_data->firstItem()+$key }}
                </td>
                <td class="text-center">
                    {{ $row->name }}
                </td>
                <td class="text-center">
                    {{ $row->orders_count }}
                </td>
                <td class="text-center">
                    {{ $row->branch->name }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
<!-- END: Data List -->
<!-- BEGIN: Pagination -->
@include('admin/layout/pagination')
        