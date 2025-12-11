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

                                                    <!-- <label><input type="search" class="form-control"
                                                                placeholder="Search Product"
                                                                aria-controls="DataTables_Table_0"></label> -->
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
                                                <div
                                                    class="d-flex justify-content-start justify-content-md-end align-items-baseline">
                                                    <label class="me-3">ตั้งแต่วันที่:</label>
                                                    <div
                                                        class="dt-action-buttons d-flex flex-column align-items-start align-items-sm-center justify-content-sm-center pt-0 gap-sm-2 gap-sm-0 flex-sm-row">
                                                        <div id="DataTables_Table_0_filter"
                                                            class="dataTables_filter mx-n2 me-2">
                                                            <input type="date" class="form-control">
                                                        </div>
                                                        <label class="me-3">ถึงวันที่:</label>
                                                        <div
                                                            class="dt-action-buttons d-flex flex-column align-items-start align-items-sm-center justify-content-sm-center pt-0 gap-sm-2 gap-sm-0 flex-sm-row">
                                                            <div id="DataTables_Table_0_filter"
                                                                class="dataTables_filter mx-n2 me-2">
                                                                <input type="date" class="form-control">
                                                            </div>
                                                            <div
                                                                class="dt-buttons btn-group flex-wrap d-flex mb-6 mb-sm-0">

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
                                                    <thead>
                                                        <tr>
                                                            <th style="width: 8%;">วันที่</th>
                                                            <th style="width: 8%;">เวลา</th>
                                                            <th style="width: 20%;">ร้อยนัดราน</th>
                                                            <th style="width: 6%;">ชม.</th>
                                                            <th style="width: 10%;">@ราคา</th>
                                                            <th style="width: 10%;">รวมเงิน</th>
                                                            <th style="width: 10%;">รหัสผู้ดูแล</th>
                                                            <th style="width: 28%;">ชื่อผู้ดูแล</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>14/09...</td>
                                                            <td>14:58</td>
                                                            <td>NAM + 40 min...</td>
                                                            <td>1</td>
                                                            <td>1,200</td>
                                                            <td>1,200</td>
                                                            <td>993</td>
                                                            <td>M* PAING ไป</td>
                                                        </tr>
                                                        <tr>
                                                            <td>14/09...</td>
                                                            <td>16:24</td>
                                                            <td>NAM + 40 min...</td>
                                                            <td>1</td>
                                                            <td>1,200</td>
                                                            <td>1,200</td>
                                                            <td>991</td>
                                                            <td>M KATE</td>
                                                        </tr>
                                                        <tr>
                                                            <td>14/09...</td>
                                                            <td>17:47</td>
                                                            <td>NAM + 60 min...</td>
                                                            <td>1</td>
                                                            <td>1,500</td>
                                                            <td>1,500</td>
                                                            <td>991</td>
                                                            <td>M KATE</td>
                                                        </tr>
                                                        <tr>
                                                            <td>14/09...</td>
                                                            <td>20:56</td>
                                                            <td>NAM + 90 min...</td>
                                                            <td>1</td>
                                                            <td>1,700</td>
                                                            <td>1,700</td>
                                                            <td>899</td>
                                                            <td>NAT</td>
                                                        </tr>
                                                        <tr class="section-total">
                                                            <td colspan="3">รวมต่อคน</td>
                                                            <td>6</td>
                                                            <td></td>
                                                            <td>8,000</td>
                                                            <td colspan="2"></td>
                                                        </tr>
                                                        <tr class="row-spacing">
                                                            <td colspan="8"></td>
                                                        </tr>

                                                        <tr>
                                                            <td>615</td>
                                                            <td>14/09... 15:36</td>
                                                            <td>PANG (M) +...</td>
                                                            <td>1</td>
                                                            <td>1,400</td>
                                                            <td>1,400</td>
                                                            <td>899</td>
                                                            <td>NAT</td>
                                                        </tr>
                                                        <tr>
                                                            <td>629</td>
                                                            <td>14/09... 17:04</td>
                                                            <td>PANG (M) +...</td>
                                                            <td>1</td>
                                                            <td>1,900</td>
                                                            <td>1,900</td>
                                                            <td>993</td>
                                                            <td>M* PAING ไป</td>
                                                        </tr>
                                                        <tr>
                                                            <td>646</td>
                                                            <td>14/09... 19:10</td>
                                                            <td>PANG (M) +...</td>
                                                            <td>1</td>
                                                            <td>1,400</td>
                                                            <td>1,400</td>
                                                            <td>991</td>
                                                            <td>M KATE</td>
                                                        </tr>
                                                        <tr>
                                                            <td>669</td>
                                                            <td>14/09... 21:01</td>
                                                            <td>PANG (M) +...</td>
                                                            <td>1</td>
                                                            <td>1,400</td>
                                                            <td>1,400</td>
                                                            <td>991</td>
                                                            <td>M KATE</td>
                                                        </tr>
                                                        <tr>
                                                            <td>678</td>
                                                            <td>14/09... 21:53</td>
                                                            <td>PANG (M) +...</td>
                                                            <td>1</td>
                                                            <td>1,900</td>
                                                            <td>1,900</td>
                                                            <td>991</td>
                                                            <td>M KATE</td>
                                                        </tr>
                                                        <tr>
                                                            <td>688</td>
                                                            <td>14/09... 23:07</td>
                                                            <td>PANG (M) +...</td>
                                                            <td>1</td>
                                                            <td>1,400</td>
                                                            <td>1,400</td>
                                                            <td>993</td>
                                                            <td>M* PAING ไป</td>
                                                        </tr>
                                                        <tr class="section-total">
                                                            <td colspan="3">รวมต่อคน</td>
                                                            <td>6</td>
                                                            <td></td>
                                                            <td>9,400</td>
                                                            <td colspan="2"></td>
                                                        </tr>
                                                        <tr class="row-spacing">
                                                            <td colspan="8"></td>
                                                        </tr>

                                                        <tr>
                                                            <td>588</td>
                                                            <td>14/09... 12:23</td>
                                                            <td>I-RIN (M) +...</td>
                                                            <td>1</td>
                                                            <td>1,900</td>
                                                            <td>1,900</td>
                                                            <td>899</td>
                                                            <td>NAT</td>
                                                        </tr>
                                                        <tr>
                                                            <td>613</td>
                                                            <td>14/09... 15:29</td>
                                                            <td>I-RIN (M) +...</td>
                                                            <td>1</td>
                                                            <td>1,400</td>
                                                            <td>1,400</td>
                                                            <td>991</td>
                                                            <td>M KATE</td>
                                                        </tr>
                                                        <tr>
                                                            <td>655</td>
                                                            <td>14/09... 20:14</td>
                                                            <td>I-RIN (M) +...</td>
                                                            <td>1</td>
                                                            <td>1,900</td>
                                                            <td>1,900</td>
                                                            <td>899</td>
                                                            <td>NAT</td>
                                                        </tr>
                                                        <tr class="section-total">
                                                            <td colspan="3">รวมต่อคน</td>
                                                            <td>3</td>
                                                            <td></td>
                                                            <td>5,200</td>
                                                            <td colspan="2"></td>
                                                        </tr>
                                                        <tr class="row-spacing">
                                                            <td colspan="8"></td>
                                                        </tr>

                                                        <tr>
                                                            <td>586</td>
                                                            <td>14/09... 12:16</td>
                                                            <td>FON (M) +...</td>
                                                            <td>1</td>
                                                            <td>1,900</td>
                                                            <td>1,900</td>
                                                            <td>899</td>
                                                            <td>NAT</td>
                                                        </tr>
                                                        <tr>
                                                            <td>595</td>
                                                            <td>14/09... 13:27</td>
                                                            <td>FON (M) + MD...</td>
                                                            <td>1</td>
                                                            <td>2,900</td>
                                                            <td>2,900</td>
                                                            <td>899</td>
                                                            <td>NAT</td>
                                                        </tr>
                                                        <tr>
                                                            <td>633</td>
                                                            <td>14/09... 17:23</td>
                                                            <td>FON (M) + MD...</td>
                                                            <td>1</td>
                                                            <td>2,900</td>
                                                            <td>2,900</td>
                                                            <td>899</td>
                                                            <td>NAT</td>
                                                        </tr>
                                                        <tr>
                                                            <td>656</td>
                                                            <td>14/09... 20:14</td>
                                                            <td>FON (M) +...</td>
                                                            <td>1</td>
                                                            <td>1,900</td>
                                                            <td>1,900</td>
                                                            <td>899</td>
                                                            <td>NAT</td>
                                                        </tr>
                                                        <tr class="section-total">
                                                            <td colspan="3">รวมต่อคน</td>
                                                            <td>4</td>
                                                            <td></td>
                                                            <td>9,600</td>
                                                            <td colspan="2"></td>
                                                        </tr>
                                                        <tr class="row-spacing">
                                                            <td colspan="8"></td>
                                                        </tr>

                                                        <tr>
                                                            <td>595</td>
                                                            <td>14/09... 11:41</td>
                                                            <td>WE (M) +...</td>
                                                            <td>1</td>
                                                            <td>1,900</td>
                                                            <td>1,900</td>
                                                            <td>899</td>
                                                            <td>NAT</td>
                                                        </tr>
                                                        <tr>
                                                            <td>596</td>
                                                            <td>14/09... 13:28</td>
                                                            <td>WE (M) + MD 90...</td>
                                                            <td>1</td>
                                                            <td>2,900</td>
                                                            <td>2,900</td>
                                                            <td>899</td>
                                                            <td>NAT</td>
                                                        </tr>
                                                        <tr>
                                                            <td>617</td>
                                                            <td>14/09... 15:48</td>
                                                            <td>WE (M) +...</td>
                                                            <td>1</td>
                                                            <td>1,400</td>
                                                            <td>1,400</td>
                                                            <td>991</td>
                                                            <td>M KATE</td>
                                                        </tr>
                                                        <tr>
                                                            <td>651</td>
                                                            <td>14/09... 19:18</td>
                                                            <td>WE (M) +...</td>
                                                            <td>1</td>
                                                            <td>1,400</td>
                                                            <td>1,400</td>
                                                            <td>899</td>
                                                            <td>NAT</td>
                                                        </tr>
                                                        <tr>
                                                            <td>670</td>
                                                            <td>14/09... 21:02</td>
                                                            <td>WE (M) +...</td>
                                                            <td>1</td>
                                                            <td>1,400</td>
                                                            <td>1,400</td>
                                                            <td>991</td>
                                                            <td>M KATE</td>
                                                        </tr>
                                                        <tr class="section-total">
                                                            <td colspan="3">รวมต่อคน</td>
                                                            <td>5</td>
                                                            <td></td>
                                                            <td>9,000</td>
                                                            <td colspan="2"></td>
                                                        </tr>
                                                        <tr class="row-spacing">
                                                            <td colspan="8"></td>
                                                        </tr>

                                                        <tr>
                                                            <td>632</td>
                                                            <td>14/09... 17:22</td>
                                                            <td>BAITOEY + MD...</td>
                                                            <td>1</td>
                                                            <td>2,900</td>
                                                            <td>2,900</td>
                                                            <td>899</td>
                                                            <td>NAT</td>
                                                        </tr>
                                                        <tr>
                                                            <td>659</td>
                                                            <td>14/09... 20:29</td>
                                                            <td>BAITOEY +...</td>
                                                            <td>1</td>
                                                            <td>1,400</td>
                                                            <td>1,400</td>
                                                            <td>991</td>
                                                            <td>M KATE</td>
                                                        </tr>
                                                        <tr>
                                                            <td>676</td>
                                                            <td>14/09... 21:40</td>
                                                            <td>BAITOEY +...</td>
                                                            <td>1</td>
                                                            <td>2,200</td>
                                                            <td>2,200</td>
                                                            <td>992</td>
                                                            <td>BOOKING</td>
                                                        </tr>
                                                        <tr class="section-total">
                                                            <td colspan="3">รวมต่อคน</td>
                                                            <td>3</td>
                                                            <td></td>
                                                            <td>6,500</td>
                                                            <td colspan="2"></td>
                                                        </tr>
                                                        <tr class="row-spacing">
                                                            <td colspan="8"></td>
                                                        </tr>

                                                        <tr>
                                                            <td>626</td>
                                                            <td>14/09... 16:51</td>
                                                            <td>MEE-TUNG (70)...</td>
                                                            <td>1</td>
                                                            <td>1,500</td>
                                                            <td>1,500</td>
                                                            <td>991</td>
                                                            <td>M KATE</td>
                                                        </tr>
                                                        <tr>
                                                            <td>644</td>
                                                            <td>14/09... 17:57</td>
                                                            <td>MEE-TUNG (70)...</td>
                                                            <td>1</td>
                                                            <td>1,000</td>
                                                            <td>1,000</td>
                                                            <td>993</td>
                                                            <td>M* PAING ไป</td>
                                                        </tr>
                                                        <tr>
                                                            <td>662</td>
                                                            <td>14/09... 20:32</td>
                                                            <td>MEE-TUNG (70)...</td>
                                                            <td>1</td>
                                                            <td>1,300</td>
                                                            <td>1,300</td>
                                                            <td>993</td>
                                                            <td>M* PAING ไป</td>
                                                        </tr>
                                                        <tr class="section-total">
                                                            <td colspan="3">รวมต่อคน</td>
                                                            <td>3</td>
                                                            <td></td>
                                                            <td>3,800</td>
                                                            <td colspan="2"></td>
                                                        </tr>
                                                        <tr class="row-spacing">
                                                            <td colspan="8"></td>
                                                        </tr>

                                                        <tr>
                                                            <td>606</td>
                                                            <td>14/09... 14:48</td>
                                                            <td>DAISY (M) + PT...</td>
                                                            <td>1</td>
                                                            <td>1,700</td>
                                                            <td>1,700</td>
                                                            <td>899</td>
                                                            <td>NAT</td>
                                                        </tr>
                                                        <tr>
                                                            <td>628</td>
                                                            <td>14/09... 17:03</td>
                                                            <td>DAISY (M) + PT...</td>
                                                            <td>1</td>
                                                            <td>1,700</td>
                                                            <td>1,700</td>
                                                            <td>993</td>
                                                            <td>M* PAING ไป</td>
                                                        </tr>
                                                        <tr>
                                                            <td>658</td>
                                                            <td>14/09... 20:22</td>
                                                            <td>DAISY (M) + PT...</td>
                                                            <td>1</td>
                                                            <td>1,700</td>
                                                            <td>1,700</td>
                                                            <td>993</td>
                                                            <td>M* PAING ไป</td>
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
