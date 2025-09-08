<div class="offcanvas offcanvas-end" id="offcanvas-section">
  <div class="offcanvas-header border-bottom">
    <h5 id="offcanvas-section-title">Section</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <form class="pt-0 pb-3 form-block" id="form-section">
      @csrf
      <div class="mb-3">
        <input type="hidden" id="id-input" name="id" />
        <label class="form-label" for="plant-select">Plant</label>
        <select class="select2 form-select" id="plant-select" name="id_plant">
          <option value="" selected disabled hidden></option>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label" for="section-input">Section</label>
        <input type="text" id="section-input" class="form-control" name="section" />
      </div>
      <div class="mb-3">
        <label class="form-label" for="section-description-input">Description</label>
        <input type="text" id="section-description-input" class="form-control" name="description" />
      </div>
      <div>
        <button type="submit" class="btn btn-primary me-sm-3 me-1" id="section-submit">Save</button>
        <button type="reset" class="btn btn-label-danger" id="section-cancel"
          data-bs-dismiss="offcanvas">Cancel</button>
      </div>
    </form>
  </div>
</div>

@push('js')
  <script type="module">
    $(document).ready(function() {
      let $offCanvas = $('#offcanvas-section');
      let $form = $('#form-section');
      $offCanvas.on('shown.bs.offcanvas', function() {
        $('.select2#plant-select').select2({ // init Select2 after Offcanvas is shown
          placeholder: 'Select Plant',
          minimumResultsForSearch: -1, // hide search box
          dropdownParent: $offCanvas,
        });
        fetchPlant();

        let sectionId = $("#id-input").val();
        fetchSection(sectionId);
      });

      let fv = FormValidation.formValidation($form[0], {
        fields: {
          id_plant: {
            validators: {
              notEmpty: {
                message: 'Plant is required',
              }
            }
          },
          section: {
            validators: {
              notEmpty: {
                message: 'Section is required',
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
        submitSection($form);
      });

      $(document).on('click', '#section-cancel', function() {
        fv.resetForm();
      });

      function fetchPlant() {
        showHideBlockUI(true, $form);
        $.ajax({
          url: "{{ route('master.plant.index') }}",
          method: "GET",
          success: function(response) {
            let $select = $('#plant-select');
            $select.empty();
            $select.append(
              $('<option>', {
                value: '',
                text: ''
              })
            );

            response.data.forEach(plant => {
              $select.append(
                $('<option>', {
                  value: plant.id,
                  text: plant.description
                })
              );
            });
            $select.trigger('change');
            showHideBlockUI(false, $form);
          },
          error: function(xhr, status, error) {
            console.error('Failed to load Plant: ', xhr.responseJSON);
            showHideBlockUI(false, $form);
          }
        });
      }

      function submitSection(form) {
        showHideBlockUI(true, $form);
        var id = $("#id-input").val();
        var url = id ? "{{ route('master.section.update', ':id') }}".replace(':id', id) :
          "{{ route('master.section.store') }}";
        var type = id ? "PUT" : "POST";

        $.ajax({
          url: url,
          type: type,
          data: form.serialize(),
          success: function(response) {
            if (response.success) {
              showSuccessAlert(response.message);
              $('#section-data-table').DataTable().ajax.reload(null, false);
            } else {
              showErrorAlert(response.message);
            }
            form[0].reset();
            showHideBlockUI(false, $form);

            let canvas = bootstrap.Offcanvas.getInstance($offCanvas);
            canvas.hide();
          },
          error: function(xhr, status, error) {
            console.error('Section Submit Error: ', xhr.responseJSON);
            showErrorAlert(xhr.responseJSON.message);
            showHideBlockUI(false, $form);
          }
        });
      }

      function fetchSection(id) {
        showHideBlockUI(true, $form);
        $.ajax({
          url: "{{ route('master.section.show', ':id') }}".replace(':id', id),
          type: "GET",
          contentType: "application/json",
          success: function(response) {
            if (response.success) {
              let data = response.data;
              $('#plant-select').val(data.plant.id).trigger('change');
              $('#section-input').val(data.section);
              $('#section-description-input').val(data.description);
              showHideBlockUI(false, $form);
            }
          },
          error: function(xhr, status, error) {
            console.error('Failed to load Section: ', xhr.responseJSON);
            showHideBlockUI(false, $form);
          }
        });
      }
    });
  </script>
@endpush
