@php
  $container = 'container-fluid';
  $containerNav = 'container-fluid';
@endphp

@extends('layouts/layout-master')

@section('title', 'Supplier Master')

{{-- prettier-ignore-start --}}
@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
    'resources/assets/vendor/libs/animate-css/animate.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
    'resources/assets/vendor/libs/@form-validation/form-validation.scss',
    ])
@endsection

@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
    'resources/assets/vendor/libs/datatables-buttons-bs5/datatables-buttons.js',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
    'resources/assets/vendor/libs/@form-validation/popular.js',
    'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
    'resources/assets/vendor/libs/@form-validation/auto-focus.js',
  ])
@endsection
{{-- prettier-ignore-end --}}

@section('content')
  <div class="nav-align-top">
    <ul class="nav nav-pills mb-4" role="tablist">
      <li class="nav-item">
        <button class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#nav-pills-top-supplier"
          aria-controls="nav-pills-top-supplier" aria-selected="true">
          Supplier
        </button>
      </li>
    </ul>
    <div class="tab-content">
      <div class="tab-pane fade show active" id="nav-pills-top-supplier" role="tabpanel">
        @include('materialproc::contents.supplier.datatables.supplier-table')
      </div>
    </div>
  </div>

  @include('materialproc::contents.supplier.modals.supplier-modal')

@endsection
