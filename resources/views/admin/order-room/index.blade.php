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
                                                <div class="col-sm-3 mb-2">
                                                    <select name="branch_id" class="form-select p_search"
                                                        onchange='loadData("{{ route('order-rooms.datatable') }}")'>
                                                        @foreach ($branches as $branch)
                                                            <option value="{{ $branch->id }}">{{ $branch->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-sm-3 mb-2">
                                                    <select name="date_range" class="form-select p_search" onchange='onDateRangeChange()'>
                                                        <option value="">-- เลือกช่วงเวลา --</option>
                                                        <option value="1">1 วัน</option>
                                                        <option value="7">7 วัน</option>
                                                        <option value="14">14 วัน</option>
                                                        <option value="30">1 เดือน</option>
                                                        <option value="custom">ระบุวันที่เอง</option>
                                                    </select>
                                                </div>
                                                <div class="col-sm-3 mb-2" id="custom-date-group" style="display:none;">
                                                    <div class="input-group">
                                                        <input type="date" name="start_date" class="form-control p_search" placeholder="เริ่มวันที่" />
                                                        <span class="input-group-text">ถึง</span>
                                                        <input type="date" name="end_date" class="form-control p_search" placeholder="ถึงวันที่" />
                                                    </div>
                                                </div>
                                                <div class="col-sm-3 mb-2">
                                                    <div class="input-group input-group-merge">
                                                        <span class="input-group-text"><i class="ti ti-search"></i></span>
                                                        <input
                                                            oninput='loadData("{{ route('order-rooms.datatable') }}")'
                                                            name="search" type="text" class="form-control p_search"
                                                            placeholder="ค้นหาชื่อลูกค้า..." />
                                                    </div>
                                                </div>
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
                                                        <option value="5">5</option>
                                                        <option value="10" selected>10</option>
                                                        <option value="15">15</option>
                                                        <option value="20">20</option>
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
            if ($('select[name="date_range"]').val() !== 'custom') {
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
        $(document).on('change', 'input[name="start_date"], input[name="end_date"]', function() {
            loadData(page);
        });

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
    </script>
</body>

</html>
