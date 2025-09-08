@php
  use App\Helpers\Helpers;
  $configData = Helpers::appClasses();

  if (isset($pageConfigs)) {
      Helpers::updatePageConfig($pageConfigs);
  }
@endphp

@isset($configData['layout'])
  @include(
      $configData['layout'] === 'horizontal'
          ? 'layouts.horizontal-layout'
          : ($configData['layout'] === 'blank'
              ? 'layouts.blank-layout'
              : ($configData['layout'] === 'front'
                  ? 'layouts.content-navbar-layout'
                  : 'layouts.content-navbar-layout')))
@endisset
