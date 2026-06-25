<div class="modal-content rounded-0">
  <div class="modal-header rounded-0">
    <span class="modal-title">
      <span class="h5" style="color: white;">&nbsp;ค่ามือ(Option)&nbsp;</span>
    </span>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
  </div>

  <div class="modal-body pb-5" style="padding: 1em 3em;">
    <div class="col-md-12">
      <div class="card shadow-none bg-transparent">
        <div class="card-body">
          <form id="edit_commission_option" enctype="multipart/form-data">
            @csrf
                      {{-- ///////////////////////////////////////
                      ค่ามือ
                      /////////////////////////////////////// --}}
                        <style>
                          .custom-table {
                              width: 100%;
                              border-collapse: collapse;
                              background-color: #ffffff;
                              font-size: 14px;    
                              border-radius: 5px;
                              overflow: hidden;
                          }

                          .custom-table thead th {
                              background-color: #54bab952;
                              color: #111827;
                              font-weight: 600;
                              text-align: center;
                              border: 1px solid #cdced1;
                              padding: 10px;
                              position: sticky;
                              top: 0;
                              z-index: 2;
                          }

                          .custom-table tbody td {
                              border: 1px solid #dee0e3;
                              padding: 8px;
                              vertical-align: middle;
                          }

                          .custom-table tbody tr:nth-child(even) {
                              background-color: #f9fafb; /* สลับสี */
                          }

                          .custom-table tbody tr:hover {
                              background-color: #eef2ff; /* hover */
                          }

                          .text-center {
                              text-align: center;
                          }

                          .custom-table input.form-control {
                              height: 32px;
                              font-size: 14px;
                              text-align: center;
                          }
                          .table-scroll-wrapper {
                              max-height: 470px;          /* ปรับตามที่ต้องการ */
                              overflow-y: auto;
                              border-radius: 8px;
                          }
                      </style>
                      
                    <div class="col-sm-12 text-start">
                        <h5 class="border-bottom pb-3">
                            <i class="ti ti-pencil text-warning"></i> จัดการค่ามือ(Option) ของ <span class="text-primary">{{ "$user->name ($user->nickname)" }}</span>
                        </h5>
                    </div>
                    <div class="table-scroll-wrapper">
                      <table class="custom-table text-black">
                        <thead>
                            <tr>
                                <th>Option</th>
                                <th class="text-end" width="8%">ราคา</th>
                                <th class="text-end" width="10%">ค่ามือ</th>
                                <th class="text-end" width="10%">คูปอง</th>
                                <th class="text-end" width="10%">ร้านรับจริง</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($all_option as $option)
                                    <tr>
                                        <td class="text-center px-3 fw-medium">
                                            {{ $option->name }}
                                        </td>
                                        <td class="text-end course-price">
                                            {{ number_format($option->price) }}
                                        </td>
                                        @if (@$option->user_has_option_commission->id)
                                            <td class="text-end">
                                                <input type="number" inputmode="numeric" pattern="[0-9]*" class="form-control commission-option-input commission-price text-end" name="update[{{$option->id}}][{{ @$option->user_has_option_commission->id }}][price]" value="{{ (int) (@$option->user_has_option_commission->price ?? 0) }}">
                                            </td>
                                            <td class="text-end">
                                                <input type="number" inputmode="numeric" pattern="[0-9]*" class="form-control commission-option-input commission-coupon text-end" name="update[{{$option->id}}][{{ @$option->user_has_option_commission->id }}][coupon]" value="{{ (int) (@$option->user_has_option_commission->coupon ?? 0) }}">
                                            </td>
                                        @else
                                            <td class="text-end">
                                              <input name="insert[{{$option->id}}][price]" type="number" class="form-control commission-option-input commission-price text-end" value="{{ @$option->user_has_option_commission->price ?? 0 }}">
                                            </td>
                                            <td class="text-end">
                                              <input name="insert[{{$option->id}}][coupon]" type="number" class="form-control commission-option-input commission-coupon text-end" value="{{ @$option->user_has_option_commission->coupon ?? 0 }}">
                                            </td>
                                        @endif
                                        <td class="text-end net-price"></td>
                                    </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">ไม่พบข้อมูล</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    </div>
                      {{-- ///////////////////////////////////////
                      ค่ามือ
                      /////////////////////////////////////// --}}
            <div class="modal-footer rounded-0 justify-content-center pb-0 pt-4">
              <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">ปิด</button>
              <button type="submit" class="btn btn-main">บันทึก</button>
            </div>
          </form>
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
  $('#edit_commission_option').on('submit', function(event) {
    event.preventDefault();
    var formData = new FormData(this);
    Swal.fire({
      title: 'ยืนยันการดำเนินการ?',
      text: 'คุณต้องการแก้ไข ค่ามือ หรือไม่?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'ตกลง',
      cancelButtonText: 'ยกเลิก'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: '{{ $page_url }}/commission-option/{{ $user->id }}',
          type: 'POST',
          data: formData,
          contentType: false,
          processData: false,
          success: function(response) {
            if (response == true) {
              
              var modalEl = document.getElementById('modal-commission-option');
              var modalInstance = bootstrap.Modal.getInstance(modalEl); // <-- ดึง instance ที่เปิดอยู่
              if (modalInstance) {
                  modalInstance.hide(); // <-- ซ่อน modal ที่เปิดอยู่จริง
              }

              Swal.fire('แก้ไขเรียบร้อยแล้ว','','success');
              loadData(page);
              // view('{{ $user->id }}');
            }
          },
          error: function(error) {
            Swal.fire('เกิดข้อผิดพลาด','','error');
          }
        });
      }
    });
  });
</script>
<script>
  $(document).on('input', '.commission-option-input', function () {

      const $row = $(this).closest('tr');

      const coursePrice = parseFloat(
          $row.find('.course-price').text().replace(/,/g, '')
      ) || 0;

      const commission = parseFloat(
          $row.find('.commission-price').val()
      ) || 0;

      const coupon = parseFloat(
          $row.find('.commission-coupon').val()
      ) || 0;

      let net = coursePrice - commission - coupon;
      if (net < 0) net = 0;

      $row.find('.net-price').text(net.toLocaleString());
  });

  // คำนวณทันทีตอนโหลดหน้า
  $('.commission-option-input').trigger('input');
</script>
