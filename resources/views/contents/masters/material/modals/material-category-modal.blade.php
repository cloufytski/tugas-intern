<div class="offcanvas offcanvas-end" id="offcanvas-category">
  <div class="offcanvas-header border-bottom">
    <h5 id="offcanvas-category-title">Class</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <form class="pt-0 form-block pb-3" id="form-category">
      @csrf
      <div class="mb-3">
        <input type="hidden" id="id-input" name="id" />
        <label class="form-label" for="category-input">Product Category</label>
        <input type="text" id="category-input" class="form-control" name="product_category" />
      </div>
      <div>
        <button type="submit" class="btn btn-primary me-sm-3 me-1" id="category-submit">Save</button>
        <button type="reset" class="btn btn-label-danger" id="category-cancel"
          data-bs-dismiss="offcanvas">Cancel</button>
      </div>
    </form>
  </div>
</div>

@push('js')
  <script type="module">
    $(document).ready(function() {
      let $form = $('#form-category');
      const $offCanvas = $('#offcanvas-category');

      let fv = FormValidation.formValidation($form[0], {
        fields: {
          product_category: {
            validators: {
              notEmpty: {
                message: 'Category is required',
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

      $(document).on('click', '#category-cancel, .text-reset', function() {
        fv.resetForm();
      });

      function submitForm(form) {
        showHideBlockUI(true, $form);
        var id = $("#id-input").val();
        var url = id ? "{{ route('master.material.category.update', ':id') }}".replace(':id', id) :
          "{{ route('master.material.category.store') }}";
        var type = id ? "PUT" : "POST";

        $.ajax({
          url: url,
          type: type,
          data: form.serialize(),
          success: function(response) {
            if (response.success) {
              showSuccessAlert(response.message);
              $('#category-data-table').DataTable().ajax.reload(null, false);
            } else {
              showErrorAlert(response.message);
            }
            form[0].reset();
            showHideBlockUI(false, $form);

            let canvas = bootstrap.Offcanvas.getInstance($offCanvas);
            canvas.hide();
          },
          error: function(xhr, status, error) {
            console.error('Material Category Submit Error: ', xhr.responseJSON);
            showErrorAlert(xhr.responseJSON.message);
            showHideBlockUI(false, $form);
          }
        });
      }
    });
  </script>
@endpush
