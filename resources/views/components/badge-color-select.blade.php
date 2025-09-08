<label class="form-label" for="badge_color-input" id="badge_color-label">Color</label>
<select class="form-select" id="badge_color-input" name="badge_color">
  <option value="" hidden></option>
  <option value="primary"><span class="badge bg-label-primary">Primary</span></option>
  <option value="secondary"><span class="badge bg-label-secondary">Secondary</span></option>
  <option value="success"><span class="badge bg-label-success">Success</span></option>
  <option value="danger"><span class="badge bg-label-danger">Danger</span></option>
  <option value="warning"><span class="badge bg-label-warning">Warning</span></option>
  <option value="info"><span class="badge bg-label-info">Info</span></option>
  <option value="dark"><span class="badge bg-label-dark">Dark</span></option>
</select>

@push('js')
  <script type="module">
    window.BadgeColor = window.BadgeColor || {};
    BadgeColor.setBadgeColor = function(dropdownContainer) {
      $('#badge_color-input').select2({
        dropdownParent: dropdownContainer,
        minimumResultsForSearch: -1,
        escapeMarkup: function(markup) {
          return markup; // allow html
        },
        templateResult: function(data) {
          if (!data.id) {
            return data.text;
          }
          return $(data.element).html();
        },
        templateSelection: function(data) {
          return $(data.element).html();
        }
      });
    }

    BadgeColor.setBadgeColorLabel = function(label) {
      $('#badge_color-label').text(label);
    }
  </script>
@endpush
