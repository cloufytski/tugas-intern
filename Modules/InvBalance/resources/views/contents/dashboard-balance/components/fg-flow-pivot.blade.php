<div id="fg-flow-pivot"></div>

@push('js')
  <script type="module">
    $(document).ready(function() {
      const pivot = initWebDataRocks();
      let pivotAggregate, pivotMetaData;

      window.FgFlowPivot = window.FgFlowPivot || {};
      FgFlowPivot.loadFgFlowTable = function(data) {
        let metaData = [
          pivotMetaData, ...data,
        ];
        setWebDataRocks(metaData);
      }

      FgFlowPivot.setColumns = function(plants, orderStatuses) {
        setMetaData(plants, orderStatuses);
        constructAggregate(plants, orderStatuses);
      }

      function initWebDataRocks() {
        return $('#fg-flow-pivot').webdatarocks({
          toolbar: false,
          customizeCell: customizeCellFunction,
          global: {
            options: {
              grid: {
                title: $('#dashboard-fg-flow-category-select').find(':selected').text(),
                showHeaders: false,
                showTotals: false,
                showGrandTotals: false,
              },
              showAggregationLabels: false,
            }
          }
        });
      }

      function setMetaData(plants, orderStatuses) {
        pivotMetaData = {
          "date_iso": {
            type: "date",
            caption: "Date",
            format: "yyyy-mm-dd",
          },
          "beginning": {
            type: "number",
            caption: "Beginning",
          },
          "receipt": {
            type: "number",
            caption: "Receipt",
          },
          "production": {
            type: "number",
            caption: "Production",
          },
          "sales": {
            type: "number",
            caption: "Sales",
          },
          @permission('master-order-read')
            "pending_ship": {
              type: "number",
              caption: "Pending Ship",
            },
            "forecast": {
              type: "number",
              caption: "Forecast",
            },
          @endpermission
          "end": {
            type: "number",
            caption: "End",
          },
        };
        plants.forEach(plant => {
          pivotMetaData[plant.description] = {
            type: "number",
            caption: plant.description,
          }
        });
        orderStatuses?.forEach(status => {
          pivotMetaData[status.order_status] = {
            type: "number",
            caption: status.order_status,
          }
        });
      }

      function constructAggregate(plants, orderStatuses) {
        pivotAggregate = [{
          uniqueName: "beginning",
          aggregation: "sum",
        }, {
          uniqueName: "production",
          aggregation: "sum",
        }];
        plants.forEach(plant => {
          pivotAggregate.push({
            uniqueName: plant.description,
            aggregation: "sum",
            caption: plant.description
          });
        });
        pivotAggregate.push({
          uniqueName: "receipt",
          aggregation: "sum",
        }, {
          uniqueName: "sales",
          aggregation: "sum",
        }, {
          uniqueName: "pending_ship",
          aggregation: "sum",
        }, {
          uniqueName: "forecast",
          aggregation: "sum",
        }, {
          uniqueName: "end",
          aggregation: "sum",
        });
      }

      function setWebDataRocks(data) {
        pivot.setReport({
          dataSource: {
            dataSourceType: 'json',
            data: data,
          },
          options: {
            grid: {
              title: $('#dashboard-fg-flow-category-select').find(':selected').text(),
            },
          },
          formats: [{
            decimalPlaces: 0,
            thousandsSeparator: ",",
          }],
          slice: {
            rows: [{
              uniqueName: "Measures",
            }],
            columns: [{
              uniqueName: "date_iso.Month",
            }, {
              uniqueName: "date_iso.Day",
            }],
            measures: pivotAggregate,
            expands: {
              expandAll: true,
            },
          }
        });
      }

      function customizeCellFunction(cellStyle, cellData) {
        // for Order Status Header on the left column
        if (cellData.type === "header" || cellData.type === "value") {
          if (cellData.label === "Beginning" || cellData.hierarchy?.uniqueName === "beginning" ||
            cellData.label === "End" || cellData.hierarchy?.uniqueName === "end"
          ) {
            cellStyle.addClass('bg-label-primary');
          } else if (cellData.label === "Production" || cellData.hierarchy?.uniqueName === "production") {
            cellStyle.addClass('bg-label-info');
          } else if (cellData.label === "Sales" || cellData.hierarchy?.uniqueName === "sales") {
            cellStyle.addClass('bg-label-warning');
          } else if (cellData.label === "Receipt" || cellData.hierarchy?.uniqueName === "receipt") {
            cellStyle.addClass('bg-label-success');
          }
        }
      }
    });
  </script>
@endpush
