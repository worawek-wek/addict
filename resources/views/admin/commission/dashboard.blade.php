<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr"
    data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template">

<head>
    @include('admin/layout/inc_header')
    <title>Dashboard คอมมิชชั่น - CRM</title>
</head>
<style>
    .table th { font-size: 14px; font-weight: bold; }
    .tile { border-radius: 10px; padding: 16px 18px; color: #fff; }
    .tile .n { font-size: 22px; font-weight: 700; }
    .tile .l { font-size: 13px; opacity: .9; }
</style>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            @include('admin/layout/inc_sidemenu')
            <div class="layout-page">
                @include('admin/layout/inc_topmenu')
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="card mb-3">
                            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <h5 class="mb-0"><i class="ti ti-layout-dashboard"></i> Dashboard สรุปคอมมิชชั่น (มาม่า)</h5>
                                <div class="btn-group" role="group">
                                    <button type="button" id="btn-day" class="btn btn-outline-main" onclick="setPeriod('day')">วันนี้</button>
                                    <button type="button" id="btn-month" class="btn btn-main" onclick="setPeriod('month')">เดือนนี้</button>
                                </div>
                            </div>
                            <div class="card-body">
                                <input type="hidden" name="period" id="period" class="p_search" value="month">
                                <div class="row g-3">
                                    <div class="col-md-3" id="wrap-month">
                                        <label class="form-label">เดือน</label>
                                        <input type="month" name="ym" class="form-control p_search"
                                            value="{{ now()->format('Y-m') }}"
                                            onchange='loadData("{{ $page_url }}/datatable")'>
                                    </div>
                                    <div class="col-md-3" id="wrap-day" style="display:none;">
                                        <label class="form-label">วันที่</label>
                                        <input type="date" name="date" class="form-control p_search"
                                            value="{{ now()->toDateString() }}"
                                            onchange='loadData("{{ $page_url }}/datatable")'>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">สาขา</label>
                                        <select name="ref_branch_id" class="form-select p_search"
                                            onchange='loadData("{{ $page_url }}/datatable")'>
                                            <option value="">ทุกสาขา</option>
                                            @foreach ($branch as $b)
                                                <option value="{{ $b->id }}" @if (auth()->user()->ref_branch_id == $b->id) selected @endif>{{ $b->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">ค้นหาชื่อ</label>
                                        <input type="text" name="name" class="form-control p_search"
                                            placeholder="ชื่อพนักงาน..." oninput='loadData("{{ $page_url }}/datatable")'>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="table-data">
                            <div class="text-center text-muted py-5">กำลังโหลด...</div>
                        </div>
                    </div>
                    @include('admin/layout/inc_footer')
                </div>
            </div>
        </div>
    </div>

    @include('admin/layout/inc_js')
    <script>
        var page = "{{ $page_url }}/datatable";

        function setPeriod(p) {
            document.getElementById('period').value = p;
            document.getElementById('wrap-month').style.display = (p === 'month') ? 'block' : 'none';
            document.getElementById('wrap-day').style.display = (p === 'day') ? 'block' : 'none';
            document.getElementById('btn-month').className = (p === 'month') ? 'btn btn-main' : 'btn btn-outline-main';
            document.getElementById('btn-day').className = (p === 'day') ? 'btn btn-main' : 'btn btn-outline-main';
            loadData(page);
        }

        function loadData(pages) {
            var searchData = {};
            $('.p_search').each(function () {
                searchData[$(this).attr('name')] = $(this).val();
            });
            page = pages;
            $.ajax({ type: "GET", url: pages, data: searchData, success: function (data) { $("#table-data").html(data); } });
        }

        $(function () { loadData(page); });
    </script>
</body>

</html>
