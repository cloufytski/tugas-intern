<div class="offcanvas offcanvas-end" id="offcanvas-plant">
  <div class="offcanvas-header border-bottom">
    <h5 id="offcanvas-plant-title">Plant</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <form class="pt-0 form-block pb-3" id="form-plant">
      @csrf
      <div class="mb-3">
        <input type="hidden" id="id-input" name="id" />
        <label class="form-label" for="plant-input">Plant</label>
        <input type="text" id="plant-input" class="form-control" name="plant" />
      </div>
      <div class="mb-3">
        <label class="form-label" for="plant-description-input">Description</label>
        <input type="text" id="plant-description-input" class="form-control" name="description" />
      </div>
      <div>
        <button type="submit" class="btn btn-primary me-sm-3 me-1" id="plant-submit">Save</button>
        <button type="reset" class="btn btn-label-danger" id="plant-cancel"
          data-bs-dismiss="offcanvas">Cancel</button>
      </div>
    </form>
  </div>
</div>

@push('js')
  <script type="module">
    $(document).ready(function() {
      let $offCanvas = $('#offcanvas-plant');
      let $form = $('#form-plant');

      let fv = FormValidation.formValidation($form[0], {
        fields: {
          plant: {
            validators: {
              notEmpty: {
                message: 'Plant is required',
              },
              integer: {
                message: 'Plant should be number',
              }
            }
          },
          description: {
            validators: {
              notEmpty: {
                message: 'Description is required',
              }
            }
          }
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
        submitPlant($form);
      });

      $(document).on('click', '#plant-cancel', function() {
        fv.resetForm();
      });

      function submitPlant(form) {
        showHideBlockUI(true, $form);
        var id = $("#id-input").val();
        var url = id ? "{{ route('master.plant.update', ':id') }}".replace(':id', id) :
          "{{ route('master.plant.store') }}";
        var type = id ? "PUT" : "POST";

        $.ajax({
          url: url,
          type: type,
          data: form.serialize(),
          success: function(response) {
            if (response.success) {
              $('#plant-data-table').DataTable().ajax.reload(null, false);
              if (id) { // if Edit Plant, reload related Section table
                $('#section-data-table').DataTable().ajax.reload(null, false);
              }
              showSuccessAlert(response.message);
            } else {
              showErrorAlert(response.message);
            }
            form[0].reset();
            showHideBlockUI(false, $form);

            let canvas = bootstrap.Offcanvas.getInstance($offCanvas);
            canvas.hide();
          },
          error: function(xhr, status, error) {
            console.error('Plant Error: ', xhr.responseJSON);
            showErrorAlert(xhr.responseJSON.message);
            showHideBlockUI(false, $form);
          }
        });
      }
    });
  </script>
@endpush
