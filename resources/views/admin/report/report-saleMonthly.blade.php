<!doctype html>

<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr"
    data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template">

<head>
    @include('admin/layout/inc_header')
    <title>Dashboard - CRM | Vuexy - Bootstrap Admin Template</title>

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css">
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
                                            <div class="card-header border-bottom border-bottom">
                                                <div class="row g-3 justify-content-between">
                                                    {{-- <div class="col-sm-4 mb-2"> --}}
                                                        <h4 class="mb-0">
                                                            <i class="tf-icons ti ti-user-dollar text-main ti-md me-2"></i>
                                                            รายงานยอดขายรวม
                                                        </h4>
                                                        <select name="ref_branch_id" class="form-select p_search mt-3"
                                                            onchange='loadData("{{ route('report-sale-monthly.datatable') }}")' required>
                                                            @if (Auth::user()->work_status == 3)
                                                                <option value="">ทั้งหมด</option>
                                                            @endif
                                                            @foreach ($branch as $bra)
                                                                <option value="{{ $bra->id }}" @if (Auth::user()->ref_branch_id == $bra->id) selected @endif>{{ $bra->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    {{-- </div> --}}
                                                </div>
                                            </div>
                                            <div
                                                class="card-header d-flex rounded-0 flex-wrap py-0 flex-column flex-md-row align-items-start">
                                                <div class="me-5 ms-n4 pe-5 mb-n6 mb-md-0">
                                                    <div class="dataTables_length mx-n2 ms-2"
                                                        id="DataTables_Table_0_length">
                                                        <label>Show
                                                            <select name="limit"
                                                                aria-controls="DataTables_Table_0" class="form-select p_search"
                                                                onchange="page='{{ route('report-sale-monthly.datatable') }}'; loadData(page)">
                                                                <option value="7">7</option>
                                                                <option value="10">10</option>
                                                                <option value="20">20</option>
                                                                <option value="50" selected>50</option>
                                                                <option value="70">70</option>
                                                                <option value="100">100</option>
                                                            </select>
                                                        </label>
                                                    </div>
                                                </div>
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
                                                            <button
                                                                class="btn btn-secondary add-new btn-primary me-2 ms-sm-0 waves-effect waves-light"
                                                                type="button" onclick="printPDF()">
                                                                <span>
                                                                    <i class="ti ti-printer me-0 me-sm-1"></i>
                                                                    <span class="d-none d-sm-inline-block">พิมพ์
                                                                    </span>
                                                                </span>
                                                            </button>
                                                            {{-- <div class="btn-group">
                                                                    <button
                                                                        class="btn btn-success buttons-collection  btn-warning waves-effect waves-light"
                                                                        tabindex="0"
                                                                        aria-controls="DataTables_Table_0"
                                                                        type="button" aria-haspopup="dialog"
                                                                        aria-expanded="false">
                                                                        <span><i class="ti ti-upload me-1"></i>ดาวน์โหลด
                                                                            Excel
                                                                        </span>
                                                                    </button>
                                                                </div> --}}
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                            <div id="table-data"><!-- ตารางจะถูกโหลดตรงนี้ --></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js"></script>
</body>

</html>

<script>
    var page = "{{ route('report-sale-monthly.datatable') }}";
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

    function printPDF() {
        var searchData = {};
        $('.p_search').each(function() {
            searchData[$(this).attr('name')] = $(this).val();
        });
        let queryString = $.param(searchData);
        window.open('/admin/report/monthly-sale/pdf?' + queryString, '_blank');
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
