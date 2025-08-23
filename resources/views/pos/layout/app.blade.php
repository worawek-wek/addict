<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS System</title>

    {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #f5f6f8;
            font-family: "Segoe UI", sans-serif;
        }

        /* บังคับความกว้างของ offcanvas */
        .offcanvas.offcanvas-start {
            width: 220px !important;
            /* ใช้ !important เพื่อ override Bootstrap */
            max-width: 80%;
            /* กันพังตอนจอเล็ก */
        }

        .offcanvas-header {
            background-color: #5e2a5f;
            color: white;
        }

        .menu-btn {
            width: 100%;
            text-align: left;
            border-radius: 8px;
            padding: 10px;
        }

        .menu-btn.active {
            background-color: #5e2a5f;
            color: #fff;
        }

        .menu-btn i {
            margin-right: 8px;
        }
    </style>

</head>

<body>
    {{-- Header --}}
    <nav class="navbar px-3 navbar-light bg-white border-bottom">
        <div class="container-fluid flex-wrap">

            {{-- Left: Toggle Sidebar + Logo --}}
            <div class="d-flex align-items-center me-auto mb-2 mb-md-0">
                <button class="btn btn-link text-dark me-3 p-0" data-bs-toggle="offcanvas"
                    data-bs-target="#sidebarMenu">
                    <i class="bi bi-list" style="font-size: 1.5rem;"></i>
                </button>
                <span class="navbar-brand mb-0 text-dark">
                    ADDICT <small class="text-muted">Point of Sale</small>
                </span>
            </div>

            {{-- Right: Date + Profile --}}
            <div class="d-flex align-items-center ms-auto flex-shrink-0">
                <span class="text-muted me-2 small">{{ now()->format('d F Y') }}</span>
                <img src="{{ asset('upload/user/' . (Auth::user()->image_name ?? 'default.png')) }}"
                    class="rounded-circle me-2" width="35" height="35">
                <div class="text-dark d-none d-sm-block">
                    <div class="fw-bold">{{ Auth::user()->name ?? '-' }}</div>
                    <small class="text-muted">สาขา {{ Auth::user()->branch->name ?? 'User' }}</small>

                </div>
            </div>

        </div>
    </nav>

    {{-- Sidebar (Offcanvas) --}}
    <div class="offcanvas offcanvas-start" tabindex="-1" id="sidebarMenu">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">ADDICT</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">

            {{-- Cashier --}}
            <a href="{{ route('pos.index') }}"
                class="btn menu-btn {{ request()->routeIs('pos.index') ? 'active' : '' }}">
                <i class="bi bi-calculator"></i> Cashier
            </a>

            {{-- Room --}}
            <a href="{{ route('pos.room.index') }}"
                class="btn menu-btn {{ request()->routeIs('pos.room.index') ? 'active' : '' }}">
                <i class="bi bi-door-open"></i> Room
            </a>

        </div>
    </div>

    {{-- Main --}}
    <main class="p-3">
        @yield('content')
    </main>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
