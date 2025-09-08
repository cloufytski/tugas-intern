<div class="offcanvas offcanvas-end" id="offcanvas-supplier">
  <div class="offcanvas-header border-bottom">
    <h5 id="offcanvas-supplier-title">Supplier</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body mx-0 flex-grow-0">
    <form class="pt-0 row g-2" id="form-supplier">
      @csrf
      <div class="col-sm-12">
        <input type="hidden" id="id-input" />
        <label class="form-label" for="supplier-input">Supplier</label>
        <input type="text" id="supplier-input" class="form-control" name="supplier" />
      </div>
      <div class="col-sm-12">
        <label class="form-label" for="certificate_no-input">Certificate No</label>
        <input type="text" id="certificate_no-input" class="form-control" name="certificate_no" />
      </div>
      <div class="col-sm-12 mt-4">
        <button type="submit" class="btn btn-primary me-3" id="supplier-submit">Submit</button>
        <button type="reset" class="btn btn-label-danger" id="supplier-cancel"
          data-bs-dismiss="offcanvas">Cancel</button>
      </div>
    </form>
  </div>
</div>

@push('js')
  <script type="module">
    $(document).ready(function() {
      let $offCanvas = $('#offcanvas-supplier');
      let $form = $('#form-supplier');

      let fv = FormValidation.formValidation($form[0], {
        fields: {
          supplier: {
            validators: {
              notEmpty: {
                message: 'Supplier is required',
              }
            }
          },
          certificate_no: {
            validators: {
              stringLength: {
                max: 50,
                message: 'Certificate No must be less than 50 characters',
              }
            }
          }
        },
        plugins: {
          trigger: new FormValidation.plugins.Trigger(),
          bootstrap5: new FormValidation.plugins.Bootstrap5({
            eleValidClass: '',
            rowSelector: '.col-sm-12',
          }),
          submitButton: new FormValidation.plugins.SubmitButton(),
          autoFocus: new FormValidation.plugins.AutoFocus()
        },
      });
      $form[0].formValidationInstance = fv;

      fv.on('core.form.valid', function() {
        submitSupplier($form);
      });

      $(document).on('click', '#supplier-cancel', function() {
        fv.resetForm();
      });

      function submitSupplier(form) {
        var id = $("#id-input").val();
        // Determine whether CREATE or UPDATE
        var url = id ?
          "{{ route('procurement.supplier.update', ':id') }}".replace(':id', id) :
          "{{ route('procurement.supplier.store') }}";
        var type = id ? "PUT" : "POST";

        $.ajax({
          url: url,
          type: type,
          data: form.serialize(),
          dataType: "json",
          success: function(response) {
            if (response.success) {
              showSuccessAlert(response.message);
              $('#supplier-data-table').DataTable().ajax.reload(null, false);
            }
            form[0].reset();
            let canvas = bootstrap.Offcanvas.getInstance($offCanvas[0]);
            canvas.hide();
          },
          error: function(xhr, status, error) {
            console.error('Supplier Update Error: ', error);
            showErrorAlert(xhr.responseJSON.message);
          }
        });
      }
    });
  </script>
@endpush
