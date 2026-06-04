<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr"
    data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template">
<head>
    @include('admin/layout/inc_header')
    <title>เพิ่ม Addon Option</title>
</head>
<style>
    .form-label { font-weight: bold; }
</style>
<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            @include('admin/layout/inc_sidemenu')
            <div class="layout-page">
                @include('admin/layout/inc_topmenu')
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="row justify-content-center">
                            <div class="col-lg-6 col-md-8">
                                <div class="card mb-3">
                                    <div class="card-header border-bottom">
                                        <h4 class="mb-0">
                                            <i class="tf-icons ti ti-plus text-main ti-md me-2"></i>
                                            เพิ่ม Addon Option
                                        </h4>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('addon_options.store') }}" method="POST">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Name</label>
                                                <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="price" class="form-label">Price</label>
                                                <input type="number" name="price" id="price" class="form-control" value="{{ old('price') }}" step="0.01" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="branch" class="form-label">Branch</label>
                                                <select name="branch" id="branch" class="form-select">
                                                    <option value="">-- เลือกสาขา --</option>
                                                    @php
                                                        $user = Auth::user();
                                                        if ($user->ref_position_id == 0) {
                                                            $branches = \App\Models\Branch::all();
                                                        } else {
                                                            $branches = \App\Models\Branch::where('id', $user->ref_branch_id)->get();
                                                        }
                                                    @endphp
                                                    @foreach($branches as $branch)
                                                        <option value="{{ $branch->id }}" {{ old('branch') == $branch->id ? 'selected' : '' }}>
                                                            {{ $branch->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="row g-3">
                                                <div class="col-sm-4">
                                                    <label class="form-label">ค่ามือ</label>
                                                    <input name="commission" type="number" step="any" class="form-control"
                                                        placeholder="ค่ามือ" />
                                                </div>
                                                <div class="col-sm-4">
                                                    <label class="form-label">รับจริงร้าน</label>
                                                    <input name="coupon" type="number" step="any" class="form-control"
                                                        placeholder="รับจริงร้าน" />
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <button type="submit" class="btn btn-success">บันทึก</button>
                                                <a href="{{ route('addon_options.index') }}" class="btn btn-secondary">กลับ</a>
                                            </div>
                                        </form>
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
