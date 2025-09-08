<div class="border-bottom d-flex justify-content-between align-items-end row py-4" id="mb-product-filter-block">
  <div class="col-md-4">
    <label class="form-label" for="date-range">Date Range</label>
    <input type="text" class="form-control flatpickr-range" id="date-range" placeholder="Start Date to End Date" />
  </div>
  <div class="col-md-8"></div>
</div>

@push('js')
  <script type="module">
    $(document).ready(function() {
      const dateRangePicker = setUpFlatpickrDateRange();
      updateFilterData();

      function setUpFlatpickrDateRange() {
        const now = dayjs();
        const startOfMonth = now.startOf('month').toDate();
        const endOfMonth = now.endOf('month').toDate();
        const dateRangePicker = flatpickr('#date-range', {
          mode: 'range',
          dateFormat: "Y-m-d",
          altInput: true,
          altFormat: 'd-M-y',
          defaultDate: [startOfMonth, endOfMonth],
          locale: {
            firstDayOfWeek: 1,
          },
          onChange: function(selectedDates, dateStr, instance) {
            if (selectedDates.length === 2) {
              const startDate = instance.formatDate(selectedDates[0], 'Y-m-d');
              const endDate = instance.formatDate(selectedDates[1], 'Y-m-d');
              updateFilterData();
              if ($('#inputProductsTable').length > 0) {
                MbProduct.loadInputProductsTable(window.filterData);
              }
              if ($('#inputProductsTableNonRspo').length > 0) {
                MbProduct.loadInputProductsTableNonRspo(window.filterData);
              }
            }
          },
        });
        return dateRangePicker;
      }

      function updateFilterData() {
        const selectedDates = getStartDateAndEndDate(dateRangePicker.selectedDates);
        window.filterData = {
          startDate: selectedDates.startDate,
          endDate: selectedDates.endDate,
        };
      }

      function getStartDateAndEndDate(selectedDates) {
        let startDate, endDate;
        if (selectedDates.length === 2) {
          startDate = dateRangePicker.formatDate(selectedDates[0], 'Y-m-d');
          endDate = dateRangePicker.formatDate(selectedDates[1], 'Y-m-d');
          return {
            startDate: startDate,
            endDate: endDate
          };
        } else {
          console.warn('Date range not selected yet.');
          return;
        }
      }
    });
  </script>
@endpush
