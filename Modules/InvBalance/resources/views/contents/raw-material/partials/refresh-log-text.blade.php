<div class="text-end">
  <small>Last <span class="fw-semibold text-info">Production</span> refresh: <span id="production-log"></span></small>
  <br />
  <small>Last <span class="fw-semibold text-success">Receipt</span> refresh: <span id="procurement-log"></span></small>
</div>

@push('js')
  <script type="module">
    $(document).ready(function() {
      window.RefreshLog = window.RefreshLog || {};

      RefreshLog.fetchLogTimestamps = function() {
        $.get("{{ route('inventory.log') }}", function(response) {
          setLogTimestamps(response);
        }).fail(function(xhr) {
          console.error('Log Timestamps Error: ', xhr.responseJSON);
          $('#production-log, #procurement-log').html(
            `<span class="text-danger fw-semibold">ERROR</span>`
          );
        });
      }

      RefreshLog.submitRefreshInventoryView = function($blockEl) {
        showHideBlockUI(true, $blockEl);
        $.post("{{ route('inventory.refresh') }}", function(response) {
          setLogTimestamps(response);
          showHideBlockUI(false, $blockEl);
        }).fail(function(xhr) {
          console.error('Refresh Inventory View Error: ', xhr.responseJSON);
          $('#production-log').html(`<span class="text-danger fw-semibold">ERROR</span>`);
          showHideBlockUI(false, $blockEl);
        });
      }

      function setLogTimestamps(response) {
        if (response.success) {
          $('#production-log').text(
            response.data.production_log ? dayjs(response.data.production_log).format(
              'DD-MMM-YYYY HH:mm:ss') : '-');
          $('#procurement-log').text(
            response.data.procurement_log ? dayjs(response.data.procurement_log).format('DD-MMM-YYYY HH:mm:ss') :
            '-');
        } else {
          $('#production-log #procurement-log').text('-');
        }
      }
    });
  </script>
@endpush
