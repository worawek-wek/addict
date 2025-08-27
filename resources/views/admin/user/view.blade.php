<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" rel="stylesheet" />

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

<div class="modal-content rounded-0">
  <div class="modal-header rounded-0">
    <span class="modal-title">
      <span class="h5" style="color: white;">&nbsp;รายละเอียด บุคลากร&nbsp;</span>
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
                        role="tab" data-bs-toggle="tab" data-bs-target="#tab-detail">
                  <i class="ti ti-user"></i> &nbsp;รายละเอียด
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="buttons-collection btn-label-warning waves-effect waves-light nav-link"
                        role="tab" data-bs-toggle="tab" data-bs-target="#tab-edit">
                  <i class="ti ti-pencil"></i> แก้ไข
                </button>
              </li>
            </ul>

            <div class="tab-content" style="box-shadow: unset;padding:0px">
              {{-- TAB รายละเอียด --}}
              <div class="tab-pane fade active show" id="tab-detail" role="tabpanel">
                <div class="col-sm-12 text-start">
                  <h5 class="border-bottom text-primary pb-2"><i class="ti ti-user"></i> รายละเอียด</h5>
                </div>
                <div class="d-flex">
                  <div class="col-sm-5">
                    <ul class="list-unstyled mb-4 mt-2" style="padding: 0 20px;">
                      <li class="d-flex mb-3"><b>สาขา:</b>&nbsp; {{ $user->branch->name }}</li>
                      <li class="d-flex mb-3"><b>ชื่อ:</b>&nbsp; {{ $user->name }} ({{ $user->nickname }})</li>
                      <li class="d-flex mb-3"><b>สถานะ:</b>&nbsp;
                        @if ($user->ref_status_id == 1)
                          <span class="text-success">ออนไลน์</span>
                        @else
                          <span class="text-muted">ออฟไลน์</span>
                        @endif
                      </li>
                      <li class="d-flex mb-3"><b>ตำแหน่ง:</b>&nbsp; {{ $user->position->position_name }}</li>
                      <li class="d-flex mb-3"><b>หมายเหตุ:</b>&nbsp; {{ $user->remark }}</li>
                      @if ($user->position->position_name == 'พนักงานนวด')
                        {{-- แก้ไขตรงนี้ --}}
                        <li class="d-flex mb-3"><b>ราคานวด:</b>&nbsp; {{ number_format($user->salary,0) }} บาท</li>
                      @endif
                    </ul>
                  </div>
                  <div class="col-sm-5">
                    <ul class="list-unstyled mb-4 mt-2" style="padding: 0 20px;">
                      <li class="d-flex mb-3"><b>อีเมล:</b>&nbsp; {{ $user->email }}</li>
                      <li class="d-flex mb-3">
                        <img src="{{ $user->image_name ? '/upload/user/'.$user->image_name : asset('images/no-image.png') }}"
                             alt="รูปภาพพนักงาน" width="100px">
                      </li>
                    </ul>
                  </div>
                </div>
              </div>

              {{-- TAB แก้ไข --}}
              <div class="tab-pane fade" id="tab-edit" role="tabpanel">
                <div class="col-sm-12 text-start">
                  <h5 class="border-bottom pb-3 text-warning"><i class="ti ti-pencil"></i> แก้ไข</h5>
                </div>

                <form id="edit_user" enctype="multipart/form-data">
                  @csrf
                  <div class="row g-3 p-4">
                    <div class="col-sm-6">
                      <label class="form-label">สาขา *</label><br>
                      @foreach ($branch as $bra)
                        <input type="radio" name="ref_branch_id" id="branch{{ $bra->id }}"
                               value="{{ $bra->id }}" {{ $user->ref_branch_id == $bra->id ? 'checked' : '' }}>
                        <label for="branch{{ $bra->id }}">{{ $bra->name }}</label>
                      @endforeach
                    </div>

                    <div class="col-sm-6">
                      <label class="form-label">บัตรพนักงาน *</label>
                      <input name="user_code" value="{{ $user->user_code }}" type="text"
                             class="form-control" required />
                    </div>

                    <div class="col-sm-6">
                      <label class="form-label">ชื่อพนักงาน *</label>
                      <input name="name" value="{{ $user->name }}" type="text" class="form-control" required />
                    </div>

                    <div class="col-sm-6">
                      <label class="form-label">ชื่อเล่น *</label>
                      <input name="nickname" value="{{ $user->nickname }}" type="text" class="form-control" required />
                    </div>

                    <div class="col-sm-6">
                      <label class="form-label">ตำแหน่ง</label>
                      <select name="ref_position_id" id="select2EditPosition" class="select2 form-select">
                        @foreach ($position as $pos)
                          <option value="{{ $pos->id }}" {{ $user->ref_position_id == $pos->id ? 'selected' : '' }}>
                            {{ $pos->position_name }}
                          </option>
                        @endforeach
                      </select>
                    </div>

                    {{-- ถ้าเป็นพนักงานนวด ให้แก้ไขราคานวดได้ --}}
                    @if ($user->position->position_name == 'พนักงานนวด')
                    <div class="col-sm-6">
                      <label class="form-label">ราคานวด (บาท)</label>
                      {{-- แก้ไขตรงนี้ --}}
                      <input name="salary" value="{{ $user->salary ?? 0 }}" type="number"
                             class="form-control" min="0" />
                    </div>
                    @endif

                    <div class="col-sm-10 mt-3">
                      <label>รูปภาพ</label>
                      <input type="file" name="image_name" class="form-control mb-2" id="image_name2">
                      <div class="preview-container mt-2">
                        <img id="preview2"
                             src="{{ $user->image_name ? '/upload/user/'.$user->image_name : '' }}"
                             style="{{ $user->image_name ? 'width:30%' : 'display:none;width:30%' }}">
                      </div>
                    </div>

                    <div class="col-sm-12">
                      <label class="form-label">หมายเหตุ</label>
                      <textarea name="remark" class="form-control">{{ $user->remark }}</textarea>
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
  // preview image
  function handleFileInput(fileInputId, previewId) {
    const fileInput = document.getElementById(fileInputId);
    const previewImage = document.getElementById(previewId);
    if (!fileInput) return;

    fileInput.addEventListener('change', function() {
      const file = fileInput.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = e => { previewImage.src = e.target.result; previewImage.style.display = 'block'; };
        reader.readAsDataURL(file);
      } else {
        previewImage.style.display = 'none';
      }
    });
  }
  handleFileInput('image_name2','preview2');

  // ajax submit form edit
  $('#edit_user').on('submit', function(event) {
    event.preventDefault();
    var formData = new FormData(this);
    Swal.fire({
      title: 'ยืนยันการดำเนินการ?',
      text: 'คุณต้องการแก้ไขพนักงานหรือไม่?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'ตกลง',
      cancelButtonText: 'ยกเลิก'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: '{{ $page_url }}/{{ $user->id }}',
          type: 'POST',
          data: formData,
          contentType: false,
          processData: false,
          success: function(response) {
            if (response == true) {
              Swal.fire('แก้ไขเรียบร้อยแล้ว','','success');
              loadData(page);
              view('{{ $user->id }}');
            }
          },
          error: function(error) {
            Swal.fire('เกิดข้อผิดพลาด','','error');
          }
        });
      }
    });
  });

  $(document).ready(function() {
    $('#select2EditPosition').select2({
      dropdownParent: $('#insurance')
    });
  });
</script>
