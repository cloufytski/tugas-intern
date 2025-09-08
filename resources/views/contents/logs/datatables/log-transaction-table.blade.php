<div class="table-responsive">
  <table class="table table-sm table-striped" id="log-transaction-data-table">
    <thead></thead>
    <tbody></tbody>
  </table>
</div>

@push('js')
  <script type="module">
    $(document).ready(function() {
      const $table = $('#log-transaction-data-table');

      initDataTable();

      function initDataTable() {
        $table.DataTable({
          processing: true,
          serverSide: true,
          ajax: {
            url: "{{ route('log.transaction.index') }}",
            type: "GET",
            data: function(d) {
              d.is_datatable = true;
            },
          },
          pageLength: 50,
          scrollX: false,
          columns: [{
            data: 'id',
            name: 'id',
            title: 'Id',
          }, {
            data: 'log_module',
            name: 'log_module',
            title: 'Module',
          }, {
            data: 'log_type',
            name: 'log_type',
            title: 'Type',
          }, {
            data: 'log_model',
            name: 'log_model',
            title: 'Model',
          }, {
            data: 'log_description',
            name: 'log_description',
            title: 'Description',
            render: function(data, type, row, meta) {
              const shortText = data.length > 200 ? data.substring(0, 200) + ' ...' : data;
              return shortText;
            }
          }, {
            data: 'created_by',
            name: 'created_by',
            title: 'Created By',
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
          },
        });
      }
    })
  </script>
@endpush
