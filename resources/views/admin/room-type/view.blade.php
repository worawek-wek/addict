    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

<div class="modal-content rounded-0">
    <div class="modal-header rounded-0">
        <span class="modal-title">
            <span class="h5" style="color: white;">&nbsp;รายละเอียด ห้อง&nbsp;</span>
        </span>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body pb-5" style="padding: 1em 3em;">

        <div class="col-md-12">

            <div class="card shadow-none bg-transparent border mb-3">
                <div class="card-body">
                    <div class="nav-align-top mb-4">
                        <div class="tab-content" style="box-shadow: unset;padding:0px">
                                <div class="col-sm-12 text-start">
                                    <h5 class="border-bottom pb-3 text-warning">
                                        <i class="ti ti-pencil"></i> แก้ไข
                                    </h5>
                                </div>
                                <form id="edit_room_type" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row g-3 p-4">
                                        {{-- <div class="col-sm-12">
                                            <label class="form-label">ห้อง <span class="text-danger">*</span></label>
                                            <select name="ref_room_id" id="select-edit-room" class="">
                                                <option selected disabled hidden value="">เลือกห้อง</option>
                                                @foreach ($room as $ro)
                                                    <option @if ($ro->id == $room_type->ref_room_id) selected @endif value="{{ $ro->id }}">{{ $ro->name }}</option>
                                                @endforeach
                                            </select>
                                        </div> --}}

                                        <div class="col-sm-6">
                                            <label class="form-label">ชื่อห้อง *</label>
                                            <input name="name" type="text" class="form-control"
                                                   value="{{ $room_type->name }}" required />
                                        </div>
                                        
                                        <div class="w-100"></div>
                                        @foreach ($room_type['room_type_has_course'] as $room_type_has_course)
                                            <div class="col-sm-4">
                                                <label class="form-label">{{ $room_type_has_course->course->name }} *</label>
                                                <input name="edit[{{$room_type_has_course->id}}][price]" type="number" step="any" class="form-control" value="{{ $room_type_has_course->price }}"
                                                    placeholder="ราคา 40 นาที/บริการ" required />
                                            </div>

                                            <div class="col-sm-4">
                                                <label class="form-label">ค่ามือ</label>
                                                <input name="edit[{{$room_type_has_course->id}}][commission]" type="number" step="any" class="form-control" value="{{ $room_type_has_course->commission }}"
                                                    placeholder="ค่ามือ" />
                                            </div>

                                            <div class="col-sm-4">
                                                <label class="form-label">คูปอง</label>
                                                <input name="edit[{{$room_type_has_course->id}}][coupon]" type="number" step="any" class="form-control" value="{{ $room_type_has_course->coupon }}"
                                                    placeholder="คูปอง" />
                                            </div>
                                        @endforeach
                                        <div class="col-sm-12">
                                            <label class="form-label">หมายเหตุ</label>
                                            <textarea name="remark" class="form-control">{{ $room_type->remark }}</textarea>
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
            </div>

        </div>
    </div>
</div>
<script src="assets/vendor/libs/jquery/jquery.js"></script>
<script>
    // new TomSelect("#select-edit-room", {
    //                     create: false,
    //                     maxItems: 1,
    //                     allowEmptyOption: true,

    //                     openOnFocus: true,   // โฟกัสแล้วโชว์ทั้งหมด
    //                     preload: true,       // โหลด option ทั้งหมดตั้งแต่แรก

    //                     // ให้ทุก option แสดงเสมอ (ไม่โดนซ่อนโดย score)
    //                     score: function(search) {
    //                         if (!search) {
    //                             return function() {
    //                                 return 1;
    //                             };
    //                         }

    //                         // ถ้ามีการค้นหา ให้ใช้ scoring ปกติ
    //                         return this.getScoreFunction(search);
    //                     },

    //                     sortField: [] // ไม่เรียง
    //                 });
    $('#edit_room_type').on('submit', function(event) {
        event.preventDefault();

        if (!this.checkValidity()) {
            this.reportValidity();
            return;
        }

        var formData = new FormData(this);
        Swal.fire({
            title: 'ยืนยันการดำเนินการ?',
            text: 'คุณต้องการแก้ไขห้องนี้หรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'ตกลง',
            cancelButtonText: 'ยกเลิก',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{$page_url}}/{{$room_type->id}}',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response == true) {
                            Swal.fire('แก้ไขห้องเรียบร้อยแล้ว', '', 'success');
                            var modalEl = document.getElementById('insurance');
                            var modalInstance = bootstrap.Modal.getInstance(modalEl); // <-- ดึง instance ที่เปิดอยู่
                            if (modalInstance) {
                                modalInstance.hide(); // <-- ซ่อน modal ที่เปิดอยู่จริง
                            }
                            loadData(page);
                        }
                    },
                    error: function(error) {
                        Swal.fire('เกิดข้อผิดพลาด', '', 'error');
                        console.error('เกิดข้อผิดพลาด:', error);
                    }
                });
            }
        });
    });

</script>
