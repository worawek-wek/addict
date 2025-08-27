<div class="row">
  <div class="col-md-6">
    <p><b>สาขา:</b> {{ $user->branch->name ?? '-' }}</p>
    <p><b>ชื่อ:</b> {{ $user->name }} ({{ $user->nickname }})</p>
    <p><b>สถานะ:</b>
      {!! $user->work_status == 1
          ? '<span class="text-success">ออนไลน์</span>'
          : '<span class="text-danger">ออฟไลน์</span>' !!}
    </p>
    <p><b>ตำแหน่ง:</b> {{ $user->position->position_name ?? '-' }}</p>
    <p><b>หมายเหตุ:</b> {{ $user->remark ?? '-' }}</p>
  </div>
  <div class="col-md-6 text-center">
    <p><b>Username:</b> {{ $user->username }}</p>
    <img src="{{ $user->image_name
                  ? asset('upload/user/'.$user->image_name)
                  : asset('images/no-image.png') }}"
         alt="รูปภาพพนักงาน" style="max-width:200px">
  </div>
</div>
