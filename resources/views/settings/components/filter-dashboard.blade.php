<div class="row align-items-center m-2 g-2" id="dashboard-block">

  @permission(['dashboard-sales-read', 'dashboard-production-read'])
    <div class="col-md-10">
      <div class="accordion" id="accordion-sales-dashboard">
        <div class="accordion-item">
          <h2 class="accordion-header">
            <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse"
              data-bs-target="#accordion-inventory-balance" aria-controls="accordion-inventory-balance">
              Inventory Balance
            </button>
          </h2>
          <div class="accordion-collapse collapse" id="accordion-inventory-balance">
            <div class="accordion-body">
              <div class="w-25 d-flex align-items-center gap-2">
                <label class="form-label me-2">Saved Filter</label>
                <select class="form-select" id="inventory-balance-tag">
                  <option value="default">Default</option>
                </select>
                <button type="button" class="btn btn-icon btn-label-danger delete-filter-btn"
                  data-menu="DASHBOARD - INVENTORY BALANCE" data-tag-selector="#inventory-balance-tag">
                  <i class="bx bx-sm bx-trash-alt"></i></button>
              </div>
              <div class="row d-flex align-items-center gy-2 mt-2">
                <div class="divider text-start my-2">
                  <div class="divider-text">Material</div>
                </div>

                <div class="col-md-1"></div>
                <div class="col-md-3">
                  <label class="text-nowrap">Product Category</label>
                </div>
                <div class="col-md-8">
                  <div class="select2-primary w-100">
                    <select class="form-select select2 category-select" id="filter-inventory-balance-category-select"
                      multiple></select>
                  </div>
                </div>

                <div class="col-md-1"></div>
                <div class="col-md-3">
                  <span>
                    <label class="text-nowrap">Product Group</label>
                    <div class="spinner-border spinner-border-sm text-info mx-2 d-none" role="status"
                      id="filter-inventory-balance-group-spinner">
                      <span class="visually-hidden">Loading...</span>
                    </div>
                  </span>
                </div>
                <div class="col-md-8">
                  <div class="select2-info w-100">
                    <select class="form-select select2 group-select" id="filter-inventory-balance-group-select"
                      multiple></select>
                  </div>
                </div>

                <div class="col-md-1"></div>
                <div class="col-md-3">
                  <label class="text-nowrap">Packaging</label>
                </div>
                <div class="col-md-8">
                  <select class="form-select select2 packaging-select" id="filter-inventory-balance-packaging-select"
                    multiple></select>
                </div>

                @permission('master-customer-read')
                  <div class="divider text-start my-2">
                    <div class="divider-text">Customer</div>
                  </div>

                  <div class="col-md-1"></div>
                  <div class="col-md-3">
                    <span>
                      <label class="text-nowrap">Customer Code</label>
                    </span>
                  </div>
                  <div class="col-md-8">
                    <select class="form-select select2 customer-code-select"
                      id="filter-inventory-balance-customer-code-select" multiple></select>
                  </div>
                @endpermission

                @permission('master-order-read')
                  <div class="divider text-start my-2">
                    <div class="divider-text">Order</div>
                  </div>

                  <div class="col-md-1"></div>
                  <div class="col-md-3">
                    <span>
                      <label class="text-nowrap">Order Status</label>
                    </span>
                  </div>
                  <div class="col-md-8">
                    <select class="form-select select2 order-status-select"
                      id="filter-inventory-balance-order-status-select" multiple></select>
                  </div>
                @endpermission

              </div>

            </div>
          </div>
        </div>

      </div>
    </div>
    <div class="col-md-2"></div>
  @endpermission
</div>

@permission(['dashboard-sales-read', 'dashboard-production-read'])
  <button type="button" class="btn btn-primary" id="filter-dashboard-save">Save changes</button>
@endpermission

@push('js')
  <script type="module">
    const logModule = 'InvBalance';
    $(document).ready(function() {
      const $block = $('#dashboard-block');

      window.FilterDashboard = window.FilterDashboard || {};
      FilterDashboard.getFilter = function() {
        return [
          @permission(['dashboard-sales-read', 'dashboard-production-read'])
            {
              key: 'inventory-balance',
              menu: 'DASHBOARD - INVENTORY BALANCE',
              tagSelector: "#inventory-balance-tag",
              field: {
                material_category: {
                  selector: "#filter-inventory-balance-category-select",
                  dependsOn: {
                    field: "material_group",
                    fetchFn: "fetchMaterialGroup", // function reference
                  },
                },
                material_group: {
                  selector: "#filter-inventory-balance-group-select",
                },
                material_packaging: {
                  selector: "#filter-inventory-balance-packaging-select",
                },
                customer_code: {
                  selector: "#filter-inventory-balance-customer-code-select",
                },
                order_status: {
                  selector: "#filter-inventory-balance-order-status-select",
                },
              }
            },
          @endpermission
        ];
      }

      $(document).on('click', '#filter-dashboard-save', function() {
        SettingsFilter.savePreferences(logModule, FilterDashboard.getFilter(), $block);
      });
    });
  </script>
@endpush
