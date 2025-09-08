<div class="card-datatable table-responsive" id="material-block">
  <table class="table table-sm table-hover mb-0" id="material-data-table">
    <thead>
      <tr>
        <th>Id</th>
        <th>Class</th>
        <th>Category</th>
        <th>Group</th>
        <th>Group Simple</th>
        <th>Packaging</th>
        <th class="table-active">Material Description</th>
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
      let $table = $('#material-data-table');
      const $block = $('#material-block');
      initDataTable(); // first opened tab

      $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
        const target = $(e.target).data('bs-target');
        if (target === '#navs-left-align-material') {
          if (!$.fn.DataTable.isDataTable($table)) {
            initDataTable();
          } else {
            $table.DataTable().ajax.reload(null, false);
          }
        }
      });

      $(document).on('click', '.material-delete', function() {
        const id = $(this).data('id');
        const description = $(this).data('material-description') || '';
        showConfirmDeleteAlert('Material ' + description, () => {
          submitDelete(id);
        });
      });

      $(document).on('click', '.material-restore', function() {
        const id = $(this).data('id');
        submitRestore(id);
      });

      $(document).on('click', '.material-add, .material-edit', function() {
        const $form = $('#form-material');
        const $offCanvas = $('#offcanvas-material');
        const id = $(this).data('id') || '';

        // resetFormValidation($form);
        $form.find('#id-input').val(id);
        $form.data('view', false);

        const isEdit = !!id;
        $offCanvas.find('.offcanvas-title').text(isEdit ? 'Update Material' : 'Add Material');
        $offCanvas.find('.btn-submit').text(isEdit ? 'Update' : 'Save');
      });

      $(document).on('click', '.material-view', function() {
        const $form = $('#form-material');
        const $offCanvas = $('#offcanvas-material');
        const id = $(this).data('id') || '';
        $form.find('#id-input').val(id);
        $form.data('view', true);
        $offCanvas.find('.offcanvas-title').text('View Material');
      });

      function initDataTable() {
        $table.DataTable({
          processing: true,
          serverSide: true,
          ajax: {
            url: "{{ route('master.material.index') }}",
            type: "GET",
            data: {
              is_datatable: true
            },
          },
          searchDelay: 1200,
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
                    return `<a data-id="${row.id}" class="dropdown-item text-info material-restore">Restore</a>`
                  } else {
                    const offCanvasAttr =
                      'data-bs-toggle="offcanvas" data-bs-target="#offcanvas-material"';
                    return `<a data-id="${row.id}" ${offCanvasAttr} class="dropdown-item text-dark material-view">View</a>
                            <a data-id="${row.id}" ${offCanvasAttr} class="dropdown-item text-dark material-edit">Edit</a>
                            <a data-id="${row.id}" data-material-description="${row.material_description}" class="dropdown-item text-danger material-delete">Delete</a>`;
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
            }, {
              targets: -3, // Material Description
              title: 'Material Description',
              className: 'bg-body',
              render: function(data, type, row, meta) {
                return `<pre class="m-0 fs-5">${data}</pre>`; // to retain double space, example: SODIUM METHYLATE 30 % SOLUTION
              }
            }
          ],
          columns: [{
              data: 'id',
              name: 'id',
            },
            {
              data: 'class',
              name: 'class',
              visible: false
            },
            {
              data: 'product_category',
              name: 'product_category'
            },
            {
              data: 'product_group',
              name: 'product_group'
            },
            {
              data: 'product_group_simple',
              name: 'product_group_simple',
              visible: false,
            },
            {
              data: 'packaging',
              name: 'packaging',
              visible: false
            },
            {
              data: 'material_description',
              name: 'material_description',
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
          buttons: [{
              extend: 'colvis',
              text: '<i class="bx bx-xs bx-filter me-2"></i>Filter',
              className: 'btn btn-sm btn-label-primary me-4',
              columns: 'th:not(:first-child):not(:nth-last-child(-n+3))'
            },
            @permission('master-material-create')
              {
                text: '<i class="bx bx-plus me-2"></i>Add Material',
                className: 'btn btn-sm btn-primary material-add',
                attr: {
                  'data-id': '',
                  'data-bs-toggle': 'offcanvas',
                  'data-bs-target': '#offcanvas-material'
                },
              },
            @endpermission
          ]
        });
      }

      function submitDelete(id) {
        showHideBlockUI(true, $block);
        $.ajax({
          url: "{{ route('master.material.destroy', ':id') }}".replace(':id', id),
          type: "DELETE",
          headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}",
          },
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
            console.error('Material Delete Error: ', xhr.responseJSON);
            showErrorAlert(xhr.responseJSON.message);
            showHideBlockUI(false, $block);
          }
        });
      }

      function submitRestore(id) {
        showHideBlockUI(true, $block);
        $.ajax({
          url: "{{ route('master.material.update', ':id') }}".replace(':id', id),
          type: "PUT",
          headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}",
          },
          data: JSON.stringify({
            is_restore: true,
          }),
          contentType: "application/json",
          dataType: 'json',
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
            console.error('Material Restore Error: ', xhr.responseJSON);
            showErrorAlert(xhr.responseJSON.message);
            showHideBlockUI(false, $block);
          }
        });
      }
    });
  </script>
@endpush
