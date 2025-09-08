@php
  use App\Helpers\Helpers;
  $configData = Helpers::appClasses();
  $container = 'container-fluid';
  $containerNav = 'container-fluid';
@endphp

@extends('layouts/layout-master')

@section('title', 'Settings - Filter')

{{-- prettier-ignore-start --}}
@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/animate-css/animate.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
  'resources/assets/vendor/libs/block-ui/block-ui.js',
])
@endsection
{{-- prettier-ignore-end --}}

@section('content')
  <div class="row">
    <div class="col-md-12">
      <div class="nav-align-top">
        <ul class="nav nav-pills flex-column flex-md-row mb-6">
          <li class="nav-item"><a class="nav-link" href="{{ route('settings.account.view') }}">
              <i class="bx bx-sm bx-user me-1_5"></i> Account</a>
          </li>
          @if (Auth::user()->is_local)
            <li class="nav-item"><a class="nav-link" href="{{ route('settings.security.view') }}">
                <i class="bx bx-sm bx-lock-alt me-1_5"></i> Security</a>
            </li>
          @endif
          <li class="nav-item"><a class="nav-link active" href="javascript:void(0);">
              <i class="bx bx-sm bx-filter-alt me-1_5"></i> Filter</a>
          </li>
        </ul>
      </div>

      <div class="col-md-12" id="filter-block">
        <div class="accordion accordion-header-primary" id="accordion-filter">
          @permission(['dashboard-sales-read', 'dashboard-production-read'])
            <div class="accordion-item">
              <h2 class="accordion-header">
                <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse"
                  data-bs-target="#accordion-dashboard" aria-expanded="false" aria-controls="accordion-dashboard">
                  Dashboard
                </button>
              </h2>

              <div id="accordion-dashboard" class="accordion-collapse collapse">
                <div class="accordion-body">
                  @include('settings.components.filter-dashboard')
                </div>
              </div>
            </div>
          @endpermission
          @permission('dashboard-sales-read')
            <div class="accordion-item">
              <h2 class="accordion-header">
                <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse"
                  data-bs-target="#accordion-sales" aria-expanded="false" aria-controls="accordion-sales">
                  Sales
                </button>
              </h2>

              <div id="accordion-sales" class="accordion-collapse collapse">
                <div class="accordion-body">
                  @include('settings.components.filter-sales')
                </div>
              </div>
            </div>
          @endpermission
          @permission('dashboard-production-read')
            <div class="accordion-item">
              <h2 class="accordion-header">
                <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse"
                  data-bs-target="#accordion-production" aria-expanded="false" aria-controls="accordion-production">
                  Production
                </button>
              </h2>
              <div id="accordion-production" class="accordion-collapse collapse">
                <div class="accordion-body">
                  @include('settings.components.filter-production')
                </div>
              </div>
            </div>
          @endpermission
          @permission('dashboard-raw-material-read')
            <div class="accordion-item">
              <h2 class="accordion-header">
                <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse"
                  data-bs-target="#accordion-raw-material" aria-expanded="false" aria-controls="accordion-raw-material">
                  Raw Material
                </button>
              </h2>
              <div id="accordion-raw-material" class="accordion-collapse collapse">
                <div class="accordion-body">
                </div>
              </div>
            </div>
          @endpermission
        </div>
      </div>
    </div>
  @endsection

  @push('js')
    @include('authentications.user-preferences')

    <script type="module">
      $(document).ready(function() {
        const $block = $('#filter-block');
        let dashboards = [];

        window.SettingsFilter = window.SettingsFilter || {};

        initSelect2().then(() => {
          setUserPreferences();
        }).finally(() => {
          showHideBlockUI(false, $block);
        });

        window.arrayDiff = function(prevArray, currentArray) {

          // handle <select> if not multiple (type = string)
          if (!Array.isArray(prevArray) && !Array.isArray(currentArray)) {
            return prevArray !== currentArray;
          }

          const prev = Array.isArray(prevArray) ? prevArray : [];
          const curr = Array.isArray(currentArray) ? currentArray : [];

          // prev is null or empty, current is empty -> don't send
          if (prev.length === 0 && curr.length === 0) return false;
          // one empty, one not -> send
          if (prev.length === 0 && curr.length > 0) return true;
          if (prev.length > 0 && curr.length === 0) return true;

          // different length -> send
          if (prev.length !== curr.length) return true;
          // compare sorted values
          const prevSorted = [...prev].sort();
          const currSorted = [...curr].sort();
          return !prevSorted.every((val, idx) => val === currSorted[idx]);
        }

        $(document).on("click", ".delete-filter-btn", function() {
          const menu = $(this).data("menu");
          const tagSelector = $(this).data("tag-selector");
          const filterTag = $(tagSelector).val();

          if (filterTag === "default") {
            showInfoAlert("Default filter cannot be deleted.");
            return;
          }
          showConfirmDeleteAlert(`Filter '${filterTag}'?`, () => {
            submitDelete(menu, filterTag, tagSelector);
          });
        })

        /**
         * Save User Preferences
         */

        SettingsFilter.savePreferences = function(logModule, dashboards, $block) {
          const data = constructPreferences(logModule, dashboards);
          if (data.length > 0) {
            submitPreferences(data, $block);
          } else {
            showSuccessAlert(''); // placeholder
          }
        }

        // accordion page will send the menu-type key and set up select2 in the main page
        function setUserPreferences() {
          @permission('dashboard-production-read')
            dashboards.push(...FilterProduction.getFilter());
          @endpermission

          @permission('dashboard-sales-read')
            dashboards.push(...FilterSales.getFilter());
          @endpermission

          @permission(['dashboard-sales-read', 'dashboard-production-read'])
            dashboards.push(...FilterDashboard.getFilter());
          @endpermission

          dashboards.forEach(({
            key,
            menu,
            field,
            tagSelector, // for filter_tag
          }) => {
            if (!field) return;
            if (tagSelector) {
              setFilterTag(key, menu, field, tagSelector);
            } else {
              setValue(key, menu, field, $(tagSelector).val() || "default");
            }
          });
        }

        function setValue(key, menu, field, filterTag) {
          Object.entries(field).forEach(([fieldName, config]) => {
            const prefVal = getPrefValue(menu, fieldName, filterTag);
            $(config.selector).val(prefVal).trigger('change');

            // handle dependency
            if (config.dependsOn) {
              const {
                field: depField,
                fetchFn
              } = config.dependsOn;
              const depConfig = field[depField];
              // dependency ex: material_group depends on material_category
              if (depConfig && typeof window[fetchFn] === 'function') {
                const $depEl = $(depConfig.selector);
                if ($depEl.length > 0) {
                  window[fetchFn](prefVal, key, menu, () => {
                    const depVal = getPrefValue(menu, depField, filterTag);
                    if (depVal) {
                      $depEl.val(depVal).trigger('change');
                    }
                  });

                  // attach on close parent selector, clear old listeners before binding new ones
                  $(config.selector).off('select2:close').on('select2:close', function() {
                    const newVal = $(this).val();
                    window[fetchFn](newVal, key, menu, () => {
                      const depVal = getPrefValue(menu, depField, filterTag);
                      if (depVal) {
                        $depEl.val(depVal).trigger('change');
                      }
                    });
                  });
                }
              }
            }
          });
        }

        function constructPreferences(logModule, dashboards) {
          const data = [];
          dashboards.forEach(({
            key,
            menu,
            field,
            tagSelector, // for filter_tag
          }) => {
            if (!field) return; // skip if field is not yet mapped
            const values = {};
            let hasDiff = false;

            Object.entries(field).forEach(([fieldName, config]) => {
              const $el = $(config.selector);
              if ($el.length > 0) {
                const newVal = $el.val();
                const prevVal = getPrefValue(menu, fieldName);

                // if different, mark hasDiff
                if (arrayDiff(prevVal, newVal)) {
                  hasDiff = true;
                }
                values[fieldName] = newVal;
              }
            });

            if (hasDiff) {
              data.push({
                log_module: logModule,
                menu: menu,
                filter_tag: $(tagSelector).val() || "default",
                value: values,
              });
            }

            addTagToSelect(menu, tagSelector, $(tagSelector).val() || "default");
          });
          return data;
        }

        function submitPreferences(data, $block) {
          showHideBlockUI(true, $block);
          $.ajax({
            url: "{{ route('user-preferences.store') }}",
            type: "POST",
            data: JSON.stringify({
              preferences: data
            }),
            contentType: "application/json",
            dataType: "json",
            success: function(response) {
              if (response.success) {
                showSuccessAlert(response.message);
                UserPreferences.updateUserPreferences(response.data);
                // dashboards is changed to global variable so that can be called after ajax and select newly added filter_tag
                addTagToSelect(response.data[0].menu, dashboards.find(item => item.menu === response.data[0].menu)
                  .tagSelector, response.data[0].filter_tag);
              } else {
                showErrorAlert(response.message);
              }
              showHideBlockUI(false, $block);
            },
            error: function(xhr) {
              console.error('Failed to save user preferences: ', xhr.responseJSON);
              showErrorAlert(xhr.responseJSON.message);
              $('<select>').val('').trigger('change');
              showHideBlockUI(false, $block);
            }
          });
        }

        function getArrayOrInt(value) {
          if (typeof value === 'string') {
            return parseInt(value);
          } else {
            return value?.map(Number);
          }
        }

        /**
         * Init Select 2
         */

        function initSelect2() {
          showHideBlockUI(true, $block);
          return Promise.all([
            initPlantSelect2(),
            initCategorySelect2(),
            initGroupSelect2(),
            initPackagingSelect2(),
            initOrderStatusSelect2(),
            initCustomerCodeSelect2(),
          ]);
        }

        function initPlantSelect2() {
          let $plantSelect = $('.plant-select');
          if ($plantSelect.length > 0) {
            return $.ajax({
              url: "{{ route('master.plant.index') }}",
              method: "GET",
              success: function(response) {
                $plantSelect.each(function() {
                  setPlantSelect2(response.data, $(this), $(this).prop('multiple'));
                });
              },
              error: function(xhr) {
                console.error('Failed to load Plant: ', xhr.responseJSON);
                $plantSelect.hide();
              }
            });
          }
        }

        function setPlantSelect2(data, $el, multiple = false) {
          $el.select2({
            placeholder: 'Plant ...',
            minimumResultsForSearch: -1, // hide search box
            multiple: multiple,
            allowClear: true,
            closeOnSelect: false,
            data: data.map(plant => ({
              id: plant.id,
              text: plant.description,
            })),
          });
          $el.val('').trigger('change');
        }

        function initCategorySelect2() {
          let $categorySelect = $('.category-select');
          if ($categorySelect.length > 0) {
            return $.ajax({
              url: "{{ route('master.material.category.index') }}",
              method: "GET",
              success: function(response) {
                $categorySelect.each(function() {
                  setCategorySelect2(response.data, $(this), $(this).prop('multiple'));
                });
              },
              error: function(xhr) {
                console.error('Failed to load Category: ', xhr.responseJSON);
                $categorySelect.hide();
              }
            });
          }
        }

        function setCategorySelect2(data, $el, multiple = false) {
          $el.select2({
            placeholder: 'Category ...',
            multiple: multiple,
            allowClear: true,
            closeOnSelect: false,
            data: data.map(category => ({
              id: category.id,
              text: category.product_category,
            })),
          });
          $el.val('').trigger('change');
        }

        function initGroupSelect2() {
          $('.group-select').select2({
            placeholder: 'Group ...',
            allowClear: true,
            closeOnSelect: false,
          });
        }

        function initOrderStatusSelect2() {
          let $orderStatusSelect = $('.order-status-select');
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

                  $orderStatusSelect.each(function() {
                    setOrderStatusSelect2(statusData, $(this), $(this).prop('multiple'));
                  });
                },
                error: function(xhr) {
                  console.error('Failed to load Order Status: ', xhr.responseJSON);
                  $orderStatusSelect.hide();
                }
              });
            @endif
          }
        }

        function setOrderStatusSelect2(data, $el, multiple = false) {
          $el.select2({
            placeholder: 'Order Status ...',
            multiple: multiple,
            allowClear: true,
            closeOnSelect: false,
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
          $el.val('').trigger('change');
        }

        function initPackagingSelect2() {
          let $packagingSelect = $('.packaging-select');
          if ($packagingSelect.length > 0) {
            return $.ajax({
              url: "{{ route('master.material.packaging.index') }}",
              method: "GET",
              success: function(response) {
                $packagingSelect.each(function() {
                  setPackagingSelect2(response.data, $(this), $(this).prop('multiple'));
                });
              },
              error: function(xhr) {
                console.error('Failed to load Packaging: ', xhr.responseJSON);
                $packagingSelect.hide();
              }
            });
          }
        }

        function setPackagingSelect2(data, $el, multiple = false) {
          $el.select2({
            placeholder: 'Packaging ...',
            multiple: multiple,
            allowClear: true,
            closeOnSelect: false,
            data: data.map(packaging => ({
              id: packaging.id,
              text: packaging.packaging,
            })),
          });
          $el.val('').trigger('change');
        }

        function initCustomerCodeSelect2() {
          let $customerCodeSelect = $('.customer-code-select');

          if ($customerCodeSelect.length > 0) {
            @if (Nwidart\Modules\Facades\Module::has('SalesPlan'))
              return $.ajax({
                url: "{{ route('sales.customer.code.index') }}",
                method: "GET",
                dataType: "json",
                success: function(response) {
                  $customerCodeSelect.each(function() {
                    setCustomerCodeSelect2(response.data, $(this), $(this).prop('multiple'));
                  });
                },
                error: function(xhr) {
                  console.error('Failed to load Customer Code: ', xhr.responseJSON);
                  $customerCodeSelect.hide();
                }
              });
            @endif
          }
        }

        function setCustomerCodeSelect2(data, $el, multiple = false) {
          $el.select2({
            placeholder: 'Customer Code ...',
            multiple: multiple,
            allowClear: true,
            closeOnSelect: false,
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
          $el.val('').trigger('change');
        }

        // exposed to window because used in config dependsOn
        window.fetchMaterialGroup = function(categoryIds, key, menu, callback) {
          const $spinner = $(`#filter-${key}-group-spinner`);

          if (categoryIds) {
            $spinner.removeClass('d-none');
            $.ajax({
              url: "{{ route('master.material.group.index') }}",
              method: "GET",
              data: {
                categories: categoryIds,
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
                console.error('Failed to fetch Material Group: ', xhr.responseJSON);
                $spinner.addClass('d-none');
              }
            });
          }
        }

        function setGroupSelect2(data, key, menu, multiple = false) {
          const $el = $(`#filter-${key}-group-select`);
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

        function setFilterTag(key, menu, field, tagSelector) {
          const $tagSelect = $(tagSelector);
          if ($tagSelect.length === 0) return;

          $tagSelect.select2({
            tags: true,
            placeholder: "Select or add filter",
            createTag: params => {
              let term = $.trim(params.term);
              if (term === '') return null;
              return {
                id: term,
                text: `➕ New: ${term}`,
                newOption: true
              };
            },
            templateResult: data => {
              let $result = $("<span>").text(data.text);
              if (data.newOption) {
                $result = $('<span class="text-primary fw-bold">').text(data.text);
              }
              return $result;
            }
          });

          // populate options from userPreferences
          addTagToSelect(menu, tagSelector);

          $tagSelect.on("change", function() {
            setValue(key, menu, field, $(this).val() || "default");
          });
          // select "default if available"
          if ($tagSelect.find(`option[value="default"]`).length > 0) {
            $tagSelect.val("default").trigger("change");
          }
        }

        function addTagToSelect(menu, tagSelector, newTag = null) {
          const $tagSelect = $(tagSelector);
          if (newTag) { // if newTag provided -> add in dropdown Saved Filter
            if ($tagSelect.find(`option[value="${newTag}"]`).length === 0) {
              $tagSelect.append(new Option(
                newTag === "default" ? "Default" : `${newTag}`,
                newTag,
                true, // selected
                true,
              ));
            }
            $tagSelect.val(newTag).trigger("change"); // apply values for this tag
            return;
          }

          // otherwise -> sync all tags from memory
          const tags = Object.keys(userPreferences[menu] || {});
          tags.forEach(tag => {
            if ($tagSelect.find(`option[value="${tag}"]`).length === 0) {
              $tagSelect.append(new Option(
                tag === "default" ? "Default" : `${tag}`,
                tag,
                false,
                false
              ));
            }
          });
        }

        function submitDelete(menu, filterTag, tagSelector) {
          $.ajax({
            url: "{{ route('user-preferences.destroy', 0) }}", // id is null
            type: "DELETE",
            data: {
              menu: menu,
              filter_tag: filterTag,
            },
            dataType: "json",
            success: function(response) {
              if (response.success) {
                showSuccessAlert(response.message);
                if (userPreferences[menu]) {
                  delete userPreferences[menu][filterTag];
                }

                $(tagSelector).find(`option[value="${filterTag}"]`).remove();
                $(tagSelector).val("default").trigger("change");
              } else {
                showInfoAlert(response.message);
              }
            },
            error: function(xhr) {
              console.error('User Preferences Delete Error: ', xhr.responseJSON);
              showErrorAlert(`Filter ${filterTag} failed to be deleted.`);
            }
          });
        }
      });
    </script>
  @endpush
