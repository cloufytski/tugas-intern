<div class="card card-action mb-4" id="fg-flow-block">
  <div class="card-header d-flex justify-content-between align-items-md-center align-items-start">
    <h5 class="card-title mb-0">FG (Finished Goods) Flow</h5>
    <div class="card-action-element">
      <li class="list-inline-item">
        <a class="card-collapsible me-2">
          <i class="icon-base bx bx-chevron-down"></i>
        </a>
        <a class="card-expand">
          <i class="icon-base bx bx-fullscreen"></i>
        </a>
      </li>
    </div>
  </div>
  <div class="collapse show">
    <div class="card-body">
      <div class="alert alert-warning d-none mb-4" id="fg-flow-alert" role="alert">
        <span class="alert-message"></span>
      </div>
      <div class="row gy-2">
        <div class="col-md-6">
          <label class="form-label" for="dashboard-fg-flow-category-select">Product Category</label>
          <select class="form-select select2 category-select" id="dashboard-fg-flow-category-select"></select>
        </div>
        <div class="col-md-6">
          <span>
            <label class="form-label" for="dashboard-fg-flow-group-select">Product Group</label>
            <div class="spinner-border spinner-border-sm text-info mx-2 d-none" role="status"
              id="dashboard-fg-flow-group-spinner">
              <span class="visually-hidden">Loading...</span>
            </div>
          </span>
          <div class="select2-info">
            <select class="form-select select2 group-select" id="dashboard-fg-flow-group-select" multiple></select>
          </div>
        </div>
        <div class="col-md-12">
          @include('invbalance::contents.dashboard-balance.components.fg-flow-pivot')
        </div>
        <div class="col-md-12">
          @include('invbalance::contents.dashboard-balance.charts.fg-flow-chart')
        </div>
      </div>
    </div>
  </div>
</div>

@push('js')
  <script type="module">
    $(document).ready(function() {
      const $block = $('#fg-flow-block');

      window.FgFlow = window.FgFlow || {};
      FgFlow.getFilter = function() {
        return [{
          key: 'fg-flow',
          menu: 'DASHBOARD - BALANCE',
        }, ]
      }

      $('#dashboard-fg-flow-group-select').on('select2:close', function() {
        const categoryId = $('#dashboard-fg-flow-category-select').val();
        const groupIds = $(this).val();
        const selectedDates = getStartDateAndEndDate($('#date-range')[0]._flatpickr.selectedDates);
        fetchProductionAndSales(selectedDates.startDate, selectedDates.endDate, categoryId, groupIds);
      });

      function fetchProductionAndSales(startDate, endDate, categoryId, groupIds) {
        showHideBlockUI(true, $block);
        setAlert(null);
        $.ajax({
          url: "{{ route('inventory.total') }}",
          type: "POST",
          data: JSON.stringify({
            date_group: 'weekly',
            start_date: startDate,
            end_date: endDate,
            categories: [categoryId],
            groups: groupIds,
          }),
          contentType: "application/json",
          dataType: "json",
          success: function(response) {
            if (response.success) {
              let merged = constructData(response.data);
              FgFlowPivot?.loadFgFlowTable(merged);
              FgFlowChart?.drawChart(merged);
            } else {
              setAlert(response.message);
            }
            showHideBlockUI(false, $block);
          },
          error: function(xhr) {
            console.error('Inventory Total (FG Flow) Error: ', xhr.responseJSON);
            setAlert(xhr.responseJSON.message);
            showHideBlockUI(false, $block);
          }
        });
      }

      function setAlert(message = null) {
        if (message) {
          $('#fg-flow-alert').removeClass('d-none');
          $('#fg-flow-alert>.alert-message').text(message);
        } else {
          $('#fg-flow-alert').addClass('d-none');
          $('#fg-flow-alert.alert-message').text('');
        }
      }

      function constructData(data) {
        let runningEnd = 0; // end of today become beginning of next day
        return Object.values(data).flat().map((item, i) => {
          // set beginning for first row
          if (i === 0) {
            item.beginning = item.beginning || runningEnd;
          } else {
            item.beginning = runningEnd;
          }

          // NOTE: hardcoded
          @permission('master-order-read')
            item.pending_ship = (item.Committed || 0) + (item.Order || 0);
            item.forecast = (item.Projection || 0) + (item.Reserved || 0) + (item.Quoted || 0);
          @endpermission

          item.end = (item.beginning || 0) + (item.receipt || 0) + (item.production || 0) - (item.sales || 0);
          runningEnd = item.end; // update runningEnd for next iteration
          return item;
        });
      }
    });
  </script>
@endpush
