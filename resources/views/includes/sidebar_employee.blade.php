{{-- Modern HRM Sidebar Navigation (Employee) --}}
<?php
    $unreadChatCount = \App\Modules\Chat\Models\Message::where('receiver_id', Auth::id())->whereNull('read_at')->count();
    $userInitials = strtoupper(substr(Auth::user()->first_name, 0, 1) . substr(Auth::user()->last_name ?? '', 0, 1));
    // Make $current safe - some controllers don't pass it
    $current = $current ?? '';
?>

<aside class="hrm-sidebar" id="hrmSidebar">
    {{-- Brand --}}
    <a href="{{ route('employee.home') }}" class="sidebar-brand">
        <div class="sidebar-brand-icon">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V8h2v8zm4 0h-2V8h2v8z"/>
            </svg>
        </div>
        <div>
            <span class="sidebar-brand-text">{{ config('app.name', 'HRM') }}</span>
            <span class="sidebar-brand-sub">Employee Portal</span>
        </div>
    </a>

    {{-- Nav Items --}}
    <ul class="sidebar-nav" style="padding-top: 12px;">

        <li class="sidebar-label">My Workspace</li>

        <li class="{{ $current == 'employee.home' ? 'active' : '' }}">
            <a href="{{ route('employee.home') }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
                    <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
                </svg>
                Dashboard
            </a>
        </li>

        <li class="{{ $current == 'employee.leaves' ? 'active' : '' }}">
            <a href="{{ route('employee.leaves.index') }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                My Leaves
            </a>
        </li>

        <li class="{{ Request::is('employee/assets*') ? 'active' : '' }}">
            <a href="{{ route('employee.assets.index') }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="2" width="20" height="8" rx="2" ry="2"/>
                    <rect x="2" y="14" width="20" height="8" rx="2" ry="2"/>
                    <line x1="6" y1="6" x2="6" y2="6"/>
                    <line x1="6" y1="18" x2="6" y2="18"/>
                </svg>
                My Assets
            </a>
        </li>

        

        <li class="{{ $current == 'employee.time' ? 'active' : '' }}">
            <a href="{{ route('employee.time.index') }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
                Time Logs
            </a>
        </li>

        <li class="{{ $current == 'employee.salary' ? 'active' : '' }}">
            <a href="{{ route('employee.salary.index') }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                </svg>
                My Salary
            </a>
        </li>

        <li class="{{ $current == 'employee.payroll' ? 'active' : '' }}">
            <a href="{{ route('employee.payroll.index') }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
                    <polyline points="10 9 9 9 8 9"/>
                </svg>
                Payslips
            </a>
        </li>

        <li class="{{ $current == 'employee.documents' ? 'active' : '' }}">
            <a href="{{ route('employee.documents.index') }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                </svg>
                My Documents
            </a>
        </li>


        <li class="{{ $current == 'employee.dashboard_documents' ? 'active' : '' }}">
            <a href="{{ route('employee.dashboard_documents.index') }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
                Company Docs
            </a>
        </li>

        <li class="sidebar-label">Attendance</li>

        <li class="{{ Request::is('employee/attendance*') ? 'active' : '' }}">
            <a href="{{ route('employee.attendance.qr') }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="5" height="5"/><rect x="16" y="3" width="5" height="5"/>
                    <rect x="3" y="16" width="5" height="5"/><line x1="21" y1="16" x2="21" y2="21"/>
                    <line x1="16" y1="21" x2="21" y2="21"/>
                </svg>
                QR Attendance
            </a>
        </li>

        <li class="sidebar-label">Communication</li>

        <li class="{{ $current == 'employee.chat' ? 'active' : '' }}">
            <a href="{{ route('employee.chat.index') }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
                Messages
                @if($unreadChatCount > 0)
                    <span class="nav-badge">{{ $unreadChatCount }}</span>
                @endif
            </a>
        </li>

        <li class="{{ Request::is('employee/hr-assistant*') ? 'active' : '' }}">
            <a href="{{ route('employee.assistant.index') }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="2"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/>
                </svg>
                AI Assistant
            </a>
        </li>

    </ul>

    {{-- User Profile --}}
    <div class="sidebar-user">
        <div class="sidebar-user-avatar">{{ $userInitials }}</div>
        <div class="sidebar-user-info">
            <span class="sidebar-user-name">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</span>
            <span class="sidebar-user-role">Employee</span>
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
