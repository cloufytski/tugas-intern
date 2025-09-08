@php
  $customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/blank-layout')

@section('title', 'Register')

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
        <!-- Register Card -->
        <div class="card px-sm-6 px-0">
          <div class="card-body">
            <!-- Logo -->
            <div class="app-brand justify-content-center mb-6">
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
            <h4 class="mb-1">Register to {{ config('variables.templateSuffix') }}! 💻</h4>
            <p class="mb-6">Please register a new account!</p>

            <form id="form-register" class="mb-6">
              @csrf
              <div class="mb-6" id="name-group">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}"
                  placeholder="Enter your full name" autocomplete="name">
              </div>
              <div class="mb-6" id="username-group">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control " id="username" name="username" value="{{ old('username') }}"
                  placeholder="Enter your username">
              </div>
              <div class="mb-6" id="email-group">
                <label for="email" class="form-label">Email</label>
                <input type="text" class="form-control" id="email" name="email" value="{{ old('email') }}"
                  placeholder="Enter your email" autocomplete="email">
              </div>
              <div class="mb-6 form-password-toggle">
                <label class="form-label" for="password">Password</label>
                <div class="input-group input-group-merge" id="password-group">
                  <input type="password" class="form-control" id="password" name="password"
                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                    aria-describedby="password" autocomplete="new-password" />
                  <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                </div>
              </div>
              <div class="mb-6 form-password-toggle">
                <label class="form-label" for="password_confirmation">Confirm Password</label>
                <div class="input-group input-group-merge" id="password_confirmation-group">
                  <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                    autocomplete="new-password" />
                  <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                </div>
              </div>

              <div class="my-8">
                <div class="form-check mb-0 ms-2">
                  <input class="form-check-input" type="checkbox" id="terms-conditions" name="terms">
                  <label class="form-check-label" for="terms-conditions">
                    I agree to
                    <a href="javascript:void(0);">privacy policy & terms</a>
                  </label>
                </div>
              </div>
              <button class="btn btn-primary d-grid w-100">
                Sign up
              </button>
            </form>

            <p class="text-center">
              <span>Already have an account?</span>
              <a href="{{ route('login.view') }}">
                <span>Sign in instead</span>
              </a>
            </p>
          </div>
        </div>
        <!-- Register Card -->
      </div>
    </div>
  </div>
@endsection

@push('js')
  <script type="module">
    $(document).ready(function() {
      const $block = $('#form-register');
      $('#form-register').on('submit', function(e) {
        e.preventDefault();
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();

        submitRegister($(this));
      });

      function submitRegister(form) {
        showHideBlockUI(true, $block);
        $.ajax({
          url: "{{ route('register') }}",
          type: "POST",
          data: JSON.stringify(form.serializeArray().reduce((obj, item) =>
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
          error: function(xhr) {
            console.error('Register failed: ', xhr.responseJSON);
            if (xhr.status == 422 && typeof xhr.responseJSON.errors == 'object') {
              let errors = xhr.responseJSON.errors;
              $.each(errors, function(field, messages) {
                let group = $('#' + field + "-group");
                let input = group.find("input");
                input.addClass('is-invalid');
                group.append('<div class="invalid-feedback">' + messages[0] + '</div>');
              });
            } else {
              showErrorAlert(xhr.responseJSON.message);
            }
            showHideBlockUI(false, $block);
          }
        });
      }
    });
  </script>
@endpush
