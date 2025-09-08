@php
  use App\Helpers\Helpers;

  $configData = Helpers::appClasses();
  $container = 'container-fluid';
  $containerNav = 'container-fluid';
@endphp

@extends('layouts/layout-master')

@section('title', 'Log Transaction')

{{-- prettier-ignore-start --}}
@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
    ])
@endsection

@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
    'resources/assets/vendor/libs/dayjs/dayjs.js',
  ])
@endsection
{{-- prettier-ignore-end --}}

@section('content')
  <div class="nav-align-top nav-tabs-shadow">
    <ul class="nav nav-tabs" role="tablist">
      <li class="nav-item">
        <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-transaction"
          aria-controls="navs-transaction" aria-selected="true">
          Transaction
        </button>
      </li>
      <li class="nav-item">
        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-module"
          aria-controls="navs-module" aria-selected="false">
          Module
        </button>
      </li>
    </ul>
    <div class="tab-content">
      <div class="tab-pane fade show active" id="navs-transaction" role="tabpanel">
        @include('contents.logs.datatables.log-transaction-table')
      </div>
      <div class="tab-pane fade" id="navs-module" role="tabpanel">
        @include('contents.logs.datatables.log-module-table')
      </div>
    </div>
  </div>
@endsection
