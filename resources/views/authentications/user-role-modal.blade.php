<div class="modal fade" id="modal-user-role">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content" id="user-role-block">
      <form id="form-user-role">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Assign Role</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6 mb-4">
              <input type="hidden" id="id-input" />
              <label for="name-input" class="form-label">Name</label>
              <input type="text" id="name-input" class="form-control-plaintext" name="name" readonly>
            </div>
            <div class="col-md-6 mb-4">
              <label for="username-input" class="form-label">Username</label>
              <input type="text" id="username-input" class="form-control-plaintext" name="name" readonly>
            </div>
            <div class="col-md-12 mb-4">
              <label for="email-input" class="form-label">Email</label>
              <input type="email" id="email-input" class="form-control-plaintext" name="email" readonly>
            </div>
            <div class="col-md-12 mb-4">
              <input type="hidden" id="role-input" />
              <label for="role-select" class="form-label">Role</label>
              <select class="form-select" id="role-select" name="roles[]" multiple></select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('js')
  <script type="module">
    $(document).ready(function() {
      const $roleSelect = $('#role-select');
      const $modal = $('#modal-user-role');
      const $form = $('#form-user-role');
      const $block = $('#user-role-block');

      $modal.on('shown.bs.modal', function() {
        initSelect2();
      });

      $form.on('submit', function(e) {
        e.preventDefault();
        const id = $('#id-input').val();
        submitAssignRole(id);
      })

      function initSelect2() {
        if (!$roleSelect.hasClass('select2-hidden-accessible')) {
          fetchRoles((data) => {
            $roleSelect.select2({
              placeholder: 'Select Role ...',
              minimumResultsForSearch: -1,
              dropdownParent: $modal,
              multiple: true,
              allowClear: true,
              data: data.map(item => ({
                id: item.name,
                text: item.display_name,
              }))
            });

            const roleInput = $('#role-input').val();
            if (roleInput && roleInput.includes(',')) {
              $roleSelect.val(roleInput.split(',')).trigger('change');
            } else if (roleInput) {
              $roleSelect.val([roleInput]).trigger('change');
            }
          });
        }
      }

      function fetchRoles(callback) {
        $.get("{{ route('role.index') }}", function(response) {
          if (response.success) {
            const data = response.data;
            if (typeof callback === 'function') {
              callback(data);
            }
          }
        });
      }

      function submitAssignRole(id) {
        showHideBlockUI(true, $block);
        $.ajax({
          url: "{{ route('user.update', ':id') }}".replace(':id', id),
          type: "PUT",
          data: $form.serialize(),
          success: function(response) {
            if (response.success) {
              showSuccessAlert(response.message);
              $('#user-data-table').DataTable().ajax.reload(null, false);
              $modal.modal('hide');
            } else {
              showErrorAlert(response.message);
            }
            showHideBlockUI(false, $block);
          },
          error: function(xhr) {
            console.error('Failed to assign role: ', xhr.responseJSON);
            showErrorAlert(xhr.responseJSON.message);
            showHideBlockUI(false, $block);
          }
        });
      }
    });
  </script>
@endpush
