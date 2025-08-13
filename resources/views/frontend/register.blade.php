<!doctype html>
<html lang="th">
<head>
    <title>Addict - Sign up</title>
    @include('frontend.layout.inc_header')
</head>
<body class="bg-addict bgs-100">
<div class="container">
    <div class="authentication-wrapper authentication-basic">
        <div class="authentication-inner">
            <div class="card p-xl-4">
                <div class="card-body">
                    <div class="text-center">
                        <div class="switch">
                            <input id="language-toggle" class="check-toggle check-toggle-round-flat" type="checkbox">
                            <label for="language-toggle"></label>
                            <span class="on">EN</span>
                            <span class="off">TH</span>
                        </div>
                    </div>

                    <h1 class="text-center ff-playfair">Addict</h1>
                    <p class="text-center text-muted">Create your account.</p>

                    {{-- แจ้งเตือน --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('customer.register.submit') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fs-14 mb-0">ชื่อ (First name) <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" value="{{ old('first_name') }}"
                                       class="form-control @error('first_name') is-invalid @enderror">
                                @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fs-14 mb-0">นามสกุล (Last name) <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" value="{{ old('last_name') }}"
                                       class="form-control @error('last_name') is-invalid @enderror">
                                @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fs-14 mb-0">Nationality</label>
                                <input type="text" name="nationality" value="{{ old('nationality') }}" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fs-14 mb-0">Phone <span class="text-danger">*</span></label>
                                <input type="text" name="phone" value="{{ old('phone') }}"
                                       class="form-control @error('phone') is-invalid @enderror">
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fs-14 mb-0">Contact App</label>
                                <select name="contact_app" class="form-select">
                                    <option value="">-- เลือก --</option>
                                    <option value="line" @selected(old('contact_app')==='line')>LINE ID</option>
                                    <option value="whatsapp" @selected(old('contact_app')==='whatsapp')>WhatsApp</option>
                                    <option value="wechat" @selected(old('contact_app')==='wechat')>WeChat</option>
                                    <option value="email" @selected(old('contact_app')==='email')>Email</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fs-14 mb-0">Contact App ID</label>
                                <input type="text" name="contact_app_handle" value="{{ old('contact_app_handle') }}" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fs-14 mb-0">ID card number<span class="text-danger">*</span></label>
                                <input type="text" name="id_card" value="{{ old('id_card') }}"
                                       class="form-control @error('id_card') is-invalid @enderror">
                                @error('id_card') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- <div class="col-md-6">
                                <label class="form-label fs-14 mb-0">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" value="{{ old('email') }}"
                                       class="form-control @error('email') is-invalid @enderror">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div> --}}

                            <div class="col-md-6">
                                <label class="form-label fs-14 mb-0">Password <span class="text-danger">*</span></label>
                                <div class="box-password">
                                    <input type="password" name="password" id="password"
                                           class="form-control @error('password') is-invalid @enderror" placeholder="Password">
                                    <span toggle="#password" class="fi fi-rr-eye field-icon toggle-password"></span>
                                </div>
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fs-14 mb-0">Confirm Password <span class="text-danger">*</span></label>
                                <div class="box-password">
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                           class="form-control" placeholder="Confirm password">
                                    <span toggle="#password_confirmation" class="fi fi-rr-eye field-icon toggle-password"></span>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-purple w-100 mt-3">สมัครสมาชิก</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


@include('frontend.layout.inc_footer')
@include('frontend.layout.inc_js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if ($errors->any())
<script>
  const list = `{!! '<ul style="text-align:left;margin:0;padding-left:1.2rem;">'.
    collect($errors->all())->map(fn($e)=>"<li>{$e}</li>")->implode('').
  '</ul>' !!}`;
  Swal.fire({ icon:'error', title:'กรอกข้อมูลไม่ครบ', html:list });
</script>
@endif

@if (session('success'))
<script>
  Swal.fire({ icon:'success', title:'สำเร็จ', text:'{{ session('success') }}' });
</script>
@endif

@if (session('error'))
<script>
  Swal.fire({ icon:'error', title:'เกิดข้อผิดพลาด', text:'{{ session('error') }}' });
</script>
@endif
<script>
    $(".toggle-password").on('click', function() {
        $(this).toggleClass("fi-rr-eye-crossed");
        const target = $(this).attr('toggle');
        const input = $(target);
        input.attr('type', input.attr('type') === 'password' ? 'text' : 'password');
    });
</script>
</body>
</html>
