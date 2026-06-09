{{-- Modern HRM Sidebar Navigation (Admin/HR) --}}
<?php
    $unreadChatCount = \App\Modules\Chat\Models\Message::where('receiver_id', Auth::id())->whereNull('read_at')->count();
    $userInitials = strtoupper(substr(Auth::user()->first_name, 0, 1) . substr(Auth::user()->last_name ?? '', 0, 1));
    $userRole = Auth::user()->role;
    $roleName = $userRole == \App\User::USER_ROLE_ADMIN ? 'Super Admin' : 'HR Manager';
    // Make $current safe - some controllers don't pass it
    $current = $current ?? '';
?>

<aside class="hrm-sidebar" id="hrmSidebar">
    {{-- Brand --}}
    <a href="{{ route('home') }}" class="sidebar-brand">
        <div class="sidebar-brand-icon">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V8h2v8zm4 0h-2V8h2v8z"/>
            </svg>
        </div>
        <div>
            <span class="sidebar-brand-text">{{ config('app.name', 'HRM') }}</span>
            <span class="sidebar-brand-sub">HR Management</span>
        </div>
    </a>

    {{-- Nav Items --}}
    <ul class="sidebar-nav" style="padding-top: 12px;">

        {{-- MAIN --}}
        <li class="sidebar-label">Main</li>

        <li class="{{ $current == 'dashboard' ? 'active' : '' }}">
            <a href="{{ route('dashboard.index') }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
                    <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
                </svg>
                Dashboard
            </a>
        </li>

        <li class="{{ $current == 'pim' ? 'active' : '' }}">
            <a href="{{ route('pim.index') }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                Employees
            </a>
        </li>

        {{-- HR OPERATIONS --}}
        <li class="sidebar-label">HR Operations</li>

        <li class="{{ $current == 'leave' ? 'active' : '' }}">
            <a href="{{ route('leave.index') }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                Leave Management
            </a>
        </li>

        <li class="{{ $current == 'time' ? 'active' : '' }}">
            <a href="{{ route('time.index') }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
                Time & Attendance
            </a>
        </li>

        <li class="{{ Request::is('admin/attendance*') ? 'active' : '' }}">
            <a href="{{ route('attendance.index') }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                </svg>
                Attendance Logs
            </a>
        </li>

        <li class="{{ $current == 'payroll' ? 'active' : '' }}">
            <a href="{{ route('payroll.index') }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                </svg>
                Payroll
            </a>
        </li>

        <li class="{{ $current == 'recruitment' ? 'active' : '' }}">
            <a href="{{ route('recruitment.index') }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                </svg>
                Recruitment
            </a>
        </li>

        <li class="{{ $current == 'discipline' ? 'active' : '' }}">
            <a href="{{ route('discipline.index') }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
                Discipline
            </a>
        </li>

        {{-- TOOLS --}}
        <li class="sidebar-label">Tools</li>

        <li class="{{ $current == 'chat' ? 'active' : '' }}">
            <a href="{{ route('chat.index') }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
                Messages
                @if($unreadChatCount > 0)
                    <span class="nav-badge">{{ $unreadChatCount }}</span>
                @endif
            </a>
        </li>

        <li class="{{ Request::is('admin/hr-assistant*') ? 'active' : '' }}">
            <a href="{{ route('assistant.index') }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="2"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/>
                </svg>
                AI Assistant
            </a>
        </li>

        <li class="{{ $current == 'settings' ? 'active' : '' }}">
            <a href="{{ route('settings.index') }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                </svg>
                Settings
            </a>
        </li>

        <li>
            <a href="{{ route('attendance.scanner') }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="5" height="5"/><rect x="16" y="3" width="5" height="5"/>
                    <rect x="3" y="16" width="5" height="5"/><line x1="21" y1="16" x2="21" y2="21"/>
                    <line x1="16" y1="21" x2="21" y2="21"/><line x1="16" y1="16" x2="16" y2="16"/>
                    <line x1="12" y1="3" x2="12" y2="9"/><line x1="12" y1="16" x2="12" y2="21"/>
                    <line x1="9" y1="12" x2="3" y2="12"/>
                </svg>
                Scanner Kiosk
            </a>
        </li>

    </ul>

    {{-- User Profile --}}
    <div class="sidebar-user">
        <div class="sidebar-user-avatar">{{ $userInitials }}</div>
        <div class="sidebar-user-info">
            <span class="sidebar-user-name">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</span>
            <span class="sidebar-user-role">{{ $roleName }}</span>
        </div>
        <form id="sidebar-logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">{{ csrf_field() }}</form>
        <a href="{{ route('logout') }}" class="sidebar-user-logout" title="Logout"
           onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                <polyline points="16 17 21 12 16 7"/>
                <line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
        </a>
    </div>
</aside>

{{-- Mobile sidebar overlay --}}
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>
