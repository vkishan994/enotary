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
              <li class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                  <a href="{{ route('dashboard') }}" class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                      <i class="menu-icon tf-icons bx bx-home-circle"></i>
                      <div data-i18n="Dashboards">Dashboards</div>
                  </a>
              </li>

              <li class="menu-item {{ request()->routeIs('testimonials.*') ? 'active' : '' }}">
                  <a href="{{ route('testimonials.index') }}" class="menu-link {{ request()->routeIs('testimonials.*') ? 'active' : '' }}">
                      <i class="menu-icon tf-icons bx bx-message-dots"></i>
                      <div data-i18n="Dashboards">Testimonials</div>
                  </a>
              </li>

              <li class="menu-item {{ request()->routeIs('notary-service-types.*') ? 'active' : '' }}">
                  <a href="{{ route('notary-service-types.index') }}" class="menu-link {{ request()->routeIs('notary-service-types.*') ? 'active' : '' }}">
                      <i class="menu-icon tf-icons bx bx-badge-check"></i>
                      <div data-i18n="Dashboards">Notary Service Types</div>
                  </a>
              </li>

              <li class="menu-item {{ request()->routeIs('documents.*') ? 'active' : '' }}">
                  <a href="{{ route('documents.index') }}" class="menu-link {{ request()->routeIs('documents.*') ? 'active' : '' }}">
                      <i class="menu-icon tf-icons bx bx-file"></i>
                      <div data-i18n="Dashboards">Documents</div>
                  </a>
              </li>

              <li class="menu-item {{ request()->routeIs('upload-documents.*') ? 'active' : '' }}">
                  <a href="{{ route('upload-documents.index') }}" class="menu-link {{ request()->routeIs('upload-documents.*') ? 'active' : '' }}">
                      <i class="menu-icon tf-icons bx bx-file"></i>
                      <div data-i18n="Dashboards">Upload Documents</div>
                  </a>
              </li>

              <li class="menu-item {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                  <a href="{{ route('admin.orders.index') }}" class="menu-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                      <i class="menu-icon tf-icons bx bx-cart"></i>
                      <div data-i18n="Dashboards">Orders</div>
                  </a>
              </li>

              {{-- <li class="menu-item {{ request()->routeIs('admin.schedule.*') ? 'active' : '' }}">
                  <a href="{{ route('admin.schedule.meetings.index') }}" class="menu-link {{ request()->routeIs('admin.schedule.*') ? 'active' : '' }}">
                      <i class="menu-icon tf-icons bx bx-calendar"></i>
                      <div data-i18n="Dashboards">Schedule Meetings</div>
                  </a>
              </li> --}}

              {{-- <li class="menu-item">
                  <a href="{{ route('admin.veriffdata.index') }}" class="menu-link">
                      <i class="menu-icon tf-icons bx bx-id-card"></i>
                      <div data-i18n="Dashboards">EKYC Verification Data</div>
                  </a>
              </li> --}}

              <li class="menu-item {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                  <a href="{{ route('admin.settings') }}" class="menu-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                      <i class="menu-icon tf-icons bx bx-cog"></i>
                      <div data-i18n="Dashboards">Settings</div>
                  </a>
              </li>

              <li class="menu-item {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                  <a href="{{ route('customers.list') }}" class="menu-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                      <i class="menu-icon tf-icons bx bx-group"></i>
                      <div data-i18n="Dashboards">Customers</div>
                  </a>
              </li>

          </ul>
      </aside>
