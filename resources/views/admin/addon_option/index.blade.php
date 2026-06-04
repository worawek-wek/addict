<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr"
    data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template">

<head>
    @include('admin/layout/inc_header')
    <title>จัดการ Addon Option</title>
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
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card mb-3">
                                    <div class="card-header border-bottom">
                                        <div class="row g-3 justify-content-between align-items-center">
                                            <div class="col-sm-6">
                                                <h4 class="mb-0">
                                                    <i class="tf-icons ti ti-copy text-main ti-md me-2"></i>
                                                    Addon Options
                                                </h4>
                                            </div>
                                            <div class="col-sm-6 text-end">
                                                <a href="{{ route('addon_options.create') }}" class="btn btn-main">
                                                    <i class="ti ti-plus"></i> เพิ่ม Option
                                                </a>
                                            </div>
                                        </div>
                                        @if(session('success'))
                                            <div class="alert alert-success mt-2 mb-0">{{ session('success') }}</div>
                                        @endif
                                    </div>
                                    <div class="card-body px-0 pt-0">
                                        <div class="table-responsive">
                                            <table class="table table-bordered mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Name</th>
                                                        <th>Price</th>
                                                        <th>ค่ามือ</th>
                                                        <th>รับจริงร้าน</th>
                                                        <th>Branch</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $branchNames = $branches->pluck('name', 'id');
                                                    @endphp
                                                    @foreach($options as $option)
                                                        <tr>
                                                            <td>{{ $option->id }}</td>
                                                            <td>{{ $option->name }}</td>
                                                            <td>{{ number_format($option->price, 2) }}</td>
                                                            <td>{{ number_format($option->commission ?? 0, 2) }}</td>
                                                            <td>{{ number_format($option->coupon ?? 0, 2) }}</td>
                                                            <td>{{ $branchNames[$option->branch] ?? '-' }}</td>
                                                            <td>
                                                                <a href="{{ route('addon_options.edit', $option->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                                                <form action="{{ route('addon_options.destroy', $option->id) }}" method="POST" style="display:inline-block;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @endforeach
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
