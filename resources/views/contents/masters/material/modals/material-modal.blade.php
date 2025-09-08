<div class="offcanvas offcanvas-end" id="offcanvas-material">
  <div class="offcanvas-header border-bottom">
    <h5 class="offcanvas-title">Material</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <form class="pt-0 form-block pb-3" id="form-material" data-view="false">
      @csrf
      <div class="mb-3">
        <label class="form-label" for="id-input">Id</label>
        <input type="text" class="form-control" id="id-input" name="id" disabled />
      </div>
      <div class="row">
        <div class="col-7 mb-3" id="material-group">
          <label class="form-label" for="material-input">Material</label>
          <input type="text" class="form-control" id="material-input" name="material" />
        </div>
        <div class="col-5 mb-3" id="id_class-group">
          <label class="form-label" for="class-input">Class</label>
          <select class="form-select select2" id="class-input" name="id_class">
            <option value="" selected disabled hidden></option>
          </select>
        </div>
      </div>
      <div class="mb-3" id="material_description-group">
        <label class="form-label" for="material_description-input">Material Description</label>
        <input type="text" class="form-control" id="material_description-input" name="material_description" />
      </div>
      <div class="mb-3" id="id_category-group">
        <label class="form-label" for="product_category-input">Product Category</label>
        <select class="form-select select2" id="product_category-input" name="id_category">
          <option value="" selected disabled hidden></option>
        </select>
      </div>
      <div class="mb-3" id="id_metric-group">
        <label class="form-label" for="product_metric-input">Product Metric</label>
        <select class="form-select select2" id="product_metric-input" name="id_metric">
          <option value="" selected disabled hidden></option>
        </select>
      </div>
      <div class="col mb-3" id="id_group-group">
        <label class="form-label" for="product_group-input">Product Group</label>
        <select class="form-select select2" id="product_group-input" name="id_group">
          <option value="" selected disabled hidden></option>
        </select>
      </div>
      <div class="col mb-3" id="id_group_simple-group">
        <label class="form-label" for="product_group_simple-input">Product Group Simple</label>
        <select class="form-select select2" id="product_group_simple-input" name="id_group_simple">
          <option value="" selected disabled hidden></option>
        </select>
      </div>
      <div class="row">
        <div class="col-5 mb-3" id="id_uom-group">
          <label class="form-label" for="uom-input">UOM</label>
          <select class="form-select select2" id="uom-input" name="id_uom">
            <option value="" selected disabled hidden></option>
          </select>
        </div>
        <div class="col-7 mb-3" id="id_packaging-group">
          <label class="form-label" for="packaging-input">Packaging</label>
          <select class="form-select select2" id="packaging-input" name="id_packaging">
            <option value="" selected disabled hidden></option>
          </select>
        </div>
      </div>
      <div class="row">
        <div class="col mb-3" id="rate-group">
          <label class="form-label" for="rate-input">Rate</label>
          <input type="number" step="any" class="form-control" id="rate-input" name="rate" />
        </div>
        <div class="col mb-3" id="conversion-group">
          <label class="form-label" for="conversion-input">Conversion</label>
          <input type="number" step="any" class="form-control" id="conversion-input" name="conversion" />
        </div>
        <div class="col mb-3" id="space-group">
          <label class="form-label" for="space-input">Space</label>
          <input type="number" step="any" class="form-control" id="space-input" name="space" />
        </div>
      </div>
      <div class="row">
        <div class="col mb-3" id="id_pp_class-group">
          <label class="form-label" for="id_pp_class-input">PP Class</label>
          <select class="form-select select2" id="pp_class-input" name="id_pp_class">
            <option value="" selected disabled hidden></option>
          </select>
        </div>
        <div class="col mb-3" id="id_pv_class-group">
          <label class="form-label" for="id_pv_class-input">PV Class</label>
          <select class="form-select select2" id="pv_class-input" name="id_pv_class">
            <option value="" selected disabled hidden></option>
          </select>
        </div>
      </div>
      <div class="row">
        <div class="col-8 mb-3" id="kind_of_pack-group">
          <label class="form-label" for="kind_of_pack-input">Kind of Pack</label>
          <input type="text" class="form-control" id="kind_of_pack-input" name="kind_of_pack" />
        </div>
        <div class="col-4 mb-3" id="base_price-group">
          <label class="form-label" for="base_price-input">Base Price</label>
          <input type="number" step="any" class="form-control" id="base_price-input" name="base_price" />
        </div>
      </div>
      <div class="row">
        <div class="col-8 mb-3" id="pack_cost-group">
          <label class="form-label" for="pack_cost-input">Pack Cost</label>
          <input type="text" class="form-control" id="pack_cost-input" name="pack_cost" />
        </div>
        <div class="col-4 mb-3" id="devider-group">
          <label class="form-label" for="devider-input">Devider</label>
          <input type="number" step="any" class="form-control" id="devider-input" name="devider" />
        </div>
      </div>
      <div class="mb-3" id="bus_line-group">
        <label class="form-label" for="bus_line-input">Bus Line</label>
        <input type="text" class="form-control" id="bus_line-input" name="bus_line" />
      </div>
      <div class="row">
        <div class="col-8 mb-3" id="rm-group">
          <label class="form-label" for="rm-input">RM</label>
          <input type="text" class="form-control" id="rm-input" name="rm" />
        </div>
        <div class="col-4 mb-3" id="conversion_to_rm-group">
          <label class="form-label" for="conversion_to_rm-input">Coversion to RM</label>
          <input type="text" class="form-control" id="conversion_to_rm-input" name="conversion_to_rm" />
        </div>
      </div>
      <div class="mb-3" id="base_product-group">
        <label class="form-label" for="base_product-input">Base Product</label>
        <input type="text" class="form-control" id="base_product-input" name="base_product" />
      </div>
      <div class="mb-3" id="rumus_molecul-group">
        <label class="form-label" for="rumus_molecul-input">Rumus Molecul</label>
        <input type="text" class="form-control" id="rumus_molecul-input" name="rumus_molecul" />
      </div>
      <div class="mb-3" id="auto_produce-group">
        <label class="form-label" for="auto_produce-input">Auto Produce</label>
        <input type="text" class="form-control" id="auto_produce-input" name="auto_produce" />
      </div>
      <div class="row">
        <div class="col mb-3" id="eudr-group">
          <input class="form-check-input" type="checkbox" value="true" id="eudr-input" name="eudr" />
          <label class="form-check-label" for="eudr-input">EUDR</label>
        </div>
        <div class="col mb-3" id="eudr_sale-group">
          <input class="form-check-input" type="checkbox" value="true" id="eudr_sale-input" name="eudr_sale" />
          <label class="form-check-label" for="eudr_sale-input">EUDR Sale</label>
        </div>
      </div>
      <div class="mb-3" id="hs_code-group">
        <label class="form-label" for="hs_code-input">HS Code</label>
        <input type="text" class="form-control" id="hs_code-input" name="hs_code" />
      </div>

      <div>
        <button type="submit" class="btn btn-primary me-sm-3 me-1 btn-submit">Save</button>
        <button type="reset" class="btn btn-label-danger btn-cancel" data-bs-dismiss="offcanvas">Cancel</button>
      </div>
    </form>
  </div>
</div>

@push('js')
  <script type="module">
    $(document).ready(function() {
      const $offCanvas = $('#offcanvas-material');
      const $form = $('#form-material');

      $offCanvas.on('shown.bs.offcanvas', function() {

        let materialId = $form.find('#id-input').val();
        if (isNotNullOrEmpty(materialId)) {
          fetchMaterial(materialId);
        } else {
          resetForm();
        }

        let isShow = $form.data('view') === true || $form.data('view') === 'true';
        showFormPlainText(isShow);

        initSelect2(
          $(this).find('#class-input'),
          "{{ route('master.material.class.index') }}",
          'class',
          item => ({
            id: item.id,
            text: item.class
          }));

        initSelect2(
          $(this).find('#product_category-input'),
          "{{ route('master.material.category.index') }}",
          'product_category',
          item => ({
            id: item.id,
            text: item.product_category
          }),
        );

        initSelect2(
          $(this).find('#product_metric-input'),
          "{{ route('master.material.metric.index') }}",
          'product_metric',
          item => ({
            id: item.id,
            text: item.product_metric
          }),
        );

        initSelect2(
          $(this).find('#product_group-input'),
          "{{ route('master.material.group.index') }}",
          'product_group',
          item => ({
            id: item.id,
            text: item.product_group
          }));

        initSelect2(
          $(this).find('#product_group_simple-input'),
          "{{ route('master.material.group.simple.index') }}",
          'product_group_simple',
          item => ({
            id: item.id,
            text: item.product_group_simple
          }));

        initSelect2(
          $(this).find('#uom-input'),
          "{{ route('master.material.uom.index') }}",
          'uom',
          item => ({
            id: item.id,
            text: item.uom
          }));

        initSelect2(
          $(this).find('#packaging-input'),
          "{{ route('master.material.packaging.index') }}",
          'packaging',
          item => ({
            id: item.id,
            text: item.packaging
          }));

        initSelect2(
          $(this).find('#pp_class-input'),
          "{{ route('master.material.packaging.class.index') }}",
          'packaging_class',
          item => ({
            id: item.id,
            text: item.packaging_class
          }));

        initSelect2(
          $(this).find('#pv_class-input'),
          "{{ route('master.material.packaging.class.index') }}",
          'packaging_class',
          item => ({
            id: item.id,
            text: item.packaging_class
          }));
      });

      $form.on('submit', function(e) {
        e.preventDefault();

        $form.find('.is-invalid').removeClass('is-invalid');
        $form.find('.invalid-feedback').remove();

        submitMaterial();
      });

      $(document).on('click', '.btn-cancel, .text-reset', function() {
        resetForm();
      });

      function submitMaterial() {
        showHideBlockUI(true, $form);
        var id = $form.find("#id-input").val();
        var url = id ? "{{ route('master.material.update', ':id') }}".replace(':id', id) :
          "{{ route('master.material.store') }}";
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
              $('#material-data-table').DataTable().ajax.reload(null, false);
            } else {
              showErrorAlert(response.message);
            }
            resetForm();
            showHideBlockUI(false, $form);

            let canvas = bootstrap.Offcanvas.getInstance($offCanvas);
            canvas.hide();
          },
          error: function(xhr, status, error) {
            console.error('Material Submit Error: ', xhr.responseJSON);
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

      function resetForm() {
        $form[0].reset();
        $form.find('.select2').val('').trigger('change');
      }

      function fetchMaterial(id) {
        showHideBlockUI(true, $form);
        $.ajax({
          url: "{{ route('master.material.show', ':id') }}".replace(':id', id),
          type: "GET",
          dataType: "json",
          success: function(response) {
            if (response.success) {
              $.each(response.data, function(field, value) {
                let $el = $form.find('#' + field + '-input');
                if (!$el.length || value === null) return true;

                const tag = $el.prop('tagName').toLowerCase();
                if (tag === 'input') { // if group contain input
                  const type = $el.attr('type');
                  if (type === 'checkbox') { // for EUDR
                    $el.prop('checked', value === true);
                  } else {
                    $el.val(value);
                  }
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
            } else {
              showErrorAlert(response.message);
            }
            showHideBlockUI(false, $form);
          },
          error: function(xhr, status, error) {
            console.error('Material Fetch Error: ', xhr.responseJSON);
            showErrorAlert(xhr.responseJSON.message);
            showHideBlockUI(false, $form);
          }
        });
      }

      function showFormPlainText(isView) {
        if (isView) {
          $form.find('select.select2').prop('disabled', true);
          $form.find('input.form-control').removeClass('form-control').addClass('form-control-plaintext').prop(
            'readonly', true);
          $form.find('.btn-submit, .btn-cancel').addClass('d-none');
        } else {
          $form.find('select.select2').prop('disabled', false);
          $form.find('input.form-control-plaintext')
            .removeClass('form-control-plaintext')
            .addClass('form-control')
            .prop('readonly', false);

          $form.find('.btn-submit, .btn-cancel').removeClass('d-none');
        }
      }
    });
  </script>
@endpush
