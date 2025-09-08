<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvas-inventory-filter" data-bs-scroll="true">
  <div class="offcanvas-header border-bottom">
    <h5 class="offcanvas-title">Filter</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body" id="filter-block">
    <form class="inventory-filter-form">
      @csrf
      <div class="row">
        <div class="col-6 mb-4">
          <label class="form-label" for="year-select">Year</label>
          <select class="form-select" id="year-select" name="year"></select>
        </div>
        <div class="col-6 mb-4">
          <label class="form-label" for="date-group-select">Date Group</label>
          <select class="form-select" id="date-group-select" name="date_group">
            <option value="daily">Daily</option>
            <option value="weekly">Weekly</option>
            <option value="monthly" selected>Monthly</option>
            <option value="yearly">Yearly</option>
          </select>
        </div>
        <div class="col-12 mb-4" id="date-range-group" style="display: none;">
          <label class="form-label" for="date-range">Date Range</label>
          <input type="text" class="form-control flatpickr-range" id="date-range"
            placeholder="Start Date to End Date" />
        </div>
        <div class="col-12 mb-4" id="saved-filter-group" style="display: none;">
          <div class="w-50">
            <label class="form-label me-2">Saved Filter</label>
            <select class="form-select" id="saved-filter-tag">
              <option value="default">Default</option>
            </select>
          </div>
        </div>
        <div class="col-12 mb-4">
          <div class="accordion" id="accordion">
            <div class="accordion-item">
              <h2 class="accordion-header">
                <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse"
                  data-bs-target="#material-accordion" aria-expanded="false">Material</button>
              </h2>
              <div id="material-accordion" class="accordion-collapse collapse show">
                <div class="accordion-body">
                  <label class="form-label" for="category-select">Product Category</label>
                  <div class="select2-primary w-100 mb-2">
                    <select class="form-select select2" id="category-select" multiple></select>
                  </div>

                  <span>
                    <label class="form-label" for="group-select">Product Group</label>
                    <div class="spinner-border spinner-border-sm text-info mx-2 d-none" role="status"
                      id="group-spinner">
                      <span class="visually-hidden">Loading...</span>
                    </div>
                  </span>
                  <div class="select2-info w-100 mb-2">
                    <select class="form-select select2" id="group-select" multiple></select>
                  </div>

                  <div id="packaging-group">
                    <label class="form-label" for="packaging-select">Packaging</label>
                    <select class="form-select select2" id="packaging-select" multiple></select>
                  </div>
                </div>
              </div>
            </div>

            @permission('master-customer-read')
              <div class="accordion-item" id="customer-group">
                <h2 class="accordion-header">
                  <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse"
                    data-bs-target="#customer-accordion" aria-expanded="false">Customer</button>
                </h2>
                <div id="customer-accordion" class="accordion-collapse collapse">
                  <div class="accordion-body">
                    <label class="form-label" for="customer_code-select">Customer Code</label>
                    <select class="form-select select2" id="customer_code-select" multiple></select>
                  </div>
                </div>
              </div>
            @endpermission

            <div class="accordion-item" id="plant-group" style="display: none;">
              <h2 class="accordion-header">
                <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse"
                  data-bs-target="#plant-accordion" aria-expanded="false">Plant</button>
              </h2>
              <div id="plant-accordion" class="accordion-collapse collapse">
                <div class="accordion-body">
                  <label class="form-label" for="plant-select">Plant</label>
                  <select class="form-select select2" id="plant-select" multiple></select>
                </div>
              </div>
            </div>

            @permission('master-order-read')
              <div class="accordion-item" id="order-group">
                <h2 class="accordion-header">
                  <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse"
                    data-bs-target="#order-accordion" aria-expanded="false">Order</button>
                </h2>
                <div id="order-accordion" class="accordion-collapse collapse">
                  <div class="accordion-body">
                    <label class="form-label" for="order-status-select">Order Status</label>
                    <select class="form-select select2" id="order-status-select" multiple></select>
                  </div>
                </div>
              </div>
            @endpermission

          </div>
        </div>
        <div class="col-6 mb-3"></div>
        <div class="col-6 mb-3 text-end">
          <button type="button" class="btn btn-label-primary w-100" id="inventory-filter">
            <i class="bx bx-sm bx-play me-2"></i>Run
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

@include('authentications.user-preferences')

@push('js')
  <script type="module">
    $(document).ready(function() {
      const $offCanvas = $('#offcanvas-inventory-filter');
      //   const $block = $('#filter-block');
      const $block = $('#tab-content-block');
      const $plantSelect = $('#plant-select');
      const $categorySelect = $('#category-select');
      const $groupSelect = $('#group-select');
      const $packagingSelect = $('#packaging-select');
      const $customerCodeSelect = $('#customer_code-select');
      const $orderStatusSelect = $('#order-status-select');
      const $yearSelect = $('#year-select');
      const $dateGroupSelect = $('#date-group-select');
      const datePickrInstance = setUpFlatpickrDateRange();
      const $form = $('#inventory-filter-form');

      const activeTabTarget = window.activeTabTarget;

      $.when(initSelect2()).done(function(result) {
        setDatePickrRange(activeTabTarget);
        populateTagSelect(activeTabTarget);
        setUserPreferences(activeTabTarget);
        setFilterField(activeTabTarget);
        sendFilterResultData(result);
        showHideBlockUI(false, $block);
      });

      $offCanvas.on('shown.bs.offcanvas', function() {
        // flatpickr here, because choosing month is blinking
        datePickrInstance._positionCalendar();
      });

      $dateGroupSelect.on('change', function() {
        let value = $(this).val();
        if (value === 'daily' || value === 'weekly') {
          showHideDiv($('#date-range-group'), true);
        } else {
          showHideDiv($('#date-range-group'), false);
          setDatePickrRange(activeTabTarget);
        }
      });

      $(document).on('click', '#inventory-filter', function() {
        const params = {
          year: $yearSelect.val(),
          date_group: $dateGroupSelect.val(),
        }
        if ($('#date-range-group').is(':visible')) {
          const weekStart = getStartDateAndEndDate(datePickrInstance.selectedDates);
          params['start_date'] = weekStart.startDate;
          params['end_date'] = weekStart.endDate;
        } else {
          params['start_date'] = dayjs($yearSelect.val()).startOf('year').format('YYYY-MM-DD');
          params['end_date'] = dayjs($yearSelect.val()).endOf('year').format('YYYY-MM-DD');
        }

        if ($plantSelect.val().length > 0) {
          params['plants'] = $plantSelect.val();
        }
        if ($categorySelect.val().length > 0) {
          params['categories'] = $categorySelect.val();
        }
        if ($groupSelect.val().length > 0) {
          params['groups'] = $groupSelect.val();
        }
        if ($packagingSelect.val().length > 0) {
          params['packagings'] = $packagingSelect.val();
        }
        if ($customerCodeSelect.val().length > 0) {
          params['customer_codes'] = $customerCodeSelect.val();
        }
        if ($orderStatusSelect.val()?.length > 0) {
          // '?' is added because only available to sales-planner, not production-planner
          params['order_statuses'] = $orderStatusSelect.val();
        }
        handleActiveTabAction(params);

        // after click run, dismiss offcanvas
        let canvas = bootstrap.Offcanvas.getInstance($offCanvas);
        canvas.hide();
      });

      function setFilterField(target) {
        switch (target) {
          case '#navs-yearly-sales':
            $dateGroupSelect.prop('disabled', true);
            break;
          case '#navs-inquiry':
            $dateGroupSelect.val('daily');
            $dateGroupSelect.prop('disabled', true);
            showHideDiv($('#date-range-group'), true);

            // Hide Material > Product Category and Order
            showHideDiv($('#order-group'), false);
            break;
          case '#navs-order':
          case '#navs-database':
            $dateGroupSelect.val('daily');
            $dateGroupSelect.prop('disabled', true);
            showHideDiv($('#date-range-group'), true);
            break;
          case '#navs-inventory-balance':
            $dateGroupSelect.val('daily');
            showHideDiv($('#date-range-group'), true);
            showHideDiv($('#plant-group'), true);
            showHideDiv($('#saved-filter-group'), true);
            break;
        }
      }

      function setUpFlatpickrDateRange() {
        return flatpickr('#date-range', {
          mode: 'range',
          dateFormat: "Y-m-d",
          allowInput: true,
          locale: {
            firstDayOfWeek: 1,
          }
        });
      }

      function getStartDateAndEndDate(selectedDates) {
        let startDate, endDate;
        if (selectedDates.length === 2) {
          startDate = datePickrInstance.formatDate(selectedDates[0], 'Y-m-d');
          endDate = datePickrInstance.formatDate(selectedDates[1], 'Y-m-d');
          return {
            startDate: startDate,
            endDate: endDate
          };
        } else {
          console.warn('Date range not selected yet.');
          return;
        }
      }

      function setDatePickrRange(target) {
        const now = dayjs();
        let startDate, endDate;
        switch (target) {
          case '#navs-inquiry':
          case '#navs-order':
          case '#navs-database':
            startDate = now.startOf('month').toDate();
            endDate = now.endOf('month').toDate();
            break;
          case '#navs-inventory-balance':
            startDate = now.startOf('month').toDate();
            endDate = now.add(2, 'month').endOf('month').toDate();
            break;
          case '#navs-yearly-sales':
            startDate = now.startOf('year').toDate();
            endDate = now.endOf('year').toDate();
            break;
          default:
            break;
        }
        datePickrInstance.setDate([startDate, endDate], true);
      }

      function setUserPreferences(target) {
        const prefValue = getUserPreferencesValueByTab(target);
        $plantSelect.val(prefValue.plantVal).trigger('change');
        $categorySelect.val(prefValue.categoryVal).trigger('change');
        if (isArrayNotNullOrEmpty(prefValue.categoryVal)) {
          fetchMaterialGroup(prefValue.categoryVal, () => {
            $groupSelect.val(prefValue.groupVal).trigger('change');
          });
        }
        $packagingSelect.val(prefValue.packagingVal).trigger('change');
        $customerCodeSelect.val(prefValue.customerCodeVal).trigger('change');
        $orderStatusSelect.val(prefValue.orderStatusVal).trigger('change');
      }

      function populateTagSelect(target) {
        const $tagSelect = $('#saved-filter-tag');
        $tagSelect.select2({
          tags: true,
          dropdownParent: $offCanvas,
        });

        let menu;
        switch (target) {
          case '#navs-inventory-balance':
            menu = 'DASHBOARD - INVENTORY BALANCE';
            break;
          case '#navs-yearly-sales':
            menu = 'DASHBOARD - YEARLY SALES';
            break;
        }

        const tags = Object.keys(userPreferences[menu] || {});
        tags.forEach(tag => {
          if ($tagSelect.find(`option[value="${tag}"]`).length === 0) {
            $tagSelect.append(new Option(tag === "default" ? "Default" : tag, tag));
          }
        });
        $tagSelect.val("default").trigger("change");

        $tagSelect.on("change", function() {
          setUserPreferences(target);
        });
      }

      function sendFilterResultData(result) {
        // result based on ajax call in initSelect2 order
        switch (activeTabTarget) {
          case '#navs-inventory-balance':
            InventoryBalance.setSpreadsheetColumns?.(result[2]?.data, result[3]?.data);
            // result[4] order_status may not be available to other roles
            break;
        }
      }

      function handleActiveTabAction(params) {
        switch (activeTabTarget) {
          case '#navs-inventory-balance':
            InventoryBalance.loadInventoryBalanceTable(params);
            break;
          case '#navs-yearly-sales':
            YearlySales.loadYearlySalesTable(params);
            break;
          case '#navs-inquiry':
            InquiryExcel.loadInquiryExcelTable(params);
            break;
          case '#navs-order':
            OrderExcel.loadOrderExcelTable(params);
            break;
          case '#navs-database':
            OrderDatabase.loadOrderDatabaseTable(params);
            break;
          default:
            console.warn(`Navigation ${activeTabTarget} is not yet registered!`);
        }
      }

      function initSelect2() {
        showHideBlockUI(true, $block);
        setGroupSelect2([], $groupSelect);
        initYearSelect2();
        return Promise.all([
          initCategorySelect2(),
          initPackagingSelect2(),
          initPlantSelect2(),
          initOrderStatusSelect2(),
          initCustomerCodeSelect2(),
        ]);
      }

      function initYearSelect2() {
        $yearSelect.select2({
          data: getYearRange(5), // 2 years back, and 2 years after
          tags: true, // allow custom year entry
          dropdownParent: $offCanvas,
          placeholder: 'Select year',
        });
        $yearSelect.val(dayjs().year()).trigger('change');
      }

      function initPlantSelect2() {
        if (activeTabTarget === '#navs-inventory-balance') {
          return $.ajax({
            url: "{{ route('master.plant.index') }}",
            method: "GET",
            success: function(response) {
              setPlantSelect2(response.data);
            },
            error: function(xhr) {
              console.error('Failed to load Plant: ', xhr.responseJSON);
            }
          });
        }
      }

      function setPlantSelect2(data) {
        if (!$plantSelect.hasClass('select2-hidden-accessible')) {
          $plantSelect.select2({
            placeholder: 'Select Plant ...',
            closeOnSelect: false,
            allowClear: true,
            minimumResultsForSearch: -1,
            dropdownParent: $offCanvas,
            data: data.map(plant => ({
              id: plant.id,
              text: plant.description,
            })),
          });
        }
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
            multiple: true,
            closeOnSelect: false,
            allowClear: true,
            dropdownParent: $offCanvas,
            data: data.map(category => ({
              id: category.id,
              text: category.product_category,
            })),
          }).on('select2:close', function(e) {
            const prefValue = getUserPreferencesValueByTab(activeTabTarget);
            const categoryIds = $(this).val();
            fetchMaterialGroup(categoryIds, () => {
              $groupSelect.val(prefValue.groupVal).trigger('change');
            });
          });
        }
      }

      function initOrderStatusSelect2() {
        if ($orderStatusSelect.length > 0) {
          @if (Nwidart\Modules\Facades\Module::has('SalesPlan'))
            return $.ajax({
              url: "{{ route('sales.order.status.index') }}",
              method: "GET",
              success: function(response) {
                const statusData = response.data.map(item => ({
                  id: item.id,
                  text: item.order_status,
                  order_status: item.order_status,
                  badge_color: item.badge_color,
                }));
                setOrderStatusSelect2(statusData);
                window.YearlySales?.setOrderStatus(statusData);
                window.OrderProjectionPivot?.setOrderStatus(statusData);
              },
              error: function(xhr) {
                console.error('Failed to load Order Status: ', xhr.responseJSON);
              }
            });
          @endif
        }
      }

      function setOrderStatusSelect2(data) {
        if (!$orderStatusSelect.hasClass('select2-hidden-accessible')) {
          $orderStatusSelect.select2({
            placeholder: 'Order Status ...',
            multiple: true,
            closeOnSelect: false,
            allowClear: true,
            dropdownParent: $offCanvas,
            data: data,
            escapeMarkup: function(markup) {
              return markup; // allow html
            },
            templateResult: function(data) {
              if (!data.id) return data.text;

              const label = data.order_status || data.text;
              const color = data.badge_color || 'secondary';
              return `<span class="badge bg-label-${color}">${label}</span>`;
            },
            templateSelection: function(data) {
              if (!data.id) return data.text;

              const label = data.order_status || data.text;
              const color = data.badge_color || 'secondary';
              return `<span class="badge bg-label-${color}">${label}</span>`;
            }
          });
        }
      }

      function fetchMaterialGroup(categoryIds, callback) {
        const $spinner = $(`#group-spinner`);
        $spinner.removeClass('d-none');

        const categories = categoryIds ? categoryIds : getUserPreferencesValueByTab(activeTabTarget).categoryVal;
        if (categories) {
          return $.ajax({
            url: "{{ route('master.material.group.index') }}",
            method: "GET",
            data: {
              categories: categories,
            },
            dataType: "json",
            success: function(response) {
              let groupedData = constructMaterialGroupAndChildren(response.data);
              setGroupSelect2(groupedData, $groupSelect);

              if (typeof callback === 'function') {
                callback();
              }

              $spinner.addClass('d-none');
            },
            error: function(xhr) {
              console.error('Failed to load Group: ', xhr.responseJSON);
              $spinner.addClass('d-none');
            }
          });
        } else { // if no saved user preferences
          setGroupSelect2([], $groupSelect);
          $spinner.addClass('d-none');
        }
      }

      function setGroupSelect2(data, $el) {
        $el.empty();
        $el.select2({
          placeholder: 'Material Group ...',
          multiple: true,
          closeOnSelect: false,
          allowClear: true,
          dropdownParent: $offCanvas,
          data: data,
        });
      }

      function initPackagingSelect2() {
        return $.ajax({
          url: "{{ route('master.material.packaging.index') }}",
          method: "GET",
          success: function(response) {
            setPackagingSelect2(response.data);
          },
          error: function(xhr) {
            console.error('Failed to load Category: ', xhr.responseJSON);
            $packagingSelect.hide();
          }
        });
      }

      function setPackagingSelect2(data) {
        if (!$packagingSelect.hasClass('select2-hidden-accessible')) {
          $packagingSelect.select2({
            placeholder: 'Packaging ...',
            multiple: true,
            closeOnSelect: false,
            allowClear: true,
            dropdownParent: $offCanvas,
            data: data.map(packaging => ({
              id: packaging.id,
              text: packaging.packaging,
            })),
          });
        }
      }

      function initCustomerCodeSelect2() {
        let menu;
        switch (activeTabTarget) {
          case '#navs-inventory-balance':
            menu = "DASHBOARD - INVENTORY BALANCE";
            break;
          case '#navs-yearly-sales':
            menu = "DASHBOARD - YEARLY SALES";
            break;
        }

        let prefValues = getPrefValue(menu, "customer_code");
        @if (Nwidart\Modules\Facades\Module::has('SalesPlan'))
          return $.ajax({
            url: "{{ route('sales.customer.code.index') }}",
            method: "GET",
            data: {
              ...(prefValues !== null ? {
                customer_codes: prefValues
              } : {})
            },
            dataType: "json",
            success: function(response) {
              setCustomerCodeSelect2(response.data);
            },
            error: function(xhr) {
              console.error('Failed to load Customer Code: ', xhr.responseJSON);
              $customerCodeSelect.hide();
            }
          });
        @endif
      }

      function setCustomerCodeSelect2(data) {
        if (!$customerCodeSelect.hasClass('select2-hidden-accessible')) {
          $customerCodeSelect.select2({
            placeholder: 'Customer Code ...',
            multiple: true,
            allowClear: true,
            closeOnSelect: false,
            dropdownParent: $offCanvas,
            data: data.map(code => ({ // set default value first
              id: code.id,
              text: code.customer_code,
            })),
            @if (Nwidart\Modules\Facades\Module::has('SalesPlan'))
              ajax: {
                url: "{{ route('sales.customer.code.index') }}",
                dataType: 'json',
                delay: 250,
                cache: true,
                data: params => ({
                  customer_code: params.term
                }),
                processResults: response => ({
                  results: response.data.map(item => ({
                    id: item.id,
                    text: item.customer_code,
                  }))
                })
              },
            @endif
          });
        }
      }

      function showHideDiv($el, isShow = false) {
        if (isShow) {
          $el.show();
        } else {
          $el.hide();
        }
      }

      function constructMaterialGroupAndChildren(data) {
        const grouped = {};

        data.forEach(item => {
          const group = item.category.product_category;
          if (!grouped[group]) {
            grouped[group] = [];
          }
          grouped[group].push({
            id: item.id,
            text: item.product_group
          });
        });

        const select2Data = Object.keys(grouped).map(group => ({
          text: group,
          children: grouped[group],
        }));
        return select2Data;
      }

      function getUserPreferencesValueByTab(target) {
        let filterTag = $('#saved-filter-tag').val() || "default";
        switch (target) {
          case '#navs-inventory-balance':
            return {
              categoryVal: getPrefValue('DASHBOARD - INVENTORY BALANCE', 'material_category', filterTag),
                groupVal: getPrefValue('DASHBOARD - INVENTORY BALANCE', 'material_group', filterTag),
                packagingVal: getPrefValue('DASHBOARD - INVENTORY BALANCE', 'material_packaging', filterTag),
                customerCodeVal: getPrefValue('DASHBOARD - INVENTORY BALANCE', 'customer_code', filterTag),
                orderStatusVal: getPrefValue('DASHBOARD - INVENTORY BALANCE', 'order_status', filterTag),
            }
            break;

          case '#navs-yearly-sales':
            return {
              categoryVal: getPrefValue('DASHBOARD - YEARLY SALES', 'material_category', filterTag),
                groupVal: getPrefValue('DASHBOARD - YEARLY SALES', 'material_group', filterTag),
                packagingVal: getPrefValue('DASHBOARD - YEARLY SALES', 'material_packaging', filterTag),
                customerCodeVal: getPrefValue('DASHBOARD - YEARLY SALES', 'customer_code', filterTag),
                orderStatusVal: getPrefValue('DASHBOARD - YEARLY SALES', 'order_status', filterTag),
            }
            break;
        }
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
    });
  </script>
@endpush
