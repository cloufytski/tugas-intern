<div class="card mb-3" id="plant-block">
  <div class="card-header header-elements border-bottom">
    <h5 class="mb-0 me-2">Plant</h5>
    <div class="card-header-elements ms-auto">
      @permission('master-plant-create')
        <button type="button" class="btn btn-sm btn-primary" id="plant-add" data-bs-toggle="offcanvas"
          data-bs-target="#offcanvas-plant">Add Plant</button>
      @endpermission
    </div>
  </div>
  <div class="card-datatable table-responsive text-nowrap">
    <table class="table table-sm border-top" id="plant-data-table">
      <thead class="table-primary">
        <tr>
          <th>Id</th>
          <th>Plant</th>
          <th>Description</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>

@push('js')
  <script type="module">
    const canUpdatePermission = @json(auth()->user()->hasPermission('master-plant-update'));

    $(document).ready(function() {
      let $table = $('#plant-data-table');
      const $block = $('#plant-block');
      initDataTable();

      function initDataTable() {
        $table.DataTable({
          processing: true,
          serverSide: true,
          ajax: {
            url: "{{ route('master.plant.index') }}",
            type: "GET",
            data: {
              is_datatable: true
            },
          },
          paging: true,
          rowId: 'id',
          columnDefs: [{
              targets: -2, // Status isDelete
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
                  if (isDeleted) {
                    return `<a data-id="${row.id}" class="dropdown-item text-info plant-restore">Restore</a>`
                  } else {
                    return `<a data-id="${row.id}" data-bs-toggle="offcanvas" data-bs-target="#offcanvas-plant" class="dropdown-item text-dark plant-edit">Edit</a>
                        <a data-id="${row.id}" data-plant="${row.description}" class="dropdown-item text-danger plant-delete">Delete</a>`;
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
            name: 'id',
            visible: false,
          }, {
            data: 'plant',
            name: 'plant',
          }, {
            data: 'description',
            name: 'description',
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

      $(document).on('click', '#plant-add, .plant-edit', function() {
        const $form = $('#form-plant');
        const id = $(this).data('id') || '';

        resetFormValidation($form);
        $('#id-input').val(id);

        const isEdit = !!id;
        $('#offcanvas-plant-title').text(isEdit ? 'Update Plant' : 'Create Plant');
        $('#plant-submit').text(isEdit ? 'Update' : 'Save');

        if (isEdit) {
          $('#id-input').val(id);
          fetchPlant(id, $form);
        }
      });

      $(document).on('click', '.plant-delete', function() {
        const id = $(this).data('id');
        const plant = $(this).data('plant');
        showConfirmDeleteAlert('Plant ' + plant, () => {
          submitDelete(id);
        });
      });

      $(document).on('click', '.plant-restore', function() {
        const id = $(this).data('id');
        submitRestore(id);
      });

      function submitDelete(id) {
        showHideBlockUI(true, $block);
        $.ajax({
          url: "{{ route('master.plant.destroy', ':id') }}".replace(':id', id),
          type: "DELETE",
          dataType: "json",
          success: function(response) {
            if (response.success) {
              showDeleteAlert(response.message);
              $table.DataTable().ajax.reload(null, false);
              // refresh related Section after delete HasMany
              $('#section-data-table').DataTable().ajax.reload(null, false);
            } else {
              showErrorAlert(response.message);
            }
            showHideBlockUI(false, $block);
          },
          error: function(xhr, status, error) {
            console.error('Plant Delete Error: ', xhr.responseJSON);
            showErrorAlert(xhr.responseJSON.message);
            showHideBlockUI(false, $block);
          }
        });
      }

      function submitRestore(id) {
        showHideBlockUI(true, $block);
        $.ajax({
          url: "{{ route('master.plant.update', ':id') }}".replace(':id', id),
          type: "PUT",
          data: {
            is_restore: true
          },
          dataType: "json",
          success: function(response) {
            if (response.success) {
              showRestoreAlert(response.message);
              $table.DataTable().ajax.reload(null, false);
              // refresh related Section after delete HasMany
              $('#section-data-table').DataTable().ajax.reload(null, false);
            } else {
              showErrorAlert(response.message);
            }
            showHideBlockUI(false, $block);
          },
          error: function(xhr, status, error) {
            console.error('Plant Restore Error: ', xhr.responseJSON);
            showErrorAlert(xhr.responseJSON.message);
            showHideBlockUI(false, $block);
          }
        });
      }

      function fetchPlant(id, $form) {
        showHideBlockUI(true, $form);
        $.ajax({
          url: "{{ route('master.plant.show', ':id') }}".replace(':id', id),
          type: "GET",
          contentType: "application/json",
          success: function(response) {
            if (response.success) {
              let data = response.data;
              $('#plant-input').val(data.plant);
              $('#plant-description-input').val(data.description);

              showHideBlockUI(false, $form);
            }
          },
          error: function(xhr, status, error) {
            console.error('Failed to load Plant: ', xhr.responseJSON);
            showHideBlockUI(false, $form);
          }
        });
      }
    });
  </script>
@endpush
