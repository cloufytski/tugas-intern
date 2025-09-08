<table class="table table-sm table-bordered table-striped w-100" id="inputProductsTable">
  <thead class="table-primary">
    <tr>
      <td rowspan="2">Supplier Name</td>
      <td colspan="1" class="text-center">From:</td>
      <td rowspan="2">Product</td>
      <td rowspan="2" class="text-end">Product Volume</td>
      <td colspan="2" class="text-center">Palm Oil Products</td>
      <td colspan="2" class="text-center">Palm Kernel Oil Products</td>
    </tr>
    <tr>
      <td>IP, SG, MB</td>
      <td class="text-end">PO Weight</td>
      <td class="text-end">Conversion Rate</td>
      <td class="text-end">PKO Weight</td>
      <td class="text-end">Conversion Rate</td>
    </tr>
  </thead>
  <tbody></tbody>
  <tfoot>
    <tr>
      <td colspan="2" class="text-center fw-bold">TOTAL</td>
      <td></td>
      <td class="text-end"></td>
      <td class="text-end"></td>
      <td class="text-end"></td>
      <td class="text-end"></td>
      <td class="text-end"></td>
    </tr>
  </tfoot>
</table>

<table class="table table-sm table-bordered table-striped w-100" id="inputProductsTableNonRspo">
  <thead class="table-primary">
    <tr>
      <td>Supplier Name</td>
      <td>Non RSPO</td>
      <td>Product</td>
      <td class="text-end">PO weight</td>
      <td class="text-end">PKO weight</td>
    </tr>
  </thead>
  <tbody></tbody>
  <tfoot>
    <tr>
      <td colspan="2" class="text-center fw-bold">TOTAL</td>
      <td></td>
      <td class="text-end"></td>
      <td class="text-end"></td>
    </tr>
  </tfoot>
</table>

@push('js')
  <script type="module">
    $(document).ready(function() {
      const $table = $('#inputProductsTable');
      const $tableNonRspo = $('#inputProductsTableNonRspo');

      window.MbProduct = window.MbProduct || {};
      MbProduct.loadInputProductsTable = function(params) {
        if ($.fn.DataTable.isDataTable($table)) {
          $table.DataTable().ajax.reload(null, false);
        } else {
          initDataTable($table, true);
        }
      }

      MbProduct.loadInputProductsTableNonRspo = function(params) {
        if ($.fn.DataTable.isDataTable($tableNonRspo)) {
          $tableNonRspo.DataTable().ajax.reload(null, false);
        } else {
          initDataTable($tableNonRspo, false);
        }
      }

      initializeFirstFilter();

      function initializeFirstFilter() {
        const startDate = dayjs().startOf('month').format('YYYY-MM-DD');
        const endDate = dayjs().endOf('month').format('YYYY-MM-DD');
        window.filterData = {
          startDate: startDate,
          endDate: endDate,
        };
        MbProduct.loadInputProductsTable(window.filterData);
        MbProduct.loadInputProductsTableNonRspo(window.filterData);
      }

      function initDataTable($table, isRspo) {
        $table.DataTable({
          processing: true,
          serverSide: false,
          ajax: {
            url: "{{ route('procurement.mb-product.index') }}",
            type: "GET",
            data: function(d) {
              d.is_datatable = true;
              d.start_date = window.filterData.startDate;
              d.end_date = window.filterData.endDate;
              d.is_rspo = isRspo ? 1 : 0;
            },
          },
          paging: true,
          pageLength: 25,
          columns: getColumns(isRspo),

          footerCallback: function(row, data, start, end, display) {
            const api = this.api();

            // indeks kolom footer (sesuai header)
            const poIndex = isRspo ? 4 : 3; // PO weight
            const pkoIndex = isRspo ? 6 : 4; // PKO: 6 utk RSPO, 4 utk non-RSPO

            // sum dari data baris (bukan dari column().data())
            let poTotal = 0;
            let pkoTotal = 0;

            api.rows({
              page: 'current'
            }).every(function() {
              const r = this.data();
              const qty = Number(r?.qty) || 0;

              const groupId = r?.material?.product_group?.id;

              // PO groups
              if ([178, 179, 183, 184, 185].includes(groupId)) poTotal += qty;
              // PKO groups
              if ([176, 177, 181, 182].includes(groupId)) pkoTotal += qty;
            });

            // update footer (check foot cell)
            const poCell = api.column(poIndex).footer();
            const pkoCell = api.column(pkoIndex).footer();
            if (poCell) $(poCell).html(convertToThousand(poTotal));
            if (pkoCell) $(pkoCell).html(convertToThousand(pkoTotal));
          },
          language: {
            sLengthMenu: 'Show _MENU_',
            search: '',
            searchPlaceholder: 'Search',
            paginate: {
              next: '<i class="bx bx-chevron-right bx-18px"></i>',
              previous: '<i class="bx bx-chevron-left bx-18px"></i>'
            },
          },
        });
      }

      function getColumns(isRspo) {
        if (isRspo) {
          return [{
              data: 'supplier.supplier',
              name: 'supplier'
            },
            {
              data: null,
              name: 'from',
              render: () => 'MB',
            },
            {
              data: 'material.material_description',
              name: 'material_description'
            },
            {
              data: 'qty',
              name: 'qty',
              render: function(data, type, row) {
                return convertToThousand(data);
              },
              className: 'text-end'
            },
            {
              data: null,
              name: 'po_weight',
              orderable: false,
              searchable: false,
              render: function(data, type, row) {
                return [178, 179, 183, 184, 185].includes(row.material.product_group.id) ?
                  convertToThousand(row.qty) :
                  '';
              },
              className: 'text-end'
            },
            {
              data: 'material.conversion',
              name: 'po_conversion_rate',
              orderable: false,
              searchable: false,
              render: function(data, type, row) {
                return [178, 179, 183, 184, 185].includes(row.material.product_group.id) ?
                  data :
                  '';
              },
              className: 'text-end'
            },
            {
              data: null,
              name: 'pko_weight',
              orderable: false,
              searchable: false,
              render: function(data, type, row) {
                return [176, 177, 181, 182].includes(row.material.product_group.id) ?
                  convertToThousand(row.qty) :
                  '';
              },
              className: 'text-end'
            },
            {
              data: 'material.conversion',
              name: 'pko_conversion_rate',
              orderable: false,
              searchable: false,
              render: function(data, type, row) {
                return [176, 177, 181, 182].includes(row.material.product_group.id) ?
                  data :
                  '';
              },
              className: 'text-end'
            }
          ];
        } else {
          return [{
              data: null,
              name: 'supplier',
              orderable: false,
              searchable: false,
              render: () => '-'
            },
            {
              data: null,
              name: 'non_rspo',
              orderable: false,
              searchable: false,
              render: () => 'Non RSPO',
            },
            {
              data: 'material.material_description',
              name: 'material_description',
            },
            {
              data: null,
              name: 'po_weight',
              orderable: false,
              searchable: false,
              render: function(data, type, row) {
                return [178, 179, 183, 184, 185].includes(row.material.product_group.id) ?
                  convertToThousand(row.qty) :
                  '';
              },
              className: 'text-end'
            },
            {
              data: null,
              name: 'pko_weight',
              orderable: false,
              searchable: false,
              render: function(data, type, row) {
                return [176, 177, 181, 182].includes(row.material.product_group.id) ?
                  convertToThousand(row.qty) :
                  '';
              },
              className: 'text-end'
            }
          ]
        }
      }
    });
  </script>
@endpush
