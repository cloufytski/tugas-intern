@php
  use App\Helpers\Helpers;
  $configData = Helpers::appClasses();
  $container = 'container-fluid';
  $containerNav = 'container-fluid';
@endphp

@extends('layouts/layout-master')

@section('title', 'Settings - Security')

{{-- prettier-ignore-start --}}
@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/@form-validation/form-validation.scss',
    'resources/assets/vendor/libs/animate-css/animate.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
  ])
@endsection

@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/@form-validation/popular.js',
    'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
    'resources/assets/vendor/libs/@form-validation/auto-focus.js',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
  'resources/assets/vendor/libs/block-ui/block-ui.js',
  ])
@endsection
{{-- prettier-ignore-end --}}

@section('content')
  <div class="row">
    <div class="col-md-12">
      <div class="nav-align-top">
        <ul class="nav nav-pills flex-column flex-md-row mb-6">
          <li class="nav-item"><a class="nav-link" href="{{ route('settings.account.view') }}">
              <i class="bx bx-sm bx-user me-1_5"></i> Account</a>
          </li>
          @if (Auth::user()->is_local)
            <li class="nav-item"><a class="nav-link active" href="javascript:void(0);">
                <i class="bx bx-sm bx-lock-alt me-1_5"></i> Security</a>
            </li>
          @endif
          <li class="nav-item"><a class="nav-link" href="{{ route('settings.filter.view') }}">
              <i class="bx bx-sm bx-filter-alt me-1_5"></i> Filter</a>
          </li>
        </ul>
      </div>

      <div class="card mb-6" id="reset-password-block">
        <h5 class="card-header">Change Password</h5>
        <div class="card-body pt-1">
          <form id="formAccountSettings">
            @csrf
            <div class="row">
              <div class="mb-6 col-md-6 form-password-toggle">
                <label class="form-label" for="current_password">Current Password</label>
                <div class="input-group input-group-merge" id="current_password-group">
                  <input class="form-control" type="password" name="current_password" id="current_password"
                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                  <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="mb-6 col-md-6 form-password-toggle">
                <label class="form-label" for="password">New Password</label>
                <div class="input-group input-group-merge" id="password-group">
                  <input class="form-control" type="password" id="password" name="password"
                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                  <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                </div>
              </div>

              <div class="mb-6 col-md-6 form-password-toggle">
                <label class="form-label" for="password_confirmation">Confirm New Password</label>
                <div class="input-group input-group-merge" id="password_confirmation-group">
                  <input class="form-control" type="password" name="password_confirmation" id="password_confirmation"
                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                  <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                </div>
              </div>
            </div>
            <h6 class="text-body">Password Requirements:</h6>
            <ul class="ps-4 mb-0">
              <li class="mb-2">Minimum 8 characters long - the more, the better</li>
              <li class="mb-2">At least one lowercase character</li>
              <li>At least one number, symbol, or whitespace character</li>
            </ul>
            <div class="mt-6">
              <button type="submit" class="btn btn-primary me-2" id="save-password-btn">Save changes</button>
              <button type="reset" class="btn btn-label-secondary" id="reset-btn">Reset</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('js')
  <script type="module">
    $(document).ready(function() {
      const formChangePass = $('#formAccountSettings');

      const fv = FormValidation.formValidation(formChangePass[0], {
        fields: {
          current_password: {
            validators: {
              notEmpty: {
                message: 'Please current password'
              },
              stringLength: {
                min: 8,
                message: 'Password must be more than 8 characters'
              }
            }
          },
          password: {
            validators: {
              notEmpty: {
                message: 'Please enter new password'
              },
              stringLength: {
                min: 8,
                message: 'Password must be more than 8 characters'
              }
            }
          },
          password_confirmation: {
            validators: {
              notEmpty: {
                message: 'Please confirm new password'
              },
              identical: {
                compare: function() {
                  return formChangePass.find('#password').val();
                },
                message: 'The password and its confirm are not the same'
              },
              stringLength: {
                min: 8,
                message: 'Password must be more than 8 characters'
              }
            }
          }
        },
        plugins: {
          trigger: new FormValidation.plugins.Trigger(),
          bootstrap5: new FormValidation.plugins.Bootstrap5({
            eleValidClass: '',
            rowSelector: '.col-md-6'
          }),
          submitButton: new FormValidation.plugins.SubmitButton(),
          autoFocus: new FormValidation.plugins.AutoFocus()
        },
        init: instance => {
          instance.on('plugins.message.placed', function(e) {
            if (e.element.parentElement.classList.contains('input-group')) {
              e.element.parentElement.insertAdjacentElement('afterend', e.messageElement);
            }
          });
        }
      });

      formChangePass[0].formValidationInstance = fv;

      fv.on('core.form.valid', function() {
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
        submitResetPassword();
      });

      $(document).on('click', '#reset-btn', function() {
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
        resetFormValidation(formChangePass);
      });

      function submitResetPassword() {
        const $block = $('#reset-password-block');
        showHideBlockUI(true, $block);

        const data = {
          'is_reset_password': true,
        };

        formChangePass.serializeArray().reduce((obj, item) =>
          (obj[item.name] = item.value, obj), data);

        $.ajax({
          url: "{{ route('user.reset-password', $user->id) }}",
          type: "POST",
          data: JSON.stringify(data),
          contentType: "application/json",
          dataType: "json",
          success: function(response) {
            if (response.success) {
              showSuccessAlert(response.message);
            } else {
              showErrorAlert(response.message);
            }
            resetFormValidation(formChangePass);
            showHideBlockUI(false, $block);
          },
          error: function(xhr) {
            console.error('Failed to reset user password: ', xhr.responseJSON);
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
