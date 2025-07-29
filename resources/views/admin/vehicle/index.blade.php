<!doctype html>

<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr"
    data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template">

<head>
    @include('layout/inc_header')
    <title>Dashboard - CRM | Vuexy - Bootstrap Admin Template</title>
</head>
<style>
.table th {
    font-size: 15px;
    font-weight: bold;
    border: 1px solid black
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

<link rel="stylesheet" href="assets/vendor/libs/select2/select2.css" />
<link rel="stylesheet" href="assets/vendor/libs/bootstrap-select/bootstrap-select.css" />

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            @include('layout/inc_sidemenu')
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->

                @include('layout/inc_topmenu')

                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->

                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="row ">
                            <div class="col-sm-12">
                                <div class="card mb-3">
                                    <div class="card-header border-bottom border-bottom">
                                        <div class="row g-3 justify-content-between">
                                            <div class="col-sm-12">
                                                <h4 class="mb-0">
                                                    <i class="tf-icons ti ti-car text-main ti-md"></i>
                                                    ยานพาหนะ
                                                </h4>
                                            </div>
                                            <div class="col-sm-12">
                                                <ul class="nav nav-pills nav-fill " role="tablist">
                                                    <li class="nav-item me-4">
                                                        <button type="button" class="nav-link active" role="tab"
                                                        style="background-color: #d6f4f7;color: black;"
                                                            data-bs-toggle="tab" data-bs-target="#navs-pills-top-home"
                                                            aria-controls="navs-pills-top-home"
                                                            aria-selected="true">ข้อมูลรถปัจจุบัน</button>
                                                    </li>
                                                    <li class="nav-item">
                                                        <button type="button" class="nav-link" role="tab"
                                                        style="background-color: #ffe2e3;color: black;"
                                                            data-bs-toggle="tab"
                                                            data-bs-target="#navs-pills-top-profile"
                                                            aria-controls="navs-pills-top-profile"
                                                            aria-selected="false">ข้อมูลรถห้องย้ายออก</button>
                                                    </li>
                                                </ul>
                                                <div class="row mt-4">
                                                    <div class="col-md-4">
                                                        <select id="selectpickerBasic" class="form-select me-2" data-style="btn-default">
                                                                <option value="">ประเภทรถ</option>
                                                                <option value="1">1</option>
                                                                <option value="2">2</option>
                                                                <option value="3">3</option>
                                                                <option value="4">4</option>
                                                                <option value="5">5</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="input-group input-group-merge">
                                                            <span class="input-group-text" id="basic-addon-search31"><i class="ti ti-search"></i></span>
                                                            <input
                                                            type="text"
                                                            class="form-control"
                                                            placeholder="ค้นหาตามหมายเลขห้อง"
                                                            aria-label="ค้นหาตามหมายเลขห้อง"
                                                            aria-describedby="basic-addon-search31" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="input-group input-group-merge">
                                                            <span class="input-group-text" id="basic-addon-search31"><i class="ti ti-search"></i></span>
                                                            <input
                                                            type="text"
                                                            class="form-control"
                                                            placeholder="ค้นหาตามทะเบียนรถ"
                                                            aria-label="ค้นหาตามทะเบียนรถ"
                                                            aria-describedby="basic-addon-search31" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="tab-content p-0">
                                            <div class="tab-pane fade show active" id="navs-pills-top-home"
                                                role="tabpanel">
                                                
                                                <div class="row border-bottom border-light p-3">
                                                    <div class="col-lg-4">
                                                        <div class="d-flex align-items-center mb-2 mb-md-0">
                                                            <label class="">Show</label>
                                                            <select name="" class="form-select ms-2 me-2" style="width:100px">
                                                                <option value="10">10</option>
                                                                <option value="25">25</option>
                                                                <option value="50">50</option>
                                                                <option value="75">75</option>
                                                                <option value="100">100</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-8 flex text-end" style="padding-right: unset !important;">
                                                        <button
                                                                style="padding-right: 14px;padding-left: 14px;"
                                                                class="btn btn-success buttons-collection btn-warning waves-effect waves-light me-2"
                                                                tabindex="0" aria-controls="DataTables_Table_0"
                                                                type="button" aria-haspopup="dialog"
                                                                aria-expanded="false">
                                                                <span>
                                                                    <i class="ti ti-upload"></i> 
                                                                    ดาวน์โหลด Excel
                                                                </span>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="card-body px-0 pt-0">
                                                    <div class="tab-content p-0" id="pills-tabContent">
                                                        <div class="tab-pane fade show active" id="pills-profile" role="tabpanel"
                                                            aria-labelledby="pills-profile-tab" tabindex="0">
                                                            <table class="datatables-basic table dataTable no-footer dtr-column"
                                                                    id="DataTables_Table_0" aria-describedby="DataTables_Table_0_info">
                                                                    <thead class="border-top">
                                                                        <tr class=" table-info">
                                                                            <th class="text-center" tabindex="0" style="width: 40px;">
                                                                                วันที่เพิ่มข้อมูล
                                                                            </th>
                                                                            <th class="text-center">
                                                                                เลขสติกเกอร์
                                                                            </th>
                                                                            <th class="text-center">
                                                                                รหัสรีโมท</th>
                                                                            <th class="text-center">
                                                                                ทะเบียนรถ</th>
                                                                            <th class="text-center">
                                                                                ประเภทรถ
                                                                            </th>
                                                                            <th class="text-center">
                                                                                รายละเอียดรถ
                                                                            </th>
                                                                            <th class="text-center">
                                                                                หมายเหตุ
                                                                            </th>
                                                                            <th class="text-center">
                                                                                ผู้เช่า
                                                                            </th>
                                                                            <th class="text-center">
                                                                                เลขห้อง
                                                                            </th>
                                                                            <th class="text-center">
                                                                                รูปภาพ
                                                                            </th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr class="odd">
                                                                            <td class="text-center">
                                                                                25/04/2022
                                                                            </td>
                                                                            <td class="text-center">
                                                                                ไม่ระบุ
                                                                            </td>
                                                                            <td class="text-center">
                                                                                ไม่ระบุ
                                                                            </td>
                                                                            <td class="text-center">
                                                                                7 กฌ 292
                                                                            </td>
                                                                            <td class="text-center">
                                                                                รถยนต์
                                                                            </td>
                                                                            <td class="text-center">
                                                                                รถสีแดง
                                                                            </td>
                                                                            <td class="text-center">
                                                                                -
                                                                            </td>
                                                                            <td class="text-center">
                                                                                นาย นิพนธ์ ม่วงสุนทร
                                                                            </td>
                                                                            <td class="text-center">
                                                                                A101
                                                                            </td>
                                                                            <td class="text-center">
                                                                                รูปภาพ
                                                                            </td>
                                                                        </tr>
                                                                        <tr class="odd">
                                                                            <td class="text-center">
                                                                                25/04/2022
                                                                            </td>
                                                                            <td class="text-center">
                                                                                ไม่ระบุ
                                                                            </td>
                                                                            <td class="text-center">
                                                                                ไม่ระบุ
                                                                            </td>
                                                                            <td class="text-center">
                                                                                7 กฌ 292
                                                                            </td>
                                                                            <td class="text-center">
                                                                                รถยนต์
                                                                            </td>
                                                                            <td class="text-center">
                                                                                รถสีแดง
                                                                            </td>
                                                                            <td class="text-center">
                                                                                -
                                                                            </td>
                                                                            <td class="text-center">
                                                                                นาย นิพนธ์ ม่วงสุนทร
                                                                            </td>
                                                                            <td class="text-center">
                                                                                A101
                                                                            </td>
                                                                            <td class="text-center">
                                                                                รูปภาพ
                                                                            </td>
                                                                        </tr>
                                                                        <tr class="odd">
                                                                            <td class="text-center">
                                                                                25/04/2022
                                                                            </td>
                                                                            <td class="text-center">
                                                                                ไม่ระบุ
                                                                            </td>
                                                                            <td class="text-center">
                                                                                ไม่ระบุ
                                                                            </td>
                                                                            <td class="text-center">
                                                                                7 กฌ 292
                                                                            </td>
                                                                            <td class="text-center">
                                                                                รถยนต์
                                                                            </td>
                                                                            <td class="text-center">
                                                                                รถสีแดง
                                                                            </td>
                                                                            <td class="text-center">
                                                                                -
                                                                            </td>
                                                                            <td class="text-center">
                                                                                นาย นิพนธ์ ม่วงสุนทร
                                                                            </td>
                                                                            <td class="text-center">
                                                                                A101
                                                                            </td>
                                                                            <td class="text-center">
                                                                                รูปภาพ
                                                                            </td>
                                                                        </tr>
                                                                        <tr class="odd">
                                                                            <td class="text-center">
                                                                                25/04/2022
                                                                            </td>
                                                                            <td class="text-center">
                                                                                ไม่ระบุ
                                                                            </td>
                                                                            <td class="text-center">
                                                                                ไม่ระบุ
                                                                            </td>
                                                                            <td class="text-center">
                                                                                7 กฌ 292
                                                                            </td>
                                                                            <td class="text-center">
                                                                                รถยนต์
                                                                            </td>
                                                                            <td class="text-center">
                                                                                รถสีแดง
                                                                            </td>
                                                                            <td class="text-center">
                                                                                -
                                                                            </td>
                                                                            <td class="text-center">
                                                                                นาย นิพนธ์ ม่วงสุนทร
                                                                            </td>
                                                                            <td class="text-center">
                                                                                A101
                                                                            </td>
                                                                            <td class="text-center">
                                                                                รูปภาพ
                                                                            </td>
                                                                        </tr>
                                                                        <tr class="odd">
                                                                            <td class="text-center">
                                                                                25/04/2022
                                                                            </td>
                                                                            <td class="text-center">
                                                                                ไม่ระบุ
                                                                            </td>
                                                                            <td class="text-center">
                                                                                ไม่ระบุ
                                                                            </td>
                                                                            <td class="text-center">
                                                                                7 กฌ 292
                                                                            </td>
                                                                            <td class="text-center">
                                                                                รถยนต์
                                                                            </td>
                                                                            <td class="text-center">
                                                                                รถสีแดง
                                                                            </td>
                                                                            <td class="text-center">
                                                                                -
                                                                            </td>
                                                                            <td class="text-center">
                                                                                นาย นิพนธ์ ม่วงสุนทร
                                                                            </td>
                                                                            <td class="text-center">
                                                                                A101
                                                                            </td>
                                                                            <td class="text-center">
                                                                                รูปภาพ
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            <div class="row">
                                                                <div class="col-sm-12 col-md-6 ps-4">
                                                                    <div class="dataTables_info" id="DataTables_Table_1_info"
                                                                        role="status" aria-live="polite">Showing 1 to 10 of 100
                                                                        entries</div>
                                                                </div>
                                                                <div class="col-sm-12 col-md-6 pe-4">
                                                                    <div class="dataTables_paginate paging_simple_numbers"
                                                                        id="DataTables_Table_1_paginate">
                                                                        <ul class="pagination justify-content-end">
                                                                            <li class="paginate_button page-item previous disabled"
                                                                                id="DataTables_Table_1_previous"><a
                                                                                    aria-controls="DataTables_Table_1"
                                                                                    aria-disabled="true" role="link"
                                                                                    data-dt-idx="previous" tabindex="-1"
                                                                                    class="page-link"><i
                                                                                        class="ti ti-chevron-left ti-sm"></i></a>
                                                                            </li>
                                                                            <li class="paginate_button page-item active"><a href="#"
                                                                                    aria-controls="DataTables_Table_1" role="link"
                                                                                    aria-current="page" data-dt-idx="0" tabindex="0"
                                                                                    class="page-link">1</a></li>
                                                                            <li class="paginate_button page-item "><a href="#"
                                                                                    aria-controls="DataTables_Table_1" role="link"
                                                                                    data-dt-idx="1" tabindex="0"
                                                                                    class="page-link">2</a></li>
                                                                            <li class="paginate_button page-item "><a href="#"
                                                                                    aria-controls="DataTables_Table_1" role="link"
                                                                                    data-dt-idx="2" tabindex="0"
                                                                                    class="page-link">3</a></li>
                                                                            <li class="paginate_button page-item "><a href="#"
                                                                                    aria-controls="DataTables_Table_1" role="link"
                                                                                    data-dt-idx="3" tabindex="0"
                                                                                    class="page-link">4</a></li>
                                                                            <li class="paginate_button page-item "><a href="#"
                                                                                    aria-controls="DataTables_Table_1" role="link"
                                                                                    data-dt-idx="4" tabindex="0"
                                                                                    class="page-link">5</a></li>
                                                                            <li class="paginate_button page-item disabled"
                                                                                id="DataTables_Table_1_ellipsis"><a
                                                                                    aria-controls="DataTables_Table_1"
                                                                                    aria-disabled="true" role="link"
                                                                                    data-dt-idx="ellipsis" tabindex="-1"
                                                                                    class="page-link">…</a></li>
                                                                            <li class="paginate_button page-item "><a href="#"
                                                                                    aria-controls="DataTables_Table_1" role="link"
                                                                                    data-dt-idx="14" tabindex="0"
                                                                                    class="page-link">15</a></li>
                                                                            <li class="paginate_button page-item next"
                                                                                id="DataTables_Table_1_next"><a href="#"
                                                                                    aria-controls="DataTables_Table_1" role="link"
                                                                                    data-dt-idx="next" tabindex="0"
                                                                                    class="page-link"><i
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
                                            <div class="tab-pane fade" id="navs-pills-top-profile" role="tabpanel">
                                                <div class="tab-pane fade show active" id="pills-profile" role="tabpanel"
                                                aria-labelledby="pills-profile-tab" tabindex="0">
                                                <div class="row border-bottom border-light p-3">
                                                    <div class="col-lg-5">
                                                        <div class="d-flex align-items-center mb-2 mb-md-0">
                                                            <label class="">Show</label>
                                                            <select name="" class="form-select ms-2 me-2" style="width:100px">
                                                                <option value="10">10</option>
                                                                <option value="25">25</option>
                                                                <option value="50">50</option>
                                                                <option value="75">75</option>
                                                                <option value="100">100</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-md-3 mt-1" style="padding-right: unset !important;font-weight: 500;" align="right">
                                                        <span style="font-size: 22px" class="me-2">พฤษภาคม 2024</span>
                                                    </div>
                                                    <div class="col-md-2" style="padding-right: unset !important;">
                                                        <input type="month" class="form-control" id="exampleFormControlInput1" placeholder="" />
                                                    </div>
                                                    <div class="col-md-2 text-end" style="padding-right: unset !important;">
                                                        <button
                                                                style="padding-right: 14px;padding-left: 14px;"
                                                                class="btn btn-success buttons-collection btn-warning waves-effect waves-light me-2"
                                                                tabindex="0" aria-controls="DataTables_Table_0"
                                                                type="button" aria-haspopup="dialog"
                                                                aria-expanded="false">
                                                                <span>
                                                                    <i class="ti ti-upload"></i> 
                                                                    ดาวน์โหลด Excel
                                                                </span>
                                                        </button>
                                                    </div>
                                                </div>
                                                
                                                {{-- <div class="row mt-3">
                                                    <div class="col-lg-4">
                                                        <div class="d-flex align-items-center mb-2 mb-md-0">
                                                            <label class="">Show</label>
                                                            <select name="" class="form-select ms-2 me-2" style="width:100px">
                                                                <option value="10">10</option>
                                                                <option value="25">25</option>
                                                                <option value="50">50</option>
                                                                <option value="75">75</option>
                                                                <option value="100">100</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 mt-1" style="padding-right: unset !important;font-weight: 500;" align="right">
                                                        <span style="font-size: 22px" class="me-2">พฤษภาคม 2024</span><span style="font-size: 16px"> วันที่</span>
                                                    </div>
                                                    <div class="col-md-2" style="padding-right: unset !important;">
                                                        <input type="month" class="form-control" id="exampleFormControlInput1" placeholder="" />
                                                    </div>
                                                    <div class="col-md-1" style="padding-right: unset !important;font-weight: 500;" align="center">
                                                        <span style="font-size: 22px"></span><span style="font-size: 16px">ถึงวันที่</span>
                                                    </div>
                                                    <div class="col-md-2" style="padding-right: unset !important;">
                                                        <input type="month" class="form-control" id="exampleFormControlInput1" placeholder="" />
                                                    </div>
                                                </div> --}}
                                                
                                                    <table class="datatables-basic table dataTable no-footer dtr-column"
                                                            id="DataTables_Table_0" aria-describedby="DataTables_Table_0_info">
                                                            <thead class="border-top">
                                                                <tr class=" table-info">
                                                                    <th class="text-center" tabindex="0" style="width: 40px;">
                                                                        วันที่เพิ่มข้อมูล
                                                                    </th>
                                                                    <th class="text-center">
                                                                        เลขสติกเกอร์
                                                                    </th>
                                                                    <th class="text-center">
                                                                        รหัสรีโมท</th>
                                                                    <th class="text-center">
                                                                        ทะเบียนรถ</th>
                                                                    <th class="text-center">
                                                                        ประเภทรถ
                                                                    </th>
                                                                    <th class="text-center">
                                                                        รายละเอียดรถ
                                                                    </th>
                                                                    <th class="text-center">
                                                                        หมายเหตุ
                                                                    </th>
                                                                    <th class="text-center">
                                                                        ผู้เช่า
                                                                    </th>
                                                                    <th class="text-center">
                                                                        เลขห้อง
                                                                    </th>
                                                                    <th class="text-center">
                                                                        รูปภาพ
                                                                    </th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr class="odd">
                                                                    <td class="text-center">
                                                                        25/04/2022
                                                                    </td>
                                                                    <td class="text-center">
                                                                        ไม่ระบุ
                                                                    </td>
                                                                    <td class="text-center">
                                                                        ไม่ระบุ
                                                                    </td>
                                                                    <td class="text-center">
                                                                        7 กฌ 292
                                                                    </td>
                                                                    <td class="text-center">
                                                                        รถยนต์
                                                                    </td>
                                                                    <td class="text-center">
                                                                        รถสีแดง
                                                                    </td>
                                                                    <td class="text-center">
                                                                        -
                                                                    </td>
                                                                    <td class="text-center">
                                                                        นาย นิพนธ์ ม่วงสุนทร
                                                                    </td>
                                                                    <td class="text-center">
                                                                        A101
                                                                    </td>
                                                                    <td class="text-center">
                                                                        รูปภาพ
                                                                    </td>
                                                                </tr>
                                                                <tr class="odd">
                                                                    <td class="text-center">
                                                                        25/04/2022
                                                                    </td>
                                                                    <td class="text-center">
                                                                        ไม่ระบุ
                                                                    </td>
                                                                    <td class="text-center">
                                                                        ไม่ระบุ
                                                                    </td>
                                                                    <td class="text-center">
                                                                        7 กฌ 292
                                                                    </td>
                                                                    <td class="text-center">
                                                                        รถยนต์
                                                                    </td>
                                                                    <td class="text-center">
                                                                        รถสีแดง
                                                                    </td>
                                                                    <td class="text-center">
                                                                        -
                                                                    </td>
                                                                    <td class="text-center">
                                                                        นาย นิพนธ์ ม่วงสุนทร
                                                                    </td>
                                                                    <td class="text-center">
                                                                        A101
                                                                    </td>
                                                                    <td class="text-center">
                                                                        รูปภาพ
                                                                    </td>
                                                                </tr>
                                                                <tr class="odd">
                                                                    <td class="text-center">
                                                                        25/04/2022
                                                                    </td>
                                                                    <td class="text-center">
                                                                        ไม่ระบุ
                                                                    </td>
                                                                    <td class="text-center">
                                                                        ไม่ระบุ
                                                                    </td>
                                                                    <td class="text-center">
                                                                        7 กฌ 292
                                                                    </td>
                                                                    <td class="text-center">
                                                                        รถยนต์
                                                                    </td>
                                                                    <td class="text-center">
                                                                        รถสีแดง
                                                                    </td>
                                                                    <td class="text-center">
                                                                        -
                                                                    </td>
                                                                    <td class="text-center">
                                                                        นาย นิพนธ์ ม่วงสุนทร
                                                                    </td>
                                                                    <td class="text-center">
                                                                        A101
                                                                    </td>
                                                                    <td class="text-center">
                                                                        รูปภาพ
                                                                    </td>
                                                                </tr>
                                                                <tr class="odd">
                                                                    <td class="text-center">
                                                                        25/04/2022
                                                                    </td>
                                                                    <td class="text-center">
                                                                        ไม่ระบุ
                                                                    </td>
                                                                    <td class="text-center">
                                                                        ไม่ระบุ
                                                                    </td>
                                                                    <td class="text-center">
                                                                        7 กฌ 292
                                                                    </td>
                                                                    <td class="text-center">
                                                                        รถยนต์
                                                                    </td>
                                                                    <td class="text-center">
                                                                        รถสีแดง
                                                                    </td>
                                                                    <td class="text-center">
                                                                        -
                                                                    </td>
                                                                    <td class="text-center">
                                                                        นาย นิพนธ์ ม่วงสุนทร
                                                                    </td>
                                                                    <td class="text-center">
                                                                        A101
                                                                    </td>
                                                                    <td class="text-center">
                                                                        รูปภาพ
                                                                    </td>
                                                                </tr>
                                                                <tr class="odd">
                                                                    <td class="text-center">
                                                                        25/04/2022
                                                                    </td>
                                                                    <td class="text-center">
                                                                        ไม่ระบุ
                                                                    </td>
                                                                    <td class="text-center">
                                                                        ไม่ระบุ
                                                                    </td>
                                                                    <td class="text-center">
                                                                        7 กฌ 292
                                                                    </td>
                                                                    <td class="text-center">
                                                                        รถยนต์
                                                                    </td>
                                                                    <td class="text-center">
                                                                        รถสีแดง
                                                                    </td>
                                                                    <td class="text-center">
                                                                        -
                                                                    </td>
                                                                    <td class="text-center">
                                                                        นาย นิพนธ์ ม่วงสุนทร
                                                                    </td>
                                                                    <td class="text-center">
                                                                        A101
                                                                    </td>
                                                                    <td class="text-center">
                                                                        รูปภาพ
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    <div class="row">
                                                        <div class="col-sm-12 col-md-6 ps-4">
                                                            <div class="dataTables_info" id="DataTables_Table_1_info"
                                                                role="status" aria-live="polite">Showing 1 to 10 of 100
                                                                entries</div>
                                                        </div>
                                                        <div class="col-sm-12 col-md-6 pe-4">
                                                            <div class="dataTables_paginate paging_simple_numbers"
                                                                id="DataTables_Table_1_paginate">
                                                                <ul class="pagination justify-content-end">
                                                                    <li class="paginate_button page-item previous disabled"
                                                                        id="DataTables_Table_1_previous"><a
                                                                            aria-controls="DataTables_Table_1"
                                                                            aria-disabled="true" role="link"
                                                                            data-dt-idx="previous" tabindex="-1"
                                                                            class="page-link"><i
                                                                                class="ti ti-chevron-left ti-sm"></i></a>
                                                                    </li>
                                                                    <li class="paginate_button page-item active"><a href="#"
                                                                            aria-controls="DataTables_Table_1" role="link"
                                                                            aria-current="page" data-dt-idx="0" tabindex="0"
                                                                            class="page-link">1</a></li>
                                                                    <li class="paginate_button page-item "><a href="#"
                                                                            aria-controls="DataTables_Table_1" role="link"
                                                                            data-dt-idx="1" tabindex="0"
                                                                            class="page-link">2</a></li>
                                                                    <li class="paginate_button page-item "><a href="#"
                                                                            aria-controls="DataTables_Table_1" role="link"
                                                                            data-dt-idx="2" tabindex="0"
                                                                            class="page-link">3</a></li>
                                                                    <li class="paginate_button page-item "><a href="#"
                                                                            aria-controls="DataTables_Table_1" role="link"
                                                                            data-dt-idx="3" tabindex="0"
                                                                            class="page-link">4</a></li>
                                                                    <li class="paginate_button page-item "><a href="#"
                                                                            aria-controls="DataTables_Table_1" role="link"
                                                                            data-dt-idx="4" tabindex="0"
                                                                            class="page-link">5</a></li>
                                                                    <li class="paginate_button page-item disabled"
                                                                        id="DataTables_Table_1_ellipsis"><a
                                                                            aria-controls="DataTables_Table_1"
                                                                            aria-disabled="true" role="link"
                                                                            data-dt-idx="ellipsis" tabindex="-1"
                                                                            class="page-link">…</a></li>
                                                                    <li class="paginate_button page-item "><a href="#"
                                                                            aria-controls="DataTables_Table_1" role="link"
                                                                            data-dt-idx="14" tabindex="0"
                                                                            class="page-link">15</a></li>
                                                                    <li class="paginate_button page-item next"
                                                                        id="DataTables_Table_1_next"><a href="#"
                                                                            aria-controls="DataTables_Table_1" role="link"
                                                                            data-dt-idx="next" tabindex="0"
                                                                            class="page-link"><i
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
                            </div>
                        </div>
                    </div>
                    <!-- / Content -->

                    <!-- Footer -->
                    @include('layout/inc_footer')
                    <!-- / Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>

        <!-- Drag Target Area To SlideIn Menu On Small Screens -->
        <div class="drag-target"></div>
    </div>
    <!--add service  Modal -->
    <div class="modal fade modalHeadDecor" id="addserviceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header rounded-0">
                    <h5 class="modal-title" id="exampleModalLabel1">เพิ่มค่าบริการ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label for="exampleFormControlInput1" class="form-label">ชื่อบริการ<span
                                    class="text-muted">(ภาษาไทย)</span></label>
                            <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="" />
                        </div>
                        <div class="col-sm-6">
                            <label for="exampleFormControlInput1" class="form-label">ชื่อบริการ<span
                                    class="text-muted">(ภาษาอังกฤษ)</span></label>
                            <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="" />
                        </div>
                        <div class="col-sm-12">
                            <label for="exampleFormControlInput1" class="form-label">ราคาค่าบริการ<span
                                    class="text-muted">(บาท/เดือน)</span></label>
                            <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer rounded-0 justify-content-center">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">ปิด</button>
                    <button type="button" class="btn btn-main">บันทึก</button>
                </div>
            </div>
        </div>
    </div>
    <!--edit service Modal -->
    <div class="modal fade modalHeadDecor" id="editserviceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header rounded-0">
                    <h5 class="modal-title" id="exampleModalLabel1">แก้ไขค่าซักผ้า</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label for="exampleFormControlInput1" class="form-label">ชื่อบริการ<span
                                    class="text-muted">(ภาษาไทย)</span></label>
                            <input type="text" class="form-control" id="exampleFormControlInput1" placeholder=""
                                value="ค่าซักผ้า" disabled />
                        </div>
                        <div class="col-sm-6">
                            <label for="exampleFormControlInput1" class="form-label">ชื่อบริการ<span
                                    class="text-muted">(ภาษาอังกฤษ)</span></label>
                            <input type="text" class="form-control" id="exampleFormControlInput1" placeholder=""
                                value="Laundry cost" disabled />
                        </div>
                        <div class="col-sm-12">
                            <label for="exampleFormControlInput1" class="form-label">ราคาค่าบริการ<span
                                    class="text-muted">(บาท/เดือน)</span></label>
                            <input type="text" class="form-control" id="exampleFormControlInput1" placeholder=""
                                value="350.00" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer rounded-0 justify-content-center">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">ปิด</button>
                    <button type="button" class="btn btn-main">บันทึก</button>
                </div>
            </div>
        </div>
    </div>
    <!--set service Modal -->
    <div class="modal fade modalHeadDecor" id="setServiceFeesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header rounded-0">
                    <h5 class="modal-title" id="exampleModalLabel1">กำหนดค่าบริการ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-sm-12">
                            <label for="exampleFormControlInput1" class="form-label">เลือกบริการ</label>
                            <div class="row g-3">
                                <div class="col-sm-4">
                                    <div class="form-check custom-option custom-option-basic checked">
                                        <label class="form-check-label custom-option-content" for="customRadioTemp1">
                                            <input name="customRadioTemp" class="form-check-input" type="radio" value=""
                                                id="customRadioTemp1" checked="">
                                            <span class="custom-option-header">
                                                <span class="h6 mb-0">ค่าซักผ้า</span>
                                            </span>
                                            <!-- <span class="custom-option-body">
                                                <small>Get 1 project with 1 teams members.</small>
                                            </span> -->
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-check custom-option custom-option-basic">
                                        <label class="form-check-label custom-option-content" for="customRadioTemp2">
                                            <input name="customRadioTemp" class="form-check-input" type="radio" value=""
                                                id="customRadioTemp2">
                                            <span class="custom-option-header">
                                                <span class="h6 mb-0">ค่าที่จอดรถ</span>
                                            </span>
                                            <!-- <span class="custom-option-body">
                                                <small>Get 1 project with 1 teams members.</small>
                                            </span> -->
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-check custom-option custom-option-basic">
                                        <label class="form-check-label custom-option-content" for="customRadioTemp3">
                                            <input name="customRadioTemp" class="form-check-input" type="radio" value=""
                                                id="customRadioTemp3">
                                            <span class="custom-option-header">
                                                <span class="h6 mb-0">ค่าอินเตอร์เน็ต</span>
                                            </span>
                                            <!-- <span class="custom-option-body">
                                                <small>Get 1 project with 1 teams members.</small>
                                            </span> -->
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <label for="exampleFormControlInput1" class="form-label">ราคาค่าบริการ<span
                                    class="text-muted">(บาท/เดือน)</span></label>
                            <input type="text" class="form-control" id="exampleFormControlInput1" placeholder=""
                                value="350.00" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer rounded-0 justify-content-center">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">ปิด</button>
                    <button type="button" class="btn btn-main">บันทึก</button>
                </div>
            </div>
        </div>
    </div>

    <!--add discount  Modal -->
    <div class="modal fade modalHeadDecor" id="adddiscountModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header rounded-0">
                    <h5 class="modal-title" id="exampleModalLabel1">เพิ่มรายการส่วนลด</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label for="exampleFormControlInput1" class="form-label">ชื่อบริการ<span
                                    class="text-muted">(ภาษาไทย)</span></label>
                            <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="" />
                        </div>
                        <div class="col-sm-6">
                            <label for="exampleFormControlInput1" class="form-label">ชื่อบริการ<span
                                    class="text-muted">(ภาษาอังกฤษ)</span></label>
                            <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="" />
                        </div>
                        <div class="col-sm-12">
                            <label for="exampleFormControlInput1" class="form-label">ราคาส่วนลด<span
                                    class="text-muted">(บาท)</span></label>
                            <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer rounded-0 justify-content-center">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">ปิด</button>
                    <button type="button" class="btn btn-main">บันทึก</button>
                </div>
            </div>
        </div>
    </div>
    <!--edit discount Modal -->
    <div class="modal fade modalHeadDecor" id="editDiscountModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header rounded-0">
                    <h5 class="modal-title" id="exampleModalLabel1">แก้ไขส่วนลดซักผ้า</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label for="exampleFormControlInput1" class="form-label">ชื่อบริการ<span
                                    class="text-muted">(ภาษาไทย)</span></label>
                            <input type="text" class="form-control" id="exampleFormControlInput1" placeholder=""
                                value="ส่วนลดซักผ้า" disabled />
                        </div>
                        <div class="col-sm-6">
                            <label for="exampleFormControlInput1" class="form-label">ชื่อบริการ<span
                                    class="text-muted">(ภาษาอังกฤษ)</span></label>
                            <input type="text" class="form-control" id="exampleFormControlInput1" placeholder=""
                                value="Laundry discount" disabled />
                        </div>
                        <div class="col-sm-12">
                            <label for="exampleFormControlInput1" class="form-label">ราคาส่วนลด<span
                                    class="text-muted">(บาท)</span></label>
                            <input type="text" class="form-control" id="exampleFormControlInput1" placeholder=""
                                value="50.00" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer rounded-0 justify-content-center">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">ปิด</button>
                    <button type="button" class="btn btn-main">บันทึก</button>
                </div>
            </div>
        </div>
    </div>
    <!--set discount Modal -->
    <div class="modal fade modalHeadDecor" id="setDiscountFeesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header rounded-0">
                    <h5 class="modal-title" id="exampleModalLabel1">กำหนดส่วนลด</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-sm-12">
                            <label for="exampleFormControlInput1" class="form-label">เลือกส่วนลด</label>
                            <div class="row g-3">
                                <div class="col-sm-4">
                                    <div class="form-check custom-option custom-option-basic checked">
                                        <label class="form-check-label custom-option-content" for="customRadioTemp1">
                                            <input name="customRadioTemp" class="form-check-input" type="radio" value=""
                                                id="customRadioTemp1" checked="">
                                            <span class="custom-option-header">
                                                <span class="h6 mb-0">ส่วนลดซักผ้า</span>
                                            </span>
                                            <!-- <span class="custom-option-body">
                                                <small>Get 1 project with 1 teams members.</small>
                                            </span> -->
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-check custom-option custom-option-basic">
                                        <label class="form-check-label custom-option-content" for="customRadioTemp2">
                                            <input name="customRadioTemp" class="form-check-input" type="radio" value=""
                                                id="customRadioTemp2">
                                            <span class="custom-option-header">
                                                <span class="h6 mb-0">ส่วนลดที่จอดรถ</span>
                                            </span>
                                            <!-- <span class="custom-option-body">
                                                <small>Get 1 project with 1 teams members.</small>
                                            </span> -->
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-check custom-option custom-option-basic">
                                        <label class="form-check-label custom-option-content" for="customRadioTemp3">
                                            <input name="customRadioTemp" class="form-check-input" type="radio" value=""
                                                id="customRadioTemp3">
                                            <span class="custom-option-header">
                                                <span class="h6 mb-0">ส่วนลดอินเตอร์เน็ต</span>
                                            </span>
                                            <!-- <span class="custom-option-body">
                                                <small>Get 1 project with 1 teams members.</small>
                                            </span> -->
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <label for="exampleFormControlInput1" class="form-label">ราคาส่วนลด<span
                                    class="text-muted">(บาท)</span></label>
                            <input type="text" class="form-control" id="exampleFormControlInput1" placeholder=""
                                value="50.00" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer rounded-0 justify-content-center">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">ปิด</button>
                    <button type="button" class="btn btn-main">บันทึก</button>
                </div>
            </div>
        </div>
    </div>
    <!-- / Layout wrapper -->
    @include('layout/inc_js')
    <script src="assets/vendor/libs/select2/select2.js"></script>
    <script src="assets/vendor/libs/bootstrap-select/bootstrap-select.js"></script>
    <script src="assets/js/forms-selects.js"></script>
</body>

</html>