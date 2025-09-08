@php
  use App\Helpers\Helpers;
  $configData = Helpers::appClasses();
  $container = 'container-fluid';
  $containerNav = 'container-fluid';
@endphp

@extends('layouts/layout-master')

@section('title', 'Raw Material Dashboard')

{{-- prettier-ignore-start --}}
@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/animate-css/animate.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
    'resources/assets/vendor/libs/apex-charts/apex-charts.scss',
    'resources/assets/vendor/libs/flatpickr/flatpickr.scss'
  ])
@endsection

@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/block-ui/block-ui.js',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
    'resources/assets/vendor/libs/dayjs/dayjs.js',
    'resources/assets/vendor/libs/apex-charts/apex-charts.js',
    'resources/assets/vendor/libs/flatpickr/flatpickr.js',
  ])
@endsection
{{-- prettier-ignore-end --}}

@section('content')
  <div class="row gy-4">
    <div class="col-12">
      @include('materialproc::contents.dashboard.components.material-total-card')
    </div>
  </div>
@endsection
