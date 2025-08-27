<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" rel="stylesheet" />

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

<style>
    .select2-container {
        z-index: 9999; /* dropdown อยู่บนสุด */
    }
    .swal2-container {
        z-index: 9999 !important; /* SweetAlert อยู่บนสุด */
    }
</style>

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
                        <ul class="nav nav-pills justify-content-end" role="tablist">
                            <li class="nav-item pe-2" role="presentation">
                                <button class="buttons-collection btn-label-primary waves-effect waves-light nav-link active"
                                        role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-top-profile"
                                        aria-selected="true">
                                    <i class="ti ti-user"></i> &nbsp;รายละเอียด
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="buttons-collection btn-label-warning waves-effect waves-light nav-link"
                                        role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-top-edit"
                                        aria-selected="false">
                                    <i class="ti ti-pencil"></i> &nbsp;แก้ไข
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" style="box-shadow: unset;padding:0px">

                            {{-- TAB รายละเอียด --}}
                            <div class="tab-pane fade active show" id="navs-pills-top-profile" role="tabpanel">
                                <div class="col-sm-12 text-start">
                                    <h5 class="border-bottom text-primary pb-2">
                                        <i class="ti ti-user"></i> รายละเอียด
                                    </h5>
                                </div>
                                <div class="d-flex">
                                    <div class="col-sm-12">
                                        <ul class="list-unstyled mb-4 mt-2" style="padding: 0 20px;">
                                            <li class="d-flex align-items-center mb-3">
                                                <i class="ti ti-building text-heading"></i>
                                                <span class="fw-medium mx-2 me-4 text-heading">สาขา:</span>
                                                <span>{{ $room->branch->name }}</span>
                                            </li>
                                            <li class="d-flex align-items-center mb-3">
                                                <i class="ti ti-home text-heading"></i>
                                                <span class="fw-medium mx-2 me-4 text-heading">ชื่อห้อง:</span>
                                                <span>{{ $room->name }}</span>
                                            </li>
                                            <li class="d-flex align-items-center mb-3">
                                                <i class="ti ti-clock text-heading"></i>
                                                <span class="fw-medium mx-2 me-4 text-heading">ราคา 40 นาที:</span>
                                                <span>{{ number_format($room->forty_minutes, 2) }}</span>
                                            </li>
                                            <li class="d-flex align-items-center mb-3">
                                                <i class="ti ti-clock-hour-1 text-heading"></i>
                                                <span class="fw-medium mx-2 me-4 text-heading">ราคา 60 นาที:</span>
                                                <span>{{ number_format($room->sixty_minutes, 2) }}</span>
                                            </li>
                                            <li class="d-flex align-items-center mb-3">
                                                <i class="ti ti-clock-hour-2 text-heading"></i>
                                                <span class="fw-medium mx-2 me-4 text-heading">ราคา 90 นาที:</span>
                                                <span>{{ number_format($room->ninety_minutes, 2) }}</span>
                                            </li>
                                            <li class="d-flex align-items-center mb-3">
                                                <i class="ti ti-file-description text-heading"></i>
                                                <span class="fw-medium mx-2 me-4 text-heading">หมายเหตุ:</span>
                                                <span>{{ $room->remark }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            {{-- TAB แก้ไข --}}
                            <div class="tab-pane fade" id="navs-pills-top-edit" role="tabpanel">
                                <div class="col-sm-12 text-start">
                                    <h5 class="border-bottom pb-3 text-warning">
                                        <i class="ti ti-pencil"></i> แก้ไข
                                    </h5>
                                </div>
                                <form id="edit_room" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row g-3 p-4">
                                        <div class="col-sm-12">
                                            <label class="form-label">สาขา *</label><br>
                                            @foreach ($branch as $bra)
                                                <input class="form-check-input" type="radio" name="ref_branch_id"
                                                       value="{{ $bra->id }}"
                                                       {{ $room->ref_branch_id == $bra->id ? 'checked' : '' }}>
                                                <label class="form-check-label me-4">{{ $bra->name }}</label>
                                            @endforeach
                                        </div>

                                        <div class="col-sm-6">
                                            <label class="form-label">ชื่อห้อง *</label>
                                            <input name="name" type="text" class="form-control"
                                                   value="{{ $room->name }}" required />
                                        </div>

                                        <div class="col-sm-6">
                                            <label class="form-label">ราคา 40 นาที *</label>
                                            <input name="forty_minutes" type="number" step="0.01" class="form-control"
                                                   value="{{ $room->forty_minutes }}" required />
                                        </div>

                                        <div class="col-sm-6">
                                            <label class="form-label">ราคา 60 นาที *</label>
                                            <input name="sixty_minutes" type="number" step="0.01" class="form-control"
                                                   value="{{ $room->sixty_minutes }}" required />
                                        </div>

                                        <div class="col-sm-6">
                                            <label class="form-label">ราคา 90 นาที *</label>
                                            <input name="ninety_minutes" type="number" step="0.01" class="form-control"
                                                   value="{{ $room->ninety_minutes }}" required />
                                        </div>

                                        <div class="col-sm-12">
                                            <label class="form-label">หมายเหตุ</label>
                                            <textarea name="remark" class="form-control">{{ $room->remark }}</textarea>
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

<script>
$('#edit_room').on('submit', function(event) {
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
                url: '{{$page_url}}/{{$room->id}}',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response == true) {
                        Swal.fire('แก้ไขห้องเรียบร้อยแล้ว', '', 'success');
                        loadData(page);
                        view('{{$room->id}}');
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
