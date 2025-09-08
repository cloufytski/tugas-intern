@php
  $container = 'container-fluid';
  $containerNav = 'container-fluid';
@endphp

@extends('layouts/content-navbar-layout')

@section('title', 'Material Master')

{{-- prettier-ignore-start --}}
@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
    'resources/assets/vendor/libs/select2/select2.scss',
    'resources/assets/vendor/libs/animate-css/animate.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
    'resources/assets/vendor/libs/@form-validation/form-validation.scss',
    ])
@endsection

@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
    'resources/assets/vendor/libs/datatables-buttons-bs5/datatables-buttons.js',
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
    'resources/assets/vendor/libs/@form-validation/popular.js',
    'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
    'resources/assets/vendor/libs/@form-validation/auto-focus.js',
    'resources/assets/vendor/libs/block-ui/block-ui.js',
  ])
@endsection
{{-- prettier-ignore-end --}}

@section('content')
  <div class="nav-align-left nav-tabs-shadow">
    <ul class="nav nav-tabs" role="tablist">
      <li class="nav-item">
        <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
          data-bs-target="#navs-left-align-material" aria-controls="navs-left-align-material" aria-selected="true">
          Material
        </button>
      </li>
      <li class="nav-item">
        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
          data-bs-target="#navs-left-align-group-simple" aria-controls="navs-left-align-group-simple"
          aria-selected="false">
          Group Simple
        </button>
      </li>
      <li class="nav-item">
        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
          data-bs-target="#navs-left-align-group" aria-controls="navs-left-align-group" aria-selected="false">
          Group
        </button>
      </li>
      <li class="nav-item">
        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
          data-bs-target="#navs-left-align-metric" aria-controls="navs-left-align-metric" aria-selected="false">
          Metric
        </button>
      </li>
      <li class="nav-item">
        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
          data-bs-target="#navs-left-align-category" aria-controls="navs-left-align-category" aria-selected="false">
          Category
        </button>
      </li>
      <li class="nav-item">
        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
          data-bs-target="#navs-left-align-class" aria-controls="navs-left-align-class" aria-selected="false">
          Class
        </button>
      </li>
      <li class="nav-item">
        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-left-align-uom"
          aria-controls="navs-left-align-uom" aria-selected="false">
          UOM
        </button>
      </li>
      <li class="nav-item">
        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
          data-bs-target="#navs-left-align-packaging" aria-controls="navs-left-align-packaging" aria-selected="false">
          Packaging
        </button>
      </li>
      <li class="nav-item">
        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
          data-bs-target="#navs-left-align-packaging-class" aria-controls="navs-left-align-packaging-class"
          aria-selected="false">
          Packaging Class
        </button>
      </li>
    </ul>
    <div class="tab-content" style="min-height: 80vh;">
      <div class="tab-pane fade show active" id="navs-left-align-material">
        @include('contents.masters.material.datatables.material-table')
      </div>
      <div class="tab-pane fade" id="navs-left-align-group-simple">
        @include('contents.masters.material.datatables.material-group-simple-table')
      </div>
      <div class="tab-pane fade" id="navs-left-align-group">
        @include('contents.masters.material.datatables.material-group-table')
      </div>
      <div class="tab-pane fade" id="navs-left-align-metric">
        @include('contents.masters.material.datatables.material-metric-table')
      </div>
      <div class="tab-pane fade" id="navs-left-align-category">
        @include('contents.masters.material.datatables.material-category-table')
      </div>
      <div class="tab-pane fade" id="navs-left-align-class">
        @include('contents.masters.material.datatables.material-class-table')
      </div>
      <div class="tab-pane fade" id="navs-left-align-uom">
        @include('contents.masters.material.datatables.material-uom-table')
      </div>
      <div class="tab-pane fade" id="navs-left-align-packaging">
        @include('contents.masters.material.datatables.material-packaging-table')
      </div>
      <div class="tab-pane fade" id="navs-left-align-packaging-class">
        @include('contents.masters.material.datatables.material-packaging-class-table')
      </div>
    </div>
  </div>

  @include('contents.masters.material.modals.material-packaging-class-modal')
  @include('contents.masters.material.modals.material-packaging-modal')
  @include('contents.masters.material.modals.material-uom-modal')
  @include('contents.masters.material.modals.material-class-modal')
  @include('contents.masters.material.modals.material-category-modal')
  @include('contents.masters.material.modals.material-metric-modal')
  @include('contents.masters.material.modals.material-group-modal')
  @include('contents.masters.material.modals.material-group-simple-modal')
  @include('contents.masters.material.modals.material-modal')
@endsection

@push('js')
  <script>
    window.initSelect2 = function(
      $element,
      url,
      queryParam = 'term',
      resultMapper = item => ({
        id: item.id,
        text: item.name | item.text | item.label | item.description
      }),
      placeholder = 'Search...') {
      if (!$element.hasClass('select2-hidden-accessible')) {
        $element.select2({
          placeholder: placeholder,
          dropdownParent: $element.closest('.offcanvas'),
          ajax: {
            url: url,
            dataType: 'json',
            delay: 250,
            data: params => ({
              [queryParam]: params.term
            }),
            processResults: response => ({
              results: response.data.map(resultMapper)
            })
          }
        });
      }
    }
  </script>
@endpush
