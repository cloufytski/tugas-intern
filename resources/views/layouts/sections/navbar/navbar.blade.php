@php
  use Illuminate\Support\Facades\Auth;
  use Illuminate\Support\Facades\Route;
  $containerNav = $configData['contentLayout'] === 'compact' ? 'container-xxl' : 'container-fluid';
  $navbarDetached = $navbarDetached ?? '';
@endphp

<!-- Navbar -->
@if (isset($navbarDetached) && $navbarDetached == 'navbar-detached')
  <nav
    class="layout-navbar {{ $containerNav }} navbar navbar-expand-xl {{ $navbarDetached }} align-items-center bg-navbar-theme"
    id="layout-navbar">
@endif
@if (isset($navbarDetached) && $navbarDetached == '')
  <nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
    <div class="{{ $containerNav }}">
@endif

<!--  Brand demo (display only for navbar-full and hide on below xl) -->
@if (isset($navbarFull))
  <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4">
    <a href="{{ url('/') }}" class="app-brand-link gap-2">
      <span class="app-brand-logo demo">@include('_partials.macros', ['width' => 25, 'withbg' => 'var(--bs-primary)'])</span>
      <span class="app-brand-text demo menu-text fw-bold text-heading">{{ config('variables.templateName') }}</span>
    </a>

    @if (isset($menuHorizontal))
      <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
        <i class="bx bx-chevron-left bx-sm d-flex align-items-center justify-content-center"></i>
      </a>
    @endif
  </div>
@endif

<!-- ! Not required for layout-without-menu -->
@if (!isset($navbarHideToggle))
  <div
    class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0{{ isset($menuHorizontal) ? ' d-xl-none ' : '' }} {{ isset($contentNavbar) ? ' d-xl-none ' : '' }}">
    <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
      <i class="bx bx-menu bx-md"></i>
    </a>
  </div>
@endif

<div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
  <ul class="navbar-nav flex-row align-items-center ms-auto">
    @if ($configData['hasCustomizer'] == true)
      <!-- Style Switcher -->
      <li class="nav-item dropdown-style-switcher dropdown me-2 me-xl-0">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
          <i class='bx bx-md'></i>
        </a>
        <ul class="dropdown-menu dropdown-menu-end dropdown-styles">
          <li>
            <a class="dropdown-item" href="javascript:void(0);" data-theme="light">
              <span><i class='bx bx-sun bx-md me-3'></i>Light</span>
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="javascript:void(0);" data-theme="dark">
              <span><i class="bx bx-moon bx-md me-3"></i>Dark</span>
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="javascript:void(0);" data-theme="system">
              <span><i class="bx bx-desktop bx-md me-3"></i>System</span>
            </a>
          </li>
        </ul>
      </li>
      <!--/ Style Switcher -->
    @endif

    <!-- User -->
    <li class="nav-item navbar-dropdown dropdown-user dropdown ms-2">
      <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
        <div class="avatar">
          <span class="avatar-initial rounded-circle bg-label-primary" id="avatar-initial-id"
            data-name="{{ Auth::user()->name }}"></span>
        </div>
      </a>
      <ul class="dropdown-menu dropdown-menu-end">
        <li>

          <div class="d-flex align-items-center py-1">
            <div class="flex-shrink-0 mx-3 my-2">
              <div class="avatar">
                <span class="avatar-initial rounded-circle bg-label-primary"></span>
              </div>
            </div>
            <div class="flex-grow-1">
              <h6 class="mb-0">{{ Auth::user()->name ?? 'Guest' }}</h6>
              <small
                class="text-muted">{{ Auth::user()->roles->pluck('display_name')->implode(', ') ?: 'No roles' }}</small>
            </div>
          </div>
        </li>
        <li>
          <div class="dropdown-divider my-1"></div>
        </li>
        @permission('developer-update')
          <li class="text-center">
            <small>{{ Route::currentRouteName() }}</small>
          </li>
          <li>
            <div class="dropdown-divider my-1"></div>
          </li>
        @endpermission
        <li>
          <a class="dropdown-item" href="/docs" target="_blank">
            <i class="flex-shrink-0 bx bx-food-menu bx-md me-3"></i>
            <span class="flex-grow-1 align-middle">Manuals</span>
          </a>
          <a class="dropdown-item" href="{{ route('settings.account.view') }}">
            <i class="flex-shrink-0 bx bx-cog bx-md me-3"></i>
            <span class="flex-grow-1 align-middle">Settings</span>
          </a>
        </li>
        <li>
          <div class="dropdown-divider my-1"></div>
        </li>
        @if (Auth::check())
          <li>
            <a class="dropdown-item logout" style="cursor: pointer;">
              <i class='text-danger text-opacity-75 bx bx-power-off bx-md me-3'></i><span>Logout</span>
            </a>
          </li>
        @else
          <li>
            <a class="dropdown-item" href="{{ Route::has('login') ? route('login.view') : url('/login') }}">
              <i class='bx bx-log-in bx-md me-3'></i><span>Login</span>
            </a>
          </li>
        @endif
      </ul>
    </li>
    <!--/ User -->
  </ul>
</div>
@if (isset($navbarDetached) && $navbarDetached == '')
  </div>
@endif
</nav>
<!-- / Navbar -->

@push('js')
  <script type="module">
    $(document).ready(function() {
      $(document).on('click', '.logout', function() {
        $.ajax({
          url: "{{ route('logout') }}",
          type: "POST",
          contentType: "application/json",
          dataType: "json",
          success: function(response) {
            if (response.success) {
              window.location.href = "{{ route('login.view') }}";
            } else {
              showErrorAlert(response.message);
            }
          },
          error: function(xhr) {
            console.error('Logout failed: ', xhr.responseJSON);
          }
        })
      });

      // set avatar initial
      var fullName = $("#avatar-initial-id").data('name') || '';
      var initials = fullName.split(' ').map(word => word.charAt(0)).filter(Boolean).slice(0, 2).join('')
        .toUpperCase();
      $('.avatar-initial').text(initials);
    });
  </script>
@endpush
