<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr"
    data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template">

<head>
    @include('admin/layout/inc_header')
    <title>การจองห้องพัก (Order Rooms)</title>
</head>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            @include('admin/layout/inc_sidemenu')
            <div class="layout-page">
                @include('admin/layout/inc_topmenu')
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card mb-3">
                                    <div class="card-header border-bottom">
                                        <div class="row g-3 justify-content-between">
                                            <div class="col-sm-12">
                                                <h4 class="mb-0">
                                                    <i class="tf-icons ti ti-bed text-main ti-md me-2"></i>
                                                    การจองห้องพัก (Order Rooms)
                                                </h4>
                                            </div>
                                            <div class="row g-3">
                                                <div class="col-sm-2 mb-2">
                                                    <select name="branch_id" class="form-select p_search"
                                                        onchange='loadData("{{ route('order-rooms.datatable') }}")'>
                                                        @foreach ($branches as $branch)
                                                            <option value="{{ $branch->id }}">{{ $branch->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-sm-2 mb-2">
                                                    <select name="date_range" class="form-select p_search"
                                                        onchange='onDateRangeChange()'>
                                                        <option value="">-- เลือกช่วงเวลา --</option>
                                                        <option value="1">1 วัน</option>
                                                        <option value="7">7 วัน</option>
                                                        <option value="14">14 วัน</option>
                                                        <option value="30">1 เดือน</option>
                                                        <option value="custom">ระบุวันที่เอง</option>
                                                    </select>
                                                </div>
                                                <div class="col-sm-6 mb-2" id="custom-date-group" style="display:none !important;">
                                                    <div
                                                        class="d-flex justify-content-start justify-content-md-end align-items-baseline">
                                                        <label class="me-3">ตั้งแต่วันที่:</label>
                                                        <div
                                                            class="dt-action-buttons d-flex flex-column align-items-start align-items-sm-center justify-content-sm-center pt-0 gap-sm-2 gap-sm-0 flex-sm-row">
                                                            <div id="DataTables_Table_0_filter"
                                                                class="dataTables_filter mx-n2 me-2">
                                                                <input name="start_date" id="start_date" type="text"
                                                                    class="form-control p_search search_date"
                                                                    value="{{ now()->hour >= 10 ? now()->format('d/m/Y') : now()->subDay()->format('d/m/Y') }}">
                                                            </div>
                                                            <div class="dataTables_filter mx-n2 me-1">
                                                                <input name="start_time_filter" id="start_time_filter" type="time"
                                                                    class="form-control p_search" value="10:00">
                                                            </div>
                                                            <label class="me-3">ถึงวันที่:</label>
                                                            <div
                                                                class="dt-action-buttons d-flex flex-column align-items-start align-items-sm-center justify-content-sm-center pt-0 gap-sm-2 gap-sm-0 flex-sm-row">
                                                                <div id="DataTables_Table_0_filter"
                                                                    class="dataTables_filter mx-n2 me-2">
                                                                    <input name="end_date" id="end_date" type="text"
                                                                        class="form-control p_search search_date"
                                                                        value="{{ now()->hour >= 10 ? now()->addDay()->format('d/m/Y') : now()->format('d/m/Y') }}">
                                                                </div>
                                                                <div class="dataTables_filter mx-n2 me-1">
                                                                    <input name="end_time_filter" id="end_time_filter" type="time"
                                                                        class="form-control p_search" value="04:01">
                                                                </div>
                                                            </div>
                                                        </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-2 mb-2">
                                                    {{-- <div class="input-group input-group-merge">
                                                        <span class="input-group-text"><i
                                                                class="ti ti-search"></i></span>
                                                        <input
                                                            oninput='loadData("{{ route('order-rooms.datatable') }}")'
                                                            name="search" type="text" class="form-control p_search"
                                                            placeholder="ค้นหาชื่อลูกค้า..." />
                                                    </div> --}}
                                                    <select name="childselect" id="childselect" class="form-select p_search" onchange="onDateRangeChange()">
                                                        <option value="">-- เลือกชื่อเด็ก --</option>
                                                        @foreach ($getchild as $item)
                                                            <option value="{{ $item->ref_user_id }}">{{ $item->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>


                                        </div>
                                    </div>

                                    <div class="card-body">
                                        <div class="row p-3">
                                            <div class="col-lg-4">
                                                <div class="d-flex align-items-center mb-2 mb-md-0">
                                                    <label class="me-2">แสดง</label>
                                                    <select onchange='loadData("{{ route('order-rooms.datatable') }}")'
                                                        name="limit" class="form-select p_search" style="width:120px">
                                                        <option value="25" selected>25</option>
                                                        <option value="50">50</option>
                                                        <option value="100">100</option>
                                                    </select>
                                                    <label class="ms-2">รายการ</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="table-data"><!-- ตารางจะถูกโหลดตรงนี้ --></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @include('admin/layout/inc_footer')
                    <div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="viewOrderRoomModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document" id="view"></div>
    </div>

    @include('admin/layout/inc_js')
    <script>
        var page = "{{ route('order-rooms.datatable') }}";
        var searchData = {};
        loadData(page);

        function loadData(pages) {
            $('.p_search').each(function() {
                var inputName = $(this).attr('name');
                var inputValue = $(this).val();
                searchData[inputName] = inputValue;
            });

            // If not custom, clear custom date fields
            if ($('select[name="date_range"]').val() == null) {
                searchData['start_date'] = '';
                searchData['end_date'] = '';
            }

            page = pages;
            $.ajax({
                type: "GET",
                url: pages,
                data: searchData,
                success: function(data) {
                    $("#table-data").html(data);

                    // bind pagination click
                    $('#table-data .pagination a').on('click', function(e) {
                        e.preventDefault();
                        loadData($(this).attr('href'));
                    });
                }
            });
        }

        function printReceipt(id){
            $.ajax({
                type: 'POST',
                url: "{{ route('order-rooms.getslip', '') }}/" + id,
                data: { _token: "{{ csrf_token() }}" },
                success: function(res) {
                    if (res.status && res.data) {
                        var w = window.open('', '_blank');
                        w.document.write(res.data);
                        w.document.close();
                        w.focus();
                        w.onload = function() { w.print(); };
                    }
                },
                error: function() {
                    Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถโหลดสลิปได้', 'error');
                }
            });
        }

        function onDateRangeChange() {
            var val = $('select[name="date_range"]').val();
            if (val === 'custom') {
                $('#custom-date-group').show();
            } else {
                $('#custom-date-group').hide();
            }
            loadData(page);
        }

        // If user changes custom date, reload
        // $(document).on('change', 'input[name="start_date"], input[name="end_date"]', function() {
        //     loadData(page);
        // });

        function view(id) {
            $.ajax({
                type: "GET",
                url: "{{ route('order-rooms.index') }}/" + id,
                success: function(data) {
                    $("#view").html(data);
                    $('#viewOrderRoomModal').modal('show');
                }
            });
        }
        function Delete(id, v, element) {
            $(element).prop('checked', v === 1 ? false : true);
            Swal.fire({
                title: 'ยืนยันการดำเนินการ?',
                text: 'คุณต้องการลบหรือไม่?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'ตกลง',
                cancelButtonText: 'ยกเลิก',
                didOpen: () => Swal.getConfirmButton().focus()
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'admin/order-rooms/' + id,
                        type: 'DELETE',
                        data: { _token: "{{ csrf_token() }}" },
                        success: function (response) {
                            if (response == true) {
                                Swal.fire('ลบเรียบร้อยแล้ว', '', 'success');
                                loadData(page);
                            }
                        },
                        error: function () {
                            Swal.fire('เกิดข้อผิดพลาด', '', 'error');
                        }
                    });
                }
            });
        }
        
    $('.search_date').datepicker({
        format: 'dd/mm/yyyy', // กำหนดรูปแบบวันที่
        autoclose: true, // ปิด datepicker เมื่อเลือกวันที่
        todayHighlight: true // ไฮไลต์วันที่ปัจจุบัน
    });
    // ⭐ สำคัญมาก: set ค่าเริ่มต้นให้ datepicker รู้
    $('#start_date').datepicker('setDate', $('#start_date').val());
    $('#end_date').datepicker('setDate', $('#end_date').val());

    // ⭐ ผูกข้อจำกัดตั้งแต่โหลด
    const startInit = $('#start_date').datepicker('getDate');
    const endInit = $('#end_date').datepicker('getDate');

    if (startInit) {
        $('#end_date').datepicker('setStartDate', startInit);
    }

    if (endInit) {
        $('#start_date').datepicker('setEndDate', endInit);
    }

    // event หลังจากนั้น
    $('#start_date').on('changeDate', function(e) {
        $('#end_date').datepicker('setStartDate', e.date);

        const endDate = $('#end_date').datepicker('getDate');
        if (endDate && endDate < e.date) {
            $('#end_date').datepicker('clearDates');
        }

        loadData(page);
    });

    $('#end_date').on('changeDate', function(e) {
        $('#start_date').datepicker('setEndDate', e.date);

        loadData(page);
    });

    $('#start_time_filter, #end_time_filter').on('change', function() {
        loadData(page);
    });
    </script>
</body>

</html>
