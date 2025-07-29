
<div class="tab-pane fade show" id="pills-home" role="tabpanel"
aria-labelledby="pills-home-tab" tabindex="0">
<div class="card card-body shadow-none" style="padding: 10px;line-height: 5px;">
    <div class="row g-3 new_box" style="padding: 0px 30px;">
        @foreach ($list_data as $key => $row)
        
        <div class="col-md-6 col-lg5">
            <div
                class="card bg-label-{{ $status_room[$row->status_name] }} card-check shadow-sm h-100">
                <div class="card-body text-center">
                    <h5 class="card-title" style="color: black"><b>{{ $row->room_name }}</b></h5>
                        <p style="color: rgb(40, 40, 40);font-weight: 430;">
                            @php
                                $renter_name = $row->prefix.' '.$row->renter_name;
                                if (strlen($renter_name) > 40) {
                                    // echo substr($renter_name, 0, 40) . '...'; // ตัดให้เหลือ 10 ตัวอักษรแล้วเพิ่ม "..."
                                }else{
                                    // echo $renter_name;
                                }
                                echo $renter_name;

                            @endphp
                    </p>
                    <div class="text-{{ $status_room[$row->status_name] }} h5 text-center" style="margin-top: 0;margin-bottom: 0;">
                        {{ $row->status_name }}
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        
        {{-- <div class="col-md-6 col-lg5">
            <div class="card bg-label-success card-check shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title" style="color: black"><b>A104</b></h5>
                        <p style="color: rgb(40, 40, 40);font-weight: 430;">นางสาว มาลินี ประเทศา</p>
                    <div class="text-success h5 text-center" style="margin-top: 0;margin-bottom: 0;">
                        ชำระเงินแล้ว
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg5">
            <div class="card bg-label-success card-check shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title" style="color: black"><b>A105</b></h5>
                        <p style="color: rgb(40, 40, 40);font-weight: 430;">นางสาว มาลินี ประเทศา</p>
                    <div class="text-success h5 text-center" style="margin-top: 0;margin-bottom: 0;">
                        ชำระเงินแล้ว
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg5">
            <div class="card bg-lightGray card-check shadow-sm h-100">
                <div class="card-body text-center">
                    <h5 class="card-title" style="color: black"><b>A106</b></h5>
                        <p>ไม่มีผู้เช่า</p>
                    <div class="h5 text-center" style="margin-top: 0;margin-bottom: 0;">
                        ห้องว่าง
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg5">
            <div class="card bg-label-danger card-check shadow-sm h-100">
                <div class="card-body text-center">
                    <h5 class="card-title" style="color: black"><b>A110</b></h5>
                        <p style="color: rgb(40, 40, 40);font-weight: 430;">นางสาว มาลินี ประเทศา</p>
                    <div class="text-danger h5 text-center" style="margin-top: 0;margin-bottom: 0;">
                        ค้างชำระ
                    </div>
                </div>
            </div>
        </div> --}}
    </div>
</div>
</div>
<div class="tab-pane fade show" id="pills-profile" role="tabpanel"
aria-labelledby="pills-profile-tab" tabindex="0">
<table class="datatables-basic table dataTable no-footer dtr-column"
    id="DataTables_Table_0" aria-describedby="DataTables_Table_0_info">
    <thead class="border-top">
        <tr class=" table-info">
            <th class="control sorting_disabled dtr-hidden" rowspan="1"
                colspan="1" style="width: 0px; display: none;"
                aria-label=""></th>
            <th class="sorting_disabled dt-checkboxes-cell dt-checkboxes-select-all"
                rowspan="1" colspan="1" style="width: 18px;"
                data-col="1" aria-label=""><input type="checkbox"
                    class="form-check-input"></th>
            <th class="text-center" tabindex="0" style="width: 40px;">
                ห้อง
            </th>
            <th class="text-center">
                ผู้เช่า</th>
            <th class="text-center">
                สถานะห้อง
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($list_data as $key => $row2)
        <tr class="odd" style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#insurance" onclick="view({{ $row2->ref_room_id }})">
            <td class="  control" tabindex="0" style="display: none;">
            </td>
            <td class="  dt-checkboxes-cell"><input type="checkbox"
                    class="dt-checkboxes form-check-input"></td>
            <td class="text-center">{{ $row2->room_name }}</td>
            <td class="text-center"><span class="text-truncate">{{ $row2->prefix.' '.$row2->renter_name }}</span>
            </td>
            <td class="text-center">
                <span class="badge bg-{{ $status_room[$row2->status_name] }}" style="font-size: unset;"
                    text-capitalized="">{{ $row2->status_name }}</span></td>
        </tr>
        @endforeach

    </tbody>
</table>
<!-- END: Data List -->
<!-- BEGIN: Pagination -->

</div>
@include('layout/pagination')
