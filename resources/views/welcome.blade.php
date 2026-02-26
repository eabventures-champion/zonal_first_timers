<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $settings['app_name']->value ?? config('app.name', 'Zonal First Timers') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #4338ca;
            --accent: #fbbf24;
        }

        body {
            font-family: 'Outfit', sans-serif;
        }

        .heading-font {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .glass {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .dark .glass {
            background: rgba(15, 23, 42, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .hero-bg {
            position: relative;
        }

        .hero-bg-img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center center;
            z-index: 0;
            image-rendering: -webkit-optimize-contrast;
        }

        .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.7));
            z-index: 1;
        }

        .text-glow {
            text-shadow: 0 0 20px rgba(79, 70, 229, 0.4);
        }

        .animate-bounce-x {
            animation: bounce-x 2s infinite;
        }

        @keyframes bounce-x {

            0%,
            100% {
                transform: translateX(0);
                animation-timing-function: cubic-bezier(0.8, 0, 1, 1);
            }

            50% {
                transform: translateX(25%);
                animation-timing-function: cubic-bezier(0, 0, 0.2, 1);
            }
        }
    </style>
</head>

<body class="antialiased bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white min-h-screen">
    {{-- Navigation --}}
    <nav class="fixed top-0 w-full z-50 transition-all duration-500" x-data="{ scrolled: false }"
        @scroll.window="scrolled = (window.pageYOffset > 50)"
        :class="scrolled ? 'bg-slate-900/90 backdrop-blur-lg py-3 shadow-2xl border-b border-white/5 px-6 lg:px-12' : 'px-6 py-6 lg:px-12'">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-2 group cursor-pointer">
                <div
                    class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/20 transition-transform group-hover:scale-110">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18 18.247 18.477 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <span class="text-xl font-bold heading-font tracking-tight text-white drop-shadow-md">
                    Zonal <span class="text-indigo-400">First Timers</span>
                </span>
            </div>

            <div class="flex items-center gap-4">
                @auth
                    <a href="{{ url('/dashboard') }}"
                        class="px-6 py-2.5 rounded-full bg-white text-indigo-600 font-semibold text-sm transition hover:bg-slate-100 shadow-xl">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="hidden sm:block px-8 py-3 rounded-full bg-indigo-600 text-white font-bold text-sm transition hover:bg-indigo-700 hover:scale-105 shadow-2xl shadow-indigo-500/40">
                        Member Login
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- Hero Section --}}
    <section class="relative h-screen flex items-center justify-center overflow-hidden hero-bg">
        <img src="{{ $settings['hero_background_image']->value ?? '/assets/images/hero-bg.png' }}" alt="Hero Background"
            class="hero-bg-img" />
        <div class="hero-overlay"></div>
        <div class="absolute inset-0 bg-indigo-900/10 backdrop-blur-[2px]" style="z-index: 2;"></div>

        <div class="relative z-10 max-w-5xl mx-auto px-6 text-center pt-20 pb-16" style="z-index: 3;">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass mb-8 animate-fade-in-down">
                <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                <span
                    class="text-xs font-semibold tracking-widest uppercase text-indigo-300">{{ $settings['hero_badge_text']->value ?? 'Experience Excellence in Discipleship' }}</span>
            </div>

            <h1
                class="text-3xl sm:text-4xl lg:text-5xl font-bold mb-4 sm:mb-6 heading-font text-white leading-tight transition-all text-glow">
                {{ $settings['hero_title_1']->value ?? 'Raising' }} <span
                    class="text-indigo-400">{{ $settings['hero_title_1_highlight']->value ?? 'Leaders' }}</span>,<br>{{ $settings['hero_title_2']->value ?? 'Building' }}
                <span
                    class="text-accent underline decoration-indigo-500 decoration-2 sm:decoration-4 underline-offset-4 sm:underline-offset-8">{{ $settings['hero_title_2_highlight']->value ?? 'Legacies' }}</span>.
            </h1>

            <p
                class="text-sm sm:text-base lg:text-lg text-slate-300 mb-6 sm:mb-8 max-w-2xl mx-auto leading-relaxed px-2">
                {{ $settings['hero_subtitle']->value ?? 'A modern platform dedicated to tracking and nurturing the spiritual growth of every first timer in the zone.' }}
            </p>

            <div
                class="flex flex-col sm:flex-row items-stretch sm:items-center justify-center gap-3 sm:gap-4 w-full sm:w-auto">
                <a href="{{ route('login') }}"
                    class="group relative px-5 sm:px-8 py-3 sm:py-3.5 bg-indigo-600 rounded-xl overflow-hidden transition-all duration-300 hover:scale-105 w-full sm:w-auto text-center">
                    <div class="absolute inset-0 bg-gradient-to-r from-indigo-500 to-purple-600 transition-opacity">
                    </div>
                    <span
                        class="relative text-white font-semibold text-sm sm:text-base flex items-center justify-center gap-2">
                        {{ $settings['hero_button_primary_text']->value ?? 'Member Portal Access' }}
                        <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </span>
                </a>

                <a href="#mission"
                    class="px-5 sm:px-8 py-3 sm:py-3.5 glass rounded-xl text-white font-semibold text-sm sm:text-base transition-all hover:bg-white/10 w-full sm:w-auto text-center">
                    Our Mandate
                </a>
            </div>
        </div>

        {{-- Floating Decorative Elements --}}
        <div class="absolute bottom-2 left-1/2 -translate-x-1/2 hidden sm:flex" style="z-index: 3;">
            <a href="#mission"
                class="w-16 h-10 rounded-full border-2 border-white/20 flex items-center justify-start pl-2 hover:border-white/40 transition-colors animate-bounce-x">
                <div class="w-3 h-1 bg-white/40 rounded-full"></div>
            </a>
        </div>
    </section>

    {{-- Mission/Feature Cards --}}
    <section id="mission" class="py-12 px-6 relative dark:bg-slate-950 overflow-hidden">
        <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-600/10 blur-[120px] rounded-full"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-purple-600/10 blur-[120px] rounded-full"></div>

        <div class="max-w-7xl mx-auto text-center mb-12 sm:mb-20 relative z-10">
            <h2 class="text-3xl lg:text-5xl font-bold heading-font mb-4 sm:mb-6">
                {{ $settings['mission_heading_1']->value ?? 'Our Discipleship' }} <span
                    class="text-indigo-500">{{ $settings['mission_heading_highlight']->value ?? 'Pillars' }}</span>
            </h2>
            <p class="text-slate-500 dark:text-slate-400 max-w-2xl mx-auto text-base sm:text-lg">
                {{ $settings['mission_subheading']->value ?? 'We are committed to the comprehensive growth and integration of every soul that walks through our doors.' }}
            </p>
        </div>

        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-8 relative z-10">
            {{-- Card 1 --}}
            <div
                class="group p-10 rounded-3xl bg-white dark:bg-slate-900 border border-slate-100 dark:border-white/5 transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl hover:shadow-indigo-500/10">
                <div
                    class="w-16 h-16 bg-indigo-50 dark:bg-indigo-500/10 rounded-2xl flex items-center justify-center text-indigo-600 dark:text-indigo-400 mb-8 transition-transform group-hover:scale-110 group-hover:rotate-6">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold mb-4 heading-font">
                    {{ $settings['mission_card_1_title']->value ?? 'Nurturing Souls' }}
                </h3>
                <p class="text-slate-500 dark:text-slate-400 leading-relaxed">
                    {{ $settings['mission_card_1_desc']->value ?? 'Dedicated follow-up systems ensuring no one is left behind in their spiritual journey after their first visit.' }}
                </p>
            </div>

            {{-- Card 2 --}}
            <div
                class="group p-10 rounded-3xl bg-white dark:bg-slate-900 border border-slate-100 dark:border-white/5 transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl hover:shadow-amber-500/10">
                <div
                    class="w-16 h-16 bg-amber-50 dark:bg-amber-500/10 rounded-2xl flex items-center justify-center text-amber-600 dark:text-amber-400 mb-8 transition-transform group-hover:scale-110 group-hover:-rotate-6">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18 18.247 18.477 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold mb-4 heading-font">
                    {{ $settings['mission_card_2_title']->value ?? 'Foundation School' }}
                </h3>
                <p class="text-slate-500 dark:text-slate-400 leading-relaxed">
                    {{ $settings['mission_card_2_desc']->value ?? 'A structured curriculum designed to ground new converts in the core doctrines of the faith and church vision.' }}
                </p>
            </div>

            {{-- Card 3 --}}
            <div
                class="group p-10 rounded-3xl bg-white dark:bg-slate-900 border border-slate-100 dark:border-white/5 transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl hover:shadow-purple-500/10">
                <div
                    class="w-16 h-16 bg-purple-50 dark:bg-purple-500/10 rounded-2xl flex items-center justify-center text-purple-600 dark:text-purple-400 mb-8 transition-transform group-hover:scale-110 group-hover:rotate-6">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold mb-4 heading-font">
                    {{ $settings['mission_card_3_title']->value ?? 'Membership Integration' }}
                </h3>
                <p class="text-slate-500 dark:text-slate-400 leading-relaxed">
                    {{ $settings['mission_card_3_desc']->value ?? 'Transitioning first timers into fully integrated, productive members of the local church community and workforce.' }}
                </p>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="py-12 px-6 border-t border-slate-100 dark:border-white/5 text-center">
        <p class="text-slate-400 text-sm">
            &copy; {{ date('Y') }} {{ config('app.name', 'Zonal First Timers') }}. For Zion's Sake, We Will Not Rest.
        </p>
    </footer>
</body>

</html>