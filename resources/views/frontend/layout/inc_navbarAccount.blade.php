<nav class="navbar bg-transparent">
    <div class="container">
        <div class="switch">
            <input id="language-toggle" class="check-toggle check-toggle-round-flat" type="checkbox">
            <label for="language-toggle"></label>
            <span class="on">EN</span>
            <span class="off">TH</span>
        </div>
        <a class="navbar-brand" href="#">
            <h2 class="ff-playfair text-white mb-0">Addict</h2>
        </a>
        <div class="d-flex">
            <div class="dropdown btn-group">
                <button class="btn dropdown-toggle d-flex align-items-center text-white" type="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fi fi-rr-circle-user me-md-1"></i>
                    <span class="d-md-inline d-none">{{ Auth::guard('customer')->user()?->name ?? 'Username' }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a href="{{ route('customer.orders.history') }}"
                            class="dropdown-item d-flex align-items-center">
                            <i class="fi fi-rr-document me-1"></i> ประวัติการจอง
                        </a>
                    </li>
                    <li>
                        <form method="POST" action="{{ route('customer.logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item d-flex align-items-center">
                                <i class="fi fi-rr-power me-1"></i> ออกจากระบบ
                            </button>
                        </form>
                    </li>
                </ul>
            </div>

            <a href="{{ route('customer.orders.history') }}" class="btn btn-icon d-flex align-items-center text-white">
                <i class="fi fi-rr-file-invoice me-2"></i> Booking history
            </a>
        </div>

    </div>
</nav>
