@php
  use App\Helpers\Helpers;
  $configData = Helpers::appClasses();
  $container = 'container-fluid';
  $containerNav = 'container-fluid';
@endphp

@extends('layouts/layout-master')

@section('title', 'Inventory Balance')

{{-- prettier-ignore-start --}}
@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/jspreadsheet/jspreadsheet.scss',
    'resources/assets/vendor/libs/animate-css/animate.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
    'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
    'resources/assets/vendor/libs/select2/select2.scss',
    'resources/assets/vendor/libs/spinkit/spinkit.scss',
  ])
@endsection

@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/jspreadsheet/jspreadsheet.js',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
    'resources/assets/vendor/libs/flatpickr/flatpickr.js',
    'resources/assets/vendor/libs/block-ui/block-ui.js',
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/dayjs/dayjs.js',
    'resources/assets/vendor/libs/jquery-sticky/jquery-sticky.js',
  ])
@endsection

@section('page-script')
  @vite([
    'resources/assets/js/cards-actions.js',
    'resources/assets/js/sticky.js',
  ])
@endsection
{{-- prettier-ignore-end --}}

@section('content')
  <div class="card card-action my-4">
    <div class="card-header pb-1">
      <h5 class="card-action-title mb-0">Inventory Balance</h5>
      <div class="card-action-element">
        <ul class="list-inline mb-0">
          <li class="list-inline-item">
            @permission(['inventory-balance-update'])
              <a href="javascript:void(0);" class="card-reload me-2">
                <i class="tf-icons bx bx-rotate-left scaleX-n1-rtl"></i>
              </a>
            @endpermission
            <a href="javascript:void(0);" class="card-expand">
              <i class="tf-icons bx bx-fullscreen"></i>
            </a>
          </li>
        </ul>
      </div>
    </div>
    <div class="card-body py-3" id="tab-content-block">
      @include('invbalance::contents.inventory-balance.excels.inventory-balance-excel')
    </div>
  </div>
  @include('invbalance::contents.inventory-balance.modals.inventory-filter-offcanvas')

@endsection
