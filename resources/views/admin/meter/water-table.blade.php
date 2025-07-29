    @php
        $monthNames = [
                        '01' => 'มกราคม', '02' => 'กุมภาพันธ์', '03' => 'มีนาคม', '04' => 'เมษายน',
                        '05' => 'พฤษภาคม', '06' => 'มิถุนายน', '07' => 'กรกฎาคม', '08' => 'สิงหาคม',
                        '09' => 'กันยายน', '10' => 'ตุลาคม', '11' => 'พฤศจิกายน', '12' => 'ธันวาคม'
                    ];
    @endphp
    <form id="update_meter">
        @csrf
    <table class="datatables-basic table dataTable no-footer dtr-column" id="DataTables_Table_0" aria-describedby="DataTables_Table_0_info">
        <thead class="border-top">
            <tr class="table-info">
                <th class="text-center" tabindex="0" style="width: 40px;">
                    ห้อง
                </th>
                <th class="text-center">
                    สถานะห้อง
                </th>
                <th class="text-center">
                    เลขมิเตอร์เดือน<br>({{ $monthNames[$month_previous].'/'.$year_previous }})</th>
                <th class="text-center" width="25%">
                    เลขมิเตอร์เดือน<br>({{ $monthNames[$search_month].'/'.$search_year }})</th>
                <th class="text-center">
                    หน่วยที่ใช้
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($list_data as $key => $row)
            <tr>
                <input type="hidden" id="water_unit{{ $row->meters_id }}" value="{{ intval($row->water_unit) }}">
                <td class="text-center">
                    {{ $row->name }}
                </td>
                <td class="text-center">
                    @php
                        if (@$row->room_for_rent){
                            $s_name = "มีผู้พักอาศัย";
                            $s_color = "success";
                        }else{
                            $s_name = "ห้องว่าง";
                            $s_color = "secondary";
                        }
                    @endphp
                    <span class="badge bg-label-{{ $s_color }} m-auto" text-capitalized="" style="font-size: small;" >
                        {{ $s_name }}
                    </span>
                </td>
                <td class="text-center">
                    0
                </td>
                <td class="text-center d-flex">
                    <span class="badge rounded-pill bg-label-info text-black d-flex" text-capitalized="" style="font-size: unset;">
                        <span class="ti ti-droplet me-2 m-auto"></span>
                        <input type="text" id="room{{ $row->meters_id }}" class="form-control form-control-sm" value="{{ intval($row->water_unit) }}" onkeydown="handleInput(event,{{ $row->meters_id }}, this.value)" oninput="editRoom({{ $row->meters_id }}, this.value)" style="background-color: #d6f7fb;border-color: #00bad1;">
                    </span>
                    <div style="padding: inherit;">
                        {{-- <button type="button" id="updateRoom{{ $row->meters_id }}" class="btn btn-sm btn-secondary" disabled onclick="updateRoom('{{ $row->meters_id }}')">
                            <i class="ti-xs ti ti-pencil me-2"></i>บันทึก
                        </button> --}}
                    </div>
                </td>
                <td class="text-center text-danger">
                    {{ $row->water_unit - 0 }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</form>
{{-- ////////////////////////////////////////////// --}}
<div class="row">
    <div class="col-sm-12 col-md-6 ps-4">
        <div class="dataTables_info" id="DataTables_Table_1_info" role="status" aria-live="polite">
            All &nbsp; {{$list_data->total()}} &nbsp; entries
        </div>
    </div>

    <div class="col-sm-12 col-md-6 pe-4">
        <div class="dataTables_paginate paging_simple_numbers" id="DataTables_Table_1_paginate">
            @if ($list_data->lastPage() > 1)
                <ul class="pagination">
                    <!-- ปุ่ม First -->
                    <li class="page-item {{ ($list_data->currentPage() == 1) ? ' disabled' : '' }}">
                        <a class="page-link" href="javascript:void(0)" onclick='loadData("{{ $list_data->url(1) }}")'>First</a>
                    </li>

                    <?php
                        // จำนวนหน้าที่ย่อ (ตัวอย่างนี้แสดงแค่ 8 หน้า)
                        $total_links = 9;  // เปลี่ยนจาก 5 เป็น 9
                        $half_total_links = floor($total_links / 2);
                        $from = $list_data->currentPage() - $half_total_links;
                        $to = $list_data->currentPage() + $half_total_links;

                        // แก้ไขการคำนวณจากหน้าแรกหรือหน้าสุดท้าย
                        if ($list_data->currentPage() < $half_total_links) {
                            $to += $half_total_links - $list_data->currentPage();
                        }
                        if ($list_data->lastPage() - $list_data->currentPage() < $half_total_links) {
                            $from -= $half_total_links - ($list_data->lastPage() - $list_data->currentPage()) - 1;
                        }

                        // กำหนดให้ค่าของ $from และ $to ไม่ให้ต่ำกว่า 1 หรือมากกว่าหน้าสุดท้าย
                        $from = max($from, 1);
                        $to = min($to, $list_data->lastPage());
                    ?>

                    <!-- แสดงหน้าที่ในช่วงที่คำนวณ -->
                    @for ($i = $from; $i <= $to; $i++)
                        <li class="page-item {{ ($list_data->currentPage() == $i) ? ' active' : '' }}">
                            <a class="page-link" href="javascript:void(0)" onclick='loadData("{{ $list_data->url($i) }}")'>{{ $i }}</a>
                        </li>
                    @endfor

                    <!-- เพิ่มการแสดงเลขหน้าสุดท้าย -->
                    @if ($to < $list_data->lastPage())
                        <li class="px-2 pe-1 mt-4">
                            ...
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="javascript:void(0)" onclick='loadData("{{ $list_data->url($list_data->lastPage()) }}")'>{{ $list_data->lastPage() }}</a>
                        </li>
                    @endif

                    <!-- ปุ่ม Last -->
                    <li class="page-item {{ ($list_data->currentPage() == $list_data->lastPage()) ? ' disabled' : '' }}">
                        <a class="page-link" href="javascript:void(0)" onclick='loadData("{{ $list_data->url($list_data->lastPage()) }}")'>Last</a>
                    </li>
                </ul>
            @endif
        </div>
    </div>
</div>
{{-- ////////////////////////////////////////////// --}}

<script>
    function handleInput(event, id, v) {
        if (event.key === 'Enter') {
            if($('#water_unit' + id).val() != v){
                updateRoom(id)
                // const userInput = event.target.value;
            }
        }
    }
    function editRoom(id, v){
        if($('#water_unit' + id).val() != v){
            $('#updateRoom' + id).prop('disabled', false);
            $('#updateRoom' + id).removeClass('btn-secondary').addClass('btn-success');
        }else{
            $('#updateRoom' + id).prop('disabled', true);
            $('#updateRoom' + id).removeClass('btn-success').addClass('btn-secondary');
        }
    }
    function updateRoom(id){
        $.ajax({
            url: '/meter/water_unit/'+id, // เปลี่ยน URL เป็นจุดหมายที่ต้องการ
            type: 'POST',
            data: {
                _token : "{{ csrf_token() }}",
                water_unit : $('#room' + id).val()
            },
            success: function(response) {
                // if(response == true){
                    $('#water_unit' + id).val(response);
                    $('#updateRoom' + id).prop('disabled', true);
                    $('#updateRoom' + id).removeClass('btn-success').addClass('btn-secondary');
                    Swal.fire({
                        title: 'แก้ไข มิเตอร์น้ำ เรียบร้อยแล้ว',
                        icon: 'success',
                        timer: 1500, // ตั้งเวลาเป็น 1500 มิลลิวินาที (1.5 วินาที)
                        timerProgressBar: true, 
                        showConfirmButton: false,
                        customClass: {
                            title: 'custom-title', // กำหนดคลาสให้กับ title
                        },
                    });
                // }
            },
            error: function(error) {
                Swal.fire('เกิดข้อผิดพลาด', '', 'error');
                console.error('เกิดข้อผิดพลาด:', error);
            }
        });
    }
</script>