<div class="modal fade modalHeadDecor" id="addRenter" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content rounded-0">
            <div class="modal-header rounded-0">
                <h5 class="modal-title" id="exampleModalLabel1">เพิ่มผู้เช่าห้อง</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
                <form id="insert_renter">
                    @csrf
            <div class="modal-body">
                <div class="m-2" style="border: 1px solid #dbdbdb;border-radius: 5px;">
                    <h5 class="border-bottom p-2" style="background-color: rgb(255, 248, 237);;">
                        <i class="tf-icons ti ti-user text-main" style="font-size: 25px;vertical-align: baseline;"></i>
                        ข้อมูลส่วนตัว
                    </h5>
                    <div class="row g-3 p-4 pt-1">
                        <div class="col-sm-2">
                            <label for="exampleFormControlSelect1" class="form-label">คำนำหน้า</label>
                            <select name="prefix" class="form-select" id="exampleFormControlSelect1"
                                aria-label="Default select example">
                                <option value="บริษัท" selected>บริษัท</option>
                                <option value="นาย">นาย</option>
                                <option value="นางสาว">นางสาว</option>
                                <option value="นาง">นาง</option>
                            </select>
                        </div>
                        <div class="col-sm-5">
                            <label for="exampleFormControlInput1" class="form-label">ชื่อจริง</label>
                            <input type="text" name="name" class="form-control" id="exampleFormControlInput1" placeholder="" />
                        </div>
                        <div class="col-sm-5">
                            <label for="exampleFormControlInput2" class="form-label">นามสกุล</label>
                            <input type="text" name="surname" class="form-control" id="exampleFormControlInput2" placeholder="" />
                        </div>
                        <div class="col-sm-6">
                            <label for="exampleFormControlInput3" class="form-label">เบอร์โทรศัพท์ (ตัวอย่าง. 0815578945)</label>
                            <input type="text" name="phone" class="form-control" id="exampleFormControlInput3" placeholder="" />
                        </div>
                        <div class="col-sm-6">
                            <label for="exampleFormControlInput4" class="form-label">หมายเลขบัตรประชาชน</label>
                            <input type="text" name="id_card_number" class="form-control" id="exampleFormControlInput4" placeholder="" />
                        </div>
                        <div class="col-sm-12">
                            <label for="exampleFormControlInput5" class="form-label">ที่อยู่ตามสำเนาทะเบียนบ้าน</label>
                            <input type="text" name="address" class="form-control" id="exampleFormControlInput5" placeholder="เลขที่ ซอย ถนน อาคาร ห้องเลขที่ หรือหมู่บ้าน" />
                        </div>
                        {{-- <div class="col-sm-4">
                            <label>เลือกจังหวัด</label>
                            <select name="ref_province_id" id="select2Basic" class="select2 form-select form-select-lg" required>
                                <option selected disabled hidden value="">เลือกจังหวัด</option>
                                @foreach ($province as $pro)
                                    <option value="{{ $pro->id }}">{{ $pro->name_in_thai }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label>เลือกอำเภอ</label>
                            <select name="ref_district_id" id="select2District" class="select2 form-select form-select-lg" required>
                                <option selected disabled hidden value="">เลือกอำเภอ</option>
                                @foreach ($district as $dis)
                                    <option value="{{ $dis->id }}">{{ $dis->name_in_thai }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label>เลือกตำบล</label>
                            <select name="ref_subdistrict_id" id="select2Subdistrict" class="select2 form-select form-select-lg" required>
                                <option selected disabled hidden value="">เลือกตำบล</option>
                                @foreach ($subdistrict as $sub_dis)
                                    <option value="{{ $sub_dis->id }}">{{ $sub_dis->name_in_thai }}</option>
                                @endforeach
                            </select>
                        </div> --}}
                        <div class="col-sm-6">
                            <label for="exampleFormControlInput9" class="form-label">รหัสไปรษณีย์</label>
                            <input type="text" name="zipcode" class="form-control" id="exampleFormControlInput9" placeholder="รหัสไปรษณีย์" />
                        </div>
                        <div class="col-sm-12">
                            <label for="bs-datepicker-format" class="form-label">วันเดือนปีเกิดผู้จอง</label>
                            <input type="text" name="birthdate" value="" class="form-control" id="bs-datepicker-format" placeholder="วัน/เดือน/ปี" required/>
                        </div>
                        <div class="col-sm-6">
                            <label for="bs-datepicker-format2" class="form-label">วันที่จอง</label>
                            <input type="text" name="booking_date" value="" class="form-control" id="bs-datepicker-format2" placeholder="วัน/เดือน/ปี" required/>
                        </div>
                        <div class="col-sm-6">
                            <label for="exampleFormControlInput12" class="form-label">ช่องทางการจอง</label>
                            <input type="text" name="booking_channel" class="form-control" id="exampleFormControlInput12" placeholder="ช่องทางการจอง" value="จองโดยตรงกับที่พัก" />
                        </div>
                    </div>
                </div>

                <div class="m-2 mt-4" style="border: 1px solid #dbdbdb;border-radius: 5px;">
                    <h5 class="border-bottom p-2" style="background-color: rgb(255, 248, 237);">
                        <i class="tf-icons ti ti-browser-plus text-main" style="font-size: 25px;vertical-align: baseline;"></i>
                        รายการจองห้อง
                    </h5>
                    <div class="row g-3 p-4 pt-1">
                        <div class="col-sm-6">
                            <label for="exampleFormControlInput13" class="form-label">วันที่เข้าพัก</label>
                            <input type="text" name="date_stay" class="form-control" id="exampleFormControlInput13" placeholder="วัน/เดือน/ปี" value="" required/>
                        </div>
                        
                        <div class="col-sm-5">
                            <div class="accordion-body py-3 mt-3">
                                <div class="ms-4" id="room-selected">
                                    @include('room/selected')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="m-2 mt-4" style="border: 1px solid #dbdbdb;border-radius: 5px;">
                    <h5 class="border-bottom p-2" style="background-color: rgb(255, 248, 237);">
                        <i class="tf-icons ti ti-device-ipad-dollar text-main" style="font-size: 25px;vertical-align: baseline;"></i>
                        รายละเอียดการชำระเงิน
                    </h5>
                    <div class="row g-3 p-4 pt-1">
                        <div class="col-sm-12">
                            <label for="exampleFormControlInput30" class="form-label">ค่ามัดจำ (บาท)</label>
                            <input type="text" name="deposit" class="form-control" id="exampleFormControlInput30" placeholder="" />
                        </div>
                        <div class="col-sm-12">
                            <div>
                                <label for="exampleFormControlInput31" class="form-label">วิธีการชำระเงิน</label>
                            </div>
                            <div class="ms-3">
                            <input
                                name="payment_method"
                                class="form-check-input"
                                type="radio"
                                value="1"
                                id="defaultRadio1"
                                checked />
                            <label class="form-check-label" for="defaultRadio1">&nbsp; เงินสด </label>
                            <input
                                name="payment_method"
                                class="form-check-input ms-2"
                                type="radio"
                                value="2"
                                id="defaultRadio2" />
                            <label class="form-check-label" for="defaultRadio2">&nbsp; โอนเงิน </label>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <label for="exampleFormControlInput33" class="form-label">วันที่รับชำระเงิน</label>
                            <input type="text" name="payment_received_date" class="form-control" id="exampleFormControlInput33" placeholder="วัน/เดือน/ปี" value="" required/>
                        </div>
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