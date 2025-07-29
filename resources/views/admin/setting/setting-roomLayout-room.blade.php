
                <table class="table table-bordered">
                    <thead>
                        <tr class="table-warning text-center">
                            <th>ชื่อห้อง</th>
                            <th>ชื่อห้องที่ต้องการแก้ไข</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($floor['room'] as $room)
                            <tr class="text-center">
                                <td id="roomName{{ $room->id }}">{{ $room->name }}</td>
                                <td>
                                    <input type="text" id="room{{ $room->id }}" class="form-control" value="{{ $room->name }}" onkeydown="handleInput(event,{{ $room->id }}, this.value)" oninput="editRoom({{ $room->id }}, this.value)">
                                </td>
                                <td>
                                    <button type="button" id="updateRoom{{ $room->id }}" class="btn btn-secondary" disabled onclick="updateRoom('{{ $room->id }}')">
                                        <i class="ti-xs ti ti-pencil me-2"></i>บันทึก
                                    </button>
                                    <button type="button" class="btn btn-danger" onclick="deleteRoom('{{ $room->id }}','{{ $room->ref_floor_id }}')">
                                        <i class="ti-xs ti ti-minus me-2"></i>ลบ
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
<script>
    function handleInput(event, id, v) {
        if (event.key === 'Enter') {
            if($('#roomName' + id).html() != v){
                updateRoom(id)
                // const userInput = event.target.value;
            }
        }
    }
    function editRoom(id, v){
        if($('#roomName' + id).html() != v){
            $('#updateRoom' + id).prop('disabled', false);
            $('#updateRoom' + id).removeClass('btn-secondary').addClass('btn-success');
        }else{
            $('#updateRoom' + id).prop('disabled', true);
            $('#updateRoom' + id).removeClass('btn-success').addClass('btn-secondary');
        }
    }
    function updateRoom(id){
        $.ajax({
            url: '/setting/room-layout/room/'+id, // เปลี่ยน URL เป็นจุดหมายที่ต้องการ
            type: 'POST',
            data: {
                _token : "{{ csrf_token() }}",
                name : $('#room' + id).val()
            },
            success: function(response) {
                // if(response == true){
                    $('#roomName' + id).html(response);
                    $('#updateRoom' + id).prop('disabled', true);
                    $('#updateRoom' + id).removeClass('btn-success').addClass('btn-secondary');
                    Swal.fire({
                        title: 'แก้ไข ชื่อห้อง เรียบร้อยแล้ว',
                        icon: 'success',
                        timer: 1500, // ตั้งเวลาเป็น 1500 มิลลิวินาที (1.5 วินาที)
                        timerProgressBar: true, 
                        showConfirmButton: false,
                        customClass: {
                            title: 'custom-title', // กำหนดคลาสให้กับ title
                        },
                    });
                // }
            },
            error: function(error) {
                Swal.fire('เกิดข้อผิดพลาด', '', 'error');
                console.error('เกิดข้อผิดพลาด:', error);
            }
        });
    }
    function deleteRoom(idRoom, idFloor){
            floor = idFloor;
            Swal.fire({
                title: 'ยืนยันการดำเนินการ?',
                text: 'คุณต้องการลบห้องหรือไม่?',
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
                        url: '/setting/room-layout/room/'+idRoom, // เปลี่ยน URL เป็นจุดหมายที่ต้องการ
                        type: 'DELETE',
                        data: {
                            _token : "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if(response == true){
                                loadRoomData();
                                Swal.fire('ลบห้องเรียบร้อยแล้ว', '', 'success');

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
    }
</script>