@php
  $containerFooter =
      isset($configData['contentLayout']) && $configData['contentLayout'] === 'compact'
          ? 'container-xxl'
          : 'container-fluid';
@endphp

<!-- Footer-->
<footer class="content-footer footer bg-footer-theme">
  <div class="{{ $containerFooter }}">
    <div class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
      <div class="text-body">
        © 2025 • Created {{ !empty(config('variables.creatorName')) ? config('variables.creatorName') : '' }} • <a
          href="http://www.ecogreenoleo.com" target="_blank" style="color: #1ca11d">PT. Ecogreen Oleochemicals</a>
      </div>
    </div>
  </div>
</footer>
<!--/ Footer-->
