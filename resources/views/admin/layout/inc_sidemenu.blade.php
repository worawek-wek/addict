{{-- <script>
    if ("{{session('branch_id')}}" == '') {
        window.location.href = '/branch/manage';  // ทำการ redirect ถ้าไม่มี branch_id
    }
</script> --}}
<style>
    .active .menu-link i {
        color: #ffffff !important; /* เปลี่ยนสีของไอคอนใน <li> ที่มีคลาส active */
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
        <a href="index.html" class="app-brand-link d-block text-center w-100">
            <img src="assets/img/illustrations/main.png" alt="" class="mw-100" height="100%">
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle text-large ms-auto" style="color: white;">
            <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
            <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
          </a>
    </div>

    {{-- <div class="menu-inner-shadow"></div> --}}

    <ul class="menu-inner py-3">
        {{-- <li class="menu-item">
            <a href="/admin/dashboard" class="menu-link">
                <i class="menu-icon tf-icons ti ti-smart-home"></i>
                <div data-i18n="Dashboards">Dashboards</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="/admin/room" class="menu-link">
                <i class="menu-icon tf-icons ti ti-sitemap"></i>
                <div data-i18n="ผังห้อง">ผังห้อง</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="/admin/meter" class="menu-link">
                <i class="menu-icon tf-icons ti ti-id"></i>
                <div data-i18n="มิเตอร์">มิเตอร์</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="/admin/bill" class="menu-link">
                <i class="menu-icon tf-icons ti ti-license"></i>
                <div data-i18n="บิลค่าเช่า">บิลค่าเช่า</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="/admin/income-expenses" class="menu-link">
                <i class="menu-icon tf-icons ti ti-calculator"></i>
                <div data-i18n="รายรับ-รายจ่าย">รายรับ-รายจ่าย</div>
            </a>
        </li> --}}
        {{-- <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-circle-half-2"></i>
                <div data-i18n="วิเคราะห์">วิเคราะห์</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="/admin/analysis/monthly-rent" class="menu-link">
                        <div data-i18n="วิเคราะห์ค่าเช่ารายเดือน">วิเคราะห์ค่าเช่ารายเดือน</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="/admin/analysis/income-expense" class="menu-link">
                        <div data-i18n="วิเคราะห์รายรับ-รายจ่าย">วิเคราะห์รายรับ-รายจ่าย</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="/admin/analysis/water" class="menu-link">
                        <div data-i18n="วิเคราะห์ค่าน้ำ">วิเคราะห์ค่าน้ำ</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="/admin/analysis/elect" class="menu-link">
                        <div data-i18n="วิเคราะห์ค่าไฟ">วิเคราะห์ค่าไฟ</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="/admin/analysis/meter" class="menu-link">
                        <div data-i18n="วิเคราะห์มิเตอร์ผู้เช่า">วิเคราะห์มิเตอร์ผู้เช่า</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="/admin/analysis/tenants" class="menu-link">
                        <div data-i18n="วิเคราะห์การเข้าออกผู้เช่า">วิเคราะห์การเข้าออกผู้เช่า</div>
                    </a>
                </li>
            </ul>
        </li> --}}
        <!-- รายงานสรุป -->
        {{-- <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-chart-pie-3"></i>
                <div data-i18n="รายงานสรุป">รายงานสรุป</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="/admin/report/view-overview" class="menu-link">
                        <div data-i18n="ภาพรวม">ภาพรวม</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="/admin/report/rent-bill" class="menu-link">
                        <div data-i18n="รายงานบิลค่าเช่า">รายงานบิลค่าเช่า</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="/admin/report/move-in" class="menu-link">
                        <div data-i18n="รายงานย้ายเข้า">รายงานย้ายเข้า</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="/admin/report/move-out" class="menu-link">
                        <div data-i18n="รายงานย้ายออก">รายงานย้ายออก</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="/admin/report/bad-debt" class="menu-link">
                        <div data-i18n="รายงานหนี้สูญ">รายงานหนี้สูญ</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="/admin/report/monthly-booking" class="menu-link">
                        <div data-i18n="รายงานจองรายเดือน">รายงานจองรายเดือน</div>
                    </a>
                </li>
            </ul>
        </li> --}}
        {{-- <li class="menu-item">
            <a href="/admin/vehicle" class="menu-link">
                <i class="menu-icon tf-icons ti ti-car"></i>
                <div data-i18n="ยานพาหนะ">ยานพาหนะ</div>
            </a>
        </li> --}}
        <li class="menu-item">
            <a href="/admin/user" class="menu-link">
                <i class="menu-icon tf-icons ti ti-copy"></i>
                <div data-i18n="บุคลากร">บุคลากร</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="/admin/product" class="menu-link">
                <i class="menu-icon tf-icons ti ti-receipt-tax"></i>
                <div data-i18n="สินค้า">สินค้า</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="/admin/room" class="menu-link">
                <i class="menu-icon tf-icons ti ti-receipt-tax"></i>
                <div data-i18n="ห้อง">ห้อง</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="/admin/customer" class="menu-link">
                <i class="menu-icon tf-icons ti ti-users"></i>
                <div data-i18n="ลูกค้า">ลูกค้า</div>
            </a>
        </li>
        <!-- ระบบรายงาน -->
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-adjustments-horizontal"></i>
                <div data-i18n="ระบบรายงาน">ระบบรายงาน</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="/admin/order" class="menu-link">
                        <div data-i18n="รายงานขายสินค้า">รายงานขายสินค้า</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="/admin/sales_report" class="menu-link">
                        <div data-i18n="รายงานยอดขาย">รายงานยอดขาย</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="/admin/card_stock_report" class="menu-link">
                        <div data-i18n="รายงานสต็อกการ์ด">รายงานสต็อกการ์ด</div>
                    </a>
                </li>
                {{-- <li class="menu-item">
                    <a href="/admin/setting/room-layout" class="menu-link">
                        <div data-i18n="ผังห้อง">ผังห้อง</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="/admin/setting/water-electric-bill" class="menu-link">
                        <div data-i18n="ค่าน้ำ-ค่าไฟ">ค่าน้ำ-ค่าไฟ</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="/admin/setting/room-rent" class="menu-link">
                        <div data-i18n="ค่าเช่าห้อง">ค่าเช่าห้อง</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="/admin/setting/service-discount" class="menu-link">
                        <div data-i18n="ค่าบริการ ส่วนลด">ค่าบริการ ส่วนลด</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="/admin/setting/fine" class="menu-link">
                        <div data-i18n="ค่าปรับ">ค่าปรับ</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="/admin/setting/bank" class="menu-link">
                        <div data-i18n="บัญชีธนาคาร">บัญชีธนาคาร</div>
                    </a>
                </li> --}}
            </ul>
        </li>
    </ul>
</aside>

    <script>
    setTimeout(() => {
        document.querySelectorAll('.menu-item').forEach(item => {
            const hasActiveChild = item.querySelector('.menu-sub .menu-item.active');
            if (hasActiveChild) {
                item.classList.add('open');
            }
        });
    }, 500);
    document.addEventListener("DOMContentLoaded", function() {
        var links = document.querySelectorAll("ul li a");
        var currentUrl = window.location.pathname;

        links.forEach(function(link) {
            if (link.getAttribute("href") === currentUrl) {
                link.parentElement.classList.add("active");
            }
        });
    });

    </script>