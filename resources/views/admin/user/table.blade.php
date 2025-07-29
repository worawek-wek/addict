    {{-- {{dd($list_data['to'])}} --}}
    <table class="datatables-basic table dataTable no-footer dtr-column" id="DataTables_Table_0" aria-describedby="DataTables_Table_0_info">
        <thead class="border-top">
            <tr class=" table-info">
                <th class="text-center" tabindex="0" style="width: 40px;">
                    ลำดับ
                </th>
                <th class="text-center">
                    รูปภาพ
                </th>
                <th class="text-center">
                    ชื่อพนักงาน
                </th>
                <th class="text-center">
                    ชื่อเล่น
                </th>
                <th class="text-center">
                    ตำแหน่ง
                </th>
                <th class="text-center">
                    สาขา
                </th>
                <th class="text-center">
                    สถานะ
                </th>
                <th class="text-center">
                    หมายเหตุ
                </th>
                <th class="text-center" colspan="2">
                    ดำเนินการ
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($list_data as $key => $row)
            <tr class="odd" style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#insurance">
                <td class="text-center" onclick="view({{ $row->id }})">
                    {{ $list_data->firstItem()+$key }}
                </td>
                <td class="text-center" onclick="view({{ $row->id }})">
                    <img src="/upload/user/{{ $row->image_name }}" alt="" width="40px">
                </td>
                <td class="text-center" onclick="view({{ $row->id }})">
                    {{ $row->name }}
                </td>
                <td class="text-center" onclick="view({{ $row->id }})">
                    {{ $row->nickname }}
                </td>
                <td class="text-center" onclick="view({{ $row->id }})">
                    {{ $row->position->position_name }}
                </td>
                <td class="text-center" onclick="view({{ $row->id }})">
                    {{ $row->branch->name }}
                </td>
                    <td class="text-center text-success">
                @if ($row->ref_status_id == 1)
                        ออนไลน์
                @endif
                </td>
                <td class="text-center" onclick="view({{ $row->id }})">
                    {{ $row->remark }}
                </td>
                <td class="text-center">
                    <div class="d-flex justify-content-center align-items-center">
                        <!-- Toggle Switch -->
                        <label class="switch switch-success mb-0">
                            <input type="checkbox" class="switch-input"
                                onchange="changeStatus({{ $row->id }}, this.checked ? 1 : 0, this)"
                                @if ($row->ref_status_id == 1) checked @endif
                            />
                            <span class="switch-toggle-slider">
                                <span class="switch-on"><i class="ti ti-check"></i></span>
                                <span class="switch-off"><i class="ti ti-x"></i></span>
                            </span>
                        </label>
                
                    </td>
                    <td class="text-center">
                        <!-- ปุ่มลบ -->
                        <a href="javascript:;"
                           onclick='delete_view({{ $row->id }})'
                           data-bs-toggle="modal"
                           data-bs-target="#delete_confirmation_modal"
                           class="text-danger"
                           style="display: flex; align-items: center; gap: 4px;">
                            <i class="fa fa-trash" aria-hidden="true"></i>
                            ลบ
                        </a>
                    </div>
                </td>
                
                                
            </tr>
            @endforeach
        </tbody>
    </table>
<!-- END: Data List -->
<!-- BEGIN: Pagination -->
@include('admin/layout/pagination')
        