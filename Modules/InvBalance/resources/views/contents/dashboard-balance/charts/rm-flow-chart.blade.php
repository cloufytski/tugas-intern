<div id="rm-flow-chart"></div>

@push('js')
  <script type="module">
    $(document).ready(function() {
      let rmChart = null;
      let plantDescriptionSet;

      window.RmFlowChart = window.RmFlowChart || {};

      RmFlowChart.drawChart = function(data) {
        if (!rmChart) {
          initApexCharts();
        }
        setApexCharts(data);
      }

      RmFlowChart.setColumns = function(plants) {
        plantDescriptionSet = new Set(plants.map(p => p.description));
      }

      function initApexCharts() {
        var options = {
          chart: {
            height: 350,
            type: 'line',
            stacked: true,
          },
          series: [],
          stroke: {
            curve: 'smooth',
          },
          plotOptions: {
            bar: {
              horizontal: false,
              columnWidth: '50%',
            },
          },
          legend: {
            position: 'bottom',
          },
          dataLabels: {
            enabled: false,
            formatter: val => convertToThousand(val)
          },
          markers: {
            size: 0,
          },
          yaxis: [{
            title: {
              text: 'Consumption (bar)',
            },
            labels: {
              formatter: val => convertToThousand(val)
            },
            axisTicks: {
              show: true,
            },
            axisBorder: {
              show: true,
            },
            forceNiceScale: true,
            decimalsInFloat: 0,
            showForNullSeries: false,
          }],
          tooltip: {
            shared: true,
            intersect: false,
            y: {
              formatter: val => convertToDouble(val)
            },
          },
          theme: {
            mode: Helpers.isDarkStyle() ? 'dark' : 'light',
          },
        };
        rmChart = new ApexCharts($('#rm-flow-chart')[0], options);
        rmChart.render();
      }

      function setApexCharts(data) {
        rmChart?.updateOptions({
          xaxis: {
            categories: data.map(item => item.date),
            labels: {
              rotate: -45,
              rotateAlways: true,
            },
          },
        });
        let series = [];
        plantDescriptionSet.forEach(key => {
          const values = data.map(item => item[key] ?? null);
          if (values.some(v => v !== null)) {
            series.push({
              name: key,
              type: 'bar',
              data: values.map(v => v !== null ? Math.abs(v) : null),
            });
          }
        });

        series.push({
          name: 'Receipt',
          type: 'line',
          data: data.map(item => item.receipt),
        });
        rmChart?.updateSeries(series);
      }
    });
  </script>
@endpush
