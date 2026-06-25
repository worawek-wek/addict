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
                    ชื่อพนักงาน(ชื่อเล่น)
                </th>
                <th class="text-center">
                    รหัสพนักงาน
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
                <th class="text-center" style="width: 202px;">
                    ดำเนินการ
                </th>
            </tr>
        </thead>
        <tbody style="font-size: small;">
            @foreach ($list_data as $key => $row)
            <tr class="odd">
                <td class="text-center" onclick="view({{ $row->id }})" style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#insurance">
                    {{ $list_data->firstItem()+$key }}
                </td>
                <td class="text-center" onclick="view({{ $row->id }})" style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#insurance">
                    @php
                        $imagePath = $row->image_name && file_exists(public_path('upload/user/' . $row->image_name))
                            ? asset('upload/user/' . $row->image_name)
                            : asset('not-found-image.png');
                    @endphp
                    <img src="{{ $imagePath }}" alt="" width="55px">
                </td>
                <td class="text-center" onclick="view({{ $row->id }})" style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#insurance">
                    <b>{{ $row->name }}</b><br>
                    {{ @$row->nickname ? "($row->nickname)": ""; }}
                </td>
                <td class="text-center" onclick="view({{ $row->id }})" style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#insurance">
                    {{ $row->user_id }}
                </td>
                <td class="text-center" onclick="view({{ $row->id }})" style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#insurance">
                    {{ $row->position->position_name }}
                </td>
                <td class="text-center" onclick="view({{ $row->id }})" style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#insurance">
                    {{ $row->branch->name }}
                </td>
                    <td class="text-center text-success">
                @if ($row->ref_status_id == 1)
                        ออนไลน์
                @endif
                </td>
                <td class="text-center" onclick="view({{ $row->id }})" style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#insurance">
                    {{ $row->remark }}
                </td>
                <td>
                    <div class="d-flex align-items-center gap-2">

                        <!-- ซ้าย : ค่ามือ -->
                        <div class="d-flex flex-column gap-1">
                            <button class="btn btn-warning btn-sm px-2"
                                    type="button"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modal-commission-room"
                                    onclick="commission_room({{ $row->id }}, 'room')">
                                <i class="ti ti-pencil fs-6 me-1"></i> ค่ามือ(ห้อง)
                            </button>

                            <button class="btn btn-primary btn-sm px-2"
                                    type="button"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modal-commission-option"
                                    onclick="commission_option({{ $row->id }}, 'option')">
                                <i class="ti ti-pencil fs-6 me-1"></i> ค่ามือ(Option)
                            </button>
                        </div>

                        <!-- ขวา : switch -->
                        <label class="switch switch-success mb-0">
                            <input type="checkbox" class="switch-input"
                                onchange="changeStatus({{ $row->id }}, this.checked ? 1 : 0, this)"
                                @if ($row->ref_status_id == 1) checked @endif>
                            <span class="switch-toggle-slider">
                                <span class="switch-on"><i class="ti ti-check"></i></span>
                                <span class="switch-off"><i class="ti ti-x"></i></span>
                            </span>
                        </label>
                        <a href="javascript:;"
                            onclick='delete_view({{ $row->id }})'
                            data-bs-toggle="modal"
                            data-bs-target="#delete_confirmation_modal"
                            class="text-danger"
                            style="display: flex; align-items: center;gap: 4px;margin-left: 27px;">
                            <i class="fa fa-trash" aria-hidden="true"></i>
                            ลบ
                        </a>
                        {{-- <select name="sort"
                                class="sort-item"
                                data-id="{{ $row->id }}"
                                data-old="{{ $row->sort }}"
                                onchange="updateSort(this)"
                                >
                            @for ($i = 1;$i<=$list_data->total();$i++)
                                <option value="{{ $i }}"
                                    @if ($i == $row->sort)
                                        selected
                                    @endif>{{ $i }}</option>
                            @endfor
                        </select> --}}

                    </div>
                </td>         
            </tr>
            @endforeach
        </tbody>
    </table>
<!-- END: Data List -->
<!-- BEGIN: Pagination -->
@include('admin/layout/pagination')
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
