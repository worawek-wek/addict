<!doctype html>

<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr"
    data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template">

<head>
    @include('admin/layout/inc_header')
    <title>Dashboard - CRM | Vuexy - Bootstrap Admin Template</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js"></script>

</head>

<style>
    .table th {
        font-size: 15px;
        font-weight: bold;
    }

    .table td {
        padding-top: 14px;
        padding-bottom: 14px;
    }

    .modalHeadDecor .modal-header {
        padding: 0;
    }

    .modalHeadDecor .modal-title {
        padding: 1.25rem 1.5rem 1.25rem;
        color: white;
        background-color: #54BAB9;
        position: relative;
    }

    .modalHeadDecor .modal-title::after {
        position: absolute;
        top: 0;
        right: -65px;
        content: '';
        width: 0;
        height: 0;
        border-top: 65px solid #54BAB9;
        border-right: 65px solid transparent;
    }
</style>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            @include('admin/layout/inc_sidemenu')
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->

                @include('admin/layout/inc_topmenu')

                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->

                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="row ">
                            <div class="col-sm-12">
                                <div class="card mb-3">
                                    <div class="card-datatable table-responsive">
                                        <div id="DataTables_Table_0_wrapper"
                                            class="dataTables_wrapper dt-bootstrap5 no-footer">
                                            <div class="card-header border-top rounded-0 py-3">

                                                {{-- Title --}}
                                                <div class="mb-3">
                                                    <h4 class="mb-0">
                                                        <i class="tf-icons ti ti-user-dollar text-main ti-md me-2"></i>
                                                        รายงานผู้ดูแลพนักงาน
                                                    </h4>
                                                </div>

                                                {{-- Filter row --}}
                                                <div class="row g-2 align-items-end">

                                                    {{-- Show rows --}}
                                                    <div class="col-auto">
                                                        <label class="form-label mb-1 text-muted small">แสดง</label>
                                                        <select name="limit" class="form-select form-select-sm p_search"
                                                            style="min-width:80px;"
                                                            onchange="loadData(page)">
                                                            <option value="7">7</option>
                                                            <option value="10" selected>10</option>
                                                            <option value="20">20</option>
                                                            <option value="50">50</option>
                                                            <option value="100">100</option>
                                                        </select>
                                                    </div>

                                                    {{-- Divider --}}
                                                    <div class="col-auto d-none d-md-block">
                                                        <div class="vr" style="height:38px; opacity:.15;"></div>
                                                    </div>

                                                    {{-- ตั้งแต่วันที่ --}}
                                                    <div class="col-auto">
                                                        <label class="form-label mb-1 text-muted small">ตั้งแต่วันที่</label>
                                                        <input name="start_date" id="start_date" type="text"
                                                            class="form-control form-control-sm p_search search_date"
                                                            style="min-width:120px;"
                                                            value="{{ now()->hour >= 10 ? now()->format('d/m/Y') : now()->subDay()->format('d/m/Y') }}">
                                                    </div>
                                                    <div class="col-auto">
                                                        <label class="form-label mb-1 text-muted small">เวลาเริ่มต้น</label>
                                                        <input name="start_time_filter" id="start_time_filter" type="time"
                                                            class="form-control form-control-sm p_search" value="10:00">
                                                    </div>

                                                    {{-- ถึงวันที่ --}}
                                                    <div class="col-auto">
                                                        <label class="form-label mb-1 text-muted small">ถึงวันที่</label>
                                                        <input name="end_date" id="end_date" type="text"
                                                            class="form-control form-control-sm p_search search_date"
                                                            style="min-width:120px;"
                                                            value="{{ now()->hour >= 10 ? now()->addDay()->format('d/m/Y') : now()->format('d/m/Y') }}">
                                                    </div>
                                                    <div class="col-auto">
                                                        <label class="form-label mb-1 text-muted small">เวลาสิ้นสุด</label>
                                                        <input name="end_time_filter" id="end_time_filter" type="time"
                                                            class="form-control form-control-sm p_search" value="04:01">
                                                    </div>

                                                    {{-- Divider --}}
                                                    <div class="col-auto d-none d-md-block">
                                                        <div class="vr" style="height:38px; opacity:.15;"></div>
                                                    </div>

                                                    {{-- ค้นหา --}}
                                                    <div class="col-auto">
                                                        <label class="form-label mb-1 text-muted small">ค้นหา</label>
                                                        <input name="search" id="search_input" type="search"
                                                            class="form-control form-control-sm p_search"
                                                            placeholder="ค้นหา..."
                                                            style="min-width:160px;"
                                                            oninput="loadData(page)">
                                                    </div>

                                                    {{-- Print PDF --}}
                                                    <div class="col-auto ms-md-auto">
                                                        <label class="form-label mb-1 d-block">&nbsp;</label>
                                                        <button class="btn btn-primary btn-sm waves-effect waves-light"
                                                            type="button" onclick="printPdf()">
                                                            <i class="ti ti-file-upload me-1"></i>พิมพ์ PDF
                                                        </button>
                                                    </div>

                                                </div>
                                                {{-- /Filter row --}}

                                            </div>{{-- /card-header --}}
                                            <div id="table-data"><!-- ตารางจะถูกโหลดตรงนี้ --></div>
                                        </div>{{-- /DataTables_Table_0_wrapper --}}
                                    </div>{{-- /card-datatable --}}
                                </div>{{-- /card --}}
                            </div>{{-- /col --}}
                        </div>{{-- /row --}}
                        <!-- / Content -->

                        <!-- Footer -->
                        @include('admin/layout/inc_footer')
                        <!-- / Footer -->

                        <div class="content-backdrop fade"></div>
                    </div>
                    <!-- / Content wrapper -->
                </div>
                <!-- / Layout page -->
            </div>

            <!-- Overlay -->
            <div class="layout-overlay layout-menu-toggle"></div>

            <!-- Drag Target Area To SlideIn Menu On Small Screens -->
            <div class="drag-target"></div>
        </div>
        <!-- / Layout wrapper -->

        @include('admin/layout/inc_js')
</body>

</html>

<script>

        function printPdf(){

            var searchData = {};

            $('.p_search').each(function() {
                var inputName = $(this).attr('name');
                var inputValue = $(this).val();
                searchData[inputName] = inputValue;
            });

            // แปลง object เป็น query string
            let queryString = $.param(searchData);

            window.open(
                '/admin/report/oversee-employee/pdf?' + queryString,
                '_blank'
            );
        }

        var page = "{{ route('report-oversee-employee.datatable') }}";
        var searchData = {};
        loadData(page);

        function loadData(pages) {
            $('.p_search').each(function() {
                var inputName = $(this).attr('name');
                var inputValue = $(this).val();
                searchData[inputName] = inputValue;
            });

            // If not custom, clear custom date fields
            // if ($('select[name="date_range"]').val() !== 'custom') {
            //     searchData['start_date'] = '';
            //     searchData['end_date'] = '';
            // }

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
        $('.search_date').datepicker({
            format: 'dd/mm/yyyy', // กำหนดรูปแบบวันที่
            autoclose: true,      // ปิด datepicker เมื่อเลือกวันที่
            todayHighlight: true  // ไฮไลต์วันที่ปัจจุบัน
        });

        // ⭐ สำคัญมาก: set ค่าเริ่มต้นให้ datepicker รู้
        $('#start_date').datepicker('setDate', $('#start_date').val());
        $('#end_date').datepicker('setDate', $('#end_date').val());

        // ⭐ ผูกข้อจำกัดตั้งแต่โหลด
        const startInit = $('#start_date').datepicker('getDate');
        const endInit   = $('#end_date').datepicker('getDate');

        if (startInit) {
            $('#end_date').datepicker('setStartDate', startInit);
        }

        if (endInit) {
            $('#start_date').datepicker('setEndDate', endInit);
        }

        // event หลังจากนั้น
        $('#start_date').on('changeDate', function (e) {
            $('#end_date').datepicker('setStartDate', e.date);

            const endDate = $('#end_date').datepicker('getDate');
            if (endDate && endDate < e.date) {
                $('#end_date').datepicker('clearDates');
            }

            loadData("{{ $page_url }}-datatable");
        });

        $('#end_date').on('changeDate', function (e) {
            $('#start_date').datepicker('setEndDate', e.date);

            loadData("{{ $page_url }}-datatable");
        });

        $('#start_time_filter, #end_time_filter').on('change', function() {
            loadData("{{ $page_url }}-datatable");
        });
    // document.addEventListener('DOMContentLoaded', function() {
    //     // Initialize datepickers
    //     flatpickr("#datepicker-from", {
    //         dateFormat: "d/m/Y",
    //         defaultDate: new Date().setDate(new Date().getDate() - 7) o
    //     });
    //     flatpickr("#datepicker-to", {
    //         dateFormat: "d/m/Y",
    //         defaultDate: new Date() // today
    //     });
    // });
</script>
