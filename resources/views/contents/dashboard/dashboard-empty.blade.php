@php
  $container = 'container-fluid';
  $containerNav = 'container-fluid';
@endphp

@extends('layouts/content-navbar-layout')

@section('title', 'Dashboard')

{{-- prettier-ignore-start --}}
@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/select2/select2.scss',
    ])
@endsection

@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/block-ui/block-ui.js',
  ])
@endsection
{{-- prettier-ignore-end --}}

@section('content')
@endsection
