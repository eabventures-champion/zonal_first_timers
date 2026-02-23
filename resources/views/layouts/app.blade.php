<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Church Manager') }} — @yield('title', 'Dashboard')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        // On page load or when changing themes, best to add inline in `head` to avoid FOUC
        if (localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .sidebar {
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .sidebar-link {
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar-link.active {
            border-left: 3px solid #818cf8;
        }

        .content-fade {
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body class="font-sans antialiased bg-gray-50 dark:bg-slate-950 transition-colors duration-300">
    <div class="min-h-screen" x-data="{ 
        sidebarOpen: false, 
        sidebarMinimized: localStorage.getItem('sidebarMinimized') === 'true',
        darkMode: localStorage.getItem('darkMode') === 'true',
        toggleSidebar() {
            this.sidebarMinimized = !this.sidebarMinimized;
            localStorage.setItem('sidebarMinimized', this.sidebarMinimized);
        },
        toggleTheme() {
            this.darkMode = !this.darkMode;
            localStorage.setItem('darkMode', this.darkMode);
            if (this.darkMode) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
    }">

        {{-- ── Sidebar ──────────────────────────────────────── --}}
        <aside
            class="sidebar fixed inset-y-0 left-0 z-40 bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900 text-white flex flex-col transition-all duration-300 lg:translate-x-0"
            :class="{
                'translate-x-0': sidebarOpen,
                '-translate-x-full': !sidebarOpen,
                'w-72 sm:w-64': !sidebarMinimized,
                'w-20': sidebarMinimized
            }" @click.away="sidebarOpen = false">

            {{-- Brand --}}
            <div class="flex items-center justify-between px-6 py-5 border-b border-white/10 overflow-hidden">
                <div class="flex items-center gap-3">
                    <div
                        class="w-9 h-9 rounded-lg bg-indigo-500 flex items-center justify-center text-white font-bold text-sm shrink-0 shadow-lg shadow-indigo-500/20">
                        ZC
                    </div>
                    <div x-show="!sidebarMinimized" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-x-2"
                        x-transition:enter-end="opacity-100 translate-x-0">
                        <h1 class="text-sm font-bold tracking-wide whitespace-nowrap">Zonal Church</h1>
                        <p class="text-[10px] text-slate-400 uppercase tracking-widest whitespace-nowrap">First Timers
                            System</p>
                    </div>
                </div>

                {{-- Toggle Sidebar (Desktop) / Close (Mobile) --}}
                <button @click="sidebarMinimized = !sidebarMinimized; if(sidebarOpen) sidebarOpen = false"
                    class="hidden lg:block p-1.5 rounded-lg hover:bg-white/10 text-slate-400">
                    <svg class="w-5 h-5 transition-transform duration-300" :class="sidebarMinimized ? 'rotate-180' : ''"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                    </svg>
                </button>

                {{-- Mobile Close Button --}}
                <button @click="sidebarOpen = false"
                    class="lg:hidden p-1.5 rounded-lg hover:bg-white/10 text-slate-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 overflow-y-auto overflow-x-hidden scrollbar-hide py-4 px-3 space-y-1">
                @include('components.sidebar')
            </nav>

            {{-- User Info --}}
            <div class="border-t border-white/10 px-4 py-4 overflow-hidden bg-slate-900/50">
                <div class="flex items-center gap-3">
                    <div
                        class="w-8 h-8 rounded-full bg-indigo-500/30 flex items-center justify-center text-xs font-semibold text-indigo-300 shrink-0 border border-indigo-500/20">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0" x-show="!sidebarMinimized"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-x-2"
                        x-transition:enter-end="opacity-100 translate-x-0">
                        <p class="text-xs font-bold truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[9px] text-slate-400 uppercase tracking-widest">
                            {{ auth()->user()->roles->first()?->name ?? 'User' }}</p>
                    </div>
                </div>
            </div>
        </aside>

        {{-- Overlay --}}
        <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 bg-black/50 z-20 lg:hidden"
            @click="sidebarOpen = false"></div>

        {{-- ── Main Content ─────────────────────────────────── --}}
        <div class="min-h-screen flex flex-col transition-all duration-300"
            :class="sidebarMinimized ? 'lg:ml-20' : 'lg:ml-64'">

            {{-- Topbar --}}
            <header
                class="sticky top-0 z-10 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-gray-200 dark:border-slate-800 transition-colors duration-300">
                <div class="flex items-center justify-between px-4 sm:px-6 lg:px-8 h-16">
                    <div class="flex items-center gap-4">
                        <button @click="sidebarOpen = !sidebarOpen"
                            class="lg:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-800">
                            <svg class="w-5 h-5 text-gray-600 dark:text-slate-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>

                        @hasSection('back-link')
                            <a href="@yield('back-link')"
                                class="p-2 -ml-2 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-800 text-gray-500 dark:text-slate-400 transition-colors"
                                title="Back">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                            </a>
                        @endif

                        <h2 class="text-lg font-semibold text-gray-800 dark:text-slate-100">
                            @yield('page-title', 'Dashboard')</h2>
                    </div>

                    <div class="flex items-center gap-3">
                        {{-- Theme Toggle --}}
                        <button @click="toggleTheme"
                            class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-800 text-gray-500 dark:text-slate-400 transition-colors">
                            <template x-if="!darkMode">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                </svg>
                            </template>
                            <template x-if="darkMode">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 3v1m0 16v1m9-9h-1M4 9H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </template>
                        </button>

                        <div class="h-6 w-px bg-gray-200 dark:bg-slate-800 mx-1"></div>

                        <a href="{{ route('profile.edit') }}"
                            class="text-sm text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-200">Profile</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="text-sm text-gray-500 dark:text-slate-400 hover:text-red-600">Logout</button>
                        </form>
                    </div>
                </div>
            </header>

            {{-- Flash Messages --}}
            @if (session('success'))
                <x-alert type="success" :message="session('success')" />
            @endif
            @if (session('error'))
                <x-alert type="error" :message="session('error')" />
            @endif

            {{-- Page Content --}}
            <main class="flex-1 p-4 sm:p-6 lg:p-8 content-fade">
                {{ $slot ?? '' }}
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>

</html>