<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr"
    data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template">

<head>
    @include('admin/layout/inc_header')
    <title>ลูกค้า - CRM</title>
</head>
<style>
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
        padding: 1.25rem 1.5rem 1.25rem;
        color: white;
        background-color: #54BAB9;
        position: relative;
    }

    .modalHeadDecor .modal-title::after {
        position: absolute;
        top: 0;
        right: -65px;
        content: '';
        width: 0;
        height: 0;
        border-top: 65px solid #54BAB9;
        border-right: 65px solid transparent;
    }
</style>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            @include('admin/layout/inc_sidemenu')

            <div class="layout-page">
                @include('admin/layout/inc_topmenu')

                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="row ">
                            <div class="col-sm-12">
                                <div class="card mb-3">
                                    <div class="card-header border-bottom border-bottom">
                                        <div class="row g-3 justify-content-between">
                                            <div class="col-sm-12">
                                                <h4 class="mb-0">
                                                    <i class="tf-icons ti ti-copy text-main ti-md me-2"></i>
                                                    รายการกลุ่มห้อง
                                                </h4>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="row align-items-center">
                                                    <div class="col-auto">
                                                        <select name="ref_branch_id" class="form-select p_search"
                                                            onchange='loadData("{{ $page_url }}/datatable")'
                                                            required>
                                                            @if (Auth::user()->work_status == 3)
                                                                <option value="-1" selected>ทั้งหมด</option>
                                                            @endif
                                                            @foreach ($branches as $bra)
                                                                <option value="{{ $bra->id }}" @if (Auth::user()->ref_branch_id == $bra->id) selected @endif>{{ $bra->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <div class="input-group input-group-merge">
                                                            <span class="input-group-text" id="basic-addon-search31"><i
                                                                    class="ti ti-search"></i></span>
                                                            <input name="search" type="text"
                                                                class="form-control p_search"
                                                                placeholder="ค้นหาคีเวิร์ดที่ต้องการ"
                                                                aria-label="ค้นหาคีเวิร์ดที่ต้องการ"
                                                                aria-describedby="basic-addon-search31"
                                                                oninput='loadData("{{ $page_url }}/datatable")' />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        <div class="row p-3">
                                            <div class="col-lg-4">
                                                <div class="d-flex align-items-center mb-2 mb-md-0">
                                                    <label class="">Show</label>
                                                    <select onchange='loadData("{{ $page_url }}/datatable")'
                                                        name="limit" class="form-select ms-2 me-2 p_search"
                                                        style="width:100px">
                                                        <option value="25">25</option>
                                                        <option value="50" selected >50</option>
                                                        <option value="100">100</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-8 text-end">
                                                <button class="btn btn-main" data-bs-toggle="modal"
                                                    data-bs-target="#addRoomGroupModal">
                                                    <i class="ti ti-plus"></i> เพิ่มกลุ่มห้อง
                                                </button>
                                            </div>
                                        </div>

                                        <div class="card-body px-0 pt-0">
                                            <div id="table-data"><!-- ตารางโหลดด้วย AJAX --></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @include('admin/layout/inc_footer')
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Add Room Group --}}
    <div class="modal fade modalHeadDecor" id="addRoomGroupModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-0">
                <div class="modal-header rounded-0">
                    <h5 class="modal-title">เพิ่มกลุ่มห้อง</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="create_room_group_form" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3 p-4">
                            <div class="col-sm-12">
                                <label class="form-label">ชื่อกลุ่มห้อง</label><span class="text-danger">*</span>
                                <input name="name" type="text" class="form-control" required />
                            </div>
                            <div class="col-sm-12">
                                <label class="form-label">สาขา</label><span class="text-danger">*</span>
                                <select name="branch_id" class="form-control" required>
                                    @if (!empty($branches) || count($branches) > 0)
                                        <option value="" disabled selected>-- เลือกสาขา --</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    @else
                                        <option value="" disabled selected>ไม่มีสาขาในระบบ</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">ปิด</button>
                        <button type="submit" class="btn btn-main" id='save_create_room_group'>บันทึก</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal: Edit Room Group --}}
    <div class="modal fade modalHeadDecor" id="editRoomGroupModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-0">
                <div class="modal-header rounded-0">
                    <h5 class="modal-title">แก้ไขกลุ่มห้อง</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="edit_room_group_form" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="edit_room_group_id" name="id">
                    <div class="modal-body">
                        <div class="row g-3 p-4">
                            <div class="col-sm-12">
                                <label class="form-label">ชื่อกลุ่มห้อง</label><span class="text-danger">*</span>
                                <input name="name" id="edit_room_group_name" type="text" class="form-control"
                                    required />
                            </div>
                            <div class="col-sm-12">
                                <label class="form-label">สาขา</label><span class="text-danger">*</span>
                                <select name="branch_id" id="edit_room_group_branch_id" class="form-control"
                                    required>
                                    @if (!empty($branches) && count($branches) > 0)
                                        <option value="" disabled selected>-- เลือกสาขา --</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    @else
                                        <option value="" disabled selected>ไม่มีสาขาในระบบ</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">ปิด</button>
                        <button type="submit" class="btn btn-main">บันทึก</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal: Add Room to Group --}}
    <div class="modal fade modalHeadDecor" id="addRoomToGroupModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-0">
                <div class="modal-header rounded-0">
                    <h5 class="modal-title">เพิ่มห้องเข้ากลุ่ม: <span id="add_room_group_name"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="add_room_to_group_form">
                    @csrf
                    <input type="hidden" id="add_room_group_id" name="group_id">
                    <div class="modal-body">
                        <div class="row g-3 p-4">
                            <div class="col-sm-12">
                                <label class="form-label">เลือกห้องที่ต้องการเพิ่ม</label>
                                <div id="available_rooms_list" class="border rounded p-3"
                                    style="max-height: 300px; overflow-y: auto;">
                                    <div class="text-center text-muted">กำลังโหลด...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">ปิด</button>
                        <button type="submit" class="btn btn-main">บันทึก</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal: View Rooms in Group --}}
    <div class="modal fade modalHeadDecor" id="viewRoomsInGroupModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-0">
                <div class="modal-header rounded-0">
                    <h5 class="modal-title">ห้องในกลุ่ม: <span id="view_room_group_name"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3 p-4">
                        <div class="col-sm-12">
                            <div id="rooms_in_group_list" class="border rounded p-3"
                                style="max-height: 400px; overflow-y: auto;">
                                <div class="text-center text-muted">กำลังโหลด...</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">ปิด</button>
                </div>
            </div>
        </div>
    </div>

    @include('admin/layout/inc_js')
</body>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        loadData("{{ $page_url }}/datatable");
    });

    const loadData = (url) => {
        let limit = document.querySelector('.p_search[name="limit"]')?.value || 25;
        let search = document.querySelector('.p_search[name="search"]')?.value || '';
        let branchId = document.querySelector('.p_search[name="ref_branch_id"]')?.value || '';

        fetch(`${url}?limit=${limit}&search=${search}&branch_id=${branchId}`)
            .then(response => response.text())
            .then(html => {
                document.getElementById('table-data').innerHTML = html;
            })
            .catch(error => console.error('Error loading data:', error));
    }

    document.getElementById('create_room_group_form').addEventListener('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);

        fetch("{{ $page_url }}/create", {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 200) {
                    bootstrap.Modal.getInstance(document.getElementById('addRoomGroupModal')).hide();
                    this.reset();
                    loadData("{{ $page_url }}/datatable");
                    alert('บันทึกสำเร็จ');
                } else {
                    alert(data.message || 'เกิดข้อผิดพลาด');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('เกิดข้อผิดพลาด');
            });
    });

    const editRoomGroup = (id, name, branchId) => {
        document.getElementById('edit_room_group_id').value = id;
        document.getElementById('edit_room_group_name').value = name;
        if (branchId) {
            document.getElementById('edit_room_group_branch_id').value = branchId;
        }
        new bootstrap.Modal(document.getElementById('editRoomGroupModal')).show();
    }

    document.getElementById('edit_room_group_form').addEventListener('submit', function(e) {
        e.preventDefault();
        let id = document.getElementById('edit_room_group_id').value;
        let formData = new FormData(this);

        fetch(`{{ $page_url }}/update/${id}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'X-HTTP-Method-Override': 'PUT'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 200) {
                    bootstrap.Modal.getInstance(document.getElementById('editRoomGroupModal')).hide();
                    loadData("{{ $page_url }}/datatable");
                    alert('แก้ไขสำเร็จ');
                } else {
                    alert(data.message || 'เกิดข้อผิดพลาด');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('เกิดข้อผิดพลาด');
            });
    });

    const deleteRoomGroup = (id) => {
        if (confirm('คุณต้องการลบกลุ่มห้องนี้หรือไม่?')) {
            fetch(`{{ $page_url }}/delete/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 200) {
                        loadData("{{ $page_url }}/datatable");
                        alert('ลบสำเร็จ');
                    } else {
                        alert(data.message || 'เกิดข้อผิดพลาด');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('เกิดข้อผิดพลาด');
                });
        }
    }



    let currentViewGroupId = null;

    const viewRoomsInGroup = (id, groupName) => {
        currentViewGroupId = id;
        document.getElementById('view_room_group_name').textContent = groupName;
        document.getElementById('rooms_in_group_list').innerHTML =
            '<div class="text-center text-muted">กำลังโหลด...</div>';

        fetch(`{{ $page_url }}/getRoom/${id}`, {
                method: 'GET',
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 200) {
                    let roomsHtml = '';
                    if (data.data.length === 0) {
                        roomsHtml = '<div class="text-center text-muted">ไม่มีห้องในกลุ่มนี้</div>';
                    } else {
                        roomsHtml =
                            '<table class="table table-sm"><thead><tr><th>#</th><th>ชื่อห้อง</th><th class="text-center">จัดการ</th></tr></thead><tbody>';
                        data.data.forEach((room, index) => {
                            roomsHtml += `
                            <tr id="room_row_${room.id}">
                                <td>${index + 1}</td>
                                <td>${room.name}</td>
                                <td class="text-center">
                                    <button class="btn btn-danger btn-sm" onclick="removeRoomFromGroup(${room.id})">
                                        <i class="ti ti-trash"></i> ลบออกจากกลุ่ม
                                    </button>
                                </td>
                            </tr>
                        `;
                        });
                        roomsHtml += '</tbody></table>';
                    }
                    document.getElementById('rooms_in_group_list').innerHTML = roomsHtml;
                } else {
                    document.getElementById('rooms_in_group_list').innerHTML =
                        '<div class="text-danger">เกิดข้อผิดพลาดในการโหลดข้อมูล</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('rooms_in_group_list').innerHTML =
                    '<div class="text-danger">เกิดข้อผิดพลาดในการโหลดข้อมูล</div>';
            });

        new bootstrap.Modal(document.getElementById('viewRoomsInGroupModal')).show();
    }

    const removeRoomFromGroup = (roomId) => {
        if (confirm('คุณต้องการลบห้องนี้ออกจากกลุ่มหรือไม่?')) {
            fetch(`{{ $page_url }}/removeRoom/${roomId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 200) {
                        let row = document.getElementById(`room_row_${roomId}`);
                        if (row) row.remove();

                        let tbody = document.querySelector('#rooms_in_group_list tbody');
                        if (tbody && tbody.children.length === 0) {
                            document.getElementById('rooms_in_group_list').innerHTML =
                                '<div class="text-center text-muted">ไม่มีห้องในกลุ่มนี้</div>';
                        }

                        loadData("{{ $page_url }}/datatable");
                        alert('ลบห้องออกจากกลุ่มสำเร็จ');
                    } else {
                        alert(data.message || 'เกิดข้อผิดพลาด');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('เกิดข้อผิดพลาด');
                });
        }
    }

    document.addEventListener('click', function(e) {
        if (e.target.closest('.pagination a')) {
            e.preventDefault();
            let url = e.target.closest('.pagination a').href;
            let limit = document.querySelector('.p_search[name="limit"]')?.value || 10;
            let search = document.querySelector('.p_search[name="search"]')?.value || '';

            fetch(`${url}&limit=${limit}&search=${search}`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('table-data').innerHTML = html;
                });
        }
    });
    const openAddRoomModal = (groupId, groupName) => {
        document.getElementById('add_room_group_id').value = groupId;
        document.getElementById('add_room_group_name').textContent = groupName;

        fetch("{{ $page_url }}/getAll/")
            .then(response => response.json())
            .then(data => {
                if (data.status === 200) {
                    let roomsHtml = '';
                    if (data.data.length === 0) {
                        roomsHtml = '<div class="text-center text-muted">ไม่มีห้องที่ยังไม่มีกลุ่ม</div>';
                    } else {
                        data.data.forEach(room => {
                            roomsHtml += `
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="room_ids[]" value="${room.id}" id="room_${room.id}">
                                    <label class="form-check-label" for="room_${room.id}">
                                        ${room.name}
                                    </label>
                                </div>
                            `;
                        });
                    }
                    document.getElementById('available_rooms_list').innerHTML = roomsHtml;
                } else {
                    document.getElementById('save_create_room_group').disabled = true;
                    document.getElementById('available_rooms_list').innerHTML =
                        '<div class="text-danger">เกิดข้อผิดพลาดในการโหลดข้อมูล</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('available_rooms_list').innerHTML =
                    '<div class="text-danger">เกิดข้อผิดพลาดในการโหลดข้อมูล</div>';
            });

        new bootstrap.Modal(document.getElementById('addRoomToGroupModal')).show();
    }

    document.getElementById('add_room_to_group_form').addEventListener('submit', function(e) {
        e.preventDefault();
        let groupId = document.getElementById('add_room_group_id').value;
        let checkedBoxes = document.querySelectorAll('input[name="room_ids[]"]:checked');

        if (checkedBoxes.length === 0) {
            alert('กรุณาเลือกห้องอย่างน้อย 1 ห้อง');
            return;
        }

        let roomIds = Array.from(checkedBoxes).map(cb => cb.value);

        fetch(`{{ $page_url }}/addRoom/${groupId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify({
                    room_ids: roomIds
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 200) {
                    bootstrap.Modal.getInstance(document.getElementById('addRoomToGroupModal')).hide();
                    loadData("{{ $page_url }}/datatable");
                    alert('เพิ่มห้องเข้ากลุ่มสำเร็จ');
                } else {
                    alert(data.message || 'เกิดข้อผิดพลาด');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('เกิดข้อผิดพลาด');
            });
    });
</script>
