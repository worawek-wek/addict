    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
{{-- <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" rel="stylesheet" /> --}}
{{-- <script src="assets/vendor/libs/jquery/jquery.js"></script> --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script> --}}

{{-- <script src="assets/vendor/libs/select2/select2.js"></script>
<script src="assets/vendor/libs/bootstrap-select/bootstrap-select.js"></script>
<script src="assets/js/forms-selects.js"></script> --}}

<link rel="stylesheet" href="assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css" />

<script src="assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js"></script>

<style>
    .select2-container--open {
        z-index: 9999;
    }
    .swal2-container {
        z-index: 9999 !important;
    }
    .nav-pills .nav-link.active {
        border: 2px solid #007bff;
        border-width: 1px;
        border-style: solid;
    }
    .nav-item .nav-link {
        border: none; /* ป้องกันไม่ให้มีเส้นโดยตรงบน .nav-link */
    }
    .dam {
        color: rgb(23, 23, 23);
        font-weight: 500;
        font-size: medium;
    }
    .dam-l {
        font-size: unset;
        color: rgb(23, 23, 23);
        font-weight: 410;
    }
    .nav-pills {
        --bs-nav-pills-link-active-bg: #ffffff;
    }
</style>
<div class="modal-content rounded-0">
    <div class="modal-header rounded-0">
        <span class="modal-title">
            <span class="h5" style="color: white;">&nbsp;{{ @$room->name }}&nbsp;</span>
        </span>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="col-md-12" style="padding-right: unset !important;">
        <div class="card shadow-none bg-transparent mb-3">
            <div class="card-body">
                <ul class="nav nav-pills" role="tablist" style="justify-content: space-between;padding: 0 35px;">
                    <li class="nav-item" role="presentation">
                    {{-- <button type="button" class="btn btn-outline-primary">Primary</button> --}}
                      <button class="btn btn-outline-info nav-link active" 
                        role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-top-edit" aria-controls="navs-pills-top-edit" aria-selected="false" tabindex="-1">
                        <span>
                          <i class="ti ti-users pe-1"></i>
                          <b class="dam">
                            ผู้เช่า
                          </b>
                        </span>
                      </button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="btn btn-outline-danger nav-link" 
                        role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-top-contract" aria-controls="navs-pills-top-contract" aria-selected="false" tabindex="-1">
                        <span>
                          <i class="ti ti-vocabulary pe-1"></i>
                          <b class="dam">
                          สัญญาเช่า
                          </b>
                        </span>
                      </button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="btn btn-outline-primary nav-link" 
                        role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-top-payment" aria-controls="navs-pills-top-payment" aria-selected="false" tabindex="-1">
                        <span>
                          <i class="ti ti-cash-banknote pe-1"></i>
                          <b class="dam">
                          ชำระเงิน
                          </b>
                        </span>
                      </button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="btn btn-outline-success nav-link" 
                        role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-top-assets" aria-controls="navs-pills-top-assets" aria-selected="false" tabindex="-1">
                        <span>
                          <i class="ti ti-building-warehouse pe-1"></i>
                          <b class="dam">
                          รายการทรัพย์สิน
                          </b>
                        </span>
                      </button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="btn btn-outline-warning nav-link" 
                        role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-top-MoveOut" aria-controls="navs-pills-top-MoveOut" aria-selected="false" tabindex="-1">
                        <span>
                          <i class="ti ti-door-exit pe-1"></i>
                          <b class="dam">
                          ย้ายออก
                          </b>
                        </span>
                      </button>
                    </li>
                </ul>
            </div>
        </div>
        <div class="modal-body" style="padding: 0 3em;">
            <div class="nav-align-top mb-3">
                    <div class="tab-content" style="box-shadow: unset;padding:0px">
                      <div class="tab-pane fade active show mb-5" id="navs-pills-top-edit" role="tabpanel">
                        <div class="card shadow-none bg-transparent">
                            <div class="card-body mb-4" style="background-color: #f5f5f5;border-radius: 1em;border: 1.6px solid #b5b5b56b;">
                                <div class="d-flex">
                                    <div class="col-sm-2">
                                        <img src="/main_picture/user-detail.png" width="100%" style="border-radius: 50%;">
                                    </div>
                                    <div class="col-sm-9 px-4">
                                        <b class="dam border-bottom border-light mb-2" style="display: block;">
                                            {{ $room_for_rent->prefix.' '.$room_for_rent->full_name }}
                                        </b>
                                            <b class="dam-l">
                                                เบอร์โทร :
                                            </b>
                                            <span>{{ $room_for_rent->phone }}</span>
                                            <br>
                                            <b class="dam-l">
                                                เลขบัตรประชาชน :
                                            </b>
                                            <span>{{ $room_for_rent->id_card_number }}</span>
                                            
                                                <div class="row mt-3">
                                                    <div class="col-md-4" style="padding-right: unset !important;">
                                                    </div>
                                                    <div class="col-md-4" style="padding-right: 0">
                                                        <select name="floor" id="select2Room" class="select2 form-select form-select-sm" data-style="btn-default">
                                                                <option value="all">ย้ายห้อง</option>
                                                                @foreach ($otherRooms as $or)
                                                                    <option value="{{ $or->id }}">{{ $or->name }}</option>
                                                                @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4 d-flex justify-content-bottom align-items-bottom">
                                                        <div class="text-end">
                                                            <button type="submit" id="move_room" class="btn btn-warning waves-effect waves-light" disabled>ยืนยัน</button>
                                                        </div>
                                                    </div>
                                                </div>
                                    </div>
                                </div>
                            </div>
                            {{-- <button type="button" class="btn btn-success waves-effect waves-light mt-3 m-auto"></button> --}}
                            <button class="btn btn-success waves-effect waves-light mt-3 m-auto"
                                    tabindex="0" aria-controls="DataTables_Table_0"
                                    type="button" aria-haspopup="dialog"
                                    aria-expanded="false" data-bs-toggle="modal" data-bs-target="#addRenter">
                                <span><i class="ti ti-plus"></i> เพิ่มข้อมูลผู้เช่า</span>
                            </button>

                        </div>
                    </div>
                      <div class="tab-pane fade" id="navs-pills-top-contract" role="tabpanel">
                        <form id="insert_one_contract">
                            @csrf
                            <input type="hidden" name="ref_renter_id" value="{{ $room_for_rent->id }}">
                            <input type="hidden" name="contract[1][ref_room_id]" value="{{ @$room->id }}">
                            <div class="m-2" style="border: 1px solid #dbdbdb;border-radius: 5px;">
                                <h5 class="border-bottom p-2" style="background-color: rgb(255, 248, 237);">
                                    <i class="tf-icons ti ti-vocabulary text-main" style="font-size: 25px;"></i>
                                    กรุณากรอกรายละเอียดสัญญาเช่า
                                </h5>
                                <div class="row g-2 p-4 pt-1">
                                    <div class="col-sm-12">
                                        <select name="ref_renter_id" id="select2RenterContract" class="select2 form-select form-select-lg" onchange="get_room_rental_contract(this.value)" required>
                                            <option selected disabled hidden value="no">เลือกข้อมูลจากผู้เช่า</option>
                                            @foreach ($renter as $rent)
                                                <option value="{{ $rent->id }}">{{ $rent->prefix.' '.$rent->name.' '.$rent->surname }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="row col-sm-12 g-2" id="room-detail-contract">
                                        @include('room/room-detail-contract')
                                    </div>
                                </div>
                                <script>
                                    $('#deduction_booking_date').datepicker({
                                        format: 'dd/mm/yyyy', // กำหนดรูปแบบวันที่
                                        autoclose: true,      // ปิด datepicker เมื่อเลือกวันที่
                                        todayHighlight: true  // ไฮไลต์วันที่ปัจจุบัน
                                    });
                                </script>
                            </div>
                        <script>
                            $('#contract_date').datepicker({
                                    format: 'dd/mm/yyyy', // กำหนดรูปแบบวันที่
                                    autoclose: true,      // ปิด datepicker เมื่อเลือกวันที่
                                    todayHighlight: true  // ไฮไลต์วันที่ปัจจุบัน
                                });
                        </script>
                        {{-- <form id="edit_user">		 --}}
                            {{-- @csrf --}}
                            
                            {{-- <div class="row g-3 p-4">
                                
                            </div> --}}
                            <div class="modal-footer rounded-0 justify-content-center">
                                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                                <button type="submit" class="btn btn-main">บันทึกสัญญา</button>
                            </div>
                        </form>
                      </div>
                      <div class="tab-pane fade" id="navs-pills-top-payment" role="tabpanel">
                                    <label class="mb-1">เลือกบิลย้อนหลัง</label>
                                        <select name="month" id="select2month" class="select2 form-select form-select-lg" onchange="get_bill(this.value)" required>
                                            {{-- <option selected disabled hidden value="no">เลือกข้อมูลจากผู้เช่า</option> --}}
                                            <option value="no">พฤษภาคม 2024</option>
                                            <option value="no">เมษายน 2024</option>
                                            <option value="no">มีนาคม 2024</option>
                                            {{-- @foreach ($renter as $rent)
                                                <option value="{{ $rent->id }}">{{ $rent->prefix.' '.$rent->name.' '.$rent->surname }}</option>
                                            @endforeach --}}
                                        </select>
                        <div id="bill">
                            <div class="alert alert-success text-black p-2 mt-4" role="alert">บิลเดือน พฤษภาคม/2024</div>
                            
                                <table class="table table-detail table-bordered">
                                    <thead>
                                        <tr>
                                            <th width="50%" style="vertical-align: middle;font-weight: 500;">สถานะบิล</th>
                                            <th style="vertical-align: middle;font-weight: 500;">
                                                <span class="text-success">ชำระเงิน (ผ่านเคาน์เตอร์หอพัก)</span><br>
                                                <span style="font-weight: 500;font-size: smaller;">เมื่อ 10/06/2024 , 16:18 น. โดย นิภา</span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td> วันที่รับชำระเงิน </td>
                                            <td>พฤหัสบดี 27/06/2024</td>
                                        </tr>
                                        <tr>
                                            <td> ช่องทางการชำระเงิน </td>
                                            <td>เงินสด</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table class="table table-detail table-bordered mt-4">
                                    <thead>
                                        <tr>
                                            <th width="70%" style="vertical-align: middle;font-weight: 500;">รายการ</th>
                                            <th style="vertical-align: middle;font-weight: 500;">
                                                จำนวนเงิน (บาท)
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td> ค่าเช่าห้อง A103 </td>
                                            <td>4,000</td>
                                        </tr>
                                        <tr>
                                            <td> ค่าน้ำประปา ( 911 - 908 = 3 ยูนิต) </td>
                                            <td>100</td>
                                        </tr>
                                        <tr>
                                            <td> ค่าไฟฟ้า ( 7194 - 7096 = 98 ยูนิต) </td>
                                            <td>680</td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>รวม</th>
                                            <th class=" mb-0 fw-bold">
                                            4,786
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                                
                                <div class="modal-footer rounded-0 justify-content-start mt-4">
                                    <button type="button" class="btn btn-label-primary waves-effect"><span
                                            class="ti-md ti ti-printer me-2"></span>พิมพ์</button>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade mb-5" id="navs-pills-top-assets" role="tabpanel">
                          <label class="mb-2" style="font-weight: 500;font-size: large;" for="">รายการทรัพย์สินทั้งหมด</label>
                              <table class="table table-detail table-bordered">
                                  <thead>
                                      <tr>
                                          <th width="50%" style="vertical-align: middle;font-weight: 500;">รายการ</th>
                                          <th width="50%" style="vertical-align: middle;font-weight: 500;">ค่าปรับ</th>
                                          <th width="50%" style="vertical-align: middle;font-weight: 500;">สถานะ</th>
                                      </tr>
                                  </thead>
                                  <tbody>
                                      <tr>
                                          <td> โทรทัศน์ </td>
                                          <td> {{number_format(1000)}} </td>
                                          <td><span class="badge bg-label-dark">ตั้งค่าข้อมูล</span></td>
                                      </tr>
                                      <tr>
                                          <td>ตู้เย็น</td>
                                          <td> {{number_format(2000)}} </td>
                                          <td>
                                            <span class="text-success">
                                                <i class="ti ti-checkbox"></i>
                                                มี
                                            </span>
                                          </td>
                                      </tr>
                                  </tbody>
                              </table>
                          </div>
                          <div class="tab-pane fade" id="navs-pills-top-MoveOut" role="tabpanel">
                            <div class="alert alert-success text-black p-2" role="alert"> รายละเอียดสัญญาเช่า</div>
                                <table class="table table-detail table-bordered">
                                    <thead>
                                        <tr>
                                            <th width="50%" style="vertical-align: middle;font-weight: 500;">วันที่ทำสัญญา : 03/06/2024</th>
                                            <th width="50%" style="vertical-align: middle;font-weight: 500;">วันที่สิ้นสุดสัญญา : 28/06/2024</th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td> สถานะสัญญา </td>
                                            <td class="text-success">ยังไม่หมดสัญญา</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="alert alert-warning p-2 mt-4" role="alert" align="center">
                                            <i class="ti ti-door-exit me-1"></i>ผู้เช่าย้ายออก
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="alert alert-danger p-2 mt-4" role="alert" align="center">
                                            <i class="ti ti-run me-1"></i>ผู้เช่าหนี
                                        </div>
                                    </div>
                                </div>

                                {{-- /////////////////////////////// --}}
                                
                                <label class="mb-0 text-black" style="font-weight: 500;font-size: large;" for="">
                                    <span class="badge badge-center rounded-pill bg-primary me-1" style="background-color: #54BAB9 !important;">1</span>
                                    รายการบิล
                                </label>

                                <table class="table table-detail table-bordered mt-4">
                                    <thead>
                                        <tr>
                                            <th style="vertical-align: middle;font-weight: 500;">รายการ</th>
                                            <th style="vertical-align: middle;font-weight: 500;">
                                                จำนวนเงิน (บาท)
                                            </th>
                                            <th>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                บิลค่าเช่าห้องเดือน 3/2024
                                                <span class="mx-2 badge bg-label-danger">ค้างชำระ</span>
                                            </td>
                                            <td>
                                                <span>{{ number_format(4000) }}</span>
                                            </td>
                                            <td>
                                                <button class="btn btn-main">ยืนยันการชำระเงิน</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="my-5 p-2 text-white" style="background-color: rgb(255, 73, 73);" align="center">
                                    ยอดค้างชำระ {{ number_format(4000) }}
                                </div>

                                {{-- /////////////////////////////// --}}

                                <label class="mb-0 text-black" style="font-weight: 500;font-size: large;" for="">
                                    <span class="badge badge-center rounded-pill bg-primary me-1" style="background-color: #54BAB9 !important;">2</span>
                                    รายการทรัพย์สิน (รับห้อง)
                                </label>
                                <style>
                                    .table-detail {
                                        border-collapse: collapse; /* รวมเส้นขอบของตาราง */
                                        /* border-radius: 10px; */
                                    }
                                    .table-detail th, .table-detail td {
                                        border: 1px solid #d9d9d9 !important; /* กำหนดเส้นขอบของ th และ td */
                                    }
                                    .table-detail th {
                                        vertical-align: middle;
                                        font-weight: 500;
                                        font-size: 14px;
                                        color: black !important;
                                    }
                                </style>
                                <table class="table table-bordered mt-4 table-detail">
                                    <thead>
                                        <tr>
                                            <th class="text-center">รายการ</th>
                                            <th class="text-center">
                                                สถานะทรัพย์สิน
                                            </th>
                                            <th class="text-center">
                                                ค่าปรับ
                                            </th>
                                            <th class="text-center">
                                                รูปภาพก่อนเข้าพัก
                                            </th>
                                            <th class="text-center">
                                                รูปภาพก่อนย้ายออก
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="text-center">
                                            <td>
                                                โทรทัศน์
                                            </td>
                                            <td>
                                                <input name="radio1" class="form-check-input" type="radio" id="defaultRadio1" checked>
                                                <label class="form-check-label" for="defaultRadio1"> ไม่เสียหาย </label>
                                                <input name="radio1" class="form-check-input" type="radio" id="defaultRadio2"> 
                                                <label class="form-check-label" for="defaultRadio2"> เสียหาย </label>
                                            </td>
                                            <td>
                                                <span >{{ number_format(1000) }}</span>
                                            </td>
                                            <td>
                                                <button class="btn btn-xs btn-label-secondary waves-effect text-black px-2"><i class="ti ti-photo me-1"></i> ภาพก่อนเข้าพัก</button>
                                            </td>
                                            <td>
                                                <button class="btn btn-xs btn-label-info waves-effect text-black px-2"><i class="ti ti-photo me-1"></i> อัพโหลดหลักฐาน</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                {{-- /////////////////////////////// --}}
                                
                                <label class="mt-4 text-black" style="font-weight: 500;font-size: large;" for="">
                                    <span class="badge badge-center rounded-pill bg-primary me-1" style="background-color: #54BAB9 !important;">3</span>
                                    ใบเสร็จย้ายออก
                                </label>
                                <div class="row g-2 pt-1">
                                    <div class="p-2">
                                        <label class="mb-1 text-black"><i class="ti ti-license text-main mb-1"></i> รายละเอียดหัวบิล</label>
                                            <select name="ref_renter_id" id="select2RenterDetail" class="select2 form-select form-select-lg" onchange="get_room_rental_contract(this.value)" required>
                                                <option selected disabled hidden value="no">เลือกข้อมูลจากผู้เช่า</option>
                                                @foreach ($renter as $rent)
                                                    <option value="{{ $rent->id }}">{{ $rent->prefix.' '.$rent->name.' '.$rent->surname }}</option>
                                                @endforeach
                                            </select>
                                    </div>
                                    <div class="col-sm-12">
                                        <label for="exampleFormControlInput1" class="form-label">ชื่อผู้เข้าพัก</label>
                                        <input type="text" name="name" class="form-control" id="exampleFormControlInput1" placeholder="" value="" />
                                    </div>
                                    <div class="col-sm-12">
                                        <label for="exampleFormControlInput2" class="form-label">ที่อยู่ผู้เข้าพัก</label>
                                        <input type="text" name="homeland" class="form-control" id="exampleFormControlInput2" placeholder="" value="" />
                                    </div>
                                    <div class="col-sm-12">
                                        <label for="exampleFormControlInput3" class="form-label">เบอร์โทรผู้เข้าพัก</label>
                                        <input type="text" name="phone" class="form-control" id="exampleFormControlInput3" placeholder="" value="" />
                                    </div>
                                    <div class="col-sm-12">
                                        <label for="exampleFormControlInput4" class="form-label">หมายเลขบัตรประชาชนผู้เข้าพัก</label>
                                        <input type="text" name="id_card_number" class="form-control" id="exampleFormControlInput4" placeholder="" value="" />
                                    </div>
                                    <div class="col-sm-12">
                                        <label for="" class="form-label">หมายเหตุ</label>
                                        <textarea name="remark" class="form-control"></textarea>
                                    </div>
                                </div>
                                
                                <table class="table table-bordered mt-4 table-detail">
                                    <thead>
                                        <tr>
                                            <th width="70%">รายการ</th>
                                            <th>
                                                จำนวนเงิน(บาท)
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                ค่าเช่าห้อง
                                            </td>
                                            <td>
                                                0
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>
                                                รวมทั้งหมด
                                            </th>
                                            <th>
                                                0
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                                <div class="mt-4 text-end col-12">
                                    <button
                                            id="add_discount"
                                            style="padding-right: 14px;padding-left: 14px;"
                                            class="btn btn-sm buttons-collection btn-info waves-effect waves-light me-2"
                                            tabindex="0" aria-controls="DataTables_Table_0"
                                            type="button" aria-haspopup="dialog"
                                            aria-expanded="false">
                                        <span>
                                        <i class="ti ti-plus"></i> ค่าน้ำ-ค่าไฟฟ้าสุดท้าย</span>
                                    </button>
                                    <button
                                            id="add_discount"
                                            style="padding-right: 14px;padding-left: 14px;"
                                            class="btn btn-sm buttons-collection btn-danger waves-effect waves-light me-2"
                                            tabindex="0" aria-controls="DataTables_Table_0"
                                            type="button" aria-haspopup="dialog"
                                            aria-expanded="false">
                                        <span>
                                        <i class="ti ti-plus"></i> เพิ่มส่วนลด</span>
                                    </button>
                                    <button
                                            id="add_expenses"
                                            style="padding-right: 14px;padding-left: 14px;"
                                            class="btn btn-sm buttons-collection btn-warning waves-effect waves-light me-2"
                                            tabindex="0" aria-controls="DataTables_Table_0"
                                            type="button" aria-haspopup="dialog"
                                            aria-expanded="false">
                                        <span>
                                        <i class="ti ti-plus"></i> เพิ่มรายการ</span>
                                    </button>
                                </div>

                                {{-- /////////////////////////////// --}}

                                <label class="mt-4 text-black" style="font-weight: 500;font-size: large;" for="">
                                    <span class="badge badge-center rounded-pill bg-primary me-1" style="background-color: #54BAB9 !important;">3</span>
                                    เงินประกัน
                                </label>
                                
                                <table class="table table-bordered mt-4 table-detail">
                                    <thead>
                                        <tr>
                                            <th width="70%">รายการ</th>
                                            <th>
                                                จำนวนเงิน(บาท)
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                เงินประกันห้อง
                                            </td>
                                            <td>
                                                <input class="form-control" type="text" disabled value="{{ number_format(1000) }}">
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>
                                                รวมรายการ
                                            </th>
                                            <th>
                                                0
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                                <div class="mt-4 text-end col-12">
                                    <button
                                            id="add_expenses"
                                            style="padding-right: 14px;padding-left: 14px;"
                                            class="btn btn-sm buttons-collection btn-warning waves-effect waves-light me-2"
                                            tabindex="0" aria-controls="DataTables_Table_0"
                                            type="button" aria-haspopup="dialog"
                                            aria-expanded="false">
                                        <span>
                                        <i class="ti ti-plus"></i> เพิ่มรายการเงินประกัน</span>
                                    </button>
                                </div>
                                {{-- /////////////////////////////// --}}
                                <div class="text-center">
                                    <span class="badge bg-label-success text-black mt-5" style="width: 100%;font-size: larger;">
                                        สรุปการย้ายออก
                                    </span>
                                    <h4 class="text-success mt-2">เงินจากการหักเงินประกัน 0 บาท</h4>
                                    
                                    <table class="table table-bordered mt-4 table-detail" style="width: 60%;margin: auto;">
                                        <thead>
                                        <tr class="text-start">
                                            <th>วันที่ย้ายออก</th>
                                            <th style="color: red !important;">
                                                25/06/2024
                                            </th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                                {{-- /////////////////////////////// --}}
                                <div class="modal-footer rounded-0 justify-content-start mb-0">
                                    <button type="button" class="btn btn-label-primary waves-effect text-black"><span
                                            class="ti-md ti ti-printer me-2"></span>พิมพ์ใบย้ายออก
                                    </button>
                                    <button type="submit" class="btn btn-main waves-effect ms-auto" onclick="paymentChannel(1)">
                                        บันทึกยอดเงินทั้งหมดแล้วย้ายออก
                                    </button>
                                </div>
                                {{-- /////////////////////////////// --}}

                            </div>
                        </div>
                    </div>
                </div>
                
        </div>
        
    </div>
<script>
    // var modalElement = document.getElementById('insurance');
    // var insuranceModal = new bootstrap.Modal(modalElement);

    // modalElement.addEventListener('hidden.bs.modal', function () {
    //     // console.log("Modal is hidden");
    //     document.querySelector('.select_off').style.display = 'block';
    // });
    // modalElement.addEventListener('shown.bs.modal', function () {
    //     // console.log("Modal is hidden");
    //     document.querySelector('.select_off').style.display = 'none';
    // });
        
    function get_bill(id){
        $.ajax({
            type: "GET",
            url: "{{ $page_url }}/get-bill/"+id,
            success: function(data) {
                $("#bill").html(data);
            }
        });
    }
    $('#edit_user').on('submit', function(event) {
            event.preventDefault(); // ป้องกันการส่งฟอร์มปกติ
            if(!this.checkValidity()) {
                // ถ้าฟอร์มไม่ถูกต้อง
                this.reportValidity();
                return console.log('ฟอร์มไม่ถูกต้อง');
            }
            // return alert(123);
            Swal.fire({
                title: 'ยืนยันการดำเนินการ?',
                text: 'คุณต้องการ แก้ไข พนักงาน หรือไม่?',
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
                        url: '{{$page_url}}/{{$room->id}}', // เปลี่ยน URL เป็นจุดหมายที่ต้องการ
                        type: 'POST',
                        data: $(this).serialize(),
                        success: function(response) {
                            if(response == true){
                                Swal.fire('แก้ไขพนักงานเรียบร้อยแล้ว', '', 'success');
                                loadData(page);
                                view('{{$room->id}}');
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
    $('#move_room').on('click', function(event) {
            event.preventDefault(); // ป้องกันการส่งฟอร์มปกติ
            if(!this.checkValidity()) {
                // ถ้าฟอร์มไม่ถูกต้อง
                this.reportValidity();
                return console.log('ฟอร์มไม่ถูกต้อง');
            }
            // return alert(123);
            Swal.fire({
                title: 'ยืนยันการดำเนินการ?',
                text: 'คุณต้องการ เปลี่ยนห้อง หรือไม่?',
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
                                Swal.fire('เปลี่ยนห้องเรียบร้อยแล้ว', '', 'success');
                                loadData(page);
            });
        });
        $('#period, #contract_date').on('input', function() {
            var contractDate = $('#contract_date').val();
            var period = $('#period').val();

            // ตรวจสอบว่า contractDate และ period มีค่าหรือไม่
            if (contractDate && period && !isNaN(period)) {
                var dateParts = contractDate.split('/');
                var day = dateParts[0];
                var month = dateParts[1];
                var year = dateParts[2];

                // สร้างวันในรูปแบบปี, เดือน, วัน
                var date = new Date(year, month - 1, day);

                // เพิ่มเดือนตาม period ที่ใส่
                date.setMonth(date.getMonth() + parseInt(period));

                // แปลงวันที่กลับเป็นรูปแบบ day/month/year
                var newDate = ('0' + date.getDate()).slice(-2) + '/' + ('0' + (date.getMonth() + 1)).slice(-2) + '/' + date.getFullYear();

                // ใส่วันที่ที่คำนวณในฟิลด์ contract_date_to
                $('#contract_date_to').val(newDate);
            }
        });
        $('#insert_one_contract').on('submit', function(event) {
            event.preventDefault(); // ป้องกันการส่งฟอร์มปกติ
            if(!this.checkValidity()) {
                // ถ้าฟอร์มไม่ถูกต้อง
                this.reportValidity();
                return console.log('ฟอร์มไม่ถูกต้อง');
            }
            // return alert(123);
            Swal.fire({
                title: 'ยืนยันการดำเนินการ?',
                text: 'คุณต้องการเพิ่ม สัญญา หรือไม่?',
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
                        url: '/room/insert_contract', // เปลี่ยน URL เป็นจุดหมายที่ต้องการ
                        type: 'POST',
                        data: $(this).serialize(),
                        success: function(response) {
                            if(response == true){
                                // $('#roomRentalContract').modal('hide');
                                // $('#insert_one_contract')[0].reset();
                                loadData(page);
                                summary();
                                Swal.fire('เพิ่มสัญญาเรียบร้อยแล้ว', '', 'success').then((result) => {
                                    location.reload();
                                });
                                // $('#room-rental-contract').html('');
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
            $('#select2Room').select2({
                placeholder: 'เลือกห้อง',
                allowClear: true
            });
        });
        $('#bs-datepicker-format2').datepicker({
            format: 'dd/mm/yyyy', // กำหนดรูปแบบวันที่
            autoclose: true,      // ปิด datepicker เมื่อเลือกวันที่
            todayHighlight: true  // ไฮไลต์วันที่ปัจจุบัน
        });
        // $(document).ready(function(){
            $('#bs-datepicker-basic').datepicker({
                format: 'mm/dd/yyyy', // Set the date format
                autoclose: true        // Close the datepicker when a date is selected
            });
        // });
        // $('#select2month').select2();
        // $('#select2RenterDetail').select2();
        $('#select2RenterContract').select2();

</script>