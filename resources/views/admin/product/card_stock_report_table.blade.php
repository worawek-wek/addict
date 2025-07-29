    {{-- {{dd($list_data['to'])}} --}}
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
                    รับเข้า
                </th>
                <th class="text-center">
                    จ่ายออก
                </th>
                <th class="text-center">
                    คงเหลือ
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
            <tr class="odd">
                <td class="text-center">
                    {{ $list_data->firstItem()+$key }}
                </td>
                <td class="text-center">
                    {{ date('d/m/Y',strtotime($row->created_at)) }}
                </td>
                <td class="text-center">
                    {{ $row->label }}
                </td>
                <td class="text-center">
                    {{ $row->product_name }}
                </td>
                <td class="text-center text-success">
                    {{ $row->type == 1 ?$row->quantity:''; }}
                </td>
                <td class="text-center text-success">
                    {{ $row->type == 2 ?$row->quantity:''; }}
                </td>
                <td class="text-center">
                    {{ $row->remain }}
                </td>
                <td class="text-center">
                    {{ $row->branch_name }}
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
        