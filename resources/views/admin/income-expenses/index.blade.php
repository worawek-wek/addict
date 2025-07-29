<!doctype html>

<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr"
    data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template">

<head>
    @include('layout/inc_header')
    <title>Dashboard - CRM | Vuexy - Bootstrap Admin Template</title>
</head>
<style>
    .new_box .col-md-6 {
        padding: 5px 12px;
    }
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
        padding: 1.25rem 0.5rem 1.25rem 1.25rem;
        color: white;
        background-color: #54BAB9;
        position: relative;
    }

    .modalHeadDecor .modal-title::after {
        position: absolute;
        top: 0;
        right: -64px;
        content: '';
        width: 0;
        height: 0;
        border-top: 67px solid #54BAB9;
        border-right: 65px solid transparent;
    }

    #pills-tablayout button {
        background: transparent;
    }

    #pills-tablayout button.active {
        color: #54BAB9 !important;
    }

    .select-floor {
        width: 100px;
    }

    .box {
        display: none;
    }

    @media screen and (min-width:1024px) {
        .col-lg5 {
            width: calc(100%/5);
        }
    }


    @media screen and (max-width:767px) {
        .select-floor {
            width: 100%;
        }
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
                                    <div class="card-header">
                                        <div class="row g-3 justify-content-between">
                                            <div class="col-sm-12">
                                                <h4 class="mb-0">
                                                    <i class="tf-icons ti ti-calculator text-main ti-xl" style="margin-right: 10px;"></i>
                                                    รายรับ-รายจ่าย
                                                </h4>
                                            </div>
                                        </div>
                                </div>
                                    <div class="row border-bottom border-light p-3">
                                        <div class="col-sm-6 col-lg-4" style="padding: 0 15px;">
                                            <div class="card card-border-shadow-info">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center mb-2 pb-1">
                                                        <div class="avatar me-2">
                                                        <span class="avatar-initial rounded bg-label-success"><i class="ti ti-chevron-up ti-md"></i></span>
                                                        </div>
                                                        <h4 class="ms-1 mb-0 text-success">{{ number_format($income,2) }}</h4>
                                                    </div>
                                                    <p class="mb-1">รายรับทั้งหมด</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-lg-4" style="padding: 0 15px;">
                                            <div class="card card-border-shadow-danger">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center mb-2 pb-1">
                                                        <div class="avatar me-2">
                                                        <span class="avatar-initial rounded bg-label-danger"><i class="ti ti-calendar-time ti-md"></i></span>
                                                        </div>
                                                        <h4 class="ms-1 mb-0 text-danger">{{ number_format($expenses,2) }}</h4>
                                                    </div>
                                                    <p class="mb-1">รายจ่ายทั้งหมด</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-lg-4 mb-4" style="padding: 0 15px;">
                                            <div class="card card-border-shadow-success" style="background-color: #dff7e9;">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center mb-2 mt-1 pb-1">
                                                        <h4 class="ms-1 mb-1" style="font-weight: 600;">{{ number_format($total,2) }}</h4>
                                                    </div>
                                                    <p class="mb-1">รวมทั้งหมด</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="input-group input-group-merge">
                                                    <span class="input-group-text" id="basic-addon-search31"><i class="ti ti-search"></i></span>
                                                    <input
                                                        name="search"
                                                        type="text"
                                                        class="form-control p_search"
                                                        placeholder="ค้นหาเลขที่/รายละเอียด"
                                                        aria-label="ค้นหาเลขที่/รายละเอียด"
                                                        oninput="loadData('{{$page_url}}/datatable')"
                                                        aria-describedby="basic-addon-search31" />
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <select onchange='loadData("{{$page_url}}/datatable")' name="ref_category_id" id="select2IEC2" class="select2 form-select form-select-lg p_search" data-allow-clear="true">
                                                    <option value="all">หมวดหมู่ทั้งหมด</option>
                                                    @foreach ($category as $cate)
                                                        <option value="{{$cate->id}}">{{$cate->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <select onchange='loadData("{{$page_url}}/datatable")' name="type" id="select2IE3" class="select2 form-select form-select-lg p_search" data-allow-clear="true">
                                                    <option value="all">รายรับ/รายจ่าย</option>
                                                    <option value="1">รายรับ</option>
                                                    <option value="2">รายจ่าย</option>
                                                </select>
                                            </div>
                                        </div>
                                    <div class="row mt-3 p-3">
                                            <div class="col-md-5" style="padding-right: unset !important;">
                                            </div>
                                            <div class="col-md-7" style="padding-right: unset !important;">
                                                <button
                                                        style="padding-right: 14px;padding-left: 14px;"
                                                        class="btn btn-success buttons-collection btn-warning waves-effect waves-light me-2"
                                                        tabindex="0" aria-controls="DataTables_Table_0"
                                                        type="button" aria-haspopup="dialog"
                                                        aria-expanded="false">
                                                    <span>
                                                    <i class="ti ti-upload"></i> ดาวน์โหลด Excel</span>
                                                </button>
                                                <button
                                                        style="padding-right: 14px;padding-left: 14px;"
                                                        class="btn btn-success buttons-collection btn-primary waves-effect waves-light me-2"
                                                        tabindex="0" aria-controls="DataTables_Table_0"
                                                        type="button" aria-haspopup="dialog"
                                                        aria-expanded="false">
                                                    <span>
                                                    <i class="ti ti-file-upload"></i> พิมพ์ใบสรุปรายรับรายจ่าย</span>
                                                </button>
                                                <button style="padding-right: 14px;padding-left: 14px;"
                                                        class="btn btn-success buttons-collection btn-danger waves-effect waves-light me-2"
                                                        tabindex="0" aria-controls="DataTables_Table_0"
                                                        type="button" aria-haspopup="dialog" data-bs-toggle="modal" data-bs-target="#expenses"
                                                        aria-expanded="false">
                                                    <span>
                                                    <i class="ti ti-plus"></i> เพิ่มรายจ่าย</span>
                                                </button>
                                                <button
                                                        style="padding-right: 14px;padding-left: 14px;margin-right: 0px;"
                                                        class="btn btn-success buttons-collection  btn-info waves-effect waves-light"
                                                        tabindex="0" aria-controls="DataTables_Table_0"
                                                        type="button" aria-haspopup="dialog"
                                                        aria-expanded="false" data-bs-toggle="modal" data-bs-target="#income">
                                                    <span><i class="ti ti-plus"></i> เพิ่มรายรับ</span>
                                                </button>
                                            </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-lg-4">
                                        <div class="d-flex align-items-center mb-2 mb-md-0">
                                            <label class="">Show</label>
                                            <select onchange='loadData("{{$page_url}}/datatable")' name="limit" class="form-select ms-2 me-2 p_search" style="width:100px">
                                                <option value="10">10</option>
                                                <option value="25">25</option>
                                                <option value="50">50</option>
                                                <option value="75">75</option>
                                                <option value="100">100</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mt-1" style="padding-right: unset !important;font-weight: 500;" align="right">
                                        <span style="font-size: 22px" class="me-2">
                                        @php
                                            $months = [
                                                'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
                                                'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
                                            ];

                                            $month_index = (int)date('m') - 1; // ดึงเดือนจากวันที่ปัจจุบัน
                                            $year = date('Y');  // ปีปัจจุบัน

                                            echo $months[$month_index] . " " . $year;
                                        @endphp    
                                        </span><span style="font-size: 16px"> ตั้งแต่</span>
                                    </div>
                                    <div class="col-md-2" style="padding-right: unset !important;">
                                        <input name="from_month" type="month" onchange='loadData("{{$page_url}}/datatable")' class="form-control p_search" id="exampleFormControlInput1" placeholder=""/>
                                    </div>
                                    <div class="col-md-1" style="padding-right: unset !important;font-weight: 500;" align="center">
                                        <span style="font-size: 22px"></span><span style="font-size: 16px">ถึง</span>
                                    </div>
                                    <div class="col-md-2" style="padding-right: unset !important;">
                                        <input name="to_month" type="month" onchange='loadData("{{$page_url}}/datatable")' class="form-control p_search" id="exampleFormControlInput1" placeholder=""  value="{{ date('Y-m') }}" />
                                    </div>
                                </div>
                                </div>
                                    <div class="card-body px-0 pt-0">
                                        <div class="tab-content p-0" id="pills-tabContent">
                                            <div class="tab-pane fade show active" id="pills-profile" role="tabpanel"
                                                aria-labelledby="pills-profile-tab" tabindex="0">

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
    <!--set rent Modal -->
    <div class="modal fade modalHeadDecor" id="expenses" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header rounded-0">
                    <span class="modal-title">
                        <span class="h5" style="color: white;">เพิ่มรายจ่าย</span>
                    </span>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="insert_expenses" enctype="multipart/form-data">		
                    @csrf
                    <input type="hidden" name="type" value="2">
                <div class="modal-body">      
                    <div class="row g-3">
                        <div class="col-sm-12">
                            <label for="exampleFormControlInput1" class="form-label">รายละเอียด <span class="text-danger">*</span></label>
                            <input name="label" type="text" class="form-control" id="exampleFormControlInput1" placeholder="รายละเอียด" />
                        </div>
                        <div class="col-sm-12">
                            <label for="exampleFormControlInput2" class="form-label">จำนวนเงิน (บาท) <span class="text-danger">*</span></label>
                            <input name="amount" type="text" class="form-control" id="exampleFormControlInput2" placeholder="จำนวนเงิน (บาท)" />
                        </div>
                        <div class="col-sm-12">
                            <label for="bs-datepicker-format" class="form-label">วันที่ <span class="text-danger">*</span></label>
                            @php
                                date_default_timezone_set('Asia/Bangkok');
                            @endphp
                            <input name="date" type="text" class="form-control" id="bs-datepicker-format" placeholder="วันที่" value="{{ date('d/m/Y') }}" />
                        </div>
                        <div class="col-sm-12">
                            <label for="select2IE1" class="form-label">หมวดหมู่ <span class="text-danger">*</span></label>
                            {{-- <input name="ref_category_id" type="text" class="form-control" id="exampleFormControlInput3" placeholder="หมวดหมู่" /> --}}
                            <select name="ref_category_id" id="select2IE1" class="select2 form-select form-select-lg" data-allow-clear="true">
                                @foreach ($category as $cate)
                                    @if ($cate->type == 2)
                                        <option value="{{$cate->id}}">{{$cate->name}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-12">
                            <label for="exampleFormControlInput4" class="form-label">รายจ่ายของห้อง</label>
                            <input name="room_name" type="text" class="form-control" id="exampleFormControlInput4" placeholder="เลขที่ ซอย ถนน อาคาร ห้องเลขที่ หรือหมู่บ้าน" />
                        </div>
                        <div class="col-sm-12">
                            <label for="exampleFormControlInput5" class="form-label">หลักฐานการจ่ายเงิน</label>
                            <input name="proof_of_payment" class="form-control" type="file" id="exampleFormControlInput5" />
                        </div>
                        <div class="col-sm-12">
                            <label for="exampleFormControlInput6" class="form-label">ใบสำคัญจ่าย</label>
                            <input name="payment_voucher" class="form-control" type="file" id="exampleFormControlInput6" />
                        </div>
                    </div>
                </div>
                {{--  --}}
                <div class="modal-footer rounded-0 justify-content-center">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">ปิด</button>
                    <button type="submit" class="btn btn-main">บันทึก</button>
                </div>
                </form>
                {{--  --}}
            </div>
        </div>
    </div>

    <!--set rent Modal -->
    <div class="modal fade modalHeadDecor" id="income" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header rounded-0">
                    <span class="modal-title">
                        <span class="h5" style="color: white;">เพิ่มรายรับ</span>
                    </span>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="insert_expenses" enctype="multipart/form-data">		
                    @csrf
                    <input type="hidden" name="type" value="1">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-sm-12">
                            <label for="exampleFormControlInput1" class="form-label">รายละเอียด <span class="text-danger">*</span></label>
                            <input name="label" type="text" class="form-control" id="exampleFormControlInput1" placeholder="รายละเอียด" />
                        </div>
                        <div class="col-sm-12">
                            <label for="exampleFormControlInput1" class="form-label">จำนวนเงิน (บาท) <span class="text-danger">*</span></label>
                            <input name="amount" type="text" class="form-control" id="exampleFormControlInput1" placeholder="จำนวนเงิน" />
                        </div>
                        <div class="col-sm-6">
                            <label for="exampleFormControlInput1" class="form-label">วันที่ <span class="text-danger">*</span></label>
                            <input name="date" type="text" class="form-control" id="exampleFormControlInput1" placeholder="วันที่" />
                        </div>
                        <div class="col-sm-6">
                            <label for="C" class="form-label">หมวดหมู่ <span class="text-danger">*</span></label>
                            <select name="ref_category_id" id="C" class="select2 form-select form-select-lg" data-allow-clear="true">
                                @foreach ($category as $cate)
                                    @if ($cate->type == 1)
                                        <option value="{{$cate->id}}">{{$cate->name}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-12">
                            <label name="room_name" for="exampleFormControlInput1" class="form-label">รายรับของห้อง</label>
                            <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="รายรับของห้อง" />
                        </div>
                        <b class="mt-4"><i class="tf-icons ti ti-calculator text-main ti-xl" style="margin-right: 10px;"></i>ใบเสร็จรับเงิน</b>
                        <div class="col-sm-12">
                            <label for="exampleFormControlInput1" class="form-label">รายละเอียดหัวบิล</label>
                            <input class="form-control" type="name" id="formFile" placeholder="รายละเอียดหัวบิล" />
                        </div>
                        <div class="col-sm-12">
                            <label for="exampleFormControlInput1" class="form-label">รายละเอียดที่อยู่</label>
                            <input class="form-control" type="name" id="formFile" placeholder="รายละเอียดที่อยู่" />
                        </div>
                        <div class="col-sm-6">
                            <label for="exampleFormControlInput1" class="form-label">เลขประจำตัวผู้เสียภาษี</label>
                            <input class="form-control" type="name" id="formFile" placeholder="เลขประจำตัวผู้เสียภาษี" />
                        </div>
                        <div class="col-sm-6">
                            <label for="exampleFormControlInput1" class="form-label">สำนักงาน/สาขาที่อยู่</label>
                            <input class="form-control" type="name" id="formFile" placeholder="สำนักงาน/สาขาที่อยู่" />
                        </div>
                        <div class="col-sm-12">
                            <label for="exampleFormControlInput1" class="form-label">หมายเลขโทรศัพท์</label>
                            <input class="form-control" type="name" id="formFile" placeholder="หมายเลขโทรศัพท์" />
                        </div>
                        <b class="mt-4"><i class="tf-icons ti ti-calculator text-main ti-xl" style="margin-right: 10px;"></i>รายการชำระเงิน</b>
                        <div class="col-sm-12">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th width="75%">รายการ</th>
                                        <th>จำนวนเงิน (บาท)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><input class="form-control" type="name" /></td>
                                        <td><input class="form-control" type="name" value="0" /></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>รวม</th>
                                        <th class=" mb-0 fw-bold">
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="col-sm-8">
                        </div>
                        <div class="col-sm-4 mt-2 demo-inline-spacing" align="right">
                        <button style="padding:5px 8px;"
                                class="btn btn-xs btn-danger buttons-collection btn-label-danger waves-effect waves-light"
                                tabindex="0" aria-controls="DataTables_Table_0"
                                type="button" aria-haspopup="dialog"
                                aria-expanded="false">
                            <span>
                            <i class="ti ti-plus"></i> เพิ่มส่วนลด</span>
                        </button>
                        <button style="padding:5px 8px;"
                                class="btn btn-xs btn-warning buttons-collection btn-label-warning waves-effect waves-light"
                                tabindex="0" aria-controls="DataTables_Table_0"
                                type="button" aria-haspopup="dialog"
                                aria-expanded="false">
                            <span><i class="ti ti-plus"></i> เพิ่มรายการ</span>
                        </button>
                        </div>
                        <div class="col-sm-12">
                            <label for="exampleFormControlInput1" class="form-label">หมายเหตุ</label>
                            <textarea class="form-control" type="name" placeholder="หมายเหตุ"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer rounded-0 justify-content-center">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">ปิด</button>
                    <button type="submit" class="btn btn-main">บันทึก</button>
                </div>
                </form>
            </div>
        </div>
    </div>


    <!--set rent Modal -->
    <div class="modal fade modalHeadDecor" id="insurance" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header rounded-0">
                    <span class="modal-title">
                        <span class="h5" style="color: white;">วันจันทร์, 10 มิถุนายน 2024</span>
                    </span>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="col-sm-12">
                        <table class="table table-bordered mt-3 mb-4">
                            <thead>
                                <tr>
                                    <th width="75%">รายละเอียด</th>
                                    <th>ค่าคืนประกันห้อง</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>ห้อง</td>
                                    <td>A101</td>
                                </tr>
                            </tbody>
                            <tbody>
                                <tr>
                                    <td>หมวดหมู่</td>
                                    <td>ย้ายออก</td>
                                </tr>
                            </tbody>
                            <tbody>
                                <tr>
                                    <td>จำนวนเงิน</td>
                                    <td class="text-danger">-7,000 บาท</td>
                                </tr>
                            </tbody>
                            <tbody>
                                <tr>
                                    <td>โดย</td>
                                    <td>นิชกานต์</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    {{-- <div class="row g-3">
                        <div class="col-sm-12">
                            <label for="exampleFormControlInput1" class="form-label">รายละเอียด <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="รายละเอียด" />
                        </div>
                        <div class="col-sm-12">
                            <label for="exampleFormControlInput1" class="form-label">จำนวนเงิน (บาท) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="จำนวนเงิน (บาท)" />
                        </div>
                        <div class="col-sm-12">
                            <label for="exampleFormControlInput1" class="form-label">วันที่ <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="วันที่" />
                        </div>
                        <div class="col-sm-12">
                            <label for="exampleFormControlInput1" class="form-label">หมวดหมู่ <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="หมวดหมู่" />
                        </div>
                        <div class="col-sm-12">
                            <label for="exampleFormControlInput1" class="form-label">รายจ่ายของห้อง</label>
                            <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="เลขที่ ซอย ถนน อาคาร ห้องเลขที่ หรือหมู่บ้าน" />
                        </div>
                        <div class="col-sm-12">
                            <label for="exampleFormControlInput1" class="form-label">หลักฐานการจ่ายเงิน</label>
                            <input class="form-control" type="file" id="formFile" />
                        </div>
                        <div class="col-sm-12">
                            <label for="exampleFormControlInput1" class="form-label">ใบสำคัญจ่าย</label>
                            <input class="form-control" type="file" id="formFile" />
                        </div>
                    </div> --}}
                </div>
                {{--  --}}
                {{-- <div class="modal-footer rounded-0 justify-content-center">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">ปิด</button>
                    <button type="button" class="btn btn-main">บันทึก</button>
                </div> --}}
                {{--  --}}
            </div>
        </div>
    </div>


    <!-- / Layout wrapper -->
    @include('layout/inc_js')
    <script>
        var page = "{{$page_url}}/datatable";
        var searchData = {};
        loadData(page);
        
        function loadData(pages){
            
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
                    $("#pills-profile").html(data);
                }
            });
            // alert(page);
        }

        function view(id){
            $.ajax({
                type: "GET",
                url: "{{ $page_url }}/"+id,
                success: function(data) {
                    $("#view").html(data);
                }
            });
        }

        $('#insert_expenses').submit(function(event) {
            event.preventDefault(); // ป้องกันการส่งฟอร์มปกติ
            if(!this.checkValidity()) {
                // ถ้าฟอร์มไม่ถูกต้อง
                this.reportValidity();
                return console.log('ฟอร์มไม่ถูกต้อง');
            }
            var formData = new FormData(this);
            // return alert(123);
            Swal.fire({
                title: 'ยืนยันการดำเนินการ?',
                text: 'คุณต้องการเพิ่มรายจ่ายหรือไม่?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'ตกลง',
                cancelButtonText: 'ยกเลิก',
                showDenyButton: false,
                didOpen: () => {
                    // โฟกัสที่ปุ่ม confirm
                    Swal.getConfirmButton().focus();
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{$page_url}}', // เปลี่ยน URL เป็นจุดหมายที่ต้องการ
                        type: 'POST',
                        data: formData,
                        contentType: false, // ไม่ต้องกำหนด contentType
                        processData: false, // ไม่ต้องแปลงข้อมูล FormData
                        success: function(response) {
                            if(response == true){
                                $('#insert_expenses')[0].reset();
                                Swal.fire('เพิ่มรายจ่ายเรียบร้อยแล้ว', '', 'success');
                                $('#expenses').modal('hide');
                                loadData(page);
                            }
                        },
                        error: function(error) {
                            Swal.fire('เกิดข้อผิดพลาด', '', 'error');
                            console.error('เกิดข้อผิดพลาด:', error);
                        }
                    });
                } else if (result.isDismissed) {
                    // Swal.fire('ยกเลิกการดำเนินการ', '', 'info');
                }
            });
        });
        $(document).ready(function() {
            $('input[type="radio"]').click(function() {
                var inputValue = $(this).attr("value");
                var targetBox = $("." + inputValue);
                $(".box").not(targetBox).hide();
                $(targetBox).show();
            });
        });
        
        $('#bs-datepicker-format').datepicker({
            format: 'dd/mm/yyyy', // กำหนดรูปแบบวันที่
            autoclose: true,      // ปิด datepicker เมื่อเลือกวันที่
            todayHighlight: true  // ไฮไลต์วันที่ปัจจุบัน
        });
        </script>
    <script src="assets/vendor/libs/select2/select2.js"></script>
    <script src="assets/vendor/libs/bootstrap-select/bootstrap-select.js"></script>
    <script src="assets/js/forms-selects.js"></script>

</body>

</html>