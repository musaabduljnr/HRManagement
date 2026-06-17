<!DOCTYPE html>
<html lang="en">
<head>
  @include('includes.head')
</head>
<body>
  <div class="hrm-wrapper">
    {{-- Employee Sidebar --}}
    @include('includes.sidebar_employee')

    {{-- Main Content --}}
    <div class="hrm-content">

      {{-- ── Premium Top Bar ── --}}
      <header class="hrm-topbar">
        <div class="topbar-left">
          <button class="sidebar-toggle" id="sidebarToggle" onclick="toggleSidebar()" aria-label="Toggle sidebar">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
            </svg>
          </button>
          <div class="topbar-title">
            {{ config('app.name', 'HRM') }} — Employee Portal
          </div>
        </div>

        <div class="topbar-right">
          {{-- Chat icon --}}
          @php
            $empTopbarUnread = \App\Modules\Chat\Models\Message::where('receiver_id', Auth::id())->whereNull('read_at')->count();
          @endphp
          <a href="{{ route('employee.chat.index') }}" class="topbar-icon-btn" title="Messages" style="text-decoration:none!important;">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            </svg>
            @if($empTopbarUnread > 0)
              <span class="notification-dot"></span>
            @endif
          </a>

          {{-- User dropdown --}}
          @php
            $empInitials = strtoupper(substr(Auth::user()->first_name, 0, 1) . substr(Auth::user()->last_name ?? '', 0, 1));
          @endphp
          <div class="topbar-user" id="topbarUserBtn" onclick="toggleTopbarDropdown()">
            <div class="topbar-user-avatar">{{ $empInitials }}</div>
            <div>
              <div class="topbar-user-name">{{ Auth::user()->first_name }}</div>
              <div class="topbar-user-role">Employee</div>
            </div>
            <svg class="topbar-user-chevron" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <polyline points="6 9 12 15 18 9"/>
            </svg>

            <div class="topbar-dropdown" id="topbarDropdown">
              <div class="topbar-dropdown-header">
                <div class="topbar-dropdown-name">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</div>
                <div class="topbar-dropdown-email">Employee</div>
              </div>

              <div class="topbar-dropdown-divider"></div>

              <form id="emp-topbar-logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">{{ csrf_field() }}</form>
              <button class="topbar-dropdown-item danger"
                onclick="document.getElementById('emp-topbar-logout-form').submit();">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
                Sign Out
              </button>
            </div>
          </div>
        </div>
      </header>

      {{-- Page Content --}}
      <main class="hrm-page">
        {!! Breadcrumbs::render(Route::currentRouteName(), @$breadcrumb) !!}

        @if(session('success'))
        <div class="alert alert-success" role="alert">{{ session('success') }}</div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
        @endif

        @yield('content')
      </main>
    </div>
  </div>

  @include('includes.footer')

  <script>
    function toggleSidebar() {
      var sidebar = document.getElementById('hrmSidebar');
      var overlay = document.getElementById('sidebarOverlay');
      sidebar.classList.toggle('open');
      overlay.classList.toggle('active');
    }
    function closeSidebar() {
      var sidebar = document.getElementById('hrmSidebar');
      var overlay = document.getElementById('sidebarOverlay');
      sidebar.classList.remove('open');
      overlay.classList.remove('active');
    }
    function toggleTopbarDropdown() {
      var dd = document.getElementById('topbarDropdown');
      dd.classList.toggle('open');
    }
    document.addEventListener('click', function(e) {
      var btn = document.getElementById('topbarUserBtn');
      var dd  = document.getElementById('topbarDropdown');
      if (btn && dd && !btn.contains(e.target)) {
        dd.classList.remove('open');
      }
    });
  </script>

  @yield('additionalCSS')
  @yield('additionalJS')
</body>
</html>
