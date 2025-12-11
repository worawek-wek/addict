<!doctype html>

<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr"
    data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template">

<head>
    @include('admin/layout/inc_header')
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
                                                    <div class="dataTables_length mx-n2 ms-2"
                                                        id="DataTables_Table_0_length">
                                                        <label>Show
                                                            <select name="DataTables_Table_0_length"
                                                                aria-controls="DataTables_Table_0" class="form-select">
                                                                <option value="7">7</option>
                                                                <option value="10">10</option>
                                                                <option value="20">20</option>
                                                                <option value="50">50</option>
                                                                <option value="70">70</option>
                                                                <option value="100">100</option>
                                                            </select>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="d-flex justify-content-start justify-content-md-end align-items-baseline">
                                                    <label class="me-3">ตั้งแต่วันที่:</label>
                                                    <div
                                                        class="dt-action-buttons d-flex flex-column align-items-start align-items-sm-center justify-content-sm-center pt-0 gap-sm-2 gap-sm-0 flex-sm-row">
                                                        <div id="DataTables_Table_0_filter"
                                                            class="dataTables_filter mx-n2 me-2">
                                                            <input type="date" class="form-control">
                                                        </div>
                                                        <div class="dt-buttons btn-group flex-wrap d-flex mb-6 mb-sm-0">

                                                            <div
                                                                class="dt-action-buttons d-flex flex-column align-items-start align-items-sm-center justify-content-sm-center pt-0 gap-sm-2 gap-sm-0 flex-sm-row">
                                                                <label class="me-1">ถึงวันที่:</label>
                                                                <div id="DataTables_Table_0_filter"
                                                                    class="dataTables_filter mx-n2 me-2">
                                                                    <input type="date" class="form-control">
                                                                </div>

                                                                <button
                                                                    class="btn btn-secondary add-new btn-primary me-2 ms-sm-0 waves-effect waves-light"
                                                                    tabindex="0" aria-controls="DataTables_Table_0"
                                                                    type="button">
                                                                    <span>
                                                                        <i class="ti ti-file-upload me-0 me-sm-1"></i>
                                                                        <span class="d-none d-sm-inline-block">พิมพ์
                                                                        </span>
                                                                    </span>
                                                                </button>
                                                                <div class="btn-group">
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
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                                <table class="datatables-products table dataTable no-footer dtr-column"
                                                    id="DataTables_Table_0" aria-describedby="DataTables_Table_0_info"
                                                    style="width: 1396px;">
                                                    <thead class="border-top">
                                                        <tr class="table-info">
                                                            <th style="width: 5%;">#</th>
                                                            <th style="width: 5%;">ห้อง</th>
                                                            <th style="width: 5%;">วันที่</th>
                                                            <th style="width: 8%;">เวลา</th>
                                                            <th style="width: 5%;">ชม.</th>
                                                            <th style="width: 6%;">ราคา</th>
                                                            <th style="width: 10%;">ค่านวด</th>
                                                            <th style="width: 10%;">อาหาร</th>
                                                            <th style="width: 10%;">เครื่องดื่มพนักงาน</th>
                                                            <th style="width: 10%;">เครื่องดื่มลูกค้า</th>
                                                            <th style="width: 10%;">รวมเงิน</th>
                                                            <th style="width: 8%;">คูปอง</th>
                                                            <th style="width: 8%;">รับจริงของร้าน</th>
                                                            <th style="width: 8%;">สถานะ</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td class="text-center">654</td>
                                                            <td class="text-center">V3</td>
                                                            <td class="text-center">14/0...</td>
                                                            <td class="text-center"> 20:09</td>
                                                            <td class="text-center">00:56</td>
                                                            <td class="text-center">เงินสด</td>
                                                            <td class="text-right">3,000</td>
                                                            <td class="text-right">0</td>
                                                            <td class="text-right">0</td>
                                                            <td class="text-right">0</td>
                                                            <td class="text-right">3,000</td>
                                                            <td class="text-right">100</td>
                                                            <td class="text-right">1,400</td>
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-center">655</td>
                                                            <td class="text-center">V8</td>
                                                            <td class="text-center">14/0...</td>
                                                            <td class="text-center">20:14</td>
                                                            <td class="text-center">01:12</td>
                                                            <td class="text-center">เงินสด</td>
                                                            <td class="text-right">3,500</td>
                                                            <td class="text-right">0</td>
                                                            <td class="text-right">0</td>
                                                            <td class="text-right">0</td>
                                                            <td class="text-right">3,500</td>
                                                            <td class="text-right">100</td>
                                                            <td class="text-right">1,500</td>
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-center">656</td>
                                                            <td class="text-center">V9</td>
                                                            <td class="text-center">14/0...</td>
                                                            <td class="text-center">20:14</td>
                                                            <td class="text-center">00:50</td>
                                                            <td class="text-center">เงินสด</td>
                                                            <td class="text-right">3,500</td>
                                                            <td class="text-right">0</td>
                                                            <td class="text-right">0</td>
                                                            <td class="text-right">0</td>
                                                            <td class="text-right">3,500</td>
                                                            <td class="text-right">100</td>
                                                            <td class="text-right">1,500</td>
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-center">657</td>
                                                            <td class="text-center">V10</td>
                                                            <td class="text-center">14/0...</td>
                                                            <td class="text-center">20:15</td>
                                                            <td class="text-center">00:55</td>
                                                            <td class="text-center">เงินสด</td>
                                                            <td class="text-right">3,500</td>
                                                            <td class="text-right">0</td>
                                                            <td class="text-right">0</td>
                                                            <td class="text-right">0</td>
                                                            <td class="text-right">3,500</td>
                                                            <td class="text-right">200</td>
                                                            <td class="text-right">1,600</td>
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-center">658</td>
                                                            <td class="text-center">V11</td>
                                                            <td class="text-center">14/0...</td>
                                                            <td class="text-center">20:22</td>
                                                            <td class="text-center">00:50</td>
                                                            <td class="text-center">เงินสด</td>
                                                            <td class="text-right">3,500</td>
                                                            <td class="text-right">0</td>
                                                            <td class="text-right">0</td>
                                                            <td class="text-right">0</td>
                                                            <td class="text-right">3,500</td>
                                                            <td class="text-right">200</td>
                                                            <td class="text-right">1,600</td>
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-center">659</td>
                                                            <td class="text-center">8</td>
                                                            <td class="text-center">14/0...</td>
                                                            <td class="text-center">20:29</td>
                                                            <td class="text-center">00:47</td>
                                                            <td class="text-center">เงินสด</td>
                                                            <td class="text-right">2,500</td>
                                                            <td class="text-right">0</td>
                                                            <td class="text-right">0</td>
                                                            <td class="text-right">0</td>
                                                            <td class="text-right">2,500</td>
                                                            <td class="text-right">100</td>
                                                            <td class="text-right">1,000</td>
                                                            <td></td>
                                                        </tr>
                                                        <tr class="note">
                                                            <td class="text-center">660</td>
                                                            <td class="text-center">9</td>
                                                            <td class="text-center">14/0...</td>
                                                            <td class="text-center">20:29</td>
                                                            <td class="text-center">00:00</td>
                                                            <td class="text-center">เงินสด</td>
                                                            <td class="text-right">2,300</td>
                                                            <td class="text-right">0</td>
                                                            <td class="text-right">0</td>
                                                            <td class="text-right">0</td>
                                                            <td class="text-right">0</td>
                                                            <td class="text-right">100</td>
                                                            <td class="text-right">0</td>
                                                            <td>ยกเลิก</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-center">661</td>
                                                            <td class="text-center">7</td>
                                                            <td class="text-center">14/0...</td>
                                                            <td class="text-center">20:30</td>
                                                            <td class="text-center">00:34</td>
                                                            <td class="text-center">เงินสด</td>
                                                            <td class="text-right">2,300</td>
                                                            <td class="text-right">0</td>
                                                            <td class="text-right">0</td>
                                                            <td class="text-right">0</td>
                                                            <td class="text-right">2,300</td>
                                                            <td class="text-right">200</td>
                                                            <td class="text-right">800</td>
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-center">662</td>
                                                            <td class="text-center">J4</td>
                                                            <td class="text-center">14/0...</td>
                                                            <td class="text-center">20:32</td>
                                                            <td class="text-center">01:04</td>
                                                            <td class="text-center">เงินสด</td>
                                                            <td class="text-right">3,000</td>
                                                            <td class="text-right">0</td>
                                                            <td class="text-right">0</td>
                                                            <td class="text-right">0</td>
                                                            <td class="text-right">3,000</td>
                                                            <td class="text-right">200</td>
                                                            <td class="text-right">1,500</td>
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-center">663</td>
                                                            <td class="text-center">9</td>
                                                            <td class="text-center">14/0...</td>
                                                            <td class="text-center">20:35</td>
                                                            <td class="text-center">00:41</td>
                                                            <td class="text-center">เงินสด</td>
                                                            <td class="text-right">2,300</td>
                                                            <td class="text-right">0</td>
                                                            <td class="text-right">0</td>
                                                            <td class="text-right">0</td>
                                                            <td class="text-right">2,300</td>
                                                            <td class="text-right">100</td>
                                                            <td class="text-right">700</td>
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-center">685</td>
                                                            <td class="text-center">10</td>
                                                            <td class="text-center">14/0...</td>
                                                            <td class="text-center">22:32</td>
                                                            <td class="text-center">00:28</td>
                                                            <td class="text-center">เงินสด</td>
                                                            <td class="text-right">1,700</td>
                                                            <td class="text-right">0</td>
                                                            <td class="text-right">0</td>
                                                            <td class="text-right">0</td>
                                                            <td class="text-right">1,700</td>
                                                            <td class="text-right">100</td>
                                                            <td class="text-right">400</td>
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-center">686</td>
                                                            <td class="text-center">8</td>
                                                            <td class="text-center">14/0...</td>
                                                            <td class="text-center">22:32</td>
                                                            <td class="text-center">00:26</td>
                                                            <td class="text-center">เงินสด</td>
                                                            <td class="text-right">1,800</td>
                                                            <td class="text-right">0</td>
                                                            <td class="text-right">0</td>
                                                            <td class="text-right">0</td>
                                                            <td class="text-right">1,800</td>
                                                            <td class="text-right">100</td>
                                                            <td class="text-right">500</td>
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-center">687</td>
                                                            <td class="text-center">9</td>
                                                            <td class="text-center">14/0...</td>
                                                            <td class="text-center">22:44</td>
                                                            <td class="text-center">01:15</td>
                                                            <td class="text-center">เงินสด</td>
                                                            <td class="text-right">1,800</td>
                                                            <td class="text-right">0</td>
                                                            <td class="text-right">0</td>
                                                            <td class="text-right">0</td>
                                                            <td class="text-right">1,800</td>
                                                            <td class="text-right">200</td>
                                                            <td class="text-right">600</td>
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-center">688</td>
                                                            <td class="text-center">7</td>
                                                            <td class="text-center">14/0...</td>
                                                            <td class="text-center">23:07</td>
                                                            <td class="text-center">00:00</td>
                                                            <td class="text-center">เงินสด</td>
                                                            <td class="text-right">2,500</td>
                                                            <td class="text-right">0</td>
                                                            <td class="text-right">0</td>
                                                            <td class="text-right">0</td>
                                                            <td class="text-right">2,500</td>
                                                            <td class="text-right">100</td>
                                                            <td class="text-right">1,000</td>
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-center">689</td>
                                                            <td class="text-center">9</td>
                                                            <td class="text-center">14/0...</td>
                                                            <td class="text-center">00:00</td>
                                                            <td class="text-center">00:00</td>
                                                            <td class="text-center">เงินสด</td>
                                                            <td class="text-right">1,800</td>
                                                            <td class="text-right">0</td>
                                                            <td class="text-right">0</td>
                                                            <td class="text-right">0</td>
                                                            <td class="text-right">1,800</td>
                                                            <td class="text-right">200</td>
                                                            <td class="text-right">600</td>
                                                            <td></td>
                                                        </tr>
                                                        <tr class="grand-total">
                                                            <td colspan="6"
                                                                style="text-align: center; font-weight: bold;">
                                                                รวมทั้งสิ้น</td>
                                                            <td class="text-right">298,500</td>
                                                            <td class="text-right">0</td>
                                                            <td class="text-right">0</td>
                                                            <td class="text-right">0</td>
                                                            <td class="text-right">293,900</td>
                                                            <td class="text-right">13,400</td>
                                                            <td class="text-right">115,000</td>
                                                            <td></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <div class="row mt-3">
                                                    <div class="col-sm-12 col-md-6">
                                                        <div class="dataTables_info" id="DataTables_Table_0_info"
                                                            role="status" aria-live="polite">
                                                            แสดง 1 ถึง 10 จาก 100 รายการ
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-12 col-md-6">
                                                        <div class="dataTables_paginate paging_simple_numbers"
                                                            id="DataTables_Table_0_paginate">
                                                            <ul class="pagination justify-content-end">
                                                                <li class="paginate_button page-item previous disabled"
                                                                    id="DataTables_Table_0_previous"><a
                                                                        aria-controls="DataTables_Table_0"
                                                                        aria-disabled="true" role="link"
                                                                        data-dt-idx="previous" tabindex="-1"
                                                                        class="page-link"><i
                                                                            class="ti ti-chevron-left ti-sm"></i></a>
                                                                </li>
                                                                <li class="paginate_button page-item active">
                                                                    <a href="#"
                                                                        aria-controls="DataTables_Table_0"
                                                                        role="link" aria-current="page"
                                                                        data-dt-idx="0" tabindex="0"
                                                                        class="page-link">1</a>
                                                                </li>
                                                                <li class="paginate_button page-item "><a
                                                                        href="#"
                                                                        aria-controls="DataTables_Table_0"
                                                                        role="link" data-dt-idx="1" tabindex="0"
                                                                        class="page-link">2</a>
                                                                </li>
                                                                <li class="paginate_button page-item "><a
                                                                        href="#"
                                                                        aria-controls="DataTables_Table_0"
                                                                        role="link" data-dt-idx="2" tabindex="0"
                                                                        class="page-link">3</a>
                                                                </li>
                                                                <li class="paginate_button page-item "><a
                                                                        href="#"
                                                                        aria-controls="DataTables_Table_0"
                                                                        role="link" data-dt-idx="3" tabindex="0"
                                                                        class="page-link">4</a>
                                                                </li>
                                                                <li class="paginate_button page-item "><a
                                                                        href="#"
                                                                        aria-controls="DataTables_Table_0"
                                                                        role="link" data-dt-idx="4" tabindex="0"
                                                                        class="page-link">5</a>
                                                                </li>
                                                                <li class="paginate_button page-item disabled"
                                                                    id="DataTables_Table_0_ellipsis"><a
                                                                        aria-controls="DataTables_Table_0"
                                                                        aria-disabled="true" role="link"
                                                                        data-dt-idx="ellipsis" tabindex="-1"
                                                                        class="page-link">…</a></li>
                                                                <li class="paginate_button page-item "><a
                                                                        href="#"
                                                                        aria-controls="DataTables_Table_0"
                                                                        role="link" data-dt-idx="14"
                                                                        tabindex="0" class="page-link">15</a>
                                                                </li>
                                                                <li class="paginate_button page-item next"
                                                                    id="DataTables_Table_0_next"><a href="#"
                                                                        aria-controls="DataTables_Table_0"
                                                                        role="link" data-dt-idx="next"
                                                                        tabindex="0" class="page-link"><i
                                                                            class="ti ti-chevron-right ti-sm"></i></a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
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
</body>

</html>

<script>
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
