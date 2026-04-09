<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr"
    data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template">

<head>
    @include('admin/layout/inc_header')
    <title>การขายสินค้า (Order Products)</title>
</head>
<style>
    @media print {
        body {
            margin: 0;
        }
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
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card mb-3">
                                    <div class="card-header border-bottom">
                                        <div class="row g-3 justify-content-between">
                                            <div class="col-sm-4">
                                                <h4 class="mb-0">
                                                    <i class="tf-icons ti ti-bed text-main ti-md me-2"></i>
                                                    การขายสินค้า (Order Products)
                                                </h4>
                                            </div>
                                            
                                            <div class="col-sm-8 d-flex justify-content-end align-items-sm-center gap-2">
                                                ประวัติปิดการขาย:
                                                <select onchange='getHistoryRound(this.value)'
                                                    name="round" class="form-select ms-2 me-2"
                                                    style="width:220px">
                                                    <option value="current">ปัจจุบัน</option>
                                                    @foreach ($rounds as $round)
                                                        <option value="{{ $round->id }}">{{ date('d/m/Y H:i น.', strtotime($round->date_time)) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="row g-3">
                                                <div class="col-sm-3 mb-2">
                                                    <select name="branch_id" class="form-select p_search"
                                                        onchange='loadData("{{ route('order-products.datatable') }}")'>
                                                        @foreach ($branches as $branch)
                                                            <option value="{{ $branch->id }}">{{ $branch->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-sm-3 mb-2">
                                                    <div class="input-group input-group-merge">
                                                        <span class="input-group-text"><i
                                                                class="ti ti-search"></i></span>
                                                        <input
                                                            oninput='loadData("{{ route('order-products.datatable') }}")'
                                                            name="search" type="text" class="form-control p_search"
                                                            placeholder="ค้นหาเลขที่คำสั่งซื้อ..." />
                                                    </div>
                                                </div>
                                                <div class="col-md-6 flex text-end"
                                                    style="padding-right: unset !important;">

                                                    <button
                                                        style="padding-right: 14px;padding-left: 14px;margin-right: 0px;"
                                                        class="btn btn-primary buttons-collection  btn-info waves-effect waves-light me-2 ButtonSummaryReport"
                                                        tabindex="0" aria-controls="DataTables_Table_0" type="button"
                                                        aria-haspopup="dialog" aria-expanded="false"
                                                        onclick="printSummaryReport('/admin/order-products/pdf')"
                                                        id="ButtonSummaryReport"
                                                        >
                                                        <span><i class="ti ti-receipt"></i>
                                                            พิมพ์รายงานสรุปยอดขายล่าสุด</span>
                                                    </button>
                                                    <button
                                                        style="padding-right: 14px;padding-left: 14px;margin-right: 0px;"
                                                        class="btn btn-warning buttons-collection  btn-info waves-effect waves-light ButtonSummaryReport"
                                                        tabindex="0" aria-controls="DataTables_Table_0" type="button"
                                                        aria-haspopup="dialog" aria-expanded="false"
                                                        onclick="closures()">
                                                        <span><i class="ti ti-receipt"></i> ปิดการขายวันนี้</span>
                                                    </button>
                                                </div>
                                            </div>


                                        </div>
                                    </div>

                                    <div class="card-body">
                                        <div class="row p-3">
                                            <div class="col-lg-4">
                                                <div class="d-flex align-items-center mb-2 mb-md-0">
                                                    <label class="me-2">แสดง</label>
                                                    <select
                                                        onchange='loadData("{{ route('order-products.datatable') }}")'
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
    <div class="modal fade modalHeadDecor" id="insurance" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document" id="view2">

        </div>
    </div>
    <iframe id="print-iframe" style="display: none;"></iframe>

    @include('admin/layout/inc_js')
    <script>
        var page = "{{ route('order-products.datatable') }}";
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
                    runAfterLoad();
                    // bind pagination click
                    $('#table-data .pagination a').on('click', function(e) {
                        e.preventDefault();
                        loadData($(this).attr('href'));
                    });
                }
            });
        }

        function getHistoryRound(round) {
            $.ajax({
                type: "GET",
                url: "/admin/order-products/history/" + round,
                success: function(data) {
                    $('#insurance').modal('show');
                    $("#view2").html(data);
                    document.querySelector('select[name="round"]').value = 'current';
                }
            });
        }

        function printSummaryReport(url) {
            
            const iframe = document.getElementById('print-iframe');

            $.ajax({
                url: url,
                type: 'GET',
                success: function(html) {
                    const doc = iframe.contentWindow.document;
                    doc.open();
                    doc.write(html);
                    doc.close();

                    // รอโหลดก่อนค่อยพิมพ์
                    iframe.onload = function() {
                        iframe.contentWindow.focus();
                        iframe.contentWindow.print();
                    };
                },
                error: function(xhr) {
                    alert('เกิดข้อผิดพลาด');
                    console.error(xhr.responseText);
                }
            });
        }

        function closures() {
            Swal.fire({
                title: 'ยืนยันการปิดการขาย?',
                text: 'คุณต้องการปิดการขายนี้หรือไม่',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'ใช่, ปิดการขาย',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/admin/order-products/closures`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                status_id: 4
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('สำเร็จ!', 'ปิดการขายเรียบร้อย', 'success')
                                    .then(() => location.reload());
                            } else {
                                Swal.fire('ผิดพลาด!', data.message || 'ไม่สามารถปิดการขายได้', 'error');
                            }
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
                url: "{{ route('order-products.index') }}/" + id,
                success: function(data) {
                    $("#view").html(data);
                    $('#viewOrderRoomModal').modal('show');
                }
            });
        }
    </script>
</body>

</html>
