<div class="card-datatable table-responsive" id="group-block">
  <table class="table table-sm table-hover mb-0" id="group-data-table">
    <thead>
      <tr>
        <th>Id</th>
        <th>Product Category</th>
        <th class="table-active">Product Group</th>
        <th>Min</th>
        <th>Max</th>
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
      let $table = $('#group-data-table');
      const $block = $('#group-block');

      $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
        const target = $(e.target).data('bs-target');
        if (target === '#navs-left-align-group') {
          if (!$.fn.DataTable.isDataTable($table)) {
            initDataTable();
          } else {
            $table.DataTable().ajax.reload(null, false);
          }
        }
      });

      $(document).on('click', '.group-delete', function() {
        const id = $(this).data('id');
        const group = $(this).data('group') || '';
        showConfirmDeleteAlert('Group ' + group, () => {
          submitDelete(id);
        });
      });

      $(document).on('click', '.group-restore', function() {
        const id = $(this).data('id');
        submitRestore(id);
      });

      $(document).on('click', '.group-add, .group-edit', function() {
        const $form = $('#form-group');
        const $offCanvas = $('#offcanvas-group');
        const id = $(this).data('id') || '';

        resetFormValidation($form);
        $form.find('#id-input').val(id);

        const isEdit = !!id;
        $offCanvas.find('.offcanvas-title').text(isEdit ? 'Update Group' : 'Add Group');
        $offCanvas.find('.btn-submit').text(isEdit ? 'Update' : 'Save');

        if (isEdit) {
          const categoryId = $(this).data('id-category') || '';
          const category = $(this).data('category') || '';
          const group = $(this).data('group') || '';
          const minThreshold = $(this).data('min-threshold') || '';
          const maxThreshold = $(this).data('max-threshold') || '';
          $form.find('#id-input').val(id);
          $form.find('#id_category-input').append($('<option>', {
            value: categoryId,
            text: category,
            selected: true,
          })).trigger('change');
          $form.find('#group-input').val(group);
          $form.find('#min_threshold-input').val(minThreshold);
          $form.find('#max_threshold-input').val(maxThreshold);
        }
      });

      function initDataTable() {
        $table.DataTable({
          processing: true,
          serverSide: true,
          ajax: {
            url: "{{ route('master.material.group.index') }}",
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
              targets: [3, 4],
              className: 'text-end',
              searchable: false,
              render: function(data, type, row, meta) {
                return `${convertToThousandOrElse(data, '')}`;
              }
            }, {
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
                    return `<a data-id="${row.id}" class="dropdown-item text-info group-restore">Restore</a>`
                  } else {
                    const offCanvasAttr = 'data-bs-toggle="offcanvas" data-bs-target="#offcanvas-group"';
                    const dataAttr = [
                      `data-id="${row.id}"`,
                      `data-group="${row.product_group}"`,
                      `data-id-category="${row.category.id}"`,
                      `data-category="${row.category.product_category}"`,
                      `data-min-threshold=${row.min_threshold}`,
                      `data-max-threshold=${row.max_threshold}`,
                    ].join(' ');
                    return `<a ${dataAttr} ${offCanvasAttr} class="dropdown-item text-dark group-edit">Edit</a>
                            <a data-id="${row.id}" data-group="${row.product_group}" class="dropdown-item text-danger group-delete">Delete</a>`;
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
          }, {
            data: 'category.product_category',
            name: 'category.product_category'
          }, {
            data: 'product_group',
            name: 'product_group',
            className: 'bg-body',
          }, {
            data: 'min_threshold',
            name: 'min_threshold',
          }, {
            data: 'max_threshold',
            name: 'max_threshold',
          }, {
            data: 'deleted_at',
            name: 'deleted_at',
            width: '5%',
          }, {
            data: null,
            name: 'action',
            width: '5%',
            visible: canUpdatePermission,
          }],
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
                text: '<i class="bx bx-sm bx-plus me-2"></i>Add Group',
                className: 'btn btn-sm btn-primary group-add',
                attr: {
                  'data-id': '',
                  'data-bs-toggle': 'offcanvas',
                  'data-bs-target': '#offcanvas-group'
                },
              },
            @endpermission
          ],
        });
      }

      function submitDelete(id) {
        showHideBlockUI(true, $block);
        $.ajax({
          url: "{{ route('master.material.group.destroy', ':id') }}".replace(':id', id),
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
            console.error('Material Group Delete Error: ', xhr.responseJSON);
            showErrorAlert(xhr.responseJSON.message);
            showHideBlockUI(false, $block);
          }
        });
      }

      function submitRestore(id) {
        showHideBlockUI(true, $block);
        $.ajax({
          url: "{{ route('master.material.group.update', ':id') }}".replace(':id', id),
          type: "PUT",
          headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}",
          },
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
            console.error('Material Group Restore Error: ', xhr.responseJSON);
            showErrorAlert(xhr.responseJSON.message);
            showHideBlockUI(false, $block);
          }
        });
      }
    });
  </script>
@endpush
