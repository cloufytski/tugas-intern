<div class="offcanvas offcanvas-end" id="offcanvas-packaging">
  <div class="offcanvas-header border-bottom">
    <h5 class="offcanvas-title">Packaging</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <form class="pt-0 form-block pb-3" id="form-packaging">
      @csrf
      <div class="mb-3">
        <input type="hidden" id="id-input" name="id" />
        <label class="form-label" for="packaging-input">Packaging</label>
        <input type="text" id="packaging-input" class="form-control" name="packaging" />
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
      let $form = $('#form-packaging');
      const $offCanvas = $('#offcanvas-packaging');

      let fv = FormValidation.formValidation($form[0], {
        fields: {
          packaging: {
            validators: {
              notEmpty: {
                message: 'Packaging is required',
              }
            }
          },
        },
        plugins: {
          trigger: new FormValidation.plugins.Trigger(),
          bootstrap5: new FormValidation.plugins.Bootstrap5({
            eleValidClass: '',
            rowSelector: '.mb-3',
          }),
          submitButton: new FormValidation.plugins.SubmitButton(),
          autoFocus: new FormValidation.plugins.AutoFocus()
        },
      });
      $form[0].formValidationInstance = fv;

      fv.on('core.form.valid', function() {
        submitForm($form);
      });

      $(document).on('click', '.btn-cancel, .text-reset', function() {
        fv.resetForm();
      });

      function submitForm(form) {
        showHideBlockUI(true, $form);
        var id = $form.find("#id-input").val();
        var url = id ? "{{ route('master.material.packaging.update', ':id') }}".replace(':id', id) :
          "{{ route('master.material.packaging.store') }}";
        var type = id ? "PUT" : "POST";

        $.ajax({
          url: url,
          type: type,
          data: form.serialize(),
          success: function(response) {
            if (response.success) {
              showSuccessAlert(response.message);
              $('#packaging-data-table').DataTable().ajax.reload(null, false);
            } else {
              showErrorAlert(response.message);
            }
            form[0].reset();
            showHideBlockUI(false, $form);

            let canvas = bootstrap.Offcanvas.getInstance($offCanvas);
            canvas.hide();
          },
          error: function(xhr, status, error) {
            console.error('Material Packaging Submit Error: ', xhr.responseJSON);
            showErrorAlert(xhr.responseJSON.message);
            showHideBlockUI(false, $form);
          }
        });
      }
    });
  </script>
@endpush
