@php
  $container = 'container-fluid';
  $containerNav = 'container-fluid';

  use App\Helpers\Helpers;
  $configData = Helpers::appClasses();
@endphp

@extends('layouts/content-navbar-layout')

@section('title', 'Dashboard')

{{-- prettier-ignore-start --}}
@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/select2/select2.scss',
    'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
    'resources/assets/vendor/libs/apex-charts/apex-charts.scss',
    'resources/assets/vendor/libs/webdatarocks/webdatarocks' . ($configData['style'] !== 'light' ? '-' . $configData['style'] : '') . '.scss',
  ])
@endsection

@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/block-ui/block-ui.js',
    'resources/assets/vendor/libs/flatpickr/flatpickr.js',
    'resources/assets/vendor/libs/dayjs/dayjs.js',
    'resources/assets/vendor/libs/webdatarocks/webdatarocks.js',
    'resources/assets/vendor/libs/apex-charts/apex-charts.js',
  ])
@endsection

@section('page-script')
  @vite(['resources/assets/js/cards-actions.js'])
@endsection
{{-- prettier-ignore-end --}}

@section('content')
  <div class="row" id="tab-content-block">
    <div class="col-md-3 mb-4" id="date-range-group">
      <label class="form-label" for="date-range">Date Range</label>
      <input type="text" class="form-control flatpickr-range" id="date-range" placeholder="Start Date to End Date" />
    </div>
    <div class="col-md-3"></div>
    <div class="col-md-6 d-flex align-items-center justify-content-end">
      @include('invbalance::contents.inventory-balance.partials.refresh-log-text')
      @permission(['inventory-balance-update'])
        <button type="button" class="btn btn-sm btn-icon btn-primary ms-4" id="refresh-btn">
          <i class="bx bx-rotate-left scaleX-n1-rtl"></i>
        </button>
      @endpermission
    </div>
    <div class="col-xl-6 col-lg-12">
      @include('invbalance::contents.dashboard-balance.partials.rm-flow-card')
    </div>
    <div class="col-xl-6 col-lg-12">
      @include('invbalance::contents.dashboard-balance.partials.fg-flow-card')
    </div>
  </div>

  @include('authentications.user-preferences')
@endsection

@push('js')
  <script type="module">
    $(document).ready(function() {
      const $block = $('#tab-content-block');
      const datePickrInstance = setUpFlatpickrDateRange();

      const activeTabTarget = window.activeTabTarget ?? '#dashboard-balance';

      initialize();

      $.when(initSelect2()).done(function(result) {
        setDatePickrRange();
        setUserPreferences();
        sendFilterResultData(result);
        setField();
        showHideBlockUI(false, $block);
      });

      $(document).on('click', '#refresh-btn', function() {
        RefreshLog?.submitRefreshInventoryView($block);
      });

      function initialize() {
        RefreshLog?.fetchLogTimestamps();
      }

      function initSelect2() {
        showHideBlockUI(true, $block);
        initGroupSelect2();
        return Promise.all([
          initCategorySelect2(),
          initPlantSelect2(),
          initOrderStatusSelect2(),
        ]);
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

      function setDatePickrRange() {
        let startDate = dayjs().startOf('month').toDate();
        let endDate = dayjs().add(1, 'month').endOf('month').toDate();
        datePickrInstance.setDate([startDate, endDate], true);
      }

      window.getStartDateAndEndDate = function(selectedDates) {
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

      function setUserPreferences() {
        let dashboards = [];

        dashboards.push(...RmFlow.getFilter());
        dashboards.push(...FgFlow.getFilter());

        dashboards.forEach(({
          key,
          menu
        }) => {
          const categoryVal = getPrefValue(menu, 'MATERIAL CATEGORY');
          $(`#dashboard-${key}-category-select`).val(categoryVal).trigger('change');

          const $groupEl = $(`#dashboard-${key}-group-select`);
          if ($groupEl.length > 0) {
            fetchMaterialGroup(categoryVal, key, menu, () => {
              const groupVal = getPrefValue(menu, 'MATERIAL GROUP');
              $groupEl.val(groupVal).trigger('change');
            });

            // set category select2 on close
            $(`#dashboard-${key}-category-select`).on('change', function(e) {
              const categoryIds = $(this).val();
              fetchMaterialGroup(categoryIds, key, menu, () => {
                const groupVal = getPrefValue(menu, 'MATERIAL GROUP');
                $groupEl.val(groupVal).trigger('change');
              });
            });
          }
        });
      }

      function setField() {
        // NOTE: hardcode select2 RM Flow
        $('#dashboard-rm-flow-category-select').val(14).trigger('change').prop('disabled', true);
      }

      function initPlantSelect2() {
        if (activeTabTarget === '#dashboard-balance') {
          return $.ajax({
            url: "{{ route('master.plant.index') }}",
            method: "GET",
            success: function(response) {
              //   setPlantSelect2(response.data);
            },
            error: function(xhr) {
              console.error('Failed to load Plant: ', xhr.responseJSON);
            }
          });
        }
      }

      function initOrderStatusSelect2() {
        @permission('master-order-read')
          if (activeTabTarget === '#dashboard-balance') {
            @if (Nwidart\Modules\Facades\Module::has('SalesPlan'))
              return $.ajax({
                url: "{{ route('sales.order.status.index') }}",
                method: "GET",
                success: function(response) {
                  // setOrderStatusSelect2(statusData);
                },
                error: function(xhr) {
                  console.error('Failed to load Order Status: ', xhr.responseJSON);
                }
              });
            @endif
          }
        @endpermission
      }

      function initCategorySelect2() {
        let $categorySelect = $('.category-select');
        if ($categorySelect.length > 0) {
          return $.ajax({
            url: "{{ route('master.material.category.index') }}",
            method: "GET",
            success: function(response) {
              $categorySelect.each(function() {
                setCategorySelect2(response.data, $(this), false);
              });
            },
            error: function(xhr) {
              console.error('Failed to load Category: ', xhr.responseJSON);
            }
          });
        }
      }

      function setCategorySelect2(data, $el, multiple = false) {
        $el.select2({
          placeholder: 'Category ...',
          multiple: multiple,
          closeOnSelect: false,
          data: data.map(category => ({
            id: category.id,
            text: category.product_category,
          })),
        });
      }

      function fetchMaterialGroup(categoryIds, key, menu, callback) {
        const $spinner = $(`#dashboard-${key}-group-spinner`);

        let categories = (typeof categoryIds !== 'array') ? [categoryIds] : categoryIds;

        if (categoryIds) {
          $spinner.removeClass('d-none');
          return $.ajax({
            url: "{{ route('master.material.group.index') }}",
            method: "GET",
            data: {
              categories: categories,
            },
            dataType: "json",
            success: function(response) {
              let groupedData = constructMaterialGroupAndChildren(response.data);
              setGroupSelect2(groupedData, key, menu, true);

              if (typeof callback === 'function') {
                callback();
              }

              $spinner.addClass('d-none');
            },
            error: function(xhr) {
              console.error('Failed to load Material Group: ', xhr.responseJSON);
              $spinner.addClass('d-none');
            }
          });
        }
      }

      function initGroupSelect2() {
        $('.group-select').select2({
          placeholder: 'Group ...',
          allowClear: true,
          closeOnSelect: false,
        });
      }

      function setGroupSelect2(data, key, menu, multiple = false) {
        const $el = $(`#dashboard-${key}-group-select`);
        $el.empty();
        $el.select2({
          placeholder: 'Group ...',
          multiple: multiple,
          allowClear: true,
          closeOnSelect: false,
          data: data,
        });
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

      function sendFilterResultData(result) {
        switch (activeTabTarget) {
          case '#dashboard-balance':
            FgFlowPivot.setColumns?.(result[1]?.data, result[2]?.data); // production + sales
            RmFlowPivot.setColumns?.(result[1]?.data); // production + receipt
            RmFlowChart.setColumns?.(result[1]?.data);
        }
      }

    });
  </script>
@endpush
