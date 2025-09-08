<div class="card-datatable table-responsive" id="uom-block">
  <table class="table table-sm table-hover mb-0" id="uom-data-table">
    <thead>
      <tr>
        <th>Id</th>
        <th class="table-active">UoM</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
    </tbody>
  </table>
</div>

@push('js')
  <script type="module">
    const canUpdatePermission = @json(auth()->user()->hasPermission('master-material-update'));

    $(document).ready(function() {
      let $table = $('#uom-data-table');
      const $block = $('#uom-block');

      $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
        const target = $(e.target).data('bs-target');
        if (target === '#navs-left-align-uom') {
          if (!$.fn.DataTable.isDataTable($table)) {
            initDataTable();
          } else {
            $table.DataTable().ajax.reload(null, false);
          }
        }
      });

      $(document).on('click', '.uom-delete', function() {
        const id = $(this).data('id');
        const uom = $(this).data('uom') || '';
        showConfirmDeleteAlert('UOM ' + uom, () => {
          submitDelete(id);
        });
      });

      $(document).on('click', '.uom-restore', function() {
        const id = $(this).data('id');
        submitRestore(id);
      });

      $(document).on('click', '.uom-add, .uom-edit', function() {
        const $form = $('#form-uom');
        const $offCanvas = $('#offcanvas-uom');
        const id = $(this).data('id') || '';

        resetFormValidation($form);
        $form.find('#id-input').val(id);

        const isEdit = !!id;
        $offCanvas.find('.offcanvas-title').text(isEdit ? 'Update UOM' : 'Add UOM');
        $offCanvas.find('.btn-submit').text(isEdit ? 'Update' : 'Save');

        if (isEdit) {
          const uom = $(this).data('uom') || '';
          $form.find('#id-input').val(id);
          $form.find('#uom-input').val(uom);
        }
      });

      function initDataTable() {
        $table.DataTable({
          processing: true,
          serverSide: true,
          ajax: {
            url: "{{ route('master.material.uom.index') }}",
            type: "GET",
            data: {
              is_datatable: true
            },
          },
          paging: true,
          pageLength: 50,
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
              className: 'text-center',
              orderable: false,
              searchable: false,
              render: function(data, type, row, meta) {
                const isDeleted = !!data.deleted_at;

                const getDropdownItems = () => {
                  if (isDeleted) {
                    return `<a data-id="${row.id}" class="dropdown-item text-info uom-restore">Restore</a>`
                  } else {
                    const offCanvasAttr =
                      'data-bs-toggle="offcanvas" data-bs-target="#offcanvas-uom"';
                    return `<a data-id="${row.id}" data-uom="${row.uom}" ${offCanvasAttr} class="dropdown-item text-dark uom-edit">Edit</a>
                          <a data-id="${row.id}" data-uom="${row.uom}" class="dropdown-item text-danger uom-delete">Delete</a>`;
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
              data: 'uom',
              name: 'uom',
              className: 'bg-body',
            },
            {
              data: 'deleted_at',
              name: 'deleted_at',
              width: '10%',
            },
            {
              data: null,
              name: 'action',
              width: '10%',
              visible: canUpdatePermission,
            }
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
          dom: '<"row"' +
            '<"col-md-2"<"ms-n2"l>>' +
            '<"col-md-10"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-6 mb-md-0 mt-n6 mt-md-0 gap-md-4"fB>>' +
            '>t' +
            '<"row"' +
            '<"col-sm-12 col-md-6"i>' +
            '<"col-sm-12 col-md-6"p>' +
            '>',
          buttons: [
            @permission('master-material-create')
              {
                text: '<i class="bx bx-sm bx-plus me-2"></i>Add UOM',
                className: 'btn btn-sm btn-primary uom-add',
                attr: {
                  'data-id': '',
                  'data-bs-toggle': 'offcanvas',
                  'data-bs-target': '#offcanvas-uom'
                },
              },
            @endpermission
          ],
        });
      }

      function submitDelete(id) {
        showHideBlockUI(true, $block);
        $.ajax({
          url: "{{ route('master.material.uom.destroy', ':id') }}".replace(':id', id),
          type: "DELETE",
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
            console.error('Material UOM Delete Error: ', xhr.responseJSON);
            showErrorAlert(xhr.responseJSON.message);
            showHideBlockUI(false, $block);
          }
        });
      }

      function submitRestore(id) {
        showHideBlockUI(true, $block);
        $.ajax({
          url: "{{ route('master.material.uom.update', ':id') }}".replace(':id', id),
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
            showHideBlockUI(false, $block);
          },
          error: function(xhr, status, error) {
            console.error('Material UOM Restore Error: ', xhr.responseJSON);
            showErrorAlert(xhr.responseJSON.message);
            showHideBlockUI(false, $block);
          }
        });
      }
    });
  </script>
@endpush
