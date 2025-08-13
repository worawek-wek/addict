<!doctype html>
<html lang="th">

<head>
    <title>Addict - Sign in</title>
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
                        <p class="text-center text-muted">Please log in to your account.</p>
                        {{-- <form action="home.php">
                            <div class="mb-3">
                                <label for="exampleFormControlInput1" class="form-label fs-14 mb-0">Email or
                                    Username</label>
                                <input type="text" class="form-control" id="exampleFormControlInput1"
                                    placeholder="Please enter your id_card or username.">
                            </div>
                            <div class="mb-3">
                                <label for="inputPass" class="form-label fs-14 mb-0">Password</label>
                                <div class="box-password">
                                    <input type="password" class="form-control" id="inputPass" placeholder="Password">
                                    <span toggle="#password-field"
                                        class="fi fi-rr-eye field-icon toggle-password"></span>
                                </div>
                            </div>
                            <button class="btn btn-purple w-100">Login</button>
                        </form> --}}
                        <form method="POST" action="{{ route('customer.login') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="id_card" class="form-label fs-14 mb-0">ID card number or Username</label>
                                <input type="text" name="id_card"
                                    class="form-control @error('id_card') is-invalid @enderror" id="id_card"
                                    value="{{ old('id_card') }}"
                                    placeholder="Please enter your ID card number or username.">
                                @error('id_card')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label fs-14 mb-0">Password</label>
                                <div class="box-password">
                                    <input type="password" name="password"
                                        class="form-control @error('password') is-invalid @enderror" id="password"
                                        placeholder="Password">
                                    <span toggle="#password" class="fi fi-rr-eye field-icon toggle-password"></span>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-purple w-100">Login</button>
                            <a href="{{ route('customer.register') }}" class="btn btn-outline-purple w-100">
                                สมัครสมาชิก
                            </a>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('frontend.layout.inc_footer')
    @include('frontend.layout.inc_js')

    <!--forgot Modal -->
    <div class="modal fade" id="forgotModal" tabindex="-1" aria-labelledby="forgotModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom-0">
                    <h1 class="modal-title fs-5" id="forgotModalLabel">Forgot Password?</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Enter your id_card and we'll send you instructions to reset your
                        password</p>
                    <div class="mb-3">
                        <label for="inputResetEmail" class="form-label fs-14 mb-0">Email</label>
                        <input type="text" class="form-control" id="inputResetEmail" placeholder="Enter your Email">
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-purple">Send Reset Link</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        $('#menu li').eq(0).addClass('active');
    </script>
    <script>
        $(".toggle-password").click(function() {
            $(this).toggleClass("fi-rr-eye-crossed");
            input = $(this).parent().find("input");
            if (input.attr("type") == "password") {
                input.attr("type", "text");
            } else {
                input.attr("type", "password");
            }
        });
    </script>
</body>

</html>

{{-- <div class="row gx-0">
                                <div class="col-sm-6">
                                    <div class="mb-3 form-check">
                                        <input type="checkbox" class="form-check-input" id="exampleCheck1">
                                        <label class="form-check-label" for="exampleCheck1">Remember Me</label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="mb-3 text-end">
                                        <button type="button" class="btn p-0 text-purple" data-bs-toggle="modal"
                                            data-bs-target="#forgotModal">Forgot Password?</button>
                                    </div>
                                </div>
                            </div> --}}
