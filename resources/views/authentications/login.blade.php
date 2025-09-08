@php
  use Illuminate\Support\Facades\Route;
  $customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/blank-layout')

@section('title', 'Login')

{{-- prettier-ignore-start --}}
@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/@form-validation/form-validation.scss',
    'resources/assets/vendor/libs/animate-css/animate.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
  ])
@endsection

@section('page-style')
  @vite([
    'resources/assets/vendor/scss/pages/page-auth.scss',
  ])
@endsection

@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/@form-validation/popular.js',
    'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
    'resources/assets/vendor/libs/@form-validation/auto-focus.js',
    'resources/assets/vendor/libs/block-ui/block-ui.js',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
  ])
@endsection
{{-- prettier-ignore-end --}}

@section('content')
  <div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
      <div class="authentication-inner">
        <!-- Register -->
        <div class="card px-sm-6 px-0">
          <div class="card-body">
            <!-- Logo -->
            <div class="app-brand justify-content-center">
              <a href="{{ url('/') }}" class="app-brand-link gap-2">
                <span class="app-brand-logo demo">@include('_partials.macros', ['width' => 50, 'withbg' => 'var(--bs-primary)'])</span>
                <div class="row justify-content-center">
                  <div class="col">
                    <div class="row">
                      <div class="col">
                        <span
                          class="app-brand-text demo text-heading fw-bold">{{ config('variables.templateName') }}</span>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col">
                        <span class="h5 text-muted fw-light">{{ config('variables.templateNameSub') }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </a>
            </div>
            <!-- /Logo -->
            <h4 class="mb-1">Welcome to {{ config('variables.templateName') }}! 👋</h4>
            <p class="mb-6">Please sign-in to your account</p>

            <form id="form-login">
              @csrf
              <div class="mb-4" id="email-group">
                <label for="email" class="form-label">Email</label>
                <input type="text" class="form-control" id="email" name="email" placeholder="Enter your email"
                  value="{{ old('email') }}" required autofocus autocomplete="username">
              </div>
              <div class="mb-4 form-password-toggle">
                <label class="form-label" for="password">Password</label>
                <div class="input-group input-group-merge">
                  <input type="password" id="password" class="form-control" name="password"
                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                    aria-describedby="password" required autocomplete="current-password" />
                  <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                </div>
              </div>
              <div class="mb-4">
                <div class="d-flex justify-content-between mt-8">
                  <div class="form-check mb-0 ms-2">
                    <input class="form-check-input" type="checkbox" id="remember" name="remember" value="true">
                    <label class="form-check-label" for="remember">
                      Remember Me
                    </label>
                  </div>
                  @if (Route::has('password-request'))
                    <a href="{{ route('password.request') }}">
                      <span>Forgot Password?</span>
                    </a>
                  @endif
                </div>
              </div>
              <div class="mb-6">
                <button type="submit" class="btn btn-primary d-grid w-100">Login</button>
              </div>
            </form>

            <p class="text-center">
              <span>New on our platform?</span>
              <a href="{{ route('register.view') }}">
                <span>Create an account</span>
              </a>
            </p>
          </div>
        </div>
      </div>
      <!-- /Register -->
    </div>
  </div>
@endsection

@push('js')
  <script type="module">
    $(document).ready(function() {
      const $block = $('#form-login');

      $('#form-login').on('submit', function(e) {
        e.preventDefault();
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();

        submitLogin();
      });

      function submitLogin() {
        showHideBlockUI(true, $block);
        $.get('/sanctum/csrf-cookie').then(function() {
          $.ajax({
            url: "{{ route('login') }}",
            type: "POST",
            data: JSON.stringify($('#form-login').serializeArray().reduce((obj, item) =>
              (obj[item.name] = item.value, obj), {})),
            contentType: "application/json",
            dataType: "json",
            success: function(response) {
              if (response.success) {
                window.location.href = "{{ route('dashboard') }}";
              } else {
                showErrorAlert(response.message);
              }
              showHideBlockUI(false, $block);
            },
            error: function(xhr, status, error) {
              console.error('Login failed: ', xhr.responseJSON);
              if (xhr.status === 401) {
                $('#email').addClass('is-invalid');
                $('#email-group').append(
                  '<div class="invalid-feedback">' + xhr.responseJSON.message + '</div>'
                );
              } else {
                showErrorAlert(xhr.responseJSON.message);
              }
              showHideBlockUI(false, $block);
            }
          });
        });
      }
    });
  </script>
@endpush
