<div class="row d-flex justify-content-between mb-4">
  <div class="col-md-4">
    @include('invbalance::contents.raw-material.partials.inventory-filter-sticky')
  </div>
  <div class="col-md-4">
    @include('invbalance::contents.inventory-balance.partials.refresh-log-text')
  </div>
</div>
<input type="text" class="form-control bg-white" id="formula-bar" placeholder="Enter formula or value ..." />
<div class="mt-4" id="raw-excel"></div>

@push('js')
  <script type="module">
    $(document).ready(function() {
      const $block = $('#tab-content-block');
      const $excel = $('#raw-excel');
      window.spreadsheetInstance = initSpreadsheet();
      let spreadsheetColumns, lastSelections;
      let plantCount; // keep track of dynamic plant total columns
      let selectedPlants = []; // Store selected plants for filtering

      window.activeTabTarget = '#navs-inventory-raw';
      window.InventoryRaw = window.InventoryRaw || {};

      InventoryRaw.loadInventoryRawTable = function(params) {
        $('#formula-bar').val('');
        // Store selected plants from params
        selectedPlants = params.plants || [];
        callInventoryRaw(params);
      }

      InventoryRaw.setSpreadsheetColumns = function(plants) {
        constructSpreadsheetColumns(plants);
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

      function constructSpreadsheetColumns(plants) {
        // Filter plants based on selected plants
        let filteredPlants = plants;
        if (selectedPlants && selectedPlants.length > 0) {
          // Convert selectedPlants to numbers if they're strings
          const selectedPlantIds = selectedPlants.map(id => parseInt(id));
          filteredPlants = plants.filter(plant => selectedPlantIds.includes(plant.id));
        }

        plantCount = filteredPlants.length;

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
        }, {
          title: `${numberToColumn(3)} (Vessel)`,
          name: 'vessel_port',
          tooltip: true,
          readOnly: true,
          width: 150,
          render: function(td, value, x, y, worksheet, options) {
            td.classList.add('bg-label-secondary');
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

        spreadsheetColumns.push({
          title: `${numberToColumn(spreadsheetColumns.length)} (End)`,
          type: 'numeric',
          mask: '#,##0',
          tooltip: true,
          render: function(td, value, x, y, worksheet, options) {
            let computed = parseFloat(td.innerText);
            td.classList.remove('bg-danger', 'bg-warning', 'bg-success', 'bg-label-dark', 'text-danger');
            // Add appropriate color based on value
            const colorClass = setProgressBarColor(computed);
            td.classList.add(colorClass);
            // Add white text for better contrast on colored backgrounds
            td.classList.add('text-white');
          },
        });

        spreadsheetColumns.push({
          title: `${numberToColumn(spreadsheetColumns.length)} (No of Days)`,
          name: 'no_of_days',
          type: 'numeric',
          mask: '#,##0.00',
          tooltip: true,
          width: 90,
          render: function(td, value, x, y, worksheet, options) {
            td.classList.add('bg-label-dark');
            td.classList.toggle('text-danger', value < 0);
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

      function callInventoryRaw(params) {
        showHideBlockUI(true, $block);
        $.ajax({
          url: "{{ route('inventory.inventory-raw') }}",
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
            console.error('Inventory Raw Error: ', xhr.responseJSON);
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
        const currentWorksheet = spreadsheetInstance.parent.worksheets[index];
        for (let row = 0; row < currentWorksheet.rows.length; row++) {
          // column positions for formula calculation
          let colBeginning = 1;
          let colReceipt = 2;
          let colProduction = 4 + plantCount;
          let colEnd = currentWorksheet.options.columns.length - 2;

          // Formula: Beginning + Receipt - Production = End
          if (row === 0 || currentWorksheet.getValue(
              `${numberToColumn(colBeginning)}${row+1}`)) { // first row or has beginning raw
            currentWorksheet.setValueFromCoords(colEnd, row,
              `=${numberToColumn(colBeginning)}${row+1}+${numberToColumn(colReceipt)}${row+1}-${numberToColumn(colProduction)}${row+1}`
            );
          } else {
            // For subsequent rows without beginning balance, use previous end balance
            currentWorksheet.setValueFromCoords(colEnd, row,
              `=${numberToColumn(colEnd)}${row}+${numberToColumn(colReceipt)}${row+1}-${numberToColumn(colProduction)}${row+1}`
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

      function setProgressBarColor(value) {
        if (value < 0) {
          return 'bg-danger'; // 🔴 Merah: < 0
        } else if (value >= 0 && value < 4000) {
          return 'bg-warning'; // 🟡 Kuning: 0 - 3999
        } else {
          return 'bg-success'; // 🟢 Hijau: ≥ 4000
        }
      }
    });
  </script>
@endpush
