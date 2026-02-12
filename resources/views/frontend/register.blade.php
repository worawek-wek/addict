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
                        <p class="text-center text-muted" data-lang-th="สร้างบัญชีของคุณ" data-lang-en="Create your account.">สร้างบัญชีของคุณ</p>

                        {{-- แจ้งเตือน --}}
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('customer.register.submit') }}">
                            @csrf
                            <input type="hidden" name="locale" id="locale-input" value="th">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fs-14 mb-0" data-lang-th="ชื่อ" data-lang-en="First name">ชื่อ <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="first_name" value="{{ old('first_name') }}"
                                        class="form-control @error('first_name') is-invalid @enderror">
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-14 mb-0" data-lang-th="นามสกุล" data-lang-en="Last name">นามสกุล <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="last_name" value="{{ old('last_name') }}"
                                        class="form-control @error('last_name') is-invalid @enderror">
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fs-14 mb-0" data-lang-th="สัญชาติ" data-lang-en="Nationality">สัญชาติ</label>
                                    <input type="text" name="nationality" value="{{ old('nationality') }}"
                                        class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-14 mb-0" data-lang-th="เบอร์โทร" data-lang-en="Phone">เบอร์โทร <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="phone" value="{{ old('phone') }}"
                                        class="form-control @error('phone') is-invalid @enderror">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row-md-6">
                                    <hr>
                                    <h3 class="form-label fs-14 mb-3" data-lang-th="แอปติดต่อ" data-lang-en="Contact App">แอปติดต่อ</h3>

                                    <p class="text-danger mt-1" data-lang-th="กรุณากรอกช่องทางติดต่ออย่างน้อย 1 ช่อง" data-lang-en="Please provide at least one contact method">กรุณากรอกช่องทางติดต่ออย่างน้อย 1 ช่อง</p>
                                    <div class="row g-2">
                                        <div class="col-12 col-md-6">
                                            <label class="form-label fs-14 mb-0">
                                                <i class="fa-brands fa-line"></i> LINE ID
                                            </label>
                                            <input type="text" name="contact_line" value="{{ old('contact_line') }}"
                                                class="form-control" placeholder="LINE ID">
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="form-label fs-14 mb-0">
                                                <i class="fa-brands fa-whatsapp text-success me-1"></i> WhatsApp
                                            </label>
                                            <input type="text" name="contact_whatsapp"
                                                value="{{ old('contact_whatsapp') }}" class="form-control"
                                                placeholder="WhatsApp Number">
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="form-label fs-14 mb-0">
                                                <i class="fa-brands fa-weixin text-success me-1"></i> WeChat
                                            </label>
                                            <input type="text" name="contact_wechat"
                                                value="{{ old('contact_wechat') }}" class="form-control"
                                                placeholder="WeChat ID">
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="form-label fs-14 mb-0">
                                                <i class="fa-brands fa-telegram text-primary me-1"></i> Telegram
                                            </label>
                                            <input type="text" name="contact_telegram"
                                                value="{{ old('contact_telegram') }}" class="form-control"
                                                placeholder="Telegram ID">
                                        </div>
                                        <div class="col-12 row-md-6 mb-3">
                                            <label class="form-label fs-14 mb-0">
                                                <i class="fa-regular fa-envelope text-secondary me-1"></i> Email
                                            </label>
                                            <input type="email" name="contact_email"
                                                value="{{ old('contact_email') }}" class="form-control"
                                                placeholder="Email">
                                        </div>
                                    </div>
                                    <hr>
                                </div>
                                <div class="row-md-6">
                                    <label class="form-label fs-14 mb-0" data-lang-th="รหัสผ่าน" data-lang-en="Password">รหัสผ่าน <span
                                            class="text-danger">*</span></label>
                                    <div class="box-password">
                                        <input type="password" name="password" id="password"
                                            class="form-control @error('password') is-invalid @enderror"
                                            placeholder="Password">
                                        <span toggle="#password"
                                            class="fi fi-rr-eye field-icon toggle-password"></span>
                                    </div>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row-md-6 mb-3">
                                    <label class="form-label fs-14 mb-0" data-lang-th="ยืนยันรหัสผ่าน" data-lang-en="Confirm Password">ยืนยันรหัสผ่าน <span
                                            class="text-danger">*</span></label>
                                    <div class="box-password">
                                        <input type="password" name="password_confirmation"
                                            id="password_confirmation" class="form-control"
                                            placeholder="Confirm password">
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-purple w-100 mt-3" data-lang-th="สมัครสมาชิก" data-lang-en="Register">สมัครสมาชิก</button>
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
            const list = `{!! '<ul style="text-align:left;margin:0;padding-left:1.2rem;">' .
                collect($errors->all())->map(fn($e) => "<li>{$e}</li>")->implode('') .
                '</ul>' !!}`;
            Swal.fire({
                icon: 'error',
                title: '{{ app()->getLocale() === "en" ? "Validation Error" : "กรอกข้อมูลไม่ครบ" }}',
                html: list
            });
        </script>
    @endif

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: '{{ app()->getLocale() === "en" ? "Success" : "สำเร็จ" }}',
                text: '{{ session('success') }}'
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: '{{ app()->getLocale() === "en" ? "Error" : "เกิดข้อผิดพลาด" }}',
                text: '{{ session('error') }}'
            });
        </script>
    @endif
    <script>
        $('form').on('submit', function(e) {
            const contactAppIds = {
                line: document.querySelector('input[name="contact_line"]').value,
                whatsapp: document.querySelector('input[name="contact_whatsapp"]').value,
                wechat: document.querySelector('input[name="contact_wechat"]').value,
                telegram: document.querySelector('input[name="contact_telegram"]').value,
                email: document.querySelector('input[name="contact_email"]').value
            };

            const checkData = Object.values(contactAppIds);
            let hasData = false;
            for (const value of checkData) {
                if (value.trim() !== '') {
                    hasData = true;
                    break;
                }
            }

            if (!hasData) {
                e.preventDefault();
                const messages = {
                    th: {
                        title: 'กรุณากรอกช่องทางติดต่อ',
                        text: 'คุณต้องกรอกช่องทางติดต่ออย่างน้อย 1 ช่อง (LINE, WhatsApp, WeChat, Telegram หรือ Email)'
                    },
                    en: {
                        title: 'Please provide at least one contact method',
                        text: 'You must fill in at least one of the contact app fields (LINE, WhatsApp, WeChat, Telegram, or Email) to proceed with registration.'
                    }
                };
                Swal.fire({
                    icon: 'warning',
                    title: messages[currentLang].title,
                    text: messages[currentLang].text
                });
                return false;
            }
        });


        let currentLang = 'en';
        $('#language-toggle').prop('checked', true);
        $('#locale-input').val('en');
        switchLanguage('en');

        $('#language-toggle').on('change', function() {
            currentLang = $(this).is(':checked') ? 'en' : 'th';
            $('#locale-input').val(currentLang);
            switchLanguage(currentLang);
        });

        function switchLanguage(lang) {
            $('[data-lang-en][data-lang-th]').each(function() {
                const th = $(this).attr('data-lang-th');
                const en = $(this).attr('data-lang-en');
                const text = lang === 'en' ? en : th;

                if ($(this).find('.text-danger').length > 0) {
                    $(this).html(text + ' ' + $(this).find('.text-danger')[0].outerHTML);
                } else if ($(this).find('i').length > 0) {
                    $(this).html($(this).find('i')[0].outerHTML + ' ' + text);
                } else {
                    $(this).text(text);
                }
            });
        }

        $(".toggle-password").on('click', function() {
            $(this).toggleClass("fi-rr-eye-crossed");
            const target = $(this).attr('toggle');
            const confirm = $('#password_confirmation');
            const input = $(target);
            const input2 = $('#password_confirmation');
            input.attr('type', input.attr('type') === 'password' ? 'text' : 'password');
            input2.attr('type', input2.attr('type') === 'password' ? 'text' : 'password');
        });
    </script>
</body>

</html>
