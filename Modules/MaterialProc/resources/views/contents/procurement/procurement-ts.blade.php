@php
  $container = 'container-fluid';
  $containerNav = 'container-fluid';
@endphp

@extends('layouts/layout-master')

@section('title', 'Procurement')

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
  <div class="card d-flex flex-column" style="min-height: 70vh;">
    <div class="card-header header-elements border-bottom py-4">
      <h5 class="mb-0 me-2">Receipt</h5>
      <div class="card-header-elements ms-auto">
        @permission('procurement-create')
          <button type="button" class="btn btn-sm btn-primary procurement-add" data-bs-toggle="offcanvas"
            data-bs-target="#offcanvas-procurement">
            <i class="bx bx-plus bx-sm me-0 me-sm-2"></i><span>Add Procurement</span>
          </button>
        @endpermission
      </div>
    </div>
    <div class="card-body">
      @include('materialproc::contents.procurement.partials.procurement-filter')
      @include('materialproc::contents.procurement.datatables.procurement-table')
    </div>
  </div>

  @include('materialproc::contents.procurement.modals.procurement-modal')
@endsection
