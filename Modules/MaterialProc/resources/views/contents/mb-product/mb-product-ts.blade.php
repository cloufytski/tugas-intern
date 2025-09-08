@php
  $container = 'container-fluid';
  $containerNav = 'container-fluid';
@endphp

@extends('layouts/layout-master')

@section('title', 'MB Product')

{{-- prettier-ignore-start --}}
@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
    'resources/assets/vendor/libs/animate-css/animate.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
    'resources/assets/vendor/libs/select2/select2.scss',
    'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
    'resources/assets/vendor/libs/typeahead-js/typeahead.scss',
    ])
@endsection

@section('vendor-script')
  @vite([
   'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
    'resources/assets/vendor/libs/datatables-buttons-bs5/datatables-buttons.js',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/bloodhound/bloodhound.js',
    'resources/assets/vendor/libs/typeahead-js/typeahead.js',
    'resources/assets/vendor/libs/block-ui/block-ui.js',
    'resources/assets/vendor/libs/flatpickr/flatpickr.js',
    'resources/assets/vendor/libs/dayjs/dayjs.js',
    'resources/assets/vendor/libs/autosize/autosize.js',
    ])
@endsection
{{-- prettier-ignore-end --}}

@section('content')
  <div class="nav-align-top nav-tabs-shadow">
    <!-- Tabs -->
    <ul class="nav nav-tabs nav-fill" role="tablist">
      <li class="nav-item">
        <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
          data-bs-target="#navs-justified-input-product" aria-controls="navs-justified-input-product" aria-selected="true">
          <span class="d-none d-sm-inline-flex align-items-center">
            <i class="icon-base bx bx-package icon-sm me-1_5"></i>Input Products
          </span>
          <i class="icon-base bx bx-package icon-sm d-sm-none"></i>
        </button>
      </li>
      <li class="nav-item">
        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
          data-bs-target="#navs-justified-supplier" aria-controls="navs-justified-supplier" aria-selected="false">
          <span class="d-none d-sm-inline-flex align-items-center">
            <i class="icon-base bx bx-info-circle icon-sm me-1_5"></i>Supplier Details
          </span>
          <i class="icon-base bx bx-info-circle icon-sm d-sm-none"></i>
        </button>
      </li>
      <li class="nav-item">
        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
          data-bs-target="#navs-justified-output-product" aria-controls="navs-justified-output-product"
          aria-selected="false">
          <span class="d-none d-sm-inline-flex align-items-center">
            <i class="icon-base bx bx-box icon-sm me-1_5"></i>Output Products
          </span>
          <i class="icon-base bx bx-box icon-sm d-sm-none"></i>
        </button>
      </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content">
      <div class="tab-pane fade show active" id="navs-justified-input-product" role="tabpanel">
        @include('materialproc::contents.mb-product.partials.mbProduct-filter')
        <p>
          @include('materialproc::contents.mb-product.components.input-products')
        </p>
      </div>
      <div class="tab-pane fade" id="navs-justified-supplier" role="tabpanel">
        <p>
          @include('materialproc::contents.mb-product.components.supplier-details')
        </p>
      </div>
      <div class="tab-pane fade" id="navs-justified-output-product" role="tabpanel">
        <p>
          @include('materialproc::contents.mb-product.components.output-products')
        </p>
      </div>
    </div>
  </div>
@endsection
