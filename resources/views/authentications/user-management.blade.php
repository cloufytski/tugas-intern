@php
  use App\Helpers\Helpers;
  $configData = Helpers::appClasses();
  $container = 'container-fluid';
  $containerNav = 'container-fluid';
@endphp

@extends('layouts/layout-master')

@section('title', 'User Management')

{{-- prettier-ignore-start --}}
@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
    'resources/assets/vendor/libs/animate-css/animate.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
    'resources/assets/vendor/libs/select2/select2.scss',
  ])
@endsection

@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/block-ui/block-ui.js',
  ])
@endsection
{{-- prettier-ignore-end --}}

@section('content')
  {{-- @permission('users-create') --}}
  <div class="row gap-2">
    <div class="col-12">
      <a class="btn btn-primary me-2" href="{{ route('user.view.register') }}">
        <span class="icon-base bx bx-user-plus me-1"></span>Register
      </a>
      @role('super-admin')
        <a class="btn btn-secondary me-2" href="{{ url('/laratrust') }}">Assign Role</a>
      @endrole
    </div>
    {{-- @endpermission --}}
    <div class="col-12">
      <div class="table-responsive">
        <table class="table table-sm" id="user-data-table">
          <thead>
            <tr>
              <th>Id</th>
              <th>Name</th>
              <th>Email</th>
              <th>Roles</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>

          </tbody>
        </table>
      </div>
    </div>
  </div>

  @include('authentications.user-role-modal')
@endsection

@push('js')
  <script type="module">
    const canUpdatePermission = @json(auth()->user()->hasPermission('users-update'));

    $(document).ready(function() {
      let $table = $('#user-data-table');
      initializeUserTable();

      $(document).on('click', '.user-delete', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        showConfirmDeleteAlert('User ' + name, () => {
          submitDelete(id);
        });
      });

      $(document).on('click', '.user-reset-password', function() {
        var id = $(this).data('id');
        var url = "{{ route('user.view.reset-password', ':id') }}".replace(':id', id);
        window.location.href = url;
      });

      $(document).on('click', '.user-restore', function() {
        var id = $(this).data('id');
        submitRestore(id);
      });

      function initializeUserTable() {
        $table.DataTable({
          processing: true,
          serverSide: true,
          ajax: "{{ route('user.index') }}",
          columnDefs: [{
              targets: -2, // Status isDelete
              title: 'Status',
              className: 'text-center',
              orderable: false,
              searchable: false,
              render: function(data, type, row, meta) {
                if (isNotNullOrEmpty(data)) { // isDeleted
                  return `<span class="badge text-bg-secondary">Inactive</span>`;
                } else {
                  return `<span class="badge text-bg-success">Active</span>`;
                }
              }
            },
            {
              targets: -1,
              title: 'Actions',
              orderable: false,
              searchable: false,
              render: function(data, type, row, meta) {
                const isDeleted = !!data.deleted_at;

                const getDropdownItems = () => {
                  const modalUserRoleAttr = [
                    `data-bs-toggle="modal"`,
                    `data-bs-target="#modal-user-role"`,
                    `data-keyboard="false"`,
                    `data-backdrop="static"`,
                  ].join(' ');
                  if (isDeleted) {
                    return `<a data-id="${row.id}" class="dropdown-item text-info user-restore">Restore</a>`;
                  } else if (data.is_local) {
                    return `
                        <a data-id="${row.id}" ${modalUserRoleAttr} class="dropdown-item text-dark user-assign-role">Assign Role</a>
                        <a data-id="${row.id}" class="dropdown-item text-dark user-reset-password">Reset Password</a>
                        <div class="dropdown-divider"></div>
                        <a data-id="${row.id}" data-name="${row.name}" class="dropdown-item text-danger user-delete">Delete</a>`;
                  } else {
                    return `<a data-id="${row.id}" ${modalUserRoleAttr} class="dropdown-item text-dark user-assign-role">Assign Role</a>`;
                  }
                }
                return `<div class="dropdown">
                      <button class="btn p-0" data-bs-toggle="dropdown">
                        <i class="bx bx-dots-vertical-rounded text-muted"></i>
                      </button>
                      <div class="dropdown-menu dropdown-menu-end">
                        ${getDropdownItems()}
                      </div>
                    </div>`;
              },
            }
          ],
          columns: [{
              data: 'id',
              name: 'id'
            },
            {
              data: 'name',
              name: 'name'
            },
            {
              data: 'email',
              name: 'email'
            },
            {
              data: 'roles',
              name: 'roles'
            },
            {
              data: 'deleted_at',
              width: '10%',
            },
            {
              data: null,
              name: 'action',
              width: '10%',
              visible: canUpdatePermission,
            },
          ],
          language: {
            sLengthMenu: 'Show _MENU_',
            search: '',
            searchPlaceholder: 'Search',
            paginate: {
              next: '<i class="bx bx-chevron-right bx-18px"></i>',
              previous: '<i class="bx bx-chevron-left bx-18px"></i>'
            }
          },
        });
      }

      $(document).on('click', '.user-assign-role', function() {
        var id = $(this).data('id');
        fetchUserById(id, (data) => {
          $('#id-input').val(data.id);
          $('#name-input').val(data.name);
          $('#username-input').val(data.username);
          $('#email-input').val(data.email);
          $('#role-input').val(data.roles.map(item => item.name)); // for first time fetch Role
          $('#role-select').val(data.roles.map(item => item.name)).trigger('change');
        });
      });

      function submitDelete(id) {
        $.ajax({
          url: "{{ route('user.destroy', ':id') }}".replace(':id', id),
          type: "DELETE",
          success: function(response) {
            if (response.success) {
              showDeleteAlert(response.message);
              $table.DataTable().ajax.reload(null, false);
            } else {
              showErrorAlert(response.message);
            }
          },
          error: function(xhr, status, error) {
            console.error('User Delete Error: ', xhr.responseJSON);
            showErrorAlert(xhr.responseJSON.message);
          }
        });
      }

      function submitRestore(id) {
        $.ajax({
          url: "{{ route('user.update', ':id') }}".replace(':id', id),
          type: "PUT",
          data: {
            is_restore: true,
          },
          success: function(response) {
            if (response.success) {
              showRestoreAlert(response.message);
              $table.DataTable().ajax.reload(null, false);
            } else {
              showErrorAlert(response.message);
            }
          },
          error: function(xhr, status, error) {
            console.error('User Restore Error: ', xhr.responseJSON);
            showErrorAlert(xhr.responseJSON.message);
          }
        });
      }

      function fetchUserById(id, callback) {
        $.get("{{ route('user.show', ':id') }}".replace(':id', id), function(response) {
          if (response.success) {
            const data = response.data;
            if (typeof callback === 'function') {
              callback(data);
            }
          }
        });
      }
    });
  </script>
@endpush
