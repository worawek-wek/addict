<!doctype html>

<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr"
    data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template">

<head>
    @include('admin/layout/inc_header')
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js">
    </script>
    <title>Dashboard - CRM | Vuexy - Bootstrap Admin Template</title>

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
                                            <div
                                                class="card-header d-flex border-top rounded-0 flex-wrap py-0 flex-column flex-md-row align-items-start">
                                                <div class="me-5 ms-n4 pe-5 mb-n6 mb-md-0">

                                                    <!-- <label><input type="search" class="form-control"
                                                                placeholder="Search Product"
                                                                aria-controls="DataTables_Table_0"></label> -->
                                                    <div class="dataTables_length mx-n2 ms-2"
                                                        id="DataTables_Table_0_length">
                                                        <label>Show
                                                            <select name="limit"
                                                                onchange='loadData("{{ $page_url }}-datatable")'
                                                                aria-controls="DataTables_Table_0"
                                                                class="form-select p_search">
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
                                                                    value="{{ now()->format('d/m/Y') }}">
                                                        </div>
                                                        <label class="me-3">ถึงวันที่:</label>
                                                        <div
                                                            class="dt-action-buttons d-flex flex-column align-items-start align-items-sm-center justify-content-sm-center pt-0 gap-sm-2 gap-sm-0 flex-sm-row">
                                                            <div id="DataTables_Table_0_filter"
                                                                class="dataTables_filter mx-n2 me-2">
                                                                <input name="end_date" id="end_date" type="text"
                                                                        class="form-control p_search search_date"
                                                                        value="{{ now()->format('d/m/Y') }}">
                                                            </div>
                                                            {{-- <div class="d-flex align-items-baseline ms-1 me-3">
                                                                <label class="me-2">User</label>
                                                                <input type="number" name="user_id" id="user_id_input"
                                                                    class="form-control p_search" placeholder="ID..."
                                                                    style="min-width:120px;"
                                                                    onchange='loadData("{{ $page_url }}-datatable")'
                                                                    onkeydown='if(event.key==="Enter"){ loadData("{{ $page_url }}-datatable"); }'>
                                                            </div> --}}
                                                            <div
                                                                class="dt-buttons btn-group flex-wrap d-flex mb-6 mb-sm-0">
                                                                <button
                                                                    class="btn btn-secondary add-new btn-primary me-2 ms-sm-0 waves-effect waves-light"
                                                                    tabindex="0" aria-controls="DataTables_Table_0"
                                                                    type="button" onclick="printPdf()"
                                                                    id ='printPdfButton' {{-- onclick="window.open('/admin/report/report-stock-history/pdf', '_blank');" --}}>
                                                                    <span>
                                                                        <i class="ti ti-file-upload me-0 me-sm-1"></i>
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
                                            </div>
                                            <div id="table-data"><!-- ตารางจะถูกโหลดตรงนี้ --></div>
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
</body>

</html>

<script>
    var page = "{{ route('report.stock_history.datatable') }}";
    var searchData = {};
    loadData(page);

    function printPdf() {

        var searchData = {};

        $('.p_search').each(function() {
            var inputName = $(this).attr('name');
            var inputValue = $(this).val();
            searchData[inputName] = inputValue;
        });

        // แปลง object เป็น query string
        let queryString = $.param(searchData);

        window.open(
            '/admin/report/report-stock-history/pdf?' + queryString,
            '_blank'
        );
    }

    function loadData(pages) {
        $('.p_search').each(function() {
            var inputName = $(this).attr('name');
            var inputValue = $(this).val();
            searchData[inputName] = inputValue;
        });
        page = pages;
        $.ajax({
            type: "GET",
            url: pages,
            data: searchData,
            success: function(data) {
                $("#table-data").html(data);
                // Check if table is empty
                var isEmpty = $("#table-data").find('td:contains("ไม่มีข้อมูล")').length > 0;
                $('#printPdfButton').prop('disabled', isEmpty);
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

        loadData("{{ $page_url }}-datatable");
    });

    $('#end_date').on('changeDate', function(e) {
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
