@php
  use App\Helpers\Helpers;

  $configData = Helpers::appClasses();
  $container = 'container-fluid';
  $containerNav = 'container-fluid';
@endphp

@extends('layouts/layout-master')

@section('title', 'Inventory Checkpoint Master')

{{-- prettier-ignore-start --}}
@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/jspreadsheet/jspreadsheet.scss',
    'resources/assets/vendor/libs/animate-css/animate.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
    'resources/assets/vendor/libs/select2/select2.scss',
  ])
@endsection

@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/jspreadsheet/jspreadsheet.js',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
    'resources/assets/vendor/libs/dayjs/dayjs.js',
    'resources/assets/vendor/libs/block-ui/block-ui.js',
    'resources/assets/vendor/libs/select2/select2.js',
  ])
@endsection
{{-- prettier-ignore-end --}}

@section('content')
  <div class="card">
    <div class="card-header header-elements">
      <h5 class="card-header-title mb-0 me-2">Inventory Checkpoints</h5>
    </div>
    <div class="card-body">
      @include('invbalance::contents.master.excels.inventory-checkpoint-excel')
    </div>
  </div>
@endsection
