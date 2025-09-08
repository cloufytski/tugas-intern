<div class="offcanvas offcanvas-end" id="offcanvas-procurement">
  <div class="offcanvas-header border-bottom">
    <h5 class="offcanvas-title">Procurement</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body mx-0 flex-grow-0">
    <form class="pt-0 row g-2" id="form-procurement">
      @csrf
      <div class="col-sm-6" id="contract_no-group">
        <input type="hidden" id="id-input" />
        <label class="form-label" for="contract_no-input">Contract No</label>
        <input type="text" class="form-control" id="contract_no-input" name="contract_no" />
      </div>
      <div class="col-sm-6" id="po_date-group">
        <label class="form-label" for="po_date-input">PO Date</label>
        <input type="text" id="po_date-input" class="form-control" name="po_date" />
      </div>
      <div class="col-sm-12" id="id_plant-group">
        <label class="form-label" for="plant-input">Plant</label>
        <select class="form-select select2" id="plant-input" name="id_plant"></select>
      </div>
      <div class="col-sm-12" id="id_supplier-group">
        <label class="form-label" for="supplier-select">Supplier</label>
        <select class="form-select select2" id="supplier-input" name="id_supplier"></select>
      </div>
      <div class="col-sm-12" id="id_material-group">
        <input type="hidden" id="id_material-input" name="id_material" />
        <label class="form-label" for="material-input">Material Description</label>
        <input type="text" id="material-input" class="form-control" name="material_description" />
      </div>

      <div class="col-sm-12" id="qty-group">
        <label class="form-label" for="qty-input">Qty</label>
        <input type="number" step="any" id="qty-input" class="form-control" name="qty" disabled />
      </div>
      <div class="col-sm-6" id="qty_actual-group">
        <label class="form-label" for="qty_actual-input">Qty Actual</label>
        <input type="number" step="any" id="qty_actual-input" class="form-control" name="qty_actual" />
      </div>
      <div class="col-sm-6" id="qty_plan-group">
        <label class="form-label" for="qty_plan-input">Qty Plan</label>
        <input type="number" step="any" id="qty_plan-input" class="form-control" name="qty_plan" />
      </div>

      <div class="col-sm-12" id="eta-group">
        <label class="form-label" for="eta-input">ETA</label>
        <input type="text" id="eta-input" class="form-control" name="eta" disabled />
      </div>
      <div class="col-sm-6" id="eta_actual-group">
        <label class="form-label" for="eta_actual-input">ETA Actual</label>
        <input type="text" id="eta_actual-input" class="form-control" name="eta_actual" />
      </div>
      <div class="col-sm-6" id="eta_plan-group">
        <label class="form-label" for="eta_plan-input">ETA Plan</label>
        <input type="text" id="eta_plan-input" class="form-control" name="eta_plan" />
      </div>

      <div class="col-sm-12" id="vessel_name-group">
        <label class="form-label" for="vessel_name-input">Vessel Name</label>
        <input type="text" id="vessel_name-input" class="form-control" name="vessel_name" />
      </div>
      <div class="col-sm-12" id="loading_port-group">
        <label class="form-label" for="loading_port-input">Loading Port</label>
        <input type="text" id="loading_port-input" class="form-control" name="loading_port" />
      </div>
      <div class="col-sm-6" id="ffa-group">
        <label class="form-label" for="ffa-input">FFA</label>
        <input type="number" step="any" id="ffa-input" class="form-control" name="ffa" />
      </div>
      <div class="col-sm-6" id="price-group">
        @permission('procurement-price-read')
          <label class="form-label" for="price-input">Price</label>
          <input type="number" step="any" id="price-input" class="form-control" name="price" />
        @endpermission
      </div>
      <div class="col-sm-12" id="is_rspo-group">
        <input class="form-check-input" type="checkbox" value="true" id="is_rspo-input" name="is_rspo" />
        <label class="form-check-label" for="is_rspo-input">RSPO</label>
      </div>
      <div class="col-sm-12" id="remarks-group">
        <label class="form-label" for="remarks-input">Remarks</label>
        <textarea class="form-control autosize" id="remarks-input" name="remarks" rows="2"></textarea>
      </div>
      <div class="col-sm-12 mt-4">
        <button type="submit" class="btn btn-primary me-3" id="procurement-submit">Submit</button>
        <button type="reset" class="btn btn-label-danger" id="procurement-cancel"
          data-bs-dismiss="offcanvas">Cancel</button>
      </div>
    </form>
  </div>
</div>

@push('js')
  <script type="module">
    $(document).ready(function() {
      const $offCanvas = $('#offcanvas-procurement');
      const $form = $('#form-procurement');
      const $plantSelect = $('#plant-input');
      const $supplierSelect = $('#supplier-input');

      initSelect2();
      initMaterialTypeahead($('#material-input'));

      // expose fetchProcurement to page
      window.Procurement = window.Procurement || {};

      $offCanvas.on('shown.bs.offcanvas', function() {
        const id = $form.find('#id-input').val();
        if (isNotNullOrEmpty(id)) {
          Procurement.fetchProcurement(id);
        }
      });

      $(document).on('click', '#procurement-cancel, .text-reset', function() {
        setForm(null);
      });

      $form.on('submit', function(e) {
        e.preventDefault();
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
        submitForm($(this));
      });

      $('#po_date-input').flatpickr({
        dateFormat: 'Y-m-d',
      });

      $('#eta_plan-input').flatpickr({
        dateFormat: 'Y-m-d',
        defaultDate: new Date(),
        onChange: function(selectedDates, dateStr, instance) {
          if (!isNotNullOrEmpty($('#eta_actual-input').val())) {
            $('#eta-input').val(dateStr);
          }
        }
      });

      $('#eta_actual-input').flatpickr({
        dateFormat: 'Y-m-d',
        onChange: function(selectedDates, dateStr, instance) {
          $('#eta-input').val(dateStr);
        }
      });

      $('#qty_plan-input').on('change', function() {
        if (!isNotNullOrEmpty($('#qty_actual-input').val())) {
          $('#qty-input').val($(this).val());
        }
      });

      $('#qty_actual-input').on('change', function() {
        $('#qty-input').val($(this).val());
      });

      function initSelect2() {
        if (!$plantSelect.hasClass('select2-hidden-accessible')) {
          $plantSelect.select2({
            placeholder: 'Select Plant ...',
            minimumResultsForSearch: -1,
            dropdownParent: $offCanvas,
            ajax: {
              url: "{{ route('master.plant.index') }}",
              dataType: 'json',
              delay: 250,
              processResults: response => ({
                results: response.data.map(item => ({
                  id: item.id,
                  text: item.description,
                }))
              })
            }
          });
        }

        if (!$supplierSelect.hasClass('select2-hidden-accessible')) {
          $supplierSelect.select2({
            placeholder: 'Select Supplier ...',
            dropdownParent: $offCanvas,
            ajax: {
              url: "{{ route('procurement.supplier.index') }}",
              dataType: 'json',
              delay: 250,
              data: params => ({
                supplier: params.term
              }),
              processResults: response => ({
                results: response.data.map(item => ({
                  id: item.id,
                  text: item.supplier,
                }))
              })
            }
          });
        }
      }

      function initMaterialTypeahead($el) {
        if ($el.data('initialized')) return;

        // Mode Material
        const bloodhoundEngine = new Bloodhound({
          datumTokenizer: Bloodhound.tokenizers.obj.whitespace('material_description'),
          queryTokenizer: Bloodhound.tokenizers.whitespace,
          remote: {
            url: "/master/material?material_description=%QUERY",
            wildcard: '%QUERY',
            transform: function(response) {
              return response.data;
            }
          }
        });

        $el.typeahead({
          hint: true,
          highlight: true,
          minLength: 2,
        }, {
          name: 'material_description',
          display: 'material_description',
          limit: 10,
          source: bloodhoundEngine,
          templates: {
            suggestion: function(data) {
              return `<p>${data.material_description}</p>`;
            },
            notFound: function() {
              return `<p class="text-muted px-2">No results found</p>`;
            }
          }
        }).on('typeahead:select typeahead:autocomplete', function(e, selected) {
          $('#id_material-input').val(selected.id);
        });
        $el.data('initialized', true); // Mark as initialized
      }

      function submitForm(form) {
        showHideBlockUI(true, $form);
        var id = $("#id-input").val();
        var url = id ? "{{ route('procurement.procurement.update', ':id') }}".replace(':id', id) :
          "{{ route('procurement.procurement.store') }}";
        var type = id ? "PUT" : "POST";

        var data = {};
        const formData = $form.serializeArray();

        // Add unchecked checkbox manually
        $form.find('input[type="checkbox"]').each(function() {
          const name = $(this).attr('name');
          const isAlreadyIncluded = formData.some(field => field.name === name);
          if (!isAlreadyIncluded) {
            formData.push({
              name: name,
              value: false
            }); // if checkbox is unchecked, send false
          }
        });
        formData.forEach(function(item) {
          data[item.name] = item.value;
        });

        $.ajax({
          url: url,
          type: type,
          data: JSON.stringify(data),
          headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
          },
          contentType: 'application/json',
          dataType: 'json',
          success: function(response) {
            if (response.success) {
              showSuccessAlert(response.message);
              $('#procurement-data-table').DataTable().ajax.reload(null, false);
            } else {
              showErrorAlert(response.message);
            }
            setForm(null);
            showHideBlockUI(false, $form);

            let canvas = bootstrap.Offcanvas.getInstance($offCanvas);
            canvas?.hide();
          },
          error: function(xhr, status, error) {
            console.error('Procurement Submit Error: ', xhr.responseJSON);
            if (xhr.status == 422 && typeof xhr.responseJSON.errors == 'object') {
              let errors = xhr.responseJSON.errors;
              $.each(errors, function(field, messages) {
                let group = $('#' + field + "-group");
                let input = group.find("input, select");
                input.addClass('is-invalid');
                group.append('<div class="invalid-feedback">' + messages[0] + '</div>');
              });
            } else {
              showErrorAlert(xhr.responseJSON.message);
            }
            showHideBlockUI(false, $form);
          }
        });
      }

      function setForm(data) {
        if (data === null) {
          $form[0].reset();
          $form.find('.select2').val('').trigger('change');
          $('.is-invalid').removeClass('is-invalid');
          $('.invalid-feedback').remove();
        } else {
          $.each(data, function(field, value) {
            let $el = $form.find('#' + field + '-input');
            if (!$el.length || value === null) return true;

            const tag = $el.prop('tagName').toLowerCase();
            if (tag === 'input') { // if group contain input
              const type = $el.attr('type');
              if (type === 'checkbox') { // for RSPO checkbox
                $el.prop('checked', value === true);
              } else {
                $el.val(value);
              }
            } else if (tag === 'textarea') {
              $el.val(value);
            } else if (tag === 'select') {
              let displayText = '';
              if (typeof value === 'object' && 'id' in value) {
                const keys = Object.keys(value).filter(k => k !== 'id');
                displayText = keys.length ? value[keys[0]] : value.id;
              } else {
                displayText = value;
              }

              $el.append(
                $('<option>', {
                  value: value.id,
                  text: displayText,
                  selected: true
                })
              ).trigger('change');
            }
          });
          $('#material-input').val(data.material.material_description); // override logic
        }
      }

      Procurement.fetchProcurement = function(id, callback) {
        showHideBlockUI(true, $form);
        $.ajax({
          url: "{{ route('procurement.procurement.show', ':id') }}".replace(':id', id),
          type: "GET",
          dataType: "json",
          success: function(response) {
            if (response.success) {
              setForm(response.data);

              if (typeof callback === 'function') {
                callback();
              }
            } else {
              showErrorAlert(response.message);
            }
            showHideBlockUI(false, $form);
          },
          error: function(xhr) {
            console.error('Procurement Fetch Error: ', xhr.responseJSON);
            showErrorAlert(xhr.responseJSON.message);
            showHideBlockUI(false, $form);
          }
        });
      }

    });
  </script>
@endpush
