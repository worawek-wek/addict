<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr"
    data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template">

<head>
    @include('admin/layout/inc_header')
    <title>รายชื่อการเข้างาน - CRM</title>
</head>
<style>
    .att-tab { cursor: pointer; }
    .att-pane { display: none; }
    .att-pane.active { display: block; }
    .table th { font-size: 14px; font-weight: bold; }
    .live-dot { width:10px; height:10px; border-radius:50%; background:#28c76f; display:inline-block; animation: blink 1.4s infinite; }
    @keyframes blink { 0%,100%{opacity:1} 50%{opacity:.3} }
</style>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            @include('admin/layout/inc_sidemenu')
            <div class="layout-page">
                @include('admin/layout/inc_topmenu')
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="card mb-3">
                            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <h5 class="mb-0">
                                    <i class="ti ti-users-group"></i> รายชื่อการเข้างาน
                                    <span class="live-dot ms-2"></span>
                                    <small class="text-muted">อัปเดตอัตโนมัติทุก 15 วินาที</small>
                                </h5>
                                <div class="d-flex align-items-center gap-2">
                                    @if (auth()->id() === 1)
                                        <select id="att-branch" class="form-select" style="width:180px;">
                                            <option value="">ทุกสาขา</option>
                                            @foreach ($branches as $b)
                                                <option value="{{ $b->id }}">{{ $b->name }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                    <input type="date" id="att-date" class="form-control" style="width:180px;"
                                        value="{{ now()->toDateString() }}">
                                    <a href="{{ url('admin/attendance/report') }}" class="btn btn-outline-primary">
                                        <i class="ti ti-report"></i> รายงาน
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="att-container">
                                    <div class="text-center text-muted py-5">กำลังโหลด...</div>
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
    <script>
        let attActiveKey = '0';

        function attApplyActive() {
            let btn = document.querySelector('.att-tab[data-key="' + attActiveKey + '"]');
            if (!btn) btn = document.querySelector('.att-tab');
            if (!btn) return;
            attSelect(btn.dataset.key);
        }

        function attSelect(key) {
            attActiveKey = key;
            document.querySelectorAll('.att-tab').forEach(t => t.classList.toggle('active', t.dataset.key === key));
            document.querySelectorAll('.att-pane').forEach(p => p.classList.toggle('active', p.dataset.key === key));
        }

        function loadAtt() {
            const cur = document.querySelector('.att-tab.active');
            if (cur) attActiveKey = cur.dataset.key;
            const branch = document.getElementById('att-branch') ? document.getElementById('att-branch').value : '';
            const date = document.getElementById('att-date').value || '';
            $.get('/admin/attendance/data', { ref_branch_id: branch, date: date }, function (html) {
                document.getElementById('att-container').innerHTML = html;
                attApplyActive();
            });
        }

        document.addEventListener('click', function (e) {
            const tab = e.target.closest('.att-tab');
            if (tab) attSelect(tab.dataset.key);
        });

        document.addEventListener('change', function (e) {
            if (e.target.id === 'att-branch' || e.target.id === 'att-date') loadAtt();
        });

        $(function () {
            loadAtt();
            setInterval(loadAtt, 15000);
        });
    </script>
</body>

</html>
