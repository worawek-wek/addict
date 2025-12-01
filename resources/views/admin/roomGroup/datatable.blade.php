<table class="table table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>ชื่อกลุ่มห้อง</th>
            <th>จำนวนห้อง</th>
            <th>วันที่สร้าง</th>
            <th>สาขา</th>
            <th class="text-center">เพิ่มห้อง</th>
            <th class="text-center">จัดการ</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $index => $roomGroup)
            <tr>
                <td>{{ $data->firstItem() + $index }}</td>
                <td>{{ $roomGroup->name }}</td>
                <td>{{ $roomGroup->room_children_count ?? 0 }}</td>
                <td>{{ $roomGroup->created_at ? $roomGroup->created_at->format('d/m/Y H:i') : '-' }}</td>
                <td>{{ $roomGroup->branch->name ?? '-' }}</td>

                <td class="text-center">
                    <button class="btn btn-info btn-sm"
                        onclick="openAddRoomModal({{ $roomGroup->id }}, '{{ $roomGroup->name }}')">
                        <i class="ti ti-plus"></i> เพิ่มห้อง
                    </button>
                </td>
                <td class="text-center">
                    <button class="btn btn-primary btn-sm"
                        onclick="viewRoomsInGroup({{ $roomGroup->id }}, '{{ $roomGroup->name }}')">
                        <i class="ti ti-eye"></i> ดูห้อง
                    </button>
                    <button class="btn btn-warning btn-sm"
                        onclick="editRoomGroup({{ $roomGroup->id }}, '{{ $roomGroup->name }}')">
                        <i class="ti ti-edit"></i> แก้ไข
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="deleteRoomGroup({{ $roomGroup->id }})">
                        <i class="ti ti-trash"></i> ลบ
                    </button>
                </td>
            </tr>
        @endforeach
        @if ($data->isEmpty())
            <tr>
                <td colspan="5" class="text-center">ไม่มีข้อมูล</td>
            </tr>
        @endif
    </tbody>
</table>

{{-- Pagination --}}
<div class="d-flex justify-content-between align-items-center mt-3 px-3">
    <div>
        แสดง {{ $data->firstItem() ?? 0 }} - {{ $data->lastItem() ?? 0 }} จาก {{ $data->total() }} รายการ
    </div>
    <div>
        {{ $data->links() }}
    </div>
</div>
