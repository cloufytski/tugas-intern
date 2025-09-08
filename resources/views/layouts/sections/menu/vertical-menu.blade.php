@php
  use Illuminate\Support\Facades\Route;
  use App\Helpers\Helpers;
  $configData = Helpers::appClasses();
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

  <!-- ! Hide app brand if navbar-full -->
  @if (!isset($navbarFull))
    <div class="app-brand demo">
      <a href="{{ url('/') }}" class="app-brand-link">
        <span class="app-brand-logo demo">@include('_partials.macros', ['width' => 28, 'withbg' => 'var(--bs-primary)'])</span>
        <span class="app-brand-text demo menu-text fw-bold ms-2">{{ config('variables.templateName') }}</span>
      </a>

      <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
        <i class="bx bx-chevron-left bx-sm d-flex align-items-center justify-content-center"></i>
      </a>
    </div>
  @endif

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">
    @foreach ($menuData[0]->menu as $menu)
      {{-- adding active and open class if child is active --}}

      {{-- menu headers --}}
      @if (isset($menu->menuHeader))
        @if (empty($menu->permission) || auth()->user()->hasPermission($menu->permission))
          <li class="menu-header small text-uppercase">
            <span class="menu-header-text">{{ __($menu->menuHeader) }}</span>
          </li>
        @endif
      @else
        {{-- active menu method --}}
        @php
          $activeClass = null;
          $currentRouteName = Route::currentRouteName();

          if ($currentRouteName === $menu->slug) {
              $activeClass = 'active';
          } elseif (isset($menu->submenu)) {
              if (gettype($menu->slug) === 'array') {
                  foreach ($menu->slug as $slug) {
                      if (str_contains($currentRouteName, $slug) and strpos($currentRouteName, $slug) === 0) {
                          $activeClass = 'active open';
                      }
                  }
              } else {
                  if (str_contains($currentRouteName, $menu->slug) and strpos($currentRouteName, $menu->slug) === 0) {
                      $activeClass = 'active open';
                  }
              }
          }
        @endphp

        {{-- main menu --}}
        @if (empty($menu->permission) || auth()->user()->hasPermission($menu->permission))
          <li class="menu-item {{ $activeClass }}">
            <a href="{{ isset($menu->url) ? url($menu->url) : 'javascript:void(0);' }}"
              class="{{ isset($menu->submenu) ? 'menu-link menu-toggle' : 'menu-link' }}"
              @if (isset($menu->target) and !empty($menu->target)) target="_blank" @endif>
              @isset($menu->icon)
                <i class="{{ $menu->icon }}"></i>
              @endisset
              <div>{{ isset($menu->name) ? __($menu->name) : '' }}</div>
              @isset($menu->badge)
                <div class="badge bg-{{ $menu->badge[0] }} rounded-pill ms-auto">{{ $menu->badge[1] }}</div>
              @endisset
            </a>

            {{-- submenu --}}
            @isset($menu->submenu)
              @include('layouts.sections.menu.submenu', ['menu' => $menu->submenu])
            @endisset
          </li>
        @endif
      @endif
    @endforeach
  </ul>

  <div class="menu-divider mb-0"></div>

  <ul class="m-0 p-0">
    <li class="menu-block my-1 d-flex flex-row justify-content-between">
      <a title="Settings" data-bs-placement="top" data-bs-toggle="tooltip" href="{{ route('settings.account.view') }}">
        <span aria-hidden="true" class="text-body bx bx-cog"></span>
      </a>
      @if (Auth::check())
        <a title="Logout" data-bs-placement="top" data-bs-toggle="tooltip" class="logout" href="javascript:void(0);">
          <span aria-hidden="true" class="text-danger text-opacity-75 bx bx-power-off"></span>
        </a>
      @endif
    </li>
  </ul>

</aside>
