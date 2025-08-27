<!DOCTYPE html>
<html lang="th" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact"
      dir="ltr" data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template">

<head>
    {{-- ส่วน head รวม --}}
    @include('admin/layout/inc_header')
    <title>@yield('title', 'Dashboard - CRM')</title>
</head>

<body>
<div class="layout-wrapper layout-content-navbar">
  <div class="layout-container">

    {{-- เมนูด้านซ้าย --}}
    @include('admin/layout/inc_sidemenu')

    <div class="layout-page">
      {{-- Top Navbar --}}
      @include('admin/layout/inc_topmenu')

      <div class="content-wrapper">
        {{-- เนื้อหาหลัก --}}
        <div class="container-xxl flex-grow-1 container-p-y">
          @yield('content')
        </div>

        {{-- Footer --}}
        @include('admin/layout/inc_footer')
      </div>
    </div>
  </div>
</div>

{{-- Overlay & drag target --}}
<div class="layout-overlay layout-menu-toggle"></div>
<div class="drag-target"></div>

{{-- script js --}}
@include('admin/layout/inc_js')
@stack('scripts')
</body>
</html>
