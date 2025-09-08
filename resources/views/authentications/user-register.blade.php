@php
  $container = 'container-xxl';
  $containerNav = 'container-xxl';
@endphp

@extends('layouts/content-navbar-layout')

@section('title', 'Register User')

{{-- prettier-ignore-start --}}
@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/animate-css/animate.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
    'resources/assets/vendor/libs/select2/select2.scss',
  ])
@endsection

@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
    'resources/assets/vendor/libs/select2/select2.js',
  ])
@endsection
{{-- prettier-ignore-end --}}

@section('content')
  <div class="row justify-content-center">
    <div class="col-lg-1">
      <a class="btn btn-text-secondary mt-2" href="{{ route('user.view') }}">Back</a>
    </div>
    <div class="col-lg-8">
      <div class="card mb-6">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Register User</h5>
        </div>
        <div class="card-body">
          <form id="form-user-register" novalidate>
            @csrf

            <div class="row mb-4 form-group">
              <label class="col-sm-3 col-form-label" for="name">Full Name</label>
              <div class="col-sm-9" id="name-group">
                <input type="text" class="form-control" id="name" name="name" placeholder="John Doe" />
              </div>
            </div>
            <div class="row mb-4 form-group">
              <label class="col-sm-3 col-form-label" for="username">Username</label>
              <div class="col-sm-9" id="username-group">
                <input type="text" class="form-control" id="username" name="username" placeholder="johndoe" />
              </div>
            </div>
            <div class="row mb-4 form-group">
              <label class="col-sm-3 col-form-label" for="email">Email</label>
              <div class="col-sm-9" id="email-group">
                <input type="email" class="form-control" id="email" name="email"
                  placeholder="john.doe@example.com" />
              </div>
            </div>
            <div class="row mb-4 form-group">
              <label class="col-sm-3 col-form-label" for="role">Role</label>
              <div class="col-sm-9" id="role-group">
                <select class="form-select" id="role" name="role"></select>
              </div>
            </div>
            <div class="row mb-4 form-group form-password-toggle">
              <label class="col-sm-3 col-form-label" for="password">Password</label>
              <div class="col-sm-9">
                <div class="input-group input-group-merge" id="password-group">
                  <input type="password" class="form-control" id="password" name="password"
                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                    aria-describedby="password" required autocomplete="new-password" />
                  <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                </div>
              </div>
            </div>
            <div class="row mb-4 form-group form-password-toggle">
              <label class="col-sm-3 col-form-label" for="password_confirmation">Confirm Password</label>
              <div class="col-sm-9">
                <div class="input-group input-group-merge" id="password_confirmation-group">
                  <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" required
                    autocomplete="new-password" />
                  <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                </div>
              </div>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('js')
  <script type="module">
    $(document).ready(function() {
      const $form = $('#form-user-register');
      $('#role').select2({
        placeholder: 'Select Role ...',
        minimumResultsForSearch: -1,
        multiple: true,
        allowClear: true,
        ajax: {
          url: "{{ route('role.index') }}",
          dataType: 'json',
          delay: 250,
          cache: true,
          processResults: response => ({
            results: response.data.map(item => ({
              id: item.name,
              text: item.display_name,
            }))
          }),
        }
      });

      $form.submit(function(event) {
        event.preventDefault();

        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();

        $.ajax({
          url: "{{ route('user.store') }}",
          type: "POST",
          data: $(this).serialize(),
          success: function(response) {
            if (response.success) {
              showSuccessAlert(response.message, 'User Registered!');
              resetForm();
            } else {
              showErrorAlert(response.message);
            }
          },
          error: function(xhr) {
            if (xhr.status == 422 && typeof xhr.responseJSON.errors == 'object') {
              let errors = xhr.responseJSON.errors;
              $.each(errors, function(field, messages) {
                let group = $("#" + field + "-group");
                let input = group.find("input, select");
                input.addClass('is-invalid');
                group.append('<div class="invalid-feedback">' + messages[0] + '</div>');
              });
            } else {
              showErrorAlert(xhr.responseJSON.message);
            }
          }
        });
      });

      function resetForm() {
        $form[0].reset();
        $('#role').val('').trigger('change');
      }
    });
  </script>
@endpush
