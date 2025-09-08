<script>
  let userPreferences = {};

  window.UserPreferences = window.UserPreferences || {};

  @if (Auth::check())
    let userPrefJson = @json(Auth::user()->preferences); // User Preferences from BE

    userPrefJson.forEach(pref => {
      if (!userPreferences[pref.menu]) {
        userPreferences[pref.menu] = {};
      }
      userPreferences[pref.menu][pref.filter_tag] = pref.value;
    });
  @endif

  window.getPrefValue = function(menu, field, filter_tag = 'default') {
    return userPreferences[menu]?.[filter_tag]?.[field] ?? null;
  };

  UserPreferences.updateUserPreferences = function(data) {
    data.forEach(pref => {
      if (!userPreferences[pref.menu]) {
        userPreferences[pref.menu] = {};
      }
      userPreferences[pref.menu][pref.filter_tag] = pref.value;
    });
  }

  function parseValue(pref, menu) {
    try {
      return JSON.parse(pref.value);
    } catch {
      console.error('set pref value error', menu);
      return {};
    }
  }
</script>
