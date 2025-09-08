@php
  use App\Helpers\Helpers;
  $configData = Helpers::appClasses();
  $container = 'container-fluid';
  $containerNav = 'container-fluid';
@endphp

@extends('layouts/layout-master')

@section('title', 'Settings - Account')

{{-- prettier-ignore-start --}}
@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/@form-validation/form-validation.scss',
  'resources/assets/vendor/libs/animate-css/animate.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/select2/select2.js',
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
          <li class="nav-item"><a class="nav-link active" href="javascript:void(0);">
              <i class="bx bx-sm bx-user me-1_5"></i> Account</a>
          </li>
          @if (Auth::user()->is_local)
            <li class="nav-item"><a class="nav-link" href="{{ route('settings.security.view') }}">
                <i class="bx bx-sm bx-lock-alt me-1_5"></i> Security</a>
            </li>
          @endif
          <li class="nav-item"><a class="nav-link" href="{{ route('settings.filter.view') }}">
              <i class="bx bx-sm bx-filter-alt me-1_5"></i> Filter</a>
          </li>
        </ul>
      </div>
      <div class="card mb-6">
        <div class="card-body">
          <div class="row g-2">
            <div class="col-md-6">
              <label for="name" class="form-label">Name</label>
              <input class="form-control-plaintext" type="text" id="name" name="name"
                value="{{ $user->name ?? '' }}" readonly />
            </div>
            <div class="col-md-6"></div>
            <div class="col-md-6">
              <label for="username" class="form-label">Username</label>
              <input class="form-control-plaintext" type="text" id="username" name="username"
                value="{{ $user->username ?? '' }}" readonly />
            </div>
            <div class="col-md-6"></div>
            <div class="col-md-6">
              <label for="email" class="form-label">Email</label>
              <input class="form-control-plaintext" type="text" id="email" name="email"
                value="{{ $user->email ?? '' }}" readonly />
            </div>
            <div class="col-md-6"></div>
            <div class="col-md-6">
              <label class="form-label" for="role">Role</label>
              <input class="form-control-plaintext" type="text" id="role" name="role"
                value="{{ $user->roles[0]->display_name ?? '' }}" />
            </div>
            <div class="col-md-6"></div>
            <div class="col-md-6 form-check">
              <input class="form-check-input" type="checkbox" value="" id="is_local" disabled
                {{ $user->is_local ? 'checked' : '' }}>
              <label class="form-check-label" for="is_local">Is local account</label>
            </div>
            <div class="col-md-6"></div>
          </div>
        </div>
        <!-- /Account -->
      </div>
      @if ($user->is_local)
        <div class="card" id="delete-account-block">
          <h5 class="card-header">Delete Account</h5>
          <div class="card-body">
            <div class="mb-6 col-12 mb-0">
              <div class="alert alert-warning">
                <h5 class="alert-heading mb-1">Are you sure you want to delete your account?</h5>
                <p class="mb-0">Once you delete your account, there is no going back. Please be certain.</p>
              </div>
            </div>
            <form id="formAccountDeactivation" onsubmit="return false">
              <div class="form-check my-8 ms-2">
                <input class="form-check-input" type="checkbox" name="accountActivation" id="accountActivation" />
                <label class="form-check-label" for="accountActivation">I confirm my account deactivation</label>
              </div>
              <button type="submit" class="btn btn-danger deactivate-account" disabled>Deactivate Account</button>
            </form>
          </div>
        </div>
      @endif
    </div>
  </div>
@endsection

@push('js')
  <script type="module">
    $(document).ready(function() {
      const deactivateAcc = $('#formAccountDeactivation'),
        deactivateButton = $('.deactivate-account');

      deactivateAcc.on('submit', function(e) {
        e.preventDefault();
      });

      if (deactivateAcc && $('#is_local').prop('checked')) {
        const fv = FormValidation.formValidation(deactivateAcc[0], {
          fields: {
            accountActivation: {
              validators: {
                notEmpty: {
                  message: 'Please confirm you want to delete account'
                }
              }
            }
          },
          plugins: {
            trigger: new FormValidation.plugins.Trigger(),
            bootstrap5: new FormValidation.plugins.Bootstrap5({
              eleValidClass: ''
            }),
            submitButton: new FormValidation.plugins.SubmitButton(),
            fieldStatus: new FormValidation.plugins.FieldStatus({
              onStatusChanged: function(areFieldsValid) {
                areFieldsValid ? deactivateButton.prop('disabled', false) : deactivateButton.prop(
                  'disabled', true);
              }
            }),
            // defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
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
      }

      deactivateButton.on('click', function() {
        if ($('#accountActivation').prop('checked') == true) {
          Swal.fire({
            text: 'Are you sure you would like to deactivate your account?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            customClass: {
              confirmButton: 'btn btn-primary me-2',
              cancelButton: 'btn btn-label-secondary'
            },
            buttonsStyling: false
          }).then(function(result) {
            if (result.value) {
              const id = $('#id').val();
              submitDeleteUser(id);
            }
          });
        }
      });

      function submitDeleteUser(id) {
        const $block = $('#delete-account-block');
        showHideBlockUI(true, $block);
        $.ajax({
          url: "{{ route('user.destroy', $user->id) }}",
          type: "DELETE",
          dataType: "json",
          success: function(response) {
            if (response.success) {
              showSuccessAlert(response.message);
              setTimeout(() => {
                window.location.href = "{{ route('login.view') }}";
              }, 200);
            } else {
              showErrorAlert(response.message);
            }
            showHideBlockUI(false, $block);
          },
          error: function(xhr) {
            console.error('Failed to delete User: ', xhr.responseJSON);
            showErrorAlert(xhr.responseJSON.message);
            showHideBlockUI(false, $block);
          }
        });
      }
    });
  </script>
@endpush
