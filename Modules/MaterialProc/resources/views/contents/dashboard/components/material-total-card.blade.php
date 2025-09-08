<div class="card h-100" id="material-total-block">
  <div class="card-header d-flex justify-content-between align-items-md-center align-items-start">
    <h5 class="card-title mb-0">Material Total</h5>
    <div class="dropdown">
      <button type="button" class="btn dropdown-toggle p-0" data-bs-toggle="dropdown" data-bs-auto-close="false"
        aria-expanded="false">
        <i class="icon-base bx bx-calendar"></i>
      </button>
      <ul class="dropdown-menu dropdown-menu-end" style="min-width: 250px;">
        <li><a id="current-month" href="javascript:void(0);" class="dropdown-item d-flex align-items-center"
            data-date-group="daily">Daily</a></li>
        <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"
            data-date-group="weekly">Weekly</a></li>
        <li>
          <hr class="dropdown-divider" />
        </li>
        <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"
            data-date-group="monthly">Monthly</a></li>
        <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"
            data-date-group="yearly">Yearly</a></li>
        <li>
          <hr class="dropdown-divider" />
        </li>
        <li class="px-2">
          <label class="form-label">Custom Date Range</label>
          <div class="flatpickr-range input-group" id="material-date-range">
            <input type="text" class="form-control" placeholder="Start to End" data-input />
            <button type="button" class="btn btn-sm btn-outline-secondary px-1" title="Clear" data-clear>
              <i class="bx bx-sm bx-x"></i>
            </button>
          </div>
        </li>
      </ul>
    </div>
  </div>
  <div class="card-body">
    <div class="alert alert-warning d-none mb-4" id="material-total-alert" role="alert">
      <span class="alert-message"></span>
    </div>
    <div id="material-total-chart"></div>
  </div>
</div>

@push('js')
  <script type="module">
    $(document).ready(function() {
      const $block = $('#material-total-block');
      let materialTotalChart;
      const dateRangePicker = setUpMaterialDateRange();
      initApexCharts();

      $('.dropdown-menu .dropdown-item').on('click', function() {
        const dateGroup = $(this).data('date-group');
        let selectedDates = getStartDateAndEndDate(dateRangePicker.selectedDates) ?? getTodayStartDateAndEndDate(
          dateGroup);
        let params = {
          start_date: selectedDates.startDate,
          end_date: selectedDates.endDate,
          date_group: dateGroup,
        };
        fetchMaterialTotal(params);
      });

      $('#current-month').trigger('click');

      $('#material-date-range [data-clear]').on('click', function() {
        $('#current-month').trigger('click');
      });

      function initApexCharts() {
        var options = {
          chart: {
            type: 'bar',
            stacked: true,
            height: 400,
          },
          series: [],
          plotOptions: {
            bar: {
              horizontal: false,
            }
          },
          legend: {
            position: 'bottom'
          },
          dataLabels: {
            enabled: true,
            formatter: val => convertToThousand(val)
          },
          yaxis: {
            labels: {
              formatter: val => convertToThousand(val)
            },
            axisTicks: {
              show: true,
            },
            axisBorder: {
              show: true,
            },
          },
          tooltip: {
            y: {
              formatter: val => convertToDouble(val)
            }
          },
          theme: {
            mode: Helpers.isDarkStyle() ? 'dark' : 'light',
          },
        };
        materialTotalChart = new ApexCharts($('#material-total-chart')[0], options);
        materialTotalChart.render();
      }

      function fetchMaterialTotal(params) {
        setAlert(null);
        showHideBlockUI(true, $block);

        $.ajax({
          url: "{{ route('procurement.dashboard.material.total') }}",
          type: "POST",
          data: JSON.stringify(params),
          contentType: "application/json",
          dataType: "json",
          success: function(response) {
            if (response.success) {
              setApexCharts(response.data, params);
            } else {
              setAlert(response.message);
            }
            showHideBlockUI(false, $block);
          },
          error: function(xhr) {
            console.error('Material Total Error: ', xhr.responseJSON);
            setAlert(xhr.responseJSON.message);
            showHideBlockUI(false, $block);
          }
        });
      }

      // Render Chart
      function setApexCharts(data, params) {
        const grouped = {};
        let categories = [];

        // kalau daily → generate semua tanggal urut
        if (params.date_group === 'daily' && params.start_date && params.end_date) {
          categories = generateDateRange(params.start_date, params.end_date);
        } else {
          data.forEach(item => {
            if (!categories.includes(item.date)) categories.push(item.date);
          });
        }

        data.forEach(item => {
          const date = item.date;
          const material = item.material_description;
          const qty = parseFloat(item.total_qty);

          if (!grouped[material]) grouped[material] = {};
          grouped[material][date] = qty;
        });

        const series = Object.keys(grouped).map(material => {
          return {
            name: material,
            data: categories.map(date => grouped[material][date] || 0),
          };
        });

        materialTotalChart.updateOptions({
          xaxis: {
            categories: categories
          }
        });
        materialTotalChart.updateSeries(series);
      }


      function generateDateRange(start, end) {
        const dates = [];
        let current = dayjs(start);
        const last = dayjs(end);
        while (current.isSameOrBefore(last)) {
          dates.push(current.format('DD-MMM-YY'));
          current = current.add(1, 'day');
        }
        return dates;
      }

      function setAlert(message = null) {
        if (message) {
          $('#material-total-alert').removeClass('d-none');
          $('#material-total-alert>.alert-message').text(message);
        } else {
          $('#material-total-alert').addClass('d-none');
          $('#material-total-alert>.alert-message').text('');
        }
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
          return;
        }
      }

      function getTodayStartDateAndEndDate(dateGroup) {
        switch (dateGroup) {
          case 'daily':
            return {
              startDate: dayjs().format('YYYY-MM-DD'),
                endDate: dayjs().add(7, 'day').format('YYYY-MM-DD'),
            };
          case 'weekly':
            return {
              startDate: dayjs().startOf('month').format('YYYY-MM-DD'),
                endDate: dayjs().add(2, 'month').endOf('month').format('YYYY-MM-DD'),
            };
          case 'monthly':
            return {
              startDate: dayjs().startOf('year').format('YYYY-MM-DD'),
                endDate: dayjs().endOf('year').format('YYYY-MM-DD'),
            };
          case 'yearly':
            return {
              startDate: dayjs().startOf('year').format('YYYY-MM-DD'),
                endDate: dayjs().endOf('year').format('YYYY-MM-DD'),
            };
        }
      }

      function setUpMaterialDateRange() {
        const now = dayjs();
        const startOfMonth = now.startOf('month').toDate();
        const endOfMonth = now.endOf('month').toDate();

        return flatpickr('#material-date-range', {
          mode: 'range',
          wrap: true,
          altInput: true,
          altFormat: 'd-M-y',
          locale: {
            firstDayOfWeek: 1
          },
          onChange: function(selectedDates, dateStr, instance) {
            if (selectedDates.length === 2) {
              const startDate = instance.formatDate(selectedDates[0], 'Y-m-d');
              const endDate = instance.formatDate(selectedDates[1], 'Y-m-d');

              const params = {
                start_date: startDate,
                end_date: endDate,
                date_group: 'daily'
              };
              fetchMaterialTotal(params);
            }
          },
        });
      }


    });
  </script>
@endpush
