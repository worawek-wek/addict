    {{-- {{dd($list_data['to'])}} --}}
    <table class="datatables-basic table dataTable no-footer dtr-column" id="DataTables_Table_0" aria-describedby="DataTables_Table_0_info">
        <thead class="border-top">
            <tr class=" table-info">
                <th class="text-center" tabindex="0" style="width: 40px;">
                    ลำดับ
                </th>
                <th class="text-center">
                    สต็อก(คลัง)
                </th>
                <th class="text-center">
                    เบิก(พร้อมขาย)
                </th>
                <th class="text-center">
                    ชื่อดื่ม
                </th>
                <th class="text-center">
                    ราคา(ลูกค้า)
                </th>
                <th class="text-center">
                    ค่ามือ
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
                <th class="text-center">
                    ดำเนินการ
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($list_data as $key => $row)
            @php
                $view = 'style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#insurance" onclick="view('.$row->id.')"';
                $s_remain = \App\Models\DrinkCardStocks::where('ref_drink_id', $row->id)->sum('remain') ?? 0;
                $r_remain = \App\Models\DrinkStockReadyForSale::where('ref_drink_id', $row->id)->sum('remain') ?? 0;
            @endphp
            <tr class="odd">
                <td class="text-center" {!! $view !!}>
                    {{ $list_data->firstItem()+$key }}
                </td>
                <td class="text-center" {!! $view !!}>
                    {{ $s_remain }}
                </td>
                <td class="text-center" {!! $view !!}>
                    {{ $r_remain }}
                </td>
                <td class="text-center" {!! $view !!}>
                    {{ $row->name }}
                </td>
                <td class="text-center" {!! $view !!}>
                    {{ $row->price }}
                </td>
                <td class="text-center" {!! $view !!}>
                    {{ $row->commission }}
                </td>
                <td class="text-center" {!! $view !!}>
                    {{ $row->cost }}
                </td>
                <td class="text-center" {!! $view !!}>
                    {{ $row->branch->name }}
                </td>
                <td class="text-center" {!! $view !!}>
                    {{ $row->remark }}
                </td>
                <td class="text-center">
                    <div class="d-flex justify-content-center align-items-center gap-3">
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
                        <a href="javascript:;"
                            class="btn btn-xs rounded-pill btn-danger d-flex align-items-center gap-1 ms-3 py-1"
                            onclick='Delete({{ $row->id }})'
                            data-bs-toggle="modal"
                            data-bs-target="#delete_confirmation_modal">
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