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
      {{-- Top Bar --}}
      <header class="hrm-topbar">
        <button class="sidebar-toggle" id="sidebarToggle" onclick="toggleSidebar()" aria-label="Toggle sidebar">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="3" y1="6" x2="21" y2="6"/>
            <line x1="3" y1="12" x2="21" y2="12"/>
            <line x1="3" y1="18" x2="21" y2="18"/>
          </svg>
        </button>
        <div class="topbar-title">
          {{ config('app.name', 'HRM') }}
        </div>
        <div class="topbar-actions">
          <a href="{{ route('profile.index') }}" class="btn btn-default btn-sm" style="gap:6px; text-decoration:none;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
            </svg>
            {{ Auth::user()->first_name }}
          </a>
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

  {{-- Mobile Sidebar JS --}}
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
  </script>

  @yield('additionalCSS')
  @yield('additionalJS')
</body>
</html>
