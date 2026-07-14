<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr"
    data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template">
<head>
    @include('admin/layout/inc_header')
    <title>ดูค่าคอมมิชชั่นพนักงานขาย - CRM</title>
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
</style>
<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            @include('admin/layout/inc_sidemenu')
            <div class="layout-page">
                @include('admin/layout/inc_topmenu')
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="row ">
                            <div class="col-sm-12">
                                <div class="card mb-3">
                                    <div class="card-header border-bottom border-bottom">
                                        <div class="row g-3 justify-content-between">
                                            <div class="col-sm-4 mb-2">
                                                <h4 class="mb-0">
                                                    <i class="tf-icons ti ti-user-dollar text-main ti-md me-2"></i>
                                                    รายงานค่าคอม (ดื่ม)
                                                </h4>
                                                <select name="ref_branch_id" class="form-select p_search mt-3"
                                                    onchange='loadData("{{ $page_url }}/datatable")' required>
                                                    @if (Auth::user()->work_status == 3)
                                                        <option value="">ทั้งหมด</option>
                                                    @endif
                                                    @foreach ($branch as $bra)
                                                        <option value="{{ $bra->id }}" @if (Auth::user()->ref_branch_id == $bra->id) selected @endif>{{ $bra->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-sm-8 d-flex justify-content-end align-items-sm-center gap-2">
                                                @if (auth()->id() === 1)
                                                <a href="{{ route('commission_ranks.index', ['category' => 'drink']) }}" class="btn btn-main">
                                                    <i class="ti ti-stairs-up"></i> ตั้งค่าบันได Rank (ดื่ม)
                                                </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body px-0 pt-0">
                                        <div class="row p-3">
                                            <div class="col-lg-2">
                                                <div class="d-flex align-items-center mb-2 mb-md-0">
                                                    <label class="">Show</label>
                                                    <select onchange='loadData("{{ $page_url }}/datatable")'
                                                        name="limit" class="form-select ms-2 me-2 p_search"
                                                        style="width:100px">
                                                        <option value="10">10</option>
                                                        <option value="25">25</option>
                                                        <option value="50">50</option>
                                                        <option value="100" selected>100</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="input-group input-group-merge">
                                                    <span class="input-group-text"><i
                                                            class="ti ti-search"></i></span>
                                                    <input
                                                        oninput='loadData("{{ $page_url }}/datatable")'
                                                        name="name" type="text" class="form-control p_search"
                                                        placeholder="ค้นหาชื่อพนักงาน..." />
                                                </div>
                                            </div>
                                            <div class="col-md-8 flex text-end" style="padding-right: unset !important;">
                                                    <div
                                                        class="dt-action-buttons d-flex flex-column align-items-start align-items-sm-center justify-content-sm-center pt-0 gap-sm-2 gap-sm-0 flex-sm-row">
                                                        <label class="me-3">ตั้งแต่วันที่:</label>
                                                        <div id="DataTables_Table_0_filter" class="dataTables_filter mx-n2 me-2">
                                                            @php
                                                                $day = date('d');

                                                                if ($day >= 15) {
                                                                    $start_date = date('15/m/Y');
                                                                } else {
                                                                    $start_date = date('01/m/Y');
                                                                }
                                                            @endphp
                                                            <input name="start_date" id="start_date" type="text" class="form-control p_search search_date" onchange='loadData("{{ $page_url }}/datatable")' value="{{ $start_date }}">
                                                        </div>
                                                        <label class="me-3">ถึงวันที่:</label>
                                                        <div
                                                            class="dt-action-buttons d-flex flex-column align-items-start align-items-sm-center justify-content-sm-center pt-0 gap-sm-2 gap-sm-0 flex-sm-row">
                                                            <div id="DataTables_Table_0_filter" class="dataTables_filter mx-n2 me-2">
                                                                <input name="end_date" id="end_date" type="text" class="form-control p_search search_date" onchange='loadData("{{ $page_url }}/datatable")' value="{{ date('d/m/Y') }}">
                                                            </div>
                                                        <div class="btn-group me-2 mb-2 mb-sm-0" role="group">
                                                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="setRange('today')">วันนี้</button>
                                                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="setRange('month')">เดือนนี้</button>
                                                        </div>
                                                        <div class="dt-buttons btn-group flex-wrap d-flex mb-6 mb-sm-0">
                                                            <button
                                                                class="btn btn-secondary add-new btn-primary me-2 ms-sm-0 waves-effect waves-light"
                                                                type="button"
                                                                onclick="printPdf()">
                                                                <span>
                                                                    <i class="ti ti-file-upload me-0 me-sm-1"></i>
                                                                    <span class="d-none d-sm-inline-block">พิมพ์
                                                                    </span>
                                                                </span>
                                                            </button>
                                                            {{-- <div class="btn-group">
                                                                <button
                                                                    class="btn btn-success buttons-collection  btn-warning waves-effect waves-light"
                                                                    tabindex="0" aria-controls="DataTables_Table_0"
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
                                        <div class="tab-content p-0" id="pills-tabContent">
                                            <div class="tab-pane fade show active" id="pills-profile"
                                                role="tabpanel" aria-labelledby="pills-profile-tab"
                                                tabindex="0">
                                                <div id="table-data">
                                                    {{-- GET ตาราง --}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @include('admin/layout/inc_footer')
                </div>
            </div>
        </div>
    </div>
    @include('admin/layout/inc_js')
</body>
</html>
<script>
    var page = "{{ $page_url }}/datatable";
    var searchData = {};
    loadData(page);

    function loadData(pages) {

        $('.p_search').each(function() {
            var inputName = $(this).attr('name'); // ดึงชื่อ attribute 'name' ของ input
            var inputValue = $(this).val(); // ดึงค่า value ของ input

            searchData[inputName] = inputValue; // เก็บข้อมูลลงในออบเจ็กต์ searchData
        });

        // alert(page);
        page = pages;
        $.ajax({
            type: "GET",
            url: pages,
            data: searchData,
            success: function(data) {
                $("#table-data").html(data);
            }
        });
        // alert(page);
    }

    function setRange(type){
        var d = new Date();
        function fmt(dt){ return ('0'+dt.getDate()).slice(-2)+'/'+('0'+(dt.getMonth()+1)).slice(-2)+'/'+dt.getFullYear(); }
        var start = type === 'today' ? fmt(d) : fmt(new Date(d.getFullYear(), d.getMonth(), 1));
        var end = fmt(d);
        $('#start_date').val(start); $('#end_date').val(end);
        try { $('#start_date').datepicker('setDate', start); $('#end_date').datepicker('setDate', end); } catch(e){}
        loadData("{{ $page_url }}/datatable");
    }
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
            '/admin/commission/drink-view-sales/pdf?' + queryString,
            '_blank'
        );
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
    });

    $('#end_date').on('changeDate', function (e) {
        $('#start_date').datepicker('setEndDate', e.date);
    });
</script>
