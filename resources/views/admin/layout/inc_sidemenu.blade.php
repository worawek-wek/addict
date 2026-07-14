{{-- <script>
    if ("{{session('branch_id')}}" == '') {
        window.location.href = '/branch/manage';  // ทำการ redirect ถ้าไม่มี branch_id
    }
</script> --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

<style>
    .active .menu-link i {
        color: #ffffff !important;
        /* เปลี่ยนสีของไอคอนใน <li> ที่มีคลาส active */
    }

    /* .active .menu-link {
        color: #000000 !important;
    } */
    /* .active .menu-link > div {
        color: #000000 !important;
    } */
    /* .layout-menu{
        background-color: #6f6f6f !important;
    }
    .menu-link > div{
        color:white;
    }
    .menu-link i {
        color: white !important;
    }
    .menu-link:hover > div,
    .menu-link:hover > i {
        color: #000000 !important;
    }
    .menu-toggle::after {
        content: "";
        position: absolute;
        top: 48%;
        display: block;
        width: 0.42em;
        height: 0.42em;
        border: 1.5px solid white;
        border-bottom: 0;
        border-left: 0;
        transform: translateY(-50%) rotate(45deg);
    }
    .menu-item.open .menu-toggle::after {
        content: "";
        position: absolute;
        top: 48%;
        display: block;
        width: 0.42em;
        height: 0.42em;
        border: 1.5px solid #6f6f6f;
        border-bottom: 0;
        border-left: 0;
        transform: translateY(-50%) rotate(45deg);
    }
    .menu-item.open .menu-icon {
        color: #6f6f6f !important;
    }
    .menu-item.open .menu-toggle > div {
        color: #6f6f6f !important;
        font-weight: 500;
    } */
</style>
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme pt-2">
    <div class="app-brand demo" style="height: 66px;">
        <div class="app-brand-link d-block text-center w-100">
            <img src="assets/img/illustrations/main.png" alt="" class="mw-100" height="100%">
        </div>

        <a href="javascript:void(0);" class="layout-menu-toggle text-large ms-auto text-main">
            <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
            <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
        </a>
    </div>

    {{-- <div class="menu-inner-shadow"></div> --}}

    <ul class="menu-inner py-3">

        @php
            $clockInBranchId = optional(auth()->user())->ref_branch_id ?: \App\Models\Branch::query()->value('id');
            $isBoss = auth()->id() === 1;
            $isCommissionAdmin = $isBoss || (auth()->user() && in_array(auth()->user()->ref_position_id, [0, 3]));
        @endphp

        {{-- ▸ หมวด: ขาย / หน้าร้าน --}}
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-building-store"></i>
                <div data-i18n="ขาย / หน้าร้าน">ขาย / หน้าร้าน</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="/pos/room" class="menu-link" target="_blank" rel="noopener noreferrer">
                        <i class="menu-icon tf-icons ti ti-shopping-cart"></i>
                        <div data-i18n="POS (ขายสินค้า)">POS (ขายสินค้า)</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="/admin/order-rooms" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-file-invoice"></i>
                        <div data-i18n="การจองห้อง">การจองห้อง</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="/admin/order-products" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-file-invoice"></i>
                        <div data-i18n="การขายสินค้า">การขายสินค้า</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="/admin/order-drinks" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-file-invoice"></i>
                        <div data-i18n="การขายดื่ม">การขายดื่ม</div>
                    </a>
                </li>
            </ul>
        </li>

        {{-- ▸ หมวด: คอมมิชชั่น --}}
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-currency-dollar"></i>
                <div data-i18n="คอมมิชชั่น">คอมมิชชั่น</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="/admin/commission/dashboard" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-layout-dashboard"></i>
                        <div data-i18n="Dashboard สรุป">Dashboard สรุป</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="/admin/commission/view-sales" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-user-dollar"></i>
                        <div data-i18n="รายงานค่าคอม (นวด+สินค้า)">รายงานค่าคอม (นวด+สินค้า)</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="/admin/commission/drink-view-sales" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-user-dollar"></i>
                        <div data-i18n="รายงานค่าคอม (ดื่ม)">รายงานค่าคอม (ดื่ม)</div>
                    </a>
                </li>
                @if ($isBoss)
                    <li class="menu-item">
                        <a href="/admin/commission-ranks" class="menu-link">
                            <i class="menu-icon tf-icons ti ti-stairs-up"></i>
                            <div data-i18n="ตั้งค่าบันได Rank (มาม่า)">ตั้งค่าบันได Rank (มาม่า)</div>
                        </a>
                    </li>
                @endif
            </ul>
        </li>

        {{-- ▸ หมวด: สินค้า & สต็อก --}}
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-box"></i>
                <div data-i18n="สินค้า & สต็อก">สินค้า &amp; สต็อก</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="/admin/product" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-receipt-tax"></i>
                        <div data-i18n="สินค้า">สินค้า</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="/admin/card_stock_report" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-cards"></i>
                        <div data-i18n="สต็อกการ์ด(สินค้า)">สต็อกการ์ด(สินค้า)</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="/admin/drink" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-glass-full"></i>
                        <div data-i18n="ดื่ม">ดื่ม</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="/admin/drink_card_stock_report" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-cards"></i>
                        <div data-i18n="สต็อกการ์ด(ดื่ม)">สต็อกการ์ด(ดื่ม)</div>
                    </a>
                </li>
            </ul>
        </li>

        {{-- ▸ หมวด: ห้อง & คอร์ส --}}
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-door"></i>
                <div data-i18n="ห้อง & คอร์ส">ห้อง &amp; คอร์ส</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="/admin/room-groups" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-layout-grid"></i>
                        <div data-i18n="กลุ่มห้อง">กลุ่มห้อง</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="/admin/room" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-door"></i>
                        <div data-i18n="ห้อง">ห้อง</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="/admin/room-type" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-versions"></i>
                        <div data-i18n="รูปแบบห้อง">รูปแบบห้อง</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="/admin/course" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-list-check"></i>
                        <div data-i18n="คอร์ส">คอร์ส</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="/admin/addon-options" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-settings"></i>
                        <div data-i18n="Addon Options">Addon Options</div>
                    </a>
                </li>
            </ul>
        </li>

        {{-- ▸ หมวด: ข้อมูลหลัก --}}
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-database"></i>
                <div data-i18n="ข้อมูลหลัก">ข้อมูลหลัก</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="/admin/user" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-users"></i>
                        <div data-i18n="บุคลากร">บุคลากร</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="/admin/customer" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-user-heart"></i>
                        <div data-i18n="ลูกค้า">ลูกค้า</div>
                    </a>
                </li>
            </ul>
        </li>

        {{-- ▸ หมวด: รายงาน --}}
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-chart-pie-3"></i>
                <div data-i18n="รายงาน">รายงาน</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="/admin/report/stock-history" class="menu-link">
                        <div data-i18n="รายงานสต็อกการ์ด(สินค้า)">รายงานสต็อกการ์ด(สินค้า)</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="/admin/report/coupon-report" class="menu-link">
                        <div data-i18n="รายงานคูปองพนักงาน">รายงานคูปองพนักงาน</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="/admin/report/oversee-employee" class="menu-link">
                        <div data-i18n="รายงานผู้ดูแลพนักงาน">รายงานผู้ดูแลพนักงาน</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="/admin/report/drink-com" class="menu-link">
                        <div data-i18n="รายงานค่าดื่มพนักงาน">รายงานค่าดื่มพนักงาน</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="/admin/report/monthly-sale" class="menu-link">
                        <div data-i18n="รายงานยอดขายรวม">รายงานยอดขายรวม</div>
                    </a>
                </li>
            </ul>
        </li>

        {{-- ▸ หมวด: ลงเวลาเข้างาน (ล่างสุด) --}}
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-clock-hour-4"></i>
                <div data-i18n="ลงเวลาเข้างาน">ลงเวลาเข้างาน</div>
            </a>
            <ul class="menu-sub">
                @if ($clockInBranchId)
                    <li class="menu-item">
                        <a href="{{ url('admin/' . $clockInBranchId . '/clock-in') }}" class="menu-link" target="_blank"
                            rel="noopener noreferrer">
                            <i class="menu-icon tf-icons ti ti-id-badge-2"></i>
                            <div data-i18n="แตะบัตรเข้างาน">แตะบัตรเข้างาน</div>
                        </a>
                    </li>
                @endif
                <li class="menu-item">
                    <a href="/admin/attendance" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-users-group"></i>
                        <div data-i18n="รายชื่อการเข้างาน">รายชื่อการเข้างาน</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="/admin/attendance/report" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-report"></i>
                        <div data-i18n="รายงานเข้างาน">รายงานเข้างาน</div>
                    </a>
                </li>
            </ul>
        </li>

    </ul>
</aside>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const currentUrl = window.location.pathname;
        const links = document.querySelectorAll(".menu-link");

        let bestMatch = null;
        let bestLength = 0;

        links.forEach(link => {
            const href = link.getAttribute("href");
            if (!href) return;

            if (currentUrl === href || currentUrl.startsWith(href + '/')) {
                if (href.length > bestLength) {
                    bestMatch = link;
                    bestLength = href.length;
                }
            }
        });

        if (bestMatch) {
            const li = bestMatch.closest("li.menu-item");
            li?.classList.add("active");

            // เปิดเมนูแม่ (submenu)
            const parentSub = li.closest("ul.menu-sub");
            if (parentSub) {
                const parentMenu = parentSub.closest("li.menu-item");
                parentMenu?.classList.add("open", "active");
            }
        }
    });
</script>
