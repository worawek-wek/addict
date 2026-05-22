{{-- {{dd($list_data['to'])}} --}}
{{-- <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script> --}}

<table class="datatables-basic table dataTable no-footer dtr-column" id="DataTables_Table_0" aria-describedby="DataTables_Table_0_info">
    <thead class="border-top">
        <tr class="table-info">
            <th class="text-center" style="width: 40px;">ลำดับ</th>
            <th class="text-center">ชื่อห้อง</th>
            {{-- <th class="text-center">ราคา 40 นาที/บริการ</th>
            <th class="text-center">ราคา 1 ชั่วโมง/บริการ</th>
            <th class="text-center">ราคา 1 ชั่วโมงครึ่ง/บริการ</th> --}}
            <th class="text-center">สาขา</th>
            <th class="text-center">หมายเหตุ</th>
            <th class="text-center">ดำเนินการ</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($list_data as $key => $row)
            <tr class="odd">
                <td class="text-center" style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#insurance" onclick="view({{ $row->id }})">{{ $list_data->firstItem() + $key }}</td>
                <td class="text-center" style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#insurance" onclick="view({{ $row->id }})">{{ $row->name }}</td>
                {{-- <td class="text-center">{{ number_format($row->forty_minutes, 2) }}</td>
                <td class="text-center">{{ number_format($row->sixty_minutes, 2) }}</td>
                <td class="text-center">{{ number_format($row->ninety_minutes, 2) }}</td> --}}
                <td class="text-center" style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#insurance" onclick="view({{ $row->id }})">{{ $row->branch->name }}</td>
                <td class="text-center" style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#insurance" onclick="view({{ $row->id }})">{{ $row->remark }}</td>
                <td class="text-center">
                    <div class="d-flex justify-content-center align-items-center gap-3">
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

                        <!-- ปุ่มลบ -->
                        <a href="javascript:;"
                            class="btn btn-xs rounded-pill btn-danger d-flex align-items-center gap-1 ms-3 py-1"
                            onclick='Delete({{ $row->id }})'>
                                <i class="fa fa-trash"></i>
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
        @empty

            <tr>
                <td colspan="20" class="text-center text-muted py-4">
                    <i class="ti ti-file-search" style="font-size: 24px;"></i><br>
                    ไม่พบข้อมูล
                </td>
            </tr>

        @endforelse
    </tbody>
</table>

<!-- BEGIN: Pagination -->
@include('admin/layout/pagination')
<script>
    document.querySelectorAll('.sort-item').forEach((el) => {
        new TomSelect(el, {
            create: false,
            maxItems: 1,
            allowEmptyOption: true,
            sortField: { field: "text", direction: "asc" },
            // ❌ ปิดการ sort (ให้เรียงตาม HTML)
            sortField: [],

            // ❌ ไม่ filter ตัวเลข
            searchField: []
        });
    });
</script>