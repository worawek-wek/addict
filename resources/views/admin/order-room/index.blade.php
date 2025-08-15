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
                                                <div class="col-sm-3">
                                                    <select name="branch_id" class="form-select p_search"
                                                        onchange='loadData("{{ route('order-rooms.datatable') }}")'>
                                                        <option value="">-- สาขา --</option>
                                                        @foreach ($branches as $branch)
                                                            <option value="{{ $branch->id }}">{{ $branch->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-sm-9">
                                                    <div class="input-group input-group-merge">
                                                        <span class="input-group-text"><i
                                                                class="ti ti-search"></i></span>
                                                        <input
                                                            oninput='loadData("{{ route('ord
                                                            er-rooms.datatable') }}")'
                                                            name="search" type="text" class="form-control p_search"
                                                            placeholder="ค้นหา..." />
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
