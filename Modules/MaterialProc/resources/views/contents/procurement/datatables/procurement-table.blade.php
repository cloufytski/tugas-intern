<div class="card-datatable table-responsive flex-grow-1" id="procurement-block" style="min-height: 0;">
  <table class="table table-sm" id="procurement-data-table">
    <thead class="table-default">
    </thead>
  </table>
</div>

@push('js')
  <script type="module">
    const canUpdatePermission = @json(auth()->user()->hasPermission('procurement-update'));

    $(document).ready(function() {
      const $table = $('#procurement-data-table');
      const $block = $('#procurement-block');

      window.Procurement = window.Procurement || {};
      Procurement.loadProcurementTable = function(params) {

        if ($.fn.DataTable.isDataTable($table)) {
          $table.DataTable().ajax.reload(null, false);
        } else {
          initDataTable();
        }
      }

      initializeFirstFilter();

      function initializeFirstFilter() {
        const startDate = dayjs().startOf('month').format('YYYY-MM-DD');
        const endDate = dayjs().endOf('month').format('YYYY-MM-DD');
        window.filterData = {
          startDate: startDate,
          endDate: endDate,
        };
        Procurement.loadProcurementTable(window.filterData);
      }

      function initDataTable() {
        $table.DataTable({
          processing: true,
          serverSide: true,
          ajax: {
            url: "{{ route('procurement.procurement.index') }}",
            type: "GET",
            data: function(d) {
              d.is_datatable = true;
              d.start_date = window.filterData.startDate;
              d.end_date = window.filterData.endDate;
            },
          },
          paging: true,
          pageLength: 25,
          columnDefs: [{
            targets: [4, 5], // append history QTY
            className: 'text-end',
            createdCell: function(td, cellData, rowData, rowIndex, colIndex) {
              const columnMap = {
                4: 'qty_actual',
                5: 'qty_plan',
              };
              const realColIndex = $table.DataTable().column(colIndex).index();
              const fieldName = columnMap[realColIndex];
              const fieldHistories = rowData.history?.filter(h => h.field_name === fieldName) || [];
              const historyHtml = fieldHistories.map(h => {
                const tooltipAttr = [
                  'data-bs-toggle="tooltip"',
                  'data-bs-placement="bottom"',
                  'data-bs-html="true"',
                  'title',
                  `data-bs-original-title="${convertToDouble(h.old_value)} → ${convertToDouble(h.new_value)}<br />${h.history_remarks ?? ''}"`,
                ].join(' ');

                return `<small ${tooltipAttr} class="text-muted">${convertToDouble(h.old_value)}</small>`
              }).join('<br />');

              let popoverAttr;
              if (canUpdatePermission) {
                popoverAttr = [
                  'data-bs-toggle="popover"',
                  'data-bs-placement="bottom"',
                  'data-bs-html="true"',
                  `data-bs-content='
                <input type="number" step="any" data-field="${fieldName}" class="form-control mb-2" id="${fieldName}-popover-input" value="${cellData}" />
                <textarea class="form-control autosize" rows="2" name="history_remarks" placeholder="Remarks"></textarea>
                <div class="d-flex justify-content-between mt-2">
                    <button type="button" class="btn btn-sm btn-label-secondary popover-cancel">Cancel</button>
                    <button type="button" data-id="${rowData.id}" data-field="${fieldName}" class="btn btn-sm btn-primary popover-update">Save</button>
                </div>'`,
                  'title',
                  `data-bs-original-title="Update ${fieldName}"`,
                  'style="cursor:pointer;"',
                ].join(' ');
              }

              $(td).html(`
                <span ${popoverAttr}>${convertToDoubleOrElse(cellData, '—')}</span>
                <div class="mt-1">${historyHtml}</div>
              `);
            }
          }, {
            targets: [8, 9], // append history ETA
            className: 'text-center',
            createdCell: function(td, cellData, rowData, rowIndex, colIndex) {
              const columnMap = {
                8: 'eta_actual',
                9: 'eta_plan'
              };
              const realColIndex = $table.DataTable().column(colIndex).index();
              const fieldName = columnMap[realColIndex];
              const fieldHistories = rowData.history?.filter(h => h.field_name === fieldName) || [];
              const historyHtml = fieldHistories.map(h => {
                const tooltipAttr = [
                  'data-bs-toggle="tooltip"',
                  'data-bs-placement="bottom"',
                  'data-bs-html="true"',
                  'title',
                  `data-bs-original-title="${h.old_value ? dayjs(h.old_value).format('DD-MMM-YY') : ''} → ${h.new_value ? dayjs(h.new_value).format('DD-MMM-YY') : ''}<br />${h.history_remarks ?? ''}"`,
                ].join(' ');

                return `<small ${tooltipAttr} class="text-muted">${h.old_value ? dayjs(h.old_value).format('DD-MMM-YY') : ''}</small>`
              }).join('<br />');

              let popoverAttr;
              if (canUpdatePermission) {
                popoverAttr = [
                  'data-bs-toggle="popover"',
                  'data-bs-placement="bottom"',
                  'data-bs-html="true"',
                  `data-field="${fieldName}"`,
                  `data-bs-content='
                <input type="text" data-field="${fieldName}" class="form-control mb-2" id="${fieldName}-popover-input" value="${cellData ?? ''}" />
                <textarea class="form-control autosize" rows="2" name="history_remarks" placeholder="Remarks"></textarea>
                <div class="d-flex justify-content-between mt-2">
                    <button type="button" class="btn btn-sm btn-label-secondary popover-cancel">Cancel</button>
                    <button type="button" data-id="${rowData.id}" class="btn btn-sm btn-primary popover-update">Save</button>
                </div>'`,
                  'title',
                  `data-bs-original-title="Update ${fieldName}"`,
                  'style="cursor:pointer;"',
                ].join(' ');
              }

              $(td).html(`
                <span ${popoverAttr}>${cellData ? dayjs(cellData).format('DD-MMM-YY') : '—'}</span>
                <div class="mt-1">${historyHtml}</div>
              `);
            }
          }, {
            targets: -1,
            title: 'Actions',
            className: 'text-center',
            orderable: false,
            searchable: false,
            render: function(data, type, row, meta) {
              const getDropdownItems = () => {
                return `<a data-id="${row.id}" data-bs-toggle="offcanvas" data-bs-target="#offcanvas-procurement" class="dropdown-item text-dark procurement-edit">Edit</a>
                        <a data-id="${row.id}" data-contract="${row.contract_no}" class="dropdown-item text-danger procurement-delete">Delete</a>`;
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
            data: 'id',
            name: 'id',
            title: 'Id',
            visible: false,
          }, {
            data: 'contract_no',
            name: 'contract_no',
            title: 'Contract No',
          }, {
            data: 'po_date',
            name: 'po_date',
            title: 'PO Date',
            visible: false,
            className: 'bg-body text-center',
            render: function(data) {
              return `<span>${dayjs(data).format('DD-MMM-YY')}</span>`;
            },
          }, {
            data: 'material_description',
            name: 'material_description',
            title: 'Material',
          }, {
            data: 'qty',
            name: 'qty',
            title: 'Qty',
            className: 'bg-body text-end',
            render: function(data) {
              return `<span>${convertToDouble(data)}</span>`;
            },
          }, {
            data: 'qty_actual',
            name: 'qty_actual',
            title: 'Qty Actual',
            visible: false,
          }, {
            data: 'qty_plan',
            name: 'qty_plan',
            title: 'Qty Plan',
            visible: false,
          }, {
            data: 'supplier',
            name: 'supplier',
            title: 'Supplier',
          }, {
            data: 'eta',
            name: 'eta',
            title: 'ETA',
            className: 'bg-body text-center',
            render: function(data) {
              return `<span>${dayjs(data).format('DD-MMM-YY')}</span>`;
            },
          }, {
            data: 'eta_actual',
            name: 'eta_actual',
            title: 'ETA Actual',
            visible: false,
          }, {
            data: 'eta_plan',
            data: 'eta_plan',
            title: 'ETA Plan',
            visible: false,
          }, {
            data: 'plant_description',
            name: 'p.description',
            title: 'Plant',
            visible: false,
          }, {
            data: 'vessel_name',
            name: 'vessel_name',
            title: 'Vessel Name',
          }, {
            data: 'loading_port',
            name: 'loading_port',
            title: 'Loading Port',
            visible: false,
          }, {
            data: 'ffa',
            name: 'ffa',
            title: 'FFA',
          }, {
            data: null,
            name: 'action',
            title: 'Actions',
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
          buttons: [{
            extend: 'colvis',
            text: '<i class="bx bx-xs bx-filter me-2"></i>Filter',
            className: 'btn btn-sm btn-label-secondary',
            columns: function(idx, data, node) {
              const colCount = $table.DataTable().columns().count();
              return idx > 0 && idx < colCount - 1; // Exclude first and last columns
            },
          }],
          stateSave: true, // to save  table state in localStorage, esp. colvis
          drawCallback: function() {
            // initialize popovers after each redraw
            $('[data-bs-toggle="popover"]').each(function() {
              new bootstrap.Popover(this, {
                html: true,
                sanitize: false
              });
            });
            // initialize tooltip after each redraw
            $('[data-bs-toggle="tooltip"]').tooltip();

            // init flatpickr after popover shown
            $('[data-bs-toggle="popover"]').on('shown.bs.popover', function() {
              const fieldName = $(this).data('field');
              if (['eta_actual', 'eta_plan'].includes(fieldName)) {
                flatpickr(`#${fieldName}-popover-input`, {
                  dateFormat: 'Y-m-d',
                });
              }
            });
          },
        });

        // because of colvis in eta plan, eta actual, qty plan, qty actual need to re-init popover and tooltip
        $table.on('column-visibility.dt', function(e, settings, column, state) {
          setTimeout(() => {
            hidePopover();
            $('[data-bs-toggle="popover"]').each(function() {
              new bootstrap.Popover(this, {
                html: true,
                sanitize: false
              });
            });
            $('[data-bs-toggle="tooltip"]').tooltip();
            // init flatpickr after popover shown
            $('[data-bs-toggle="popover"]').on('shown.bs.popover', function() {
              const fieldName = $(this).data('field');
              if (['eta_actual', 'eta_plan'].includes(fieldName)) {
                flatpickr(`#${fieldName}-popover-input`, {
                  dateFormat: 'Y-m-d',
                });
              }
            });
          }, 0);
        });
      }

      $(document).on('click', '.popover-cancel', function() {
        hidePopover();
      });

      $(document).on('click', '.popover-update', function() {
        const $popover = $(this).closest('.popover');
        const $input = $popover.find('input');
        const fieldName = $input.data('field') || '';
        const id = $(this).data('id') || '';

        const $form = $('#form-procurement');
        if (isNotNullOrEmpty(id) && isNotNullOrEmpty(fieldName)) {
          const data = {
            id: id,
            [fieldName]: $input.val(),
            history_remarks: $popover.find('textarea').val(),
          };
          submitQtyOrEta(id, data);
        } else {
          showErrorAlert('Procurement failed to update quantity.');
        }
      });

      function hidePopover() {
        $('[data-bs-toggle="popover"]').popover('hide'); // hide all popover
      }

      $(document).on('click', '.procurement-add, .procurement-edit', function() {
        const $form = $('#form-procurement');
        const $offCanvas = $('#offcanvas-procurement');
        const id = $(this).data('id') || '';

        $form.find('#id-input').val(id);
        $form.data('view', false);

        const isEdit = !!id;
        $offCanvas.find('.offcanvas-title').text(isEdit ? 'Update Procurement' : 'Add Procurement');
        $offCanvas.find('.btn-submit').text(isEdit ? 'Update' : 'Save');
      });

      $(document).on('click', '.procurement-delete', function() {
        const id = $(this).data('id') || '';
        const contract = $(this).data('contract') || '';
        showConfirmDeleteAlert(`Procurement Contract: ${contract}`, () => {
          submitDelete(id);
        });
      });

      function submitDelete(id) {
        showHideBlockUI(true, $block);
        $.ajax({
          url: "{{ route('procurement.procurement.destroy', ':id') }}".replace(':id', id),
          type: "DELETE",
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
            console.error('Procurement Delete Error: ', error);
            showErrorAlert(xhr.responseJSON.message);
            showHideBlockUI(false, $block);
          }
        });
      }

      function submitQtyOrEta(id, data) {
        showHideBlockUI(true, $block);
        $.ajax({
          url: "{{ route('procurement.procurement.update.simple', ':id') }}".replace(':id', id),
          type: "POST",
          data: JSON.stringify(data),
          contentType: "application/json",
          dataType: "json",
          success: function(response) {
            if (response.success) {
              showSuccessAlert(response.message);
              $table.DataTable().ajax.reload(null, false);
            } else {
              showErrorAlert(response.message);
            }
            showHideBlockUI(false, $block);
            hidePopover();
          },
          error: function(xhr) {
            console.error('Procurement Submit Simple Error: ', xhr.responseJSON);
            showErrorAlert(xhr.responseJSON.message);
            showHideBlockUI(false, $block);
            hidePopover();
          }
        });
      }
    });
  </script>
@endpush
