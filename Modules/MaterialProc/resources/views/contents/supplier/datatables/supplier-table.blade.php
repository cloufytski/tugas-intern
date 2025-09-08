<table class="table table-sm table-bordered" id="supplier-data-table">
  <thead class="table-primary">
    <tr>
      <td>Id</td>
      <td>Supplier</td>
      <td>Certificate No</td>
      <td>Status</td>
      <td>Actions</td>
    </tr>
  </thead>
  <tbody></tbody>
</table>

@push('js')
  <script type="module">
    const canUpdatePermission = @json(auth()->user()->hasPermission('master-supplier-update'));

    $(document).ready(function() {
      var table = $('#supplier-data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: "{{ route('procurement.supplier.index') }}",
          type: "GET",
          data: {
            is_datatable: true,
          },
        },
        paging: true,
        pageLength: 25,
        columnDefs: [{ // Status for SoftDeletes
            targets: -2,
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
                  return `<a data-id="${row.id}" class="dropdown-item text-info supplier-restore">Restore</a>`
                } else {
                  return `<a data-id="${row.id}" data-supplier="${row.supplier}"data-certificate_no="${row.certificate_no} "class="dropdown-item text-dark supplier-edit">Edit</a>
                     <a data-id="${row.id}"data-supplier="${row.supplier}"class="dropdown-item text-danger supplier-delete">Delete</a>`;
                }
              };

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
        }, {
          data: 'supplier',
          name: 'supplier',
        }, {
          data: 'certificate_no',
          name: 'certificate_no',
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
          @permission('master-supplier-create')
            {
              text: '<i class="bx bx-plus bx-sm me-0 me-sm-2"></i><span>Add Supplier</span>',
              className: 'btn btn-sm btn-primary supplier-add',
              attr: {
                'data-id': '',
                'data-bs-toggle': 'offcanvas',
                'data-bs-target': '#offcanvas-supplier'
              },
            }
          @endpermission
        ],
      });

      $(document).on('click', '.supplier-add, .supplier-edit', function() {
        const $form = $('#form-supplier');
        const id = $(this).data('id') || '';

        // Reset the form first
        $form[0].reset();
        resetFormValidation($form);
        $form.find('#id-input').val(id);

        // Change modal text based on ID
        const isEdit = !!id;
        $('#offcanvas-supplier-title').text(isEdit ? 'Update Supplier' : 'Create Supplier');
        $('#supplier-submit').text(isEdit ? 'Update' : 'Save');

        if (isEdit) {
          const id = $(this).data('id');
          const supplier = $(this).data('supplier');
          const certificate = $(this).data('certificate_no') || '';

          $('#id-input').val(id);
          $('#supplier-input').val(supplier);
          $('#certificate_no-input').val(certificate);

          const offcanvas = new bootstrap.Offcanvas('#offcanvas-supplier');
          offcanvas.show();
        }

      });

      $(document).on('click', '.supplier-delete', function() {
        const id = $(this).data('id');
        const supplier = $(this).data('supplier');
        showConfirmDeleteAlert('Supplier ' + supplier, () => {
          submitDelete(id);
        });
      });

      $(document).on('click', '.supplier-restore', function() {
        const id = $(this).data('id');
        submitRestore(id);
      });

      function submitDelete(id) {
        $.ajax({
          url: "{{ route('procurement.supplier.destroy', ':id') }}".replace(':id', id),
          type: "DELETE",
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Laravel CSRF protection
          },
          dataType: "json",
          success: function(response) {
            if (response.success) {
              showDeleteAlert(response.message);
              table.ajax.reload(null, false);
            } else {
              showErrorAlert(response.message);
            }
          },
          error: function(xhr, status, error) {
            console.error('Supplier Delete Error: ', error);
            showErrorAlert(xhr.responseJSON.message);
          }
        });
      }

      function submitRestore(id) {
        $.ajax({
          url: "{{ route('procurement.supplier.update', ':id') }}".replace(':id', id),
          type: "PUT",
          data: {
            is_restore: true
          },
          headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
          },
          dataType: "json",
          success: function(response) {
            if (response.success) {
              showRestoreAlert(response.message);
              table.ajax.reload(null, false);
            } else {
              showErrorAlert(response.message);
            }
          },
          error: function(xhr, status, error) {
            console.error('Supplier Restore Error: ', error);
            showErrorAlert(xhr.responseJSON.message);
          }
        });
      }
    });
  </script>
@endpush
