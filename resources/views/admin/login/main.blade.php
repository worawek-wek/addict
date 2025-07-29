<!doctype html>
@extends('../admin/layout/' . $layout)
<html
  lang="en"
  class="light-style layout-wide customizer-hide"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="{{url('')}}/assets/"
  data-template="vertical-menu-template">
  <head>
    @include('admin/layout/inc_header')
    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="{{url('')}}/assets/vendor/css/pages/page-auth.css" />
  </head>

  <body>
    <!-- Content -->
    <div class="authentication-wrapper authentication-cover authentication-bg">
      <div class="authentication-inner row">
        <!-- /Left Text -->
        <div class="d-none d-lg-flex col-lg-7 p-0">
          <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center mx-0">
            <img style="max-height: none;"
              src="{{url('')}}/assets/img/illustrations/auth-login-illustration-light.png"
              alt="auth-login-cover"
              class="img-fluid auth-illustration"
              data-app-light-img="illustrations/auth-login-illustration-light.png"
              data-app-dark-img="illustrations/auth-login-illustration-dark.png" />
          </div>
        </div>
        <!-- /Left Text -->
        <!-- Login -->
        <div class="d-flex col-12 col-lg-5 align-items-center p-sm-5 p-4">
          <div class="col-12 mx-auto">
            <!-- Logo -->
            <div class="app-brand mb-4">
              <img src="{{url('')}}/assets/img/illustrations/main.png" alt="" style="margin: auto;">
            </div>
            <!-- /Logo -->
            <h3 class="mb-1">ยินดีต้อนรับ</h3>
            <p class="mb-4">Please sign-in to your account and start the adventure</p>

                    {{-- @include('layout/inc_footer') --}}
            <div id="formAuthentication" class="mb-3">
              <form id="login-form">
                <div class="mb-3">
                  <label for="email" class="form-label">Email or Username</label>
                  <input
                    type="text"
                    class="form-control"
                    id="email"
                    name="email-username"
                    placeholder=""
                    autofocus
                    value="wolverine.wek@gmail.com" />
                <div id="error-email" class="login__input-error text-danger mt-2"></div>
                </div>
                <div class="mb-3 form-password-toggle">
                  <div class="d-flex justify-content-between">
                    <label class="form-label" for="password">Password</label>
                    {{-- <a href="auth-forgot-password-cover.html">
                      <small>Forgot Password?</small>
                    </a> --}}
                  </div>
                  <div class="input-group">
                    <input
                      type="password"
                      id="password"
                      class="form-control"
                      name="password"
                      placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                      aria-describedby="password"
                      value="172839" />
                    <span class="input-group-text cursor-pointer" onclick="togglePassword()"><i id="eye-icon" class="ti ti-eye-off"></i></span>
                  </div>
                  <div id="error-password" class="login__input-error text-danger mt-2"></div>
                </div>
              </form>
                {{-- <div class="mb-3">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember-me" />
                    <label class="form-check-label" for="remember-me"> Remember Me </label>
                  </div>
                </div> --}}
                <button id="btn-login" class="btn btn-main w-100">Sign in</button>
            </div>
          </div>
        </div>
        <!-- /Login -->
      </div>
    </div>

    @include('admin/layout/inc_js')
    
@section('script')

<script>
  document.addEventListener('DOMContentLoaded', function() {
    function togglePassword() {
      var passwordField = document.getElementById('password');
      var eyeIcon = document.getElementById('eye-icon');
      
      // Toggle the type of the input field between password and text
      if (passwordField.type === "password") {
        passwordField.type = "text";
        eyeIcon.classList.remove("ti-eye-off");
        eyeIcon.classList.add("ti-eye");
      } else {
        passwordField.type = "password";
        eyeIcon.classList.remove("ti-eye");
        eyeIcon.classList.add("ti-eye-off");
      }
    }
  });
</script>
<script>
    (function () {
        async function login() {
            // Reset state
            $('#login-form').find('.login__input').removeClass('border-danger')
            $('#login-form').find('.login__input-error').html('')

            // Post form
            let email = $('#email').val()
            let password = $('#password').val()

            // Loading state
            $('#btn-login').html('<i data-loading-icon="oval" data-color="white" class="w-5 h-5 mx-auto"></i>')
            tailwind.svgLoader()
            await helper.delay(1500)

            axios.post(`admin/login`, {
                email: email,
                password: password
            }).then(res => {
                location.href = '/admin/user'
            }).catch(err => {
                $('#btn-login').html('Login')
                if (err.response.data.message != 'Wrong email or password.') {
                    for (const [key, val] of Object.entries(err.response.data.errors)) {
                        $(`#${key}`).addClass('border-danger')
                        $(`#error-${key}`).html(val)
                    }
                } else {
                    $(`#password`).addClass('border-danger')
                    $(`#error-password`).html(err.response.data.message)
                }
            })
        }

        $('#login-form').on('keyup', function(e) {
            if (e.keyCode === 13) {
                login()
            }
        })

        $('#btn-login').on('click', function() {
            login()
        })
    })()
</script>
@endsection
  </body>
</html>
