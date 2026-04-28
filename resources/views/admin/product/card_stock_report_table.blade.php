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
                {{-- <th class="text-center">
                    จัดการ
                </th> --}}
            </tr>
        </thead>
        <tbody>
            @foreach ($list_data as $key => $row)
            <tr class="odd">
                <td class="text-center" onclick="view({{ $row->id }})" style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#insurance">
                    {{ $list_data->firstItem()+$key }}
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
                    {{ $row->type == 1 ?$row->quantity:''; }}
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
                {{-- <td>
                    <div class="d-flex align-items-center gap-2">
                        <button class="btn btn-warning btn-sm px-2"
                                type="button"
                                data-bs-toggle="modal"
                                data-bs-target="#modal-commission-room"
                                onclick="commission_room({{ $row->id }}, 'room')">
                            นำออก
                        </button>
                    </div>
                </td> --}}
            </tr>
            @endforeach
        </tbody>
    </table>
<!-- END: Data List -->
<!-- BEGIN: Pagination -->
@include('admin/layout/pagination')
