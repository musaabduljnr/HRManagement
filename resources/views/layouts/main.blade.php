<!DOCTYPE html>
<html lang="en">
<head>
  @include('includes.head')
</head>
<body>
  <div class="hrm-wrapper">
    {{-- Sidebar --}}
    @include('includes.sidebar')

    {{-- Main Content --}}
    <div class="hrm-content">

      {{-- ── Premium Top Bar ── --}}
      <header class="hrm-topbar">
        {{-- Left: Toggle + Title --}}
        <div class="topbar-left">
          <button class="sidebar-toggle" id="sidebarToggle" onclick="toggleSidebar()" aria-label="Toggle sidebar">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
            </svg>
          </button>
          <div class="topbar-title">
            {{ config('app.name', 'HRM') }}
          </div>
        </div>

        {{-- Right: Actions + User --}}
        <div class="topbar-right">

          {{-- Messages icon --}}
          @php
            $topbarUnread = \App\Modules\Chat\Models\Message::where('receiver_id', Auth::id())->whereNull('read_at')->count();
          @endphp
          <a href="{{ route('chat.index') }}" class="topbar-icon-btn" title="Messages" style="text-decoration:none!important;">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            </svg>
            @if($topbarUnread > 0)
              <span class="notification-dot"></span>
            @endif
          </a>

          {{-- Settings icon --}}
          <a href="{{ route('settings.index') }}" class="topbar-icon-btn" title="Settings" style="text-decoration:none!important;">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
            </svg>
          </a>

          {{-- User profile dropdown --}}
          @php
            $topbarInitials = strtoupper(substr(Auth::user()->first_name, 0, 1) . substr(Auth::user()->last_name ?? '', 0, 1));
            $topbarRole     = Auth::user()->role == \App\User::USER_ROLE_ADMIN ? 'Super Admin' : (Auth::user()->role == \App\User::USER_ROLE_HR_MANAGER ? 'HR Manager' : 'Staff');
          @endphp
          <div class="topbar-user" id="topbarUserBtn" onclick="toggleTopbarDropdown()">
            <div class="topbar-user-avatar">{{ $topbarInitials }}</div>
            <div>
              <div class="topbar-user-name">{{ Auth::user()->first_name }}</div>
              <div class="topbar-user-role">{{ $topbarRole }}</div>
            </div>
            <svg class="topbar-user-chevron" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <polyline points="6 9 12 15 18 9"/>
            </svg>

            {{-- Dropdown --}}
            <div class="topbar-dropdown" id="topbarDropdown">
              <div class="topbar-dropdown-header">
                <div class="topbar-dropdown-name">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</div>
                <div class="topbar-dropdown-email">{{ $topbarRole }}</div>
              </div>

              <a href="{{ route('profile.index') }}" class="topbar-dropdown-item" style="color: var(--text-secondary)!important;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                </svg>
                My Profile
              </a>
              <a href="{{ route('settings.index') }}" class="topbar-dropdown-item" style="color: var(--text-secondary)!important;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06A1.65 1.65 0 0 0 15 19.4a1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                </svg>
                Settings
              </a>

              <div class="topbar-dropdown-divider"></div>

              <form id="topbar-dropdown-logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">{{ csrf_field() }}</form>
              <button class="topbar-dropdown-item danger"
                onclick="document.getElementById('topbar-dropdown-logout-form').submit();">
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
        {{-- Breadcrumbs --}}
        {!! Breadcrumbs::render(Route::currentRouteName(), @$breadcrumb) !!}

        {{-- Flash Messages --}}
        @if(session('success'))
        <div class="alert alert-success" role="alert">
          {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger" role="alert">
          {{ session('error') }}
        </div>
        @endif

        @yield('content')
      </main>
    </div>
  </div>

  @include('includes.footer')

  {{-- Sidebar & Topbar Dropdown JS --}}
  <script>
    // ── Sidebar ──
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

    // ── Topbar User Dropdown ──
    function toggleTopbarDropdown() {
      var dd = document.getElementById('topbarDropdown');
      dd.classList.toggle('open');
    }

    // Close dropdown when clicking outside
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
