<div class="table-responsive text-nowrap">
  <table class="table table-sm table-bordered" id="log-module-data-table">
    <thead></thead>
    <tbody></tbody>
  </table>
</div>

@push('js')
  <script type="module">
    $(document).ready(function() {
      const $table = $('#log-module-data-table');

      initDataTable();

      function initDataTable() {
        $table.DataTable({
          processing: true,
          serverSide: true,
          ajax: {
            url: "{{ route('log.module.index') }}",
            type: "GET",
            data: function(d) {
              d.is_datatable = true;
            },
          },
          columns: [{
            data: 'module',
            name: 'module',
            title: 'Module',
          }, {
            data: 'description',
            name: 'description',
            title: 'Description',
          }, {
            data: 'created_at',
            name: 'created_at',
            title: 'Created At',
            render: function(data, type, row, meta) {
              return dayjs(data).format('YYYY-MM-DD HH:mm:ss');
            }
          }],
          language: {
            sLengthMenu: 'Show _MENU_',
            search: '',
            searchPlaceholder: 'Search',
            paginate: {
              next: '<i class="bx bx-chevron-right bx-18px"></i>',
              previous: '<i class="bx bx-chevron-left bx-18px"></i>'
            },
          }
        });
      }
    })
  </script>
@endpush
