<div id="rm-flow-pivot"></div>

@push('js')
  <script type="module">
    $('document').ready(function() {
      const pivot = initWebDataRocks();
      let pivotAggregate, pivotMetaData;

      window.RmFlowPivot = window.RmFlowPivot || {};
      RmFlowPivot.loadRmFlowTable = function(data) {
        let metaData = [
          pivotMetaData, ...data,
        ];
        setWebDataRocks(metaData);
      }

      RmFlowPivot.setColumns = function(plants) {
        setMetaData(plants);
        constructAggregate(plants);
      }

      function initWebDataRocks() {
        return $('#rm-flow-pivot').webdatarocks({
          toolbar: false,
          customizeCell: customizeCellFunction,
          global: {
            options: {
              grid: {
                title: $('#dashboard-rm-flow-category-select').find(':selected').text(),
                showHeaders: false,
                showTotals: false,
                showGrandTotals: false,
              },
              showAggregationLabels: false,
            }
          }
        });
      }

      function setMetaData(plants) {
        pivotMetaData = {
          "date_iso": {
            "type": "date",
            "caption": "Date",
            "format": "yyyy-mm-dd",
          },
          "beginning": {
            type: "number",
            caption: "Beginning",
          },
          "receipt": {
            type: "number",
            caption: "Receipt",
          },
          "receipt_actual": {
            "type": "number",
            "caption": "Actual Receipt"
          },
          "receipt_plan": {
            "type": "number",
            "caption": "Confirmed Receipt"
          },
          "production": {
            type: "number",
            caption: "Plant Consumption",
          },
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
      }

      function constructAggregate(plants) {
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
          uniqueName: "receipt_actual",
          aggregation: "sum",
        }, {
          uniqueName: "receipt_plan",
          aggregation: "sum",
        }, {
          uniqueName: "end",
          aggregation: "sum"
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
              title: $('#dashboard-rm-flow-category-select').find(':selected').text(),
            },
          },
          formats: [{
            decimalPlaces: 0,
            thousandsSeparator: ",",
          }],
          slice: {
            rows: [{
              uniqueName: "Measures"
            }],
            columns: [{
              uniqueName: "date_iso.Month"
            }, {
              uniqueName: "date_iso.Day"
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
            cellData.label === 'End' || cellData.hierarchy?.uniqueName === 'end') {
            cellStyle.addClass('bg-label-primary');
          } else if (cellData.label === "Plant Consumption" || cellData.hierarchy?.uniqueName === "production") {
            cellStyle.addClass('bg-label-info');
          } else if (cellData.label === "Receipt" || cellData.hierarchy?.uniqueName === "receipt") {
            cellStyle.addClass('bg-label-success');
          }
        }
      }
    });
  </script>
@endpush
