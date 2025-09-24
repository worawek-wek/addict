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
                                            <div class="col-sm-12 mb-2">
                                                <h4 class="mb-0">
                                                    <i class="tf-icons ti ti-user-dollar text-main ti-md me-2"></i>
                                                    ดูค่าคอมมิชชั่นพนักงานขาย
                                                </h4>
                                            </div>
                                            <div class="col-sm-12 d-flex justify-content-end gap-2">
                                                <a href="{{ route('commission.index') }}" class="btn btn-main">
                                                    <i class="ti ti-currency-dollar"></i> จัดการค่าคอมมิชชั่น
                                                </a>
                                                <a href="{{ route('commission.view_massage') }}" class="btn btn-info">
                                                    <i class="ti ti-user"></i> ดูค่าคอมมิชชั่นพนักงานนวด
                                                </a>
                                            </div>
                                            <div class="col-sm-12">
                                                <form id="filter-form" class="row g-2 align-items-center" method="GET" action="">
                                                    <div class="col-auto">
                                                        <select class="form-select" id="date-range" name="range">
                                                            <option value="1">1 วันล่าสุด</option>
                                                            <option value="7">7 วันล่าสุด</option>
                                                            <option value="14">14 วันล่าสุด</option>
                                                            <option value="30">1 เดือนล่าสุด</option>
                                                            <option value="custom">ระบุวันที่เอง</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-auto" id="custom-date-fields" style="display:none;">
                                                        <input type="date" class="form-control" name="start" id="start-date" value="{{ request('start') }}">
                                                    </div>
                                                    <div class="col-auto" id="custom-date-fields-end" style="display:none;">
                                                        <input type="date" class="form-control" name="end" id="end-date" value="{{ request('end') }}">
                                                    </div>
                                                    <div class="col-auto" id="search-btn" style="display:none;">
                                                        <button type="submit" class="btn btn-primary">ค้นหา</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body px-0 pt-0">
                                        <div class="table-responsive">
                                            <table class="datatables-basic table dataTable no-footer dtr-column" id="commission-table-view" aria-describedby="commission-table-view_info">
                                                <thead class="border-top">
                                                    <tr class="table-info">
                                                        <th class="text-center" style="width: 10px;">#</th>
                                                        <th class="text-center">ชื่อพนักงาน</th>
                                                        <th class="text-center">สาขา</th>
                                                        <th class="text-center">ชื่อตำแหน่ง</th>
                                                        <th class="text-center">จำนวนเงินคอมมิชชั่น</th>
                                                        <th class="text-center">ดู Order</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="commission-table-body">
                                                    @foreach($staffData as $i => $staff)
                                                    <tr>
                                                        <td class="text-center">{{ $i + 1 }}</td>
                                                        <td class="text-center">{{ $staff['name'] }}{{ $staff['nickname'] ? ' (' . $staff['nickname'] . ')' : '' }}</td>
                                                        <td class="text-center">{{ $staff['branch'] }}</td>
                                                        <td class="text-center">{{ $staff['position'] }}</td>
                                                        <td class="text-center">{{ number_format($staff['commission'], 2) }} บาท</td>
                                                        <td class="text-center">
                                                            <a href="#" class="btn btn-sm btn-outline-info order-link-btn" data-base-url="{{ route('commission.sales_orders') }}" data-user-id="{{ $staff['id'] }}" target="_blank">
                                                                ดู Order
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />
                                            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                                            <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
                                            <script>
                                                function reloadTable() {
                                                    var params = $('#filter-form').serialize();
                                                    $.ajax({
                                                        url: window.location.pathname + '?ajax=1&' + params,
                                                        type: 'GET',
                                                        success: function(res) {
                                                            $('#commission-table-body').html(res);
                                                            updateOrderLinks();
                                                        }
                                                    });
                                                }

                                                function updateOrderLinks() {
                                                    var range = $('#date-range').val();
                                                    var start = $('#start-date').val();
                                                    var end = $('#end-date').val();
                                                    $('.order-link-btn').each(function() {
                                                        var baseUrl = $(this).data('base-url');
                                                        var userId = $(this).data('user-id');
                                                        var url = baseUrl + '?user_id=' + userId + '&range=' + range + '&start=' + start + '&end=' + end;
                                                        $(this).attr('href', url);
                                                    });
                                                }
                                                $(document).ready(function() {
                                                    $('#commission-table-view').DataTable({
                                                        language: {
                                                            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/th.json'
                                                        },
                                                        pageLength: 10,
                                                        ordering: true,
                                                        searching: true,
                                                        lengthChange: false
                                                    });
                                                    $('#date-range').on('change', function() {
                                                        if ($(this).val() === 'custom') {
                                                            $('#custom-date-fields').show();
                                                            $('#custom-date-fields-end').show();
                                                        } else {
                                                            $('#custom-date-fields').hide();
                                                            $('#custom-date-fields-end').hide();
                                                        }
                                                        reloadTable();
                                                    });
                                                    if ($('#date-range').val() === 'custom') {
                                                        $('#custom-date-fields').show();
                                                        $('#custom-date-fields-end').show();
                                                    }
                                                    $('#start-date, #end-date').on('change', function() {
                                                        if ($('#date-range').val() === 'custom') {
                                                            reloadTable();
                                                        }
                                                    });
                                                    updateOrderLinks();
                                                    $(document).on('mouseenter', '.order-link-btn', function() {
                                                        updateOrderLinks();
                                                    });
                                                });
                                            </script>
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
