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
                                            <!-- DataTable search box will be used instead of manual filter form -->
                                                <div class="col-sm-12 d-flex justify-content-end gap-2">
                                                    <a href="{{ route('commission.create') }}" class="btn btn-main">
                                                        <i class="ti ti-plus"></i> เพิ่มค่าคอมมิชชั่นพนักงานนวด
                                                    </a>
                                                    <a href="{{ route('sales_commission_tier.index') }}" class="btn btn-info">
                                                        <i class="ti ti-settings"></i> ตั้งค่าคอมมิชชั่นพนักงานขาย
                                                    </a>
                                                    <a href="{{ route('massage_default_setting.index') }}" class="btn btn-secondary">
                                                        <i class="ti ti-user"></i> ตั้งค่าเริ่มต้นพนักงานนวด
                                                    </a>
                                                    <a href="{{ route('cheer_charge.index') }}" class="btn btn-warning">
                                                        <i class="ti ti-star"></i> ตั้งค่าค่าเชียร์
                                                    </a>
                                                </div>
                                        </div>
                                    </div>
                                    <div class="card-body px-0 pt-0">
                                        <!-- ตารางนี้แสดงข้อมูลจาก massage_commissions -->
                                        <div class="table-responsive">
                                            <table class="datatables-basic table dataTable no-footer dtr-column" id="commission-table" aria-describedby="commission-table_info">
                                                <thead class="border-top">
                                                    <tr class="table-info">
                                                        <th class="text-center" style="width: 10px;">#</th>
                                                        <th class="text-center">ชื่อพนักงาน</th>
                                                        <th class="text-center">สาขา</th>
                                                        <th class="text-center">ชื่อตำแหน่ง</th>
                                                        <th class="text-center">ชื่อบริการ</th>
                                                        <th class="text-center">ระยะเวลา</th>
                                                        <th class="text-center">จำนวนเงินคอมมิชชั่น</th>
                                                        <th class="text-center">จัดการ</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($commissions as $item)
                                                    <tr>
                                                        <td class="text-center">{{ $loop->iteration }}</td>
                                                        <td class="text-center">
                                                            @if($item->user)
                                                                {{ $item->user->name }}
                                                                @if($item->user->nickname)
                                                                    ({{ $item->user->nickname }})
                                                                @endif
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if($item->user && $item->user->branch)
                                                                {{ $item->user->branch->name }}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if($item->position)
                                                                {{ $item->position->position_name }}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td class="text-center">{{ $item->service_name }}</td>
                                                        <td class="text-center">{{ $item->service_duration }}</td>
                                                        <td class="text-center">
                                                            @if($item->commission_amount)
                                                                {{ number_format($item->commission_amount,2) }} บาท
                                                            @elseif($item->commission_percent)
                                                                {{ number_format($item->commission_percent,2) }} %
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
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
                                                        <td class="text-center">-</td>
                                                        <td class="text-center">- ไม่มีข้อมูล -</td>
                                                        <td class="text-center">-</td>
                                                        <td class="text-center">-</td>
                                                        <td class="text-center">-</td>
                                                        <td class="text-center">-</td>
                                                        <td class="text-center">-</td>
                                                        <td class="text-center">-</td>
                                                    </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                            {{-- @include('admin/layout/pagination') --}}
                                        </div>
                                        <!-- DataTables JS/CSS -->
                                        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />
                                        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                                        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
                                        <script>
                                            $(document).ready(function() {
                                                $('#commission-table').DataTable({
                                                    language: {
                                                        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/th.json'
                                                    },
                                                    pageLength: 10,
                                                    ordering: true,
                                                    searching: true,
                                                    lengthChange: false
                                                });
                                            });
                                        </script>
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
