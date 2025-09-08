<div id="inventory-checkpoint-block">
  <div class="row mb-3">
    <div class="col-2">
      <label class="col-form-label" for="year-select">Year</label>
    </div>
    <div class="col-2">
      <select class="form-select" id="year-select" name="year"></select>
    </div>
    <div class="col-2 text-end">
      @permission('inventory-checkpoint-create')
        <button type="button" class="btn btn-label-primary w-75" id="inventory-checkpoint-save">Save</button>
      @endpermission
    </div>
  </div>
  <div class="row mb-3">
    <div class="col-2">
      <label class="col-form-label" for="category-select">Product Category</label>
    </div>
    <div class="col-4">
      <select class="form-select" id="category-select"></select>
    </div>
  </div>
  <div class="row mb-3">
    <div class="col-6">
      <div class="alert alert-warning alert-dismissible d-none mb-4" role="alert" id="checkpoint-alert">
        <span class="alert-message"></span>
      </div>
    </div>
  </div>
  <div class="row mb-3">
    <div class="col-12">
      <div id="spreadsheet-inventory-checkpoint"></div>
    </div>
  </div>
</div>

@push('js')
  <script type="module">
    const canUpdatePermission = @json(auth()->user()->hasPermission('inventory-checkpoint-create'));

    $(document).ready(function() {
      const $block = $('#inventory-checkpoint-block');
      const $yearSelect = $('#year-select');
      const $categorySelect = $('#category-select');
      let spreadsheetInstance = initSpreadsheet();
      let spreadsheetChangedRows = new Set();

      initSelect2();

      $(document).on('click', '#inventory-checkpoint-save', function() {
        const jsonData = [];
        Array.from(spreadsheetChangedRows).map(rowIndex => {
          const row = spreadsheetInstance.getRowData(rowIndex);
          if (!row || row[3] === null || row[3] === '') return; // check beginning_balance empty, then skip
          jsonData.push({
            product_group: row[2],
            beginning_balance: parseFloat(row[3].toString().replace(/,/g, '')), // remove commas from mask
            date: row[4],
          });
          if (row[1]) { // if id_group exists
            jsonData['id_group'] = row[1];
          }
        });
        submitInventoryCheckpoint(jsonData);
        spreadsheetChangedRows.clear();
      });

      function initSelect2() {
        initYearSelect2();
        initCategorySelect2();
      }

      function initYearSelect2() {
        $yearSelect.select2({
          data: getYearRange(5), // 2 years back, and 2 years after
          tags: true, // allow custom year entry
          placeholder: 'Select year',
        }).on('select2:close', function() {
          const year = $(this).val();
          const categoryIds = $categorySelect.val();
          fetchMaterialGroupAndEndBalance(categoryIds, year);
        });

        $yearSelect.val(dayjs().year()).trigger('change');
      }

      function getYearRange(divider) {
        const currentYear = dayjs().year();
        const years = [];
        const addition = Math.floor(divider / 2);
        for (let y = currentYear - addition; y <= currentYear + addition; y++) {
          years.push({
            id: y,
            text: y.toString()
          });
        }
        return years;
      }

      function initSpreadsheet() {
        const $spreadsheet = $('#spreadsheet-inventory-checkpoint');
        let jss = jspreadsheet($spreadsheet[0], {
          worksheets: [{
            minDimensions: [3, 3],
            data: [],
            dataType: 'json',
            columns: [{
              title: 'Product Category',
              name: 'product_category',
              type: 'text',
              width: 200,
              readOnly: true,
            }, {
              title: 'ID Product Group',
              name: 'id_group',
              type: 'numeric',
              width: 1,
              readOnly: true,
            }, {
              title: 'Product Group',
              name: 'product_group',
              type: 'text',
              width: 200,
              readOnly: !canUpdatePermission,
            }, {
              title: 'Beginning Balance',
              name: 'beginning_balance',
              type: 'numeric',
              width: 150,
              readOnly: !canUpdatePermission,
              mask: '#,##0.00',
              decimal: '.',
              delimiter: ',', // use comma for thousands
            }, {
              title: 'Date',
              name: 'date',
              type: 'date',
              mask: 'mmm-yy',
              width: 150, // hide Date for now
              readOnly: !canUpdatePermission,
              type: 'calendar',
              options: {
                type: 'year-month-picker',
                today: false,
                format: 'DD-MMM-YY',
              },
            }],
          }],
          onchange: function(instance, cell, x, y, value) {
            spreadsheetChangedRows.add(parseInt(y)); // track the changed row index
          },
        });
        return jss[0];
      }

      function initCategorySelect2() {
        return $.ajax({
          url: "{{ route('master.material.category.index') }}",
          method: "GET",
          success: function(response) {
            setCategorySelect2(response.data);
          },
          error: function(xhr) {
            console.error('Failed to load Category: ', xhr.responseJSON);
          }
        });

      }

      function setCategorySelect2(data) {
        if (!$categorySelect.hasClass('select2-hidden-accessible')) {
          $categorySelect.select2({
            placeholder: 'Material Category ...',
            minimumResultsForSearch: -1,
            multiple: true,
            closeOnSelect: false,
            allowClear: true,
            data: data.map(category => ({
              id: category.id,
              text: category.product_category,
            })),
          }).on('select2:close', function() {
            const categoryIds = $(this).val();
            const year = $yearSelect.val();
            fetchMaterialGroupAndEndBalance(categoryIds, year);
          });
        }
      }

      function fetchMaterialGroupAndEndBalance(categoryIds, year) {
        setAlert(null);
        showHideBlockUI(true, $block);
        if (isArrayNotNullOrEmpty(categoryIds) && isNotNullOrEmpty(year)) {
          $.when(
            $.ajax({
              url: "{{ route('master.material.group.index') }}",
              method: "GET",
              data: {
                categories: categoryIds
              },
            }),
            $.ajax({
              url: "{{ route('inventory.checkpoint.index') }}",
              method: "GET",
              data: {
                // start_date: dayjs(year).startOf('year').format('YYYY-MM-DD'),
                year: year,
                categories: categoryIds,
              }
            }),
          ).done(function(response1, response2) {
            const materialGroupData = response1[0].data;
            const checkpointData = response2[0].data;

            const flattenData = [];
            materialGroupData.forEach(item1 => {
              const category = item1.category?.product_category || null;

              const matching = checkpointData.filter(item2 => item2.id_group === item1.id);
              if (matching.length > 0) {
                matching.forEach(checkpoint => {
                  flattenData.push({
                    product_category: category,
                    id_group: item1.id,
                    product_group: item1.product_group,
                    beginning_balance: checkpoint?.beginning_balance,
                    date: checkpoint?.date,
                  });
                });
              } else {
                flattenData.push({
                  product_category: category,
                  id_group: item1.id,
                  product_group: item1.product_group,
                  beginning_balance: null,
                  date: null,
                });
              }
            });

            spreadsheetInstance.setData(flattenData);
            showHideBlockUI(false, $block);
          }).fail(function(xhr) {
            console.error('Failed to load Material Group and Checkpoint: ', xhr.responseJSON);
            setAlert(xhr.responseJSON.message);
            showHideBlockUI(false, $block);
          });
        } else {
          setAlert('Product Category ' + categoryIds + ' is empty.');
          showHideBlockUI(false, $block);
        }
      }

      function submitInventoryCheckpoint(rowData) {
        setAlert(null);
        showHideBlockUI(true, $block);
        $.ajax({
          url: "{{ route('inventory.checkpoint.store') }}",
          type: "POST",
          data: JSON.stringify({
            checkpoints: rowData,
          }),
          contentType: "application/json",
          dataType: "json",
          success: function(response) {
            if (response.success) {
              showSuccessAlert(response.message);
            } else {
              setAlert(response.message + ' ' + response.errors);
            }
            showHideBlockUI(false, $block);
          },
          error: function(xhr) {
            console.error('Inventory Checkpoint Submit Error: ', xhr.responseJSON);
            setAlert(xhr.responseJSON.message);
            showHideBlockUI(false, $block);
          }
        });
      }

      function setAlert(message = null) {
        if (message) {
          $('#checkpoint-alert').removeClass('d-none');
          $('#checkpoint-alert>.alert-message').text(message);
          spreadsheetInstance.setData([]);
        } else {
          $('#checkpoint-alert').addClass('d-none');
          $('#checkpoint-alert>.alert-message').text('');
        }
      }
    });
  </script>
@endpush
