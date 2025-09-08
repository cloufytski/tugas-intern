<div class="card mb-3" id="section-block">
  <div class="card-header header-elements border-bottom">
    <h5 class="mb-0 me-2">Section</h5>
    <div class="card-header-elements ms-auto">
      @permission('master-plant-create')
        <button type="button" class="btn btn-sm btn-primary" id="section-add" data-bs-toggle="offcanvas"
          data-bs-target="#offcanvas-section">Add Section</button>
      @endpermission
    </div>
  </div>
  <div class="card-datatable table-responsive text-nowrap">
    <table class="table table-sm" id="section-data-table">
      <thead class="table-active">
        <tr>
          <th>Id</th>
          <th>Plant</th>
          <th>Section</th>
          <th>Description</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
    </table>
  </div>
</div>

@push('js')
  <script type="module">
    const canUpdatePermission = @json(auth()->user()->hasPermission('master-plant-update'));

    $(document).ready(function() {
      let $table = $('#section-data-table');
      const $block = $('#section-block');
      initDataTable();

      function initDataTable() {
        $table.DataTable({
          processing: true,
          serverSide: true,
          ajax: {
            url: "{{ route('master.section.index') }}",
            type: "GET",
            data: {
              is_datatable: true
            },
          },
          paging: true,
          rowId: 'id',
          columnDefs: [{
            targets: -2, // status softDelete
            title: 'Status',
            className: 'text-center',
            orderable: false,
            searchable: false,
            render: function(data, type, row, meta) {
              if (data !== null) { // isDeleted
                return `<span class="text-danger"><i class="bx bx-sm bx-x-circle"></i></span>`;
              } else {
                return `<span class="text-success"><i class="bx bx-sm bx-check-circle"></i></span>`;
              }
            },
          }, {
            targets: -1,
            title: 'Actions',
            orderable: false,
            searchable: false,
            render: function(data, type, row, meta) {
              const isDeleted = !!data.deleted_at;

              const getDropdownItems = () => {
                if (isDeleted) {
                  return `<a data-id="${row.id}" class="dropdown-item text-info section-restore">Restore</a>`
                } else {
                  return `<a data-id="${row.id}" data-bs-toggle="offcanvas" data-bs-target="#offcanvas-section" class="dropdown-item text-dark section-edit">Edit</a>
                        <a data-id="${row.id}" data-section="${row.section}" class="dropdown-item text-danger section-delete">Delete</a>`;
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
          }],
          columns: [{
            data: 'id'
          }, {
            data: 'plant.description',
            orderable: false,
          }, {
            data: 'section'
          }, {
            data: 'description'
          }, {
            data: 'deleted_at',
            width: '10%',
          }, {
            data: null,
            width: '10%',
            visible: canUpdatePermission,
          }],
          language: {
            sLengthMenu: 'Show _MENU_',
            search: '',
            searchPlaceholder: 'Search',
            paginate: {
              next: '<i class="bx bx-chevron-right bx-18px"></i>',
              previous: '<i class="bx bx-chevron-left bx-18px"></i>'
            },
          }
        });
      }

      $(document).on('click', '#section-add, .section-edit', function() {
        const $form = $('#form-section');
        const id = $(this).data('id') || '';

        resetFormValidation($form);
        $('#id-input').val(id);

        const isEdit = !!id;
        $('#offcanvas-section-title').text(isEdit ? 'Update Section' : 'Create Section');
        $('#section-submit').text(isEdit ? 'Update' : 'Save');

        if (isEdit) {
          $('#id-input').val(id);
        }
      });

      $(document).on('click', '.section-delete', function() {
        const id = $(this).data('id');
        const section = $(this).data('section');
        showConfirmDeleteAlert('Section ' + section, () => {
          submitDelete(id);
        });
      });

      $(document).on('click', '.section-restore', function() {
        const id = $(this).data('id');
        submitRestore(id);
      });

      function submitDelete(id) {
        showHideBlockUI(true, $block);
        $.ajax({
          url: "{{ route('master.section.destroy', ':id') }}".replace(':id', id),
          type: "DELETE",
          headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
          },
          dataType: "json",
          success: function(response) {
            if (response.success) {
              showDeleteAlert(response.message);
              $table.DataTable().ajax.reload(null, false);
            } else {
              showErrorAlert(response.message);
            }
            showHideBlockUI(false, $block);
          },
          error: function(xhr, status, error) {
            console.error('Section Delete Error: ', xhr.responseJSON);
            showErrorAlert(xhr.responseJSON.message);
            showHideBlockUI(false, $block);
          }
        });
      }

      function submitRestore(id) {
        showHideBlockUI(true, $block);
        $.ajax({
          url: "{{ route('master.section.update', ':id') }}".replace(':id', id),
          type: "PUT",
          headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
          },
          data: {
            is_restore: true
          },
          dataType: "json",
          success: function(response) {
            if (response.success) {
              showRestoreAlert(response.message);
              $table.DataTable().ajax.reload(null, false);
            } else {
              showErrorAlert(response.message);
            }
            showHideBlockUI(false, $block);
          },
          error: function(xhr, status, error) {
            console.error('Section Restore Error: ', xhr.responseJSON);
            showErrorAlert(xhr.responseJSON.message);
            showHideBlockUI(false, $block);
          }
        });
      }
    });
  </script>
@endpush
