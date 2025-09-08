@php
  $container = 'container-xxl';
  $containerNav = 'container-xxl';
@endphp

@extends('layouts/content-navbar-layout')

@section('title', 'Register User')

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

@section('content')
  <div class="row justify-content-center">
    <div class="col-lg-1">
      <a class="btn btn-text-secondary mt-2" href="{{ route('user.view') }}">Back</a>
    </div>
    <div class="col-lg-8">
      <div class="card mb-6">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Reset Password</h5>
        </div>
        <div class="card-body">
          <form id="form-user-reset-password" novalidate>
            @csrf

            <div class="row mb-4 form-group">
              <label class="col-sm-3 col-form-label" for="email">Email</label>
              <div class="col-sm-9">
                <input type="email" class="form-control-plaintext" id="email" name="email" readonly
                  value="{{ $user->email }}" />
              </div>
            </div>
            <div class="row mb-4 form-group form-password-toggle">
              <label class="col-sm-3 col-form-label" for="password">Password</label>
              <div class="col-sm-9">
                <div class="input-group input-group-merge" id="password-group">
                  <input type="password" class="form-control" id="password" name="password" aria-describedby="password"
                    required autocomplete="new-password" />
                  <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                </div>
              </div>
            </div>
            <div class="row mb-4 form-group form-password-toggle">
              <label class="col-sm-3 col-form-label" for="password_confirmation">Confirm Password</label>
              <div class="col-sm-9">
                <div class="input-group input-group-merge" id="password_confirmation-group">
                  <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                    autocomplete="new-password" />
                  <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                </div>
              </div>
            </div>
            <button type="submit" class="btn btn-primary">Reset Password</button>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('js')
  <script type="module">
    $(document).ready(function() {
      var resetPasswordForm = $('#form-user-reset-password').submit(function(event) {
        event.preventDefault();

        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();

        const data = {
          'is_reset_password': true,
        };

        $.ajax({
          url: "{{ route('user.reset-password', $user->id) }}",
          type: "POST",
          data: JSON.stringify($(this).serializeArray().reduce((obj, item) =>
            (obj[item.name] = item.value, obj), data)),
          contentType: 'application/json',
          dataType: 'json',
          success: function(response) {
            if (response.success) {
              showSuccessAlert(response.message, 'Password Reset');
              resetPasswordForm[0].reset();
              window.location.href = "{{ route('user.view') }}";
            } else {
              showErrorAlert(response.message);
            }
          },
          error: function(xhr) {
            if (xhr.status == 422 && typeof xhr.responseJSON.errors == 'object') {
              let errors = xhr.responseJSON.errors;
              $.each(errors, function(field, messages) {
                let group = $("#" + field + "-group");
                let input = group.find("input");
                input.addClass('is-invalid');
                group.append('<div class="invalid-feedback">' + messages[0] + '</div>');
              });
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Something went wrong. Please try again.'
              });
            }
          }
        });
      });
    });
  </script>
@endpush
