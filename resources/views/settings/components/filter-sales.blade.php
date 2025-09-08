<div class="row align-items-center m-2 g-2" id="sales-block">

  @permission('dashboard-sales-read')
    <div class="col-md-4">
      <p class="text-nowrap text-heading fw-semibold">Dashboard</p>
    </div>
    <div class="col-md-6"></div>
    <div class="col-md-2"></div>

    <div class="col-md-10">
      <div class="accordion" id="accordion-sales-dashboard">

        <div class="accordion-item">
          <h2 class="accordion-header">
            <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse"
              data-bs-target="#accordion-yearly-sales" aria-controls="accordion-yearly-sales">
              Yearly Sales
            </button>
          </h2>
          <div class="accordion-collapse collapse" id="accordion-yearly-sales">
            <div class="accordion-body">
              <div class="row d-flex align-items-center gy-2">

                <div class="divider text-start my-2">
                  <div class="divider-text">Material</div>
                </div>

                <div class="col-md-1"></div>
                <div class="col-md-3">
                  <label class="text-nowrap">Product Category</label>
                </div>
                <div class="col-md-8">
                  <div class="select2-primary">
                    <select class="form-select select2 category-select" id="filter-yearly-sales-category-select"
                      multiple></select>
                  </div>
                </div>

                <div class="col-md-1"></div>
                <div class="col-md-3">
                  <span>
                    <label class="text-nowrap">Product Group</label>
                    <div class="spinner-border spinner-border-sm text-info mx-2 d-none" role="status"
                      id="filter-yearly-sales-group-spinner">
                      <span class="visually-hidden">Loading...</span>
                    </div>
                  </span>
                </div>
                <div class="col-md-8">
                  <div class="select2-info w-100">
                    <select class="form-select select2 group-select" id="filter-yearly-sales-group-select"
                      multiple></select>
                  </div>
                </div>

                <div class="col-md-1"></div>
                <div class="col-md-3">
                  <label class="text-nowrap">Packaging</label>
                </div>
                <div class="col-md-8">
                  <select class="form-select select2 packaging-select" id="filter-yearly-sales-packaging-select"
                    multiple></select>
                </div>

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
                  <select class="form-select select2 customer-code-select" id="filter-yearly-sales-customer-code-select"
                    multiple></select>
                </div>

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
                  <select class="form-select select2 order-status-select" id="filter-yearly-sales-order-status-select"
                    multiple></select>
                </div>

              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
    <div class="col-md-2"></div>
  @endpermission
</div>

@permission(['dashboard-sales-read'])
  <button type="button" class="btn btn-primary" id="filter-sales-save">Save changes</button>
@endpermission

@push('js')
  <script type="module">
    const logModule = 'SalesPlan';
    $(document).ready(function() {
      const $block = $('#sales-block');

      window.FilterSales = window.FilterSales || {};
      FilterSales.getFilter = function() {
        return [
          @permission('dashboard-sales-read')
            {
              key: 'yearly-sales',
              menu: 'DASHBOARD - YEARLY SALES',
              field: {
                material_category: {
                  selector: "#filter-yearly-sales-category-select",
                  dependsOn: {
                    field: "material_group",
                    fetchFn: "fetchMaterialGroup", // function reference
                  },
                },
                material_group: {
                  selector: "#filter-yearly-sales-group-select",
                },
                material_packaging: {
                  selector: "#filter-yearly-sales-packaging-select",
                },
                customer_code: {
                  selector: "#filter-yearly-sales-customer-code-select",
                },
                order_status: {
                  selector: "#filter-yearly-sales-order-status-select",
                },
              }
            },
          @endpermission
        ];
      }

      $(document).on('click', '#filter-sales-save', function() {
        SettingsFilter.savePreferences(logModule, FilterSales.getFilter(), $block);
      });
    });
  </script>
@endpush
