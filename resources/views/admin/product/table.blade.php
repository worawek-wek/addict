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
                <th class="text-center">
                    ดำเนินการ
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($list_data as $key => $row)
            @php
                $view = 'style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#insurance" onclick="view('.$row->id.')"';
                $remain = \App\Models\CardStocks::where('ref_product_id', $row->id)->sum('remain') ?? 0;
                $qty = \App\Models\StockReadyForSale::where('ref_product_id', $row->id)->sum('qty') ?? 0;
            @endphp
            <tr class="odd">
                <td class="text-center" {!! $view !!}>
                    {{ $list_data->firstItem()+$key }}
                </td>
                <td class="text-center" {!! $view !!}>
                    {{ $remain-$qty }}
                </td>
                <td class="text-center" {!! $view !!}>
                    {{ $qty }}
                </td>
                <td class="text-center" {!! $view !!}>
                    {{ $row->name }}
                </td>
                <td class="text-center" {!! $view !!}>
                    {{ $row->price }}
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
                    <select name="sort"
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
                    </select>
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