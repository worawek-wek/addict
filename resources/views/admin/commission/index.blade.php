<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr"
    data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template">

<head>
    @include('admin/layout/inc_header')
    <title>ค่าคอมมิชชั่น - CRM</title>
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
                                                    <i class="tf-icons ti ti-currency-dollar text-main ti-md me-2"></i>
                                                    ค่าคอมมิชชั่น
                                                </h4>
                                            </div>
                                            <div class="col-sm-3">
                                                <!-- future: filter/search -->
                                            </div>
                                            <div class="col-sm-9 text-end">
                                                <a href="{{ route('commission.create') }}" class="btn btn-main">
                                                    <i class="ti ti-plus"></i> เพิ่มค่าคอมมิชชั่น
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body px-0 pt-0">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>ชื่อพนักงาน</th>
                                                        <th>สาขา</th>
                                                        <th>ชื่อตำแหน่ง</th>
                                                        <th>ชื่อบริการ</th>
                                                        <th>ระยะเวลา</th>
                                                        <th>จำนวนเงินคอมมิชชั่น</th>
                                                        <th>จัดการ</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($commissions as $item)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>
                                                            @if($item->user)
                                                                {{ $item->user->name }}
                                                                @if($item->user->nickname)
                                                                    ({{ $item->user->nickname }})
                                                                @endif
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($item->user && $item->user->branch)
                                                                {{ $item->user->branch->name }}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($item->position)
                                                                {{ $item->position->position_name }}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td>{{ $item->service_name }}</td>
                                                        <td>{{ $item->service_duration }}</td>
                                                        <td>{{ number_format($item->commission_amount,2) }}</td>
                                                        <td>
                                                            <a href="{{ route('commission.edit', $item->id) }}" class="btn btn-sm btn-warning">แก้ไข</a>
                                                            <form action="{{ route('commission.destroy', $item->id) }}" method="POST" style="display:inline-block;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('ยืนยันการลบ?')">ลบ</button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                    @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center">- ไม่มีข้อมูล -</td>
                                                    </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
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
    @include('admin/layout/inc_js')
</body>

</html>
