<div class="row align-items-center m-2 g-2" id="production-block">

  @permission('dashboard-production-read')
    <div class="col-md-4">
      <p class="text-nowrap text-heading fw-semibold">Dashboard</p>
    </div>
    <div class="col-md-6"></div>
    <div class="col-md-2"></div>

    <div class="col-md-10">
      <div class="accordion" id="accordion-production-dashboard">
        <div class="accordion-item">
          <h2 class="accordion-header">
            <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse"
              data-bs-target="#accordion-limit-threshold" aria-controls="accordion-limit-treshold">
              Limit Threshold
            </button>
          </h2>
          <div class="accordion-collapse collapse" id="accordion-limit-threshold">
            <div class="accordion-body">
              <div class="row d-flex align-items-center gy-2">
                <div class="col-md-1"></div>
                <div class="col-md-3">
                  <label class="text-nowrap">Plant</label>
                </div>
                <div class="col-md-8">
                  <select class="form-select select2 plant-select" id="filter-limit-threshold-plant-select"
                    multiple></select>
                </div>

                <div class="col-md-1"></div>
                <div class="col-md-3">
                  <label class="text-nowrap">Product Category</label>
                </div>
                <div class="col-md-8">
                  <div class="select2-primary w-100">
                    <select class="form-select select2 category-select" id="filter-limit-threshold-category-select"
                      multiple></select>
                  </div>
                </div>

                <div class="col-md-1"></div>
                <div class="col-md-3">
                  <span>
                    <label class="text-nowrap">Product Group</label>
                    <div class="spinner-border spinner-border-sm text-info mx-2 d-none" role="status"
                      id="filter-limit-threshold-group-spinner">
                      <span class="visually-hidden">Loading...</span>
                    </div>
                  </span>
                </div>
                <div class="col-md-8">
                  <div class="select2-info w-100">
                    <select class="form-select select2 group-select" id="filter-limit-threshold-group-select"
                      multiple></select>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>

        <div class="accordion-item">
          <h2 class="accordion-header">
            <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse"
              data-bs-target="#accordion-end-balance" aria-controls="accordion-end-balance">
              End Balance
            </button>
          </h2>
          <div class="accordion-collapse collapse" id="accordion-end-balance">
            <div class="accordion-body">
              <div class="row d-flex align-items-center gy-2">
                <div class="col-md-1"></div>
                <div class="col-md-3">
                  <label class="text-nowrap">Plant</label>
                </div>
                <div class="col-md-8">
                  <select class="form-select select2 plant-select" id="filter-end-balance-plant-select" multiple></select>
                </div>

                <div class="col-md-1"></div>
                <div class="col-md-3">
                  <label class="text-nowrap">Product Category</label>
                </div>
                <div class="col-md-8">
                  <div class="select2-primary">
                    <select class="form-select select2 category-select" id="filter-end-balance-category-select"
                      multiple></select>
                  </div>
                </div>

                <div class="col-md-1"></div>
                <div class="col-md-3">
                  <span>
                    <label class="text-nowrap">Product Group</label>
                    <div class="spinner-border spinner-border-sm text-info mx-2 d-none" role="status"
                      id="filter-end-balance-group-spinner">
                      <span class="visually-hidden">Loading...</span>
                    </div>
                  </span>
                </div>
                <div class="col-md-8">
                  <div class="select2-info w-100">
                    <select class="form-select select2 group-select" id="filter-end-balance-group-select"
                      multiple></select>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="accordion-item">
          <h2 class="accordion-header">
            <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse"
              data-bs-target="#accordion-plan-actual" aria-controls="accordion-plan-actual">
              Plan vs Actual
            </button>
          </h2>
          <div class="accordion-collapse collapse" id="accordion-plan-actual">
            <div class="accordion-body">
              <div class="row d-flex align-items-center gy-2">
                <div class="col-md-1"></div>
                <div class="col-md-3">
                  <label class="text-nowrap">Plant</label>
                </div>
                <div class="col-md-8">
                  <select class="form-select select2 plant-select" id="filter-plan-actual-plant-select" multiple></select>
                </div>

                <div class="col-md-1"></div>
                <div class="col-md-3">
                  <label class="text-nowrap">Product Category</label>
                </div>
                <div class="col-md-8">
                  <div class="select2-primary w-100">
                    <select class="form-select select2 category-select" id="filter-plan-actual-category-select"
                      multiple></select>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-2"></div>
  @endpermission

  @permission('mode-read')
    <div class="col-md-4">
      <p class="text-nowrap text-heading fw-semibold">Mode</p>
    </div>
    <div class="col-md-6">
      <select class="form-select select2 plant-select" id="filter-mode-plant-select"></select>
    </div>
    <div class="col-md-2"></div>
  @endpermission

  @permission('schedule-read')
    <div class="col-md-4">
      <p class="text-nowrap text-heading fw-semibold">Schedule</p>
    </div>
    <div class="col-md-6">
      <select class="form-select select2 plant-select" id="filter-schedule-plant-select"></select>
    </div>
    <div class="col-md-2"></div>
  @endpermission

  @permission('prodsum-read')
    <div class="col-md-4">
      <p class="text-nowrap text-heading fw-semibold">Prodsum</td>
    </div>
    <div class="col-md-6">
      <select class="form-select select2 plant-select" id="filter-prodsum-plant-select"></select>
    </div>
    <div class="col-md-2"></div>

    <div class="col-md-4"></div>
    <div class="col-md-6">
      <div class="select2-primary w-100">
        <select class="form-select select2 category-select" id="filter-prodsum-category-select" multiple></select>
      </div>
    </div>
    <div class="col-md-2"></div>
  @endpermission

</div>

@permission(['mode-read', 'schedule-read', 'prodsum-read', 'dashboard-production-read'])
  <button type="button" class="btn btn-primary" id="filter-production-save">Save changes</button>
@endpermission

@push('js')
  <script type="module">
    const logModule = 'ProductionPlan';
    $(document).ready(function() {
      const $block = $('#production-block');

      window.FilterProduction = window.FilterProduction || {};
      FilterProduction.getFilter = function() {
        return [
          @permission('mode-read')
            {
              key: 'mode',
              menu: 'MODE',
              field: {
                plant: {
                  selector: "#filter-mode-plant-select"
                },
              },
            },
          @endpermission
          @permission('schedule-read')
            {
              key: 'schedule',
              menu: 'SCHEDULE',
              field: {
                plant: {
                  selector: "#filter-schedule-plant-select"
                },
              },
            },
          @endpermission
          @permission('prodsum-read')
            {
              key: 'prodsum',
              menu: 'PRODSUM',
              field: {
                plant: {
                  selector: "#filter-prodsum-plant-select",
                },
                material_category: {
                  selector: "#filter-prodsum-category-select",
                },
              },
            },
          @endpermission
          @permission('dashboard-production-read')
            {
              key: 'end-balance',
              menu: 'DASHBOARD - END BALANCE',
              field: {
                plant: {
                  selector: "#filter-end-balance-plant-select",
                },
                material_category: {
                  selector: "#filter-end-balance-category-select",
                  dependsOn: {
                    field: "material_group",
                    fetchFn: "fetchMaterialGroup", // function reference
                  },
                },
                material_group: {
                  selector: "#filter-end-balance-group-select",
                },
              }
            }, {
              key: 'plan-actual',
              menu: 'DASHBOARD - PLAN ACTUAL',
              field: {
                plant: {
                  selector: "#filter-plan-actual-plant-select",
                },
                material_category: {
                  selector: "#filter-plan-actual-category-select",
                },
              },
            }, {
              key: 'limit-threshold',
              menu: 'DASHBOARD - LIMIT THRESHOLD',
              field: {
                plant: {
                  selector: "#filter-limit-threshold-plant-select",
                },
                material_category: {
                  selector: "#filter-limit-threshold-category-select",
                  dependsOn: {
                    field: "material_group",
                    fetchFn: "fetchMaterialGroup", // function reference
                  },
                },
                material_group: {
                  selector: "#filter-limit-threshold-group-select",
                },
              }
            },
          @endpermission
        ];
      }

      $(document).on('click', '#filter-production-save', function() {
        SettingsFilter.savePreferences(logModule, FilterProduction.getFilter(), $block);
      });
    });
  </script>
@endpush
