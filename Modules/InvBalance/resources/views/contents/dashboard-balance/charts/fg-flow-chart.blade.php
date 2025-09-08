<div id="fg-flow-chart"></div>

@push('js')
  <script type="module">
    $(document).ready(function() {
      let fgChart = null;

      window.FgFlowChart = window.FgFlowChart || {};

      FgFlowChart.drawChart = function(data) {
        if (!fgChart) {
          initApexCharts();
        }
        setApexCharts(data);
      }

      function initApexCharts() {
        var options = {
          chart: {
            height: 350,
            type: 'line',
            stacked: false,
          },
          series: [],
          stroke: {
            curve: 'smooth',
          },
          plotOptions: {
            bar: {
              horizontal: false,
              columnWidth: '50%',
            }
          },
          legend: {
            position: 'bottom',
          },
          dataLabels: {
            enabled: false,
            formatter: val => convertToThousand(val)
          },
          markers: {
            size: 0
          },
          yaxis: [{
              seriesName: 'Production',
              title: {
                text: 'Production (bar)',
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
              min: 0,
              showForNullSeries: false,
            },
            @if (Laratrust::hasPermission('master-order-read'))
              {
                opposite: true,
                seriesName: 'Pending Ship',
                title: {
                  text: 'Pending Ship (line)'
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
                min: 0,
                showForNullSeries: false,
              }, {
                opposite: true,
                seriesName: 'Forecast',
                title: {
                  text: 'Forecast (line)'
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
                min: 0,
              },
            @else
              {
                opposite: true,
                seriesName: 'Sales',
                title: {
                  text: 'Sales (line)'
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
                min: 0,
              },
            @endif
          ],
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
        fgChart = new ApexCharts($('#fg-flow-chart')[0], options);
        fgChart.render();
      }

      function setApexCharts(data) {
        fgChart?.updateOptions({
          xaxis: {
            categories: data.map(item => item.date),
            labels: {
              rotate: -45,
              rotateAlways: true,
            }
          },

        });
        fgChart?.updateSeries([{
            name: 'Production',
            type: 'bar',
            data: data.map(item => item.production),
            yAxisIndex: 0,
          },
          @if (Laratrust::hasPermission('master-order-read'))
            {
              name: 'Pending Ship',
              type: 'line',
              data: data.map(item => item.pending_ship),
              yAxisIndex: 1,
            }, {
              name: 'Forecast',
              type: 'line',
              data: data.map(item => item.forecast),
              yAxisIndex: 2,
            },
          @else
            {
              name: 'Sales',
              type: 'line',
              data: data.map(item => item.sales),
              yAxisIndex: 1,
            },
          @endif
        ]);
      }
    });
  </script>
@endpush
