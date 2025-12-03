      <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
          <div class="app-brand demo">
              <a href="index.html" class="app-brand-link">
                  <span class="app-brand-logo demo">
                      <img src="{{ asset('front/img/logo/logo.png') }}" style="max-width: 70%; width: auto;">
                  </span>
              </a>

              <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
                  <i class="bx bx-chevron-left bx-sm align-middle"></i>
              </a>
          </div>

          <div class="menu-inner-shadow"></div>

          <ul class="menu-inner py-1">
              <!-- Dashboards -->
              <li class="menu-item">
                  <a href="{{ route('dashboard') }}" class="menu-link">
                      <i class="menu-icon tf-icons bx bx-home-circle"></i>
                      <div data-i18n="Dashboards">Dashboards</div>
                  </a>
              </li>

              <li class="menu-item">
                  <a href="{{ route('testimonials.index') }}" class="menu-link">
                      <i class="menu-icon tf-icons bx bx-message-dots"></i>
                      <div data-i18n="Dashboards">Testimonials</div>
                  </a>
              </li>
          </ul>
      </aside>
