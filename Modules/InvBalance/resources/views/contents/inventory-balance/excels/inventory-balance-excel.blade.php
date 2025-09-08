<div class="row d-flex justify-content-between mb-4">
  <div class="col-md-4">
    @include('invbalance::contents.inventory-balance.partials.inventory-filter-sticky')
  </div>
  <div class="col-md-4">
    @include('invbalance::contents.inventory-balance.partials.refresh-log-text')
  </div>
</div>
<input type="text" class="form-control bg-white" id="formula-bar" placeholder="Enter formula or value ..." />
<div class="mt-4" id="balance-excel"></div>

@push('js')
  <script type="module">
    $(document).ready(function() {
      const $block = $('#tab-content-block');
      const $excel = $('#balance-excel');
      window.spreadsheetInstance = initSpreadsheet();
      let spreadsheetColumns, lastSelections;
      let plantCount, orderStatusCount; // keep track of dynamic plant total columns

      window.activeTabTarget = '#navs-inventory-balance';
      window.InventoryBalance = window.InventoryBalance || {};

      InventoryBalance.loadInventoryBalanceTable = function(params) {
        $('#formula-bar').val('');
        callInventoryBalance(params);
      }

      InventoryBalance.setSpreadsheetColumns = function(plants, orderStatuses) {
        constructSpreadsheetColumns(plants, orderStatuses);
      }

      initialize();

      $('#formula-bar').val('');
      $('#formula-bar').sticky({
        topSpacing: 50,
        zIndex: 10,
      });

      $(document).on('click', '#clear-table-btn', function() {
        spreadsheetInstance.deleteWorksheet(spreadsheetInstance.getWorksheetActive());
      });

      $(document).on('change', '#formula-bar', function() {
        const currentWorksheet = spreadsheetInstance.parent.worksheets[spreadsheetInstance.getWorksheetActive()];
        if (lastSelections) {
          currentWorksheet.setValueFromCoords(lastSelections[0], lastSelections[1], $(this).val());
        }
      });

      $(document).on('click', '.card-reload', function() {
        RefreshLog?.submitRefreshInventoryView($block);
      });

      function initialize() {
        RefreshLog?.fetchLogTimestamps();
      }

      function constructSpreadsheetColumns(plants, orderStatuses) {
        plantCount = plants.length;
        orderStatusCount = orderStatuses ? 5 : 0;
        // NOTE: hardcode, only take from Projection to Order only available to role sales-planner

        spreadsheetColumns = [{
          title: `${numberToColumn(0)} (Date)`,
          name: 'date',
          tooltip: true,
          readOnly: true,
        }, {
          title: `${numberToColumn(1)} (Beginning)`,
          name: 'beginning',
          type: 'numeric',
          mask: '#,##0',
          tooltip: true,
          // readOnly: true,
          render: function(td, value, x, y, worksheet, options) {
            td.classList.add('bg-label-primary');
            td.classList.toggle('text-danger', value < 0);
          },
        }, {
          title: `${numberToColumn(2)} (Receipt)`,
          name: 'receipt',
          type: 'numeric',
          mask: '#,##0',
          tooltip: true,
          render: function(td, value, x, y, worksheet, options) {
            td.classList.add('bg-label-success');
            td.classList.toggle('text-danger', value < 0);
          },
        }];
        plants.forEach(plant => {
          spreadsheetColumns.push({
            title: `${numberToColumn(spreadsheetColumns.length)} (${plant.description})`,
            name: plant.description,
            type: 'numeric',
            mask: '#,##0',
            tooltip: true,
            width: 70,
            // readOnly: true,
            render: function(td, value, x, y, worksheet, options) {
              td.classList.toggle('text-danger', value < 0);
            },
          });
        });
        spreadsheetColumns.push({
          title: `${numberToColumn(spreadsheetColumns.length)} (Production)`,
          name: 'production',
          type: 'numeric',
          mask: '#,##0',
          tooltip: true,
          width: 80,
          render: function(td, value, x, y, worksheet, options) {
            td.classList.add('bg-label-info');
            td.classList.toggle('text-danger', value < 0);
          },
        });

        // NOTE: only take from Projection to Order
        orderStatuses?.slice(0, 5).forEach(status => {
          spreadsheetColumns.push({
            title: `${numberToColumn(spreadsheetColumns.length)} (${status.order_status})`,
            name: status.order_status,
            type: 'numeric',
            mask: '#,##0',
            tooltip: true,
            width: 70,
            // readOnly: true,
            render: function(td, value, x, y, worksheet, options) {
              td.classList.toggle('text-danger', value < 0);
            },
          });
        });
        spreadsheetColumns.push({
          title: `${numberToColumn(spreadsheetColumns.length)} (Sales)`,
          name: 'sales',
          type: 'numeric',
          mask: '#,##0',
          tooltip: true,
          width: 80,
          render: function(td, value, x, y, worksheet, options) {
            td.classList.add('bg-label-warning');
            td.classList.toggle('text-danger', value < 0);
          },
        });
        spreadsheetColumns.push({
          title: `${numberToColumn(spreadsheetColumns.length)} (End)`,
          type: 'numeric',
          mask: '#,##0',
          tooltip: true,
          render: function(td, value, x, y, worksheet, options) {
            let computed = parseFloat(td.innerText);
            td.classList.toggle('text-danger', computed < 0);
          },
        });
      }

      function initSpreadsheet() {
        const jss = jspreadsheet($excel[0], {
          tableOverflow: true,
          toolbar: false,
          worksheets: [{
            minDimensions: [10, 10],
            data: [],
            dataType: 'json',
          }],
          onselection: function(instance, x1, y1, x2, y2) {
            lastSelections = [x1, y1, x2, y2];
            $('#formula-bar').val(instance.getValueFromCoords(x1, y1));
          },
        });
        return jss[0];
      }

      function callInventoryBalance(params) {
        showHideBlockUI(true, $block);
        $.ajax({
          url: "{{ route('inventory.balance') }}",
          type: "POST",
          data: JSON.stringify(params),
          contentType: "application/json",
          dataType: "json",
          success: function(response) {
            if (response.success) {
              deleteWorksheets();
              Object.entries(response.data).forEach(([key, value], index) => {
                setWorksheet(key, value, index);
              });
            }
            showHideBlockUI(false, $block);
          },
          error: function(xhr) {
            console.error('Inventory Balance Error: ', xhr.responseJSON);
            showErrorAlert(xhr.responseJSON.message);
            showHideBlockUI(false, $block);
          }
        });
      }

      function setWorksheet(worksheetName, data, index) {
        spreadsheetInstance.createWorksheet({
          worksheetName: worksheetName,
          columns: spreadsheetColumns,
          data: data,
        }, index);
        setTimeout(() => {
          setSpreadsheetAggregate(index);
          spreadsheetInstance.openWorksheet(0); // open first sheet
        }, 100);
      }

      function deleteWorksheets() {
        // Delete all worksheets (from last to first)
        for (let i = $excel[0].spreadsheet.worksheets.length - 1; i >= 0; i--) {
          spreadsheetInstance.deleteWorksheet(i);
        }
      }

      function setSpreadsheetAggregate(index) {
        // production total
        const currentWorksheet = spreadsheetInstance.parent.worksheets[index];
        for (let row = 0; row < currentWorksheet.rows.length; row++) {
          // change column colors for Beginning, Receipt, Production, Sales
          let colBeginning = 1;
          let colProcurement = 2;

          let colProduction = 3 + plantCount;
          let colSales = 4 + plantCount + orderStatusCount;

          // NOTE: hardcoded for formula
          let colEnd = currentWorksheet.options.columns.length - 1;
          if (row === 0 || currentWorksheet.getValue(`${numberToColumn(colBeginning)}${row+1}`)) { // first row
            currentWorksheet.setValueFromCoords(colEnd, row,
              `=${numberToColumn(colBeginning)}${row+1}+${numberToColumn(colProcurement)}${row+1}+${numberToColumn(colProduction)}${row+1}-${numberToColumn(colSales)}${row+1}`
            );
          } else {
            currentWorksheet.setValueFromCoords(colEnd, row,
              `=${numberToColumn(colSales + 1)}${row}+${numberToColumn(colProcurement)}${row+1}+${numberToColumn(colProduction)}${row+1}-${numberToColumn(colSales)}${row+1}`
            );
          }
        }
      }

      function numberToColumn(n) { // 0 -> A, 1 -> B, 2 -> C
        let letter = '';
        while (n >= 0) {
          letter = String.fromCharCode((n % 26) + 65) + letter;
          n = Math.floor(n / 26) - 1;
        }
        return letter;
      }
    });
  </script>
@endpush
