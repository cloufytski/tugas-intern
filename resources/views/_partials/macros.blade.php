@php
  $width = $width ?? '25';
  $withbg = $withbg ?? '#696cff';
@endphp
{{-- For logo branding https://demos.themeselection.com/sneat-bootstrap-html-admin-template/documentation/laravel-faqs.html#custom-logo --}}
<img src="{{ asset('images/Logo only Transparent.png') }}" width="{{ $width }}" alt="logo">
