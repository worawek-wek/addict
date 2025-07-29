<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" rel="stylesheet" />

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<style>
    .select2-container {
        z-index: 9999; /* เพิ่ม z-index เพื่อให้ dropdown แสดงอยู่ข้างบน */
    }
    .swal2-container {
        z-index: 9999 !important; /* ปรับ z-index ให้สูงกว่า modal อื่น */
    }
</style>
<div class="modal-content rounded-0">
    <div class="modal-header rounded-0">
        <span class="modal-title">
            <span class="h5" style="color: white;">&nbsp;รายละเอียด บุคลากร&nbsp;</span>
        </span>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body pb-5" style="padding: 1em 3em;">
        
        <div class="col-md-12" style="padding-right: unset !important;">

        <div class="card shadow-none bg-transparent border mb-3">
            <div class="card-body">
                <div class="nav-align-top mb-4">
                    <ul class="nav nav-pills justify-content-end" role="tablist">
                      <li class="nav-item pe-2" role="presentation">
                        <button class="buttons-collection btn-label-primary waves-effect waves-light nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-top-profile" aria-controls="navs-pills-top-profile" aria-selected="true">
                            <i class="ti ti-user"></i> &nbsp;รายละเอียด
                        </button>
                      </li>
                      <li class="nav-item" role="presentation">
                        <button
                            class="buttons-collection btn-label-warning waves-effect waves-light nav-link" 
                            role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-top-edit" aria-controls="navs-pills-top-edit" aria-selected="false" tabindex="-1">
                            <span>
                                <i class="ti ti-pencil"></i> แก้ไข
                            </span>
                        </button>
                      </li>
                    </ul>
                    <div class="tab-content" style="box-shadow: unset;padding:0px">
                      <div class="tab-pane fade active show" id="navs-pills-top-profile" role="tabpanel">
                                <div class="col-sm-12 text-start">
                                    <h5 class="border-bottom text-primary pb-2">
                                        <i class="ti ti-user"></i> รายละเอียด
                                    </h5>
                                </div>
                                <div class="d-flex">
                                    <div class="col-sm-5">
                                        <ul class="list-unstyled mb-4 mt-2" style="padding: 0 20px;">
                                            <li class="d-flex align-items-center mb-3">
                                            <i class="ti ti-user text-heading"></i><span class="fw-medium mx-2 me-4 text-heading">สาขา:</span> <span>{{ $user->branch->name }}</span>
                                            </li>
                                            <li class="d-flex align-items-center mb-3">
                                            <i class="ti ti-user text-heading"></i><span class="fw-medium mx-2 me-4 text-heading">ชื่อ:</span> <span>{{ $user->name }}({{$user->nickname}})</span>
                                            </li>
                                            <li class="d-flex align-items-center mb-3">
                                            <i class="ti ti-check text-heading"></i><span class="fw-medium mx-2 me-4 text-heading">สถานะ:</span> 
                                                @if ($user->ref_status_id == 1)
                                                    <span class="text-success">ออนไลน์</span>
                                                @else
                                                    <span class="">ออฟไลน์</span>
                                                @endif
                                            </li>
                                            <li class="d-flex align-items-center mb-3">
                                            <i class="ti ti-crown text-heading"></i><span class="fw-medium mx-2 me-4 text-heading">ตำแหน่ง:</span> <span>{{ $user->position->position_name }}</span>
                                            </li>
                                            <li class="d-flex align-items-center mb-3">
                                            <i class="ti ti-file-description text-heading"></i><span class="fw-medium mx-2 me-4 text-heading">หมายเหตุ:</span> <span>{{ $user->remark }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-sm-5">
                                        <ul class="list-unstyled mb-4 mt-2" style="padding: 0 20px;">
                                            <li class="d-flex align-items-center mb-3">
                                                <i class="ti ti-user text-heading"></i><span class="fw-medium mx-2 me-4 text-heading">Username:</span> <span>{{ $user->email }}</span>
                                            </li>
                                            <li class="d-flex align-items-center mb-3">
                                                <img src="/upload/user/{{ $user->image_name }}" alt="" width="100px">
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                        </div>
                      <div class="tab-pane fade" id="navs-pills-top-edit" role="tabpanel">
                        <div class="col-sm-12 text-start">
                            <h5 class="border-bottom pb-3 text-warning">
                                <i class="ti ti-pencil"></i> แก้ไข
                            </h5>
                        </div>
                        <form id="edit_user" enctype="multipart/form-data">		
                            @csrf
                            
                            <div class="row g-3 p-4">
                                <div class="col-sm-6">
                                    <label for="" class="form-label">สาขา</label><span class="text-danger"> *</span><br>
                                      <input class="form-check-input" type="radio" name="ref_branch_id" id="inlineRadio3" value="1" @if ($user->ref_branch_id == 1) checked @endif>
                                      <label class="form-check-label me-4" for="inlineRadio3">อ่อนนุช</label>
                                      <input class="form-check-input" type="radio" name="ref_branch_id" id="inlineRadio4" value="2" @if ($user->ref_branch_id == 2) checked @endif>
                                      <label class="form-check-label" for="inlineRadio4">ทองหล่อ</label>
                                </div>
                                <div class="col-sm-12"></div>
                                <div class="col-sm-6">
                                    <label for="" class="form-label">บัตรพนักงาน</label><span class="text-danger"> *</span>
                                    <input name="user_code" type="password" class="form-control" placeholder="บัตรพนักงาน" value="{{ $user->user_code }}" id="user_code_up" required />
                                </div>
                                <div class="col-sm-6">
                                    <label for="" class="form-label">ชื่อพนักงาน</label><span class="text-danger"> *</span>
                                    <input name="name" value="{{ $user->name }}" type="text" class="form-control" placeholder="ชื่อพนักงาน" required />
                                </div>
                                <div class="col-sm-6">
                                    <label for="" class="form-label">ชื่อเล่น</label><span class="text-danger"> *</span>
                                    <input name="nickname" value="{{ $user->nickname }}" type="text" class="form-control" placeholder="ชื่อเล่น" required />
                                </div>
                                <div class="col-sm-6">
                                    <label for="" class="form-label">ตำแหน่ง</label>
                                    <select name="ref_position_id" id="select2Position2" class="select2 form-select form-select-lg" data-allow-clear="true">
                                        @foreach ($position as $pos)
                                            <option @if ($user->ref_position_id == $pos->id)
                                                selected
                                            @endif value="{{$pos->id}}">{{$pos->position_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-sm-10 mt-3">
                                    <label for="image_name2">รูปภาพ</label>
                                    <input type="file" name="image_name" class="form-control mb-2" id="image_name2">
                                    @if ($user->image_name == '')
                                        <div class="preview-container">
                                            <img id="preview2" src="" alt="Preview 1" style="display: none; width:30%">
                                        </div>
                                    @else
                                        <div class="preview-container">
                                            <img id="preview2" src="/upload/user/{{ $user->image_name }}" alt="Preview 1" style="width:30%">
                                        </div>
                                    @endif
                                </div>

                                <div class="col-span-12">
                                    <div class="col-sm-6 mt-3">
                                        <label for="" class="form-label">ชื่อผู้ใช้</label><span class="text-danger"> *</span>
                                        <input name="email" value="{{ $user->email }}" type="text" class="form-control" placeholder="ชื่อผู้ใช้" required />
                                    </div>
                                    <div class="col-sm-6 mt-3">
                                        <label for="update-profile-form-2" class="form-label">รหัสผ่าน <span class="text-warning">(กรณีเปลี่ยนรหัสผ่าน)</span></label>
                                        <input name="password" id="password2" type="password" class="form-control" placeholder="รหัสผ่าน">
                                    </div>
                                    <div class="col-sm-6 mt-3">
                                        <label for="update-profile-form-3" class="form-label">ยืนยัน รหัสผ่าน</label>
                                        <input id="confirm_password2" type="password" class="form-control" placeholder="ยืนยัน รหัสผ่าน">
                                    </div>
                                </div>
                                <script>
                                    //// ทำ เช็ค Password เริ่ม
                                    
                                    $('#user_code_up').on('keydown', function(e) {
                                        if (e.key === 'Enter') {
                                            e.preventDefault();
                                        }
                                    });
                                    var password2 = document.getElementById("password2"), confirm_password2 = document.getElementById("confirm_password2");

                                    function validatePassword2(){
                                        if(password2.value != confirm_password2.value) {
                                            confirm_password2.setCustomValidity("Passwords Don't Match");
                                        } else {
                                            confirm_password2.setCustomValidity('');
                                        }
                                    }

                                    password2.onchange = validatePassword2;
                                    confirm_password2.onkeyup = validatePassword2;
                                    //// ทำ เช็ค Password จบ
                                    function handleFileInput(fileInputId, previewId) {
                                    const fileInput = document.getElementById(fileInputId);
                                    const previewImage = document.getElementById(previewId);

                                    fileInput.addEventListener('change', function () {
                                        const file = fileInput.files[0];

                                        if (file) {
                                            const reader = new FileReader();

                                            reader.onload = function (e) {
                                                previewImage.src = e.target.result;
                                                previewImage.style.display = 'block';  // แสดงภาพพรีวิว
                                            };

                                            reader.readAsDataURL(file);
                                        } else {
                                            previewImage.style.display = 'none'; // ซ่อนพรีวิวถ้าไม่ได้เลือกไฟล์
                                        }
                                    });
                                }
                            
                                handleFileInput('image_name2', 'preview2');
                                </script> 
                                <div class="col-sm-12">
                                    <label for="" class="form-label">หมายเหตุ</label>
                                    <textarea name="remark" class="form-control">{{ $user->stock }}</textarea>
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
                </div>
                {{-- <button
                        style="padding-right: 14px;padding-left: 14px;"
                        class="btn btn-success buttons-collection btn-label-warning waves-effect waves-light me-2"
                        tabindex="0" aria-controls="DataTables_Table_0"
                        type="button" aria-haspopup="dialog"
                        aria-expanded="false">
                        <span>
                            <i class="ti ti-upload"></i> 
                            ดาวน์โหลด Excel
                        </span>
                </button> --}}
                
        </div>
        
        {{-- <div class="col-sm-12">
            <div class="card shadow-none bg-transparent border mb-3">
                <div class="card-body">
                  <h5 class="card-title">Secondary card title</h5>
                  <p class="card-text">Some quick example text to build on the card title and make up.</p>
                </div>
              </div>
        </div> --}}
    </div>
</div>
</div>
<script>
    
    $('#edit_user').on('submit', function(event) {
            event.preventDefault(); // ป้องกันการส่งฟอร์มปกติ
            if(!this.checkValidity()) {
                // ถ้าฟอร์มไม่ถูกต้อง
                this.reportValidity();
                return console.log('ฟอร์มไม่ถูกต้อง');
            }
            // return alert(123);
            
            var formData = new FormData(this);
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
                        url: '{{$page_url}}/{{$user->id}}', // เปลี่ยน URL เป็นจุดหมายที่ต้องการ
                        type: 'POST',
                        data: formData,
                        contentType: false, // ✅ ต้องมี
                        processData: false, // ✅ ต้องมี
                        success: function(response) {
                            if(response == true){
                                Swal.fire('แก้ไขพนักงานเรียบร้อยแล้ว', '', 'success');
                                loadData(page);
                                view('{{$user->id}}');
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
            $('#select2Position2').select2({
                placeholder: 'เลือกตำแหน่ง',
                allowClear: true
            });
        });
        $('#bs-datepicker-format2').datepicker({
            format: 'dd/mm/yyyy', // กำหนดรูปแบบวันที่
            autoclose: true,      // ปิด datepicker เมื่อเลือกวันที่
            todayHighlight: true  // ไฮไลต์วันที่ปัจจุบัน
        });
</script>