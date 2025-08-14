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
            <th class="text-center">
                สถานะ
            </th>
            <th class="text-center">
                Actions
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
            <td class="text-center">
                @if ($row->status == 1)
                    <span class="badge rounded-pill bg-success">ปกติ</span>
                @else
                    <span class="badge rounded-pill bg-danger">ถูกระงับ</span>
                @endif
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-icon btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editCustomerModal" onclick="editCustomer({{ $row->id }})">
                    <i class="ti ti-edit"></i>
                </button>
                @if ($row->status == 1)
                    <button type="button" class="btn btn-icon btn-sm btn-danger" onclick="lockCustomer({{ $row->id }})">
                        <i class="ti ti-lock"></i>
                    </button>
                @else
                    <button type="button" class="btn btn-icon btn-sm btn-success" onclick="unlockCustomer({{ $row->id }})">
                        <i class="ti ti-lock-open"></i>
                    </button>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@include('admin/layout/pagination')
