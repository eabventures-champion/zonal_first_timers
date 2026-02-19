{{-- Sidebar Navigation — Role-aware --}}
@php
    $isAdmin = auth()->user()->hasAnyRole(['Super Admin', 'Admin']);
    $isSuperAdmin = auth()->user()->hasRole('Super Admin');
    $isRO = auth()->user()->hasRole('Retaining Officer');
@endphp

@if($isAdmin)
    <p x-show="!sidebarMinimized" class="px-3 mb-2 text-[10px] font-semibold uppercase tracking-widest text-slate-500">
        Overview</p>

    <a href="{{ route('admin.dashboard') }}"
        class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-slate-300 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1" />
        </svg>
        <span x-show="!sidebarMinimized">Dashboard</span>
    </a>

    <p x-show="!sidebarMinimized" class="px-3 mt-5 mb-2 text-[10px] font-semibold uppercase tracking-widest text-slate-500">
        Church Hierarchy</p>

    @if($isSuperAdmin)
        <a href="{{ route('admin.church-categories.index') }}"
            class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-slate-300 {{ request()->routeIs('admin.church-categories.*') ? 'active' : '' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            <span x-show="!sidebarMinimized">Categories</span>
        </a>
        <a href="{{ route('admin.church-groups.index') }}"
            class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-slate-300 {{ request()->routeIs('admin.church-groups.*') ? 'active' : '' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span x-show="!sidebarMinimized">Groups</span>
        </a>
    @endif

    <a href="{{ route('admin.churches.index') }}"
        class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-slate-300 {{ request()->routeIs('admin.churches.*') ? 'active' : '' }}">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
        </svg>
        <span x-show="!sidebarMinimized">Churches</span>
    </a>

    <p x-show="!sidebarMinimized" class="px-3 mt-5 mb-2 text-[10px] font-semibold uppercase tracking-widest text-slate-500">
        People</p>

    <a href="{{ route('admin.first-timers.index') }}"
        class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-slate-300 {{ request()->routeIs('admin.first-timers.*') ? 'active' : '' }}">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
        </svg>
        <span x-show="!sidebarMinimized">First Timers</span>
    </a>

    <a href="{{ route('admin.foundation-school.index') }}"
        class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-slate-300 {{ request()->routeIs('admin.foundation-school.*') ? 'active' : '' }}">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
        </svg>
        <span x-show="!sidebarMinimized">Foundation School</span>
    </a>

    @if($isSuperAdmin)
        <a href="{{ route('admin.users.index') }}"
            class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-slate-300 {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m9 5.197V21" />
            </svg>
            <span x-show="!sidebarMinimized">Users</span>
        </a>
    @endif
@endif

@if($isRO)
    <p x-show="!sidebarMinimized" class="px-3 mb-2 text-[10px] font-semibold uppercase tracking-widest text-slate-500">
        Overview</p>

    <a href="{{ route('ro.dashboard') }}"
        class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-slate-300 {{ request()->routeIs('ro.dashboard') ? 'active' : '' }}">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1" />
        </svg>
        <span x-show="!sidebarMinimized">Dashboard</span>
    </a>

    <p x-show="!sidebarMinimized" class="px-3 mt-5 mb-2 text-[10px] font-semibold uppercase tracking-widest text-slate-500">
        My Church</p>

    <a href="{{ route('ro.first-timers.index') }}"
        class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-slate-300 {{ request()->routeIs('ro.first-timers.*') ? 'active' : '' }}">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
        </svg>
        <span x-show="!sidebarMinimized">First Timers</span>
    </a>

    <a href="{{ route('ro.foundation-school.index') }}"
        class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-slate-300 {{ request()->routeIs('ro.foundation-school.*') ? 'active' : '' }}">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
        </svg>
        <span x-show="!sidebarMinimized">Foundation School</span>
    </a>

    <a href="{{ route('ro.attendance.index') }}"
        class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-slate-300 {{ request()->routeIs('ro.attendance.*') ? 'active' : '' }}">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
        </svg>
        <span x-show="!sidebarMinimized">Weekly Attendance</span>
    </a>
@endif