@php
  use App\Helpers\Helpers;
  $configData = Helpers::appClasses();

  if (isset($pageConfigs)) {
      Helpers::updatePageConfig($pageConfigs);
  }

  /* Display elements */
  $customizerHidden = $customizerHidden ?? '';

@endphp

@extends('layouts/common-master')

@section('layout-content')
  <!-- Content -->
  @yield('content')
  <!--/ Content -->
@endsection
