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
                                                    <div
                                                        class="dt-action-buttons d-flex flex-column align-items-start align-items-sm-center justify-content-sm-center pt-0 gap-sm-2 gap-sm-0 flex-sm-row">
                                                        <label class="me-1">ตั้งแต่วันที่:</label>
                                                        <div id="DataTables_Table_0_filter"
                                                            class="dataTables_filter mx-n2 me-2">
                                                            <input type="date" class="form-control">
                                                        </div>
                                                        <label class="me-1">ถึงวันที่:</label>
                                                        <div id="DataTables_Table_0_filter"
                                                            class="dataTables_filter mx-n2 me-2">
                                                            <input type="date" class="form-control">
                                                        </div>
                                                        <div class="dt-buttons btn-group flex-wrap d-flex mb-6 mb-sm-0">

                                                            <button
                                                                class="btn btn-secondary add-new btn-primary me-2 ms-sm-0 waves-effect waves-light"
                                                                tabindex="0" aria-controls="DataTables_Table_0"
                                                                type="button"
                                                                    onclick="window.open('/admin/report/oversee-employee/pdf', '_blank');"
                                                                >
                                                                <span>
                                                                    <i class="ti ti-file-upload me-0 me-sm-1"></i>
                                                                    <span class="d-none d-sm-inline-block">พิมพ์
                                                                    </span>
                                                                </span>
                                                            </button>
                                                            <div class="btn-group">
                                                                <button
                                                                    class="btn btn-success buttons-collection  btn-warning waves-effect waves-light"
                                                                    tabindex="0" aria-controls="DataTables_Table_0"
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
                                                <div id="table-data"><!-- ตารางจะถูกโหลดตรงนี้ --></div>
                                            <table class="datatables-products table dataTable no-footer dtr-column"
                                                id="DataTables_Table_0" aria-describedby="DataTables_Table_0_info" style="display: none;"
                                                style="width: 1396px;">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 6%;">ห้อง</th>
                                                        <th style="width: 6%;">วันที่</th>
                                                        <th style="width: 8%;">เวลา</th>
                                                        <th style="width: 10%;">รหัสผู้ดูแล</th>
                                                        <th style="width: 15%;">ชื่อผู้ดูแล</th>
                                                        <th style="width: 28%;">ชื่อพนักงาน</th>
                                                        <th style="width: 6%;">นาที</th>
                                                        <th style="width: 10%;">@ราคา</th>
                                                        <th style="width: 8%;">ราคาเต็ม</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>549</td>
                                                        <td>14/09... </td>
                                                        <td>19:12</td>
                                                        <td>991</td>
                                                        <td>M KATE</td>
                                                        <td>MICKY + 40 min...</td>
                                                        <td>1</td>
                                                        <td class="amount">1,800</td>
                                                        <td class="amount">1,800</td>
                                                    </tr>
                                                    <tr>
                                                        <td>659</td>
                                                        <td>14/09...</td>
                                                        <td>20:29</td>
                                                        <td>991</td>
                                                        <td>M KATE</td>
                                                        <td>BAITOEY +...</td>
                                                        <td>1</td>
                                                        <td class="amount">2,500</td>
                                                        <td class="amount">2,500</td>
                                                    </tr>
                                                    <tr>
                                                        <td>661</td>
                                                        <td>14/09...</td>
                                                        <td>20:30</td>
                                                        <td>991</td>
                                                        <td>M KATE</td>
                                                        <td>MIN (61) + PT 60...</td>
                                                        <td>1</td>
                                                        <td class="amount">2,300</td>
                                                        <td class="amount">2,300</td>
                                                    </tr>
                                                    <tr>
                                                        <td>663</td>
                                                        <td>14/09...</td>
                                                        <td>20:35</td>
                                                        <td>991</td>
                                                        <td>M KATE</td>
                                                        <td>OIL + 60 min...</td>
                                                        <td>1</td>
                                                        <td class="amount">2,300</td>
                                                        <td class="amount">2,300</td>
                                                    </tr>
                                                    <tr>
                                                        <td>664</td>
                                                        <td>14/09...</td>
                                                        <td>20:40</td>
                                                        <td>991</td>
                                                        <td>M KATE</td>
                                                        <td>NE-NE + Nuru 60...</td>
                                                        <td>1</td>
                                                        <td class="amount">3,500</td>
                                                        <td class="amount">3,500</td>
                                                    </tr>
                                                    <tr>
                                                        <td>669</td>
                                                        <td>14/09...</td>
                                                        <td>21:01</td>
                                                        <td>991</td>
                                                        <td>M KATE</td>
                                                        <td>PANG (M) +...</td>
                                                        <td>1</td>
                                                        <td class="amount">2,500</td>
                                                        <td class="amount">2,500</td>
                                                    </tr>
                                                    <tr>
                                                        <td>670</td>
                                                        <td>14/09...</td>
                                                        <td>21:02</td>
                                                        <td>991</td>
                                                        <td>M KATE</td>
                                                        <td>WE (M) +...</td>
                                                        <td>1</td>
                                                        <td class="amount">2,500</td>
                                                        <td class="amount">2,500</td>
                                                    </tr>
                                                    <tr>
                                                        <td>672</td>
                                                        <td>14/09...</td>
                                                        <td>21:16</td>
                                                        <td>991</td>
                                                        <td>M KATE</td>
                                                        <td>MICKY + 40 min...</td>
                                                        <td>1</td>
                                                        <td class="amount">1,800</td>
                                                        <td class="amount">1,800</td>
                                                    </tr>
                                                    <tr>
                                                        <td>677</td>
                                                        <td>14/09...</td>
                                                        <td>21:52</td>
                                                        <td>991</td>
                                                        <td>M KATE</td>
                                                        <td>YOSHI + Nuru 60...</td>
                                                        <td>1</td>
                                                        <td class="amount">3,300</td>
                                                        <td class="amount">3,300</td>
                                                    </tr>
                                                    <tr>
                                                        <td>678</td>
                                                        <td>14/09...</td>
                                                        <td>21:53</td>
                                                        <td>991</td>
                                                        <td>M KATE</td>
                                                        <td>PANG (M) +...</td>
                                                        <td>1</td>
                                                        <td class="amount">3,300</td>
                                                        <td class="amount">3,300</td>
                                                    </tr>
                                                    <tr>
                                                        <td>679</td>
                                                        <td>14/09...</td>
                                                        <td>21:53</td>
                                                        <td>991</td>
                                                        <td>M KATE</td>
                                                        <td>ENGFA (M) + PT...</td>
                                                        <td>1</td>
                                                        <td class="amount">3,300</td>
                                                        <td class="amount">3,300</td>
                                                    </tr>
                                                    <tr>
                                                        <td>681</td>
                                                        <td>14/09...</td>
                                                        <td>22:03</td>
                                                        <td>991</td>
                                                        <td>M KATE</td>
                                                        <td>PUNPUN (67) +...</td>
                                                        <td>1</td>
                                                        <td class="amount">3,500</td>
                                                        <td class="amount">3,500</td>
                                                    </tr>
                                                    <tr>
                                                        <td>682</td>
                                                        <td>14/09...</td>
                                                        <td>22:03</td>
                                                        <td>991</td>
                                                        <td>M KATE</td>
                                                        <td>OIL + Nuru 60 min</td>
                                                        <td>1</td>
                                                        <td class="amount">3,500</td>
                                                        <td class="amount">3,500</td>
                                                    </tr>
                                                    <tr class="section-total">
                                                        <td colspan="5"
                                                            style="text-align: right; padding-right: 20px;">รวมต่อคน
                                                        </td>
                                                        <td>41</td>
                                                        <td></td>
                                                        <td class="amount">98,800</td>
                                                    </tr>
                                                    <tr class="double-line">
                                                        <td colspan="8"></td>
                                                    </tr>

                                                    <tr>
                                                        <td>609</td>
                                                        <td>14/09...</td>
                                                        <td>15:15</td>
                                                        <td>992</td>
                                                        <td>BOOKING</td>
                                                        <td>MIGUEL + 90...</td>
                                                        <td>1</td>
                                                        <td class="amount">3,000</td>
                                                        <td class="amount">3,000</td>
                                                    </tr>
                                                    <tr>
                                                        <td>638</td>
                                                        <td>14/09...</td>
                                                        <td>17:35</td>
                                                        <td>992</td>
                                                        <td>BOOKING</td>
                                                        <td>MIGUEL + 60...</td>
                                                        <td>1</td>
                                                        <td class="amount">2,500</td>
                                                        <td class="amount">2,500</td>
                                                    </tr>
                                                    <tr>
                                                        <td>676</td>
                                                        <td>14/09...</td>
                                                        <td>21:40</td>
                                                        <td>992</td>
                                                        <td>BOOKING</td>
                                                        <td>BAITOEY +...</td>
                                                        <td>1</td>
                                                        <td class="amount">4,500</td>
                                                        <td class="amount">4,500</td>
                                                    </tr>
                                                    <tr class="section-total">
                                                        <td colspan="5"
                                                            style="text-align: right; padding-right: 20px;">รวมต่อคน
                                                        </td>
                                                        <td>3</td>
                                                        <td></td>
                                                        <td class="amount">10,000</td>
                                                    </tr>
                                                    <tr class="double-line">
                                                        <td colspan="8"></td>
                                                    </tr>

                                                    <tr>
                                                        <td>601</td>
                                                        <td>14/09...</td>
                                                        <td>14:09</td>
                                                        <td>993</td>
                                                        <td>M* PAING ไป</td>
                                                        <td>GAM + 40 min...</td>
                                                        <td>1</td>
                                                        <td class="amount">1,800</td>
                                                        <td class="amount">1,800</td>
                                                    </tr>
                                                    <tr>
                                                        <td>604</td>
                                                        <td>14/09...</td>
                                                        <td>14:35</td>
                                                        <td>993</td>
                                                        <td>M* PAING ไป</td>
                                                        <td>MANOW + 90...</td>
                                                        <td>1</td>
                                                        <td class="amount">4,500</td>
                                                        <td class="amount">4,500</td>
                                                    </tr>
                                                    <tr>
                                                        <td>607</td>
                                                        <td>14/09...</td>
                                                        <td>14:58</td>
                                                        <td>993</td>
                                                        <td>M* PAING ไป</td>
                                                        <td>NAM + 40 min...</td>
                                                        <td>1</td>
                                                        <td class="amount">1,800</td>
                                                        <td class="amount">1,800</td>
                                                    </tr>
                                                    <tr>
                                                        <td>612</td>
                                                        <td>14/09...</td>
                                                        <td>15:29</td>
                                                        <td>993</td>
                                                        <td>M* PAING ไป</td>
                                                        <td>OIL + 40 min...</td>
                                                        <td>1</td>
                                                        <td class="amount">1,800</td>
                                                        <td class="amount">1,800</td>
                                                    </tr>
                                                    <tr>
                                                        <td>616</td>
                                                        <td>14/09...</td>
                                                        <td>15:37</td>
                                                        <td>993</td>
                                                        <td>M* PAING ไป</td>
                                                        <td>MICKY + 80 min...</td>
                                                        <td>1</td>
                                                        <td class="amount">3,000</td>
                                                        <td class="amount">3,000</td>
                                                    </tr>
                                                    <tr>
                                                        <td>621</td>
                                                        <td>14/09...</td>
                                                        <td>16:27</td>
                                                        <td>993</td>
                                                        <td>M* PAING ไป</td>
                                                        <td>BELL + 90 min...</td>
                                                        <td>1</td>
                                                        <td class="amount">3,500</td>
                                                        <td class="amount">3,500</td>
                                                    </tr>
                                                    <tr>
                                                        <td>624</td>
                                                        <td>14/09...</td>
                                                        <td>16:38</td>
                                                        <td>993</td>
                                                        <td>M* PAING ไป</td>
                                                        <td>MIN (61) + PT 40...</td>
                                                        <td>1</td>
                                                        <td class="amount">1,800</td>
                                                        <td class="amount">1,800</td>
                                                    </tr>
                                                    <tr>
                                                        <td>628</td>
                                                        <td>14/09...</td>
                                                        <td>17:03</td>
                                                        <td>993</td>
                                                        <td>M* PAING ไป</td>
                                                        <td>DAISY (M) + PT...</td>
                                                        <td>1</td>
                                                        <td class="amount">3,500</td>
                                                        <td class="amount">3,500</td>
                                                    </tr>
                                                    <tr>
                                                        <td>629</td>
                                                        <td>14/09...</td>
                                                        <td>17:04</td>
                                                        <td>993</td>
                                                        <td>M* PAING ไป</td>
                                                        <td>PANG (M) +...</td>
                                                        <td>1</td>
                                                        <td class="amount">3,500</td>
                                                        <td class="amount">3,500</td>
                                                    </tr>
                                                    <tr>
                                                        <td>634</td>
                                                        <td>14/09...</td>
                                                        <td>17:23</td>
                                                        <td>993</td>
                                                        <td>M* PAING ไป</td>
                                                        <td>PANDA + 60 min...</td>
                                                        <td>1</td>
                                                        <td class="amount">2,500</td>
                                                        <td class="amount">2,500</td>
                                                    </tr>
                                                    <tr>
                                                        <td>644</td>
                                                        <td>14/09...</td>
                                                        <td>17:57</td>
                                                        <td>993</td>
                                                        <td>M* PAING ไป</td>
                                                        <td>MEE-TUNG (70)...</td>
                                                        <td>1</td>
                                                        <td class="amount">1,800</td>
                                                        <td class="amount">1,800</td>
                                                    </tr>
                                                    <tr>
                                                        <td>653</td>
                                                        <td>14/09...</td>
                                                        <td>20:09</td>
                                                        <td>993</td>
                                                        <td>M* PAING ไป</td>
                                                        <td>YOSHI + 90 Bath...</td>
                                                        <td>1</td>
                                                        <td class="amount">4,000</td>
                                                        <td class="amount">4,000</td>
                                                    </tr>
                                                    <tr>
                                                        <td>658</td>
                                                        <td>14/09...</td>
                                                        <td>20:22</td>
                                                        <td>993</td>
                                                        <td>M* PAING ไป</td>
                                                        <td>DAISY (M) + PT...</td>
                                                        <td>1</td>
                                                        <td class="amount">3,500</td>
                                                        <td class="amount">3,500</td>
                                                    </tr>
                                                    <tr>
                                                        <td>662</td>
                                                        <td>14/09...</td>
                                                        <td>20:32</td>
                                                        <td>993</td>
                                                        <td>M* PAING ไป</td>
                                                        <td>MEE-TUNG (70)...</td>
                                                        <td>1</td>
                                                        <td class="amount">3,000</td>
                                                        <td class="amount">3,000</td>
                                                    </tr>
                                                    <tr>
                                                        <td>665</td>
                                                        <td>14/09...</td>
                                                        <td>20:56</td>
                                                        <td>993</td>
                                                        <td>M* PAING ไป</td>
                                                        <td>PINK + Nuru 60...</td>
                                                        <td>1</td>
                                                        <td class="amount">3,500</td>
                                                        <td class="amount">3,500</td>
                                                    </tr>
                                                    <tr>
                                                        <td>666</td>
                                                        <td>14/09...</td>
                                                        <td>20:56</td>
                                                        <td>993</td>
                                                        <td>M* PAING ไป</td>
                                                        <td>PUNPUN (67) +...</td>
                                                        <td>1</td>
                                                        <td class="amount">3,500</td>
                                                        <td class="amount">3,500</td>
                                                    </tr>
                                                    <tr>
                                                        <td>671</td>
                                                        <td>14/09...</td>
                                                        <td>21:04</td>
                                                        <td>993</td>
                                                        <td>M* PAING ไป</td>
                                                        <td>BELL + 40 min...</td>
                                                        <td>1</td>
                                                        <td class="amount">1,800</td>
                                                        <td class="amount">1,800</td>
                                                    </tr>
                                                    <tr>
                                                        <td>683</td>
                                                        <td>14/09...</td>
                                                        <td>22:05</td>
                                                        <td>993</td>
                                                        <td>M* PAING ไป</td>
                                                        <td>BELL + 60 min...</td>
                                                        <td>1</td>
                                                        <td class="amount">2,500</td>
                                                        <td class="amount">2,500</td>
                                                    </tr>
                                                    <tr>
                                                        <td>686</td>
                                                        <td>14/09...</td>
                                                        <td>22:32</td>
                                                        <td>993</td>
                                                        <td>M* PAING ไป</td>
                                                        <td>MIGUEL + 40...</td>
                                                        <td>1</td>
                                                        <td class="amount">1,800</td>
                                                        <td class="amount">1,800</td>
                                                    </tr>
                                                </tbody>
                                            </table>
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

        var page = "{{ route('report-oversee-employee.datatable') }}";
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
