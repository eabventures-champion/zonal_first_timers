@props(['icon' => null, 'value' => 0, 'label' => '', 'color' => 'indigo', 'trend' => null])

@php
    $colors = [
        'indigo' => 'from-indigo-500 to-indigo-600',
        'emerald' => 'from-emerald-500 to-emerald-600',
        'amber' => 'from-amber-500 to-amber-600',
        'rose' => 'from-rose-500 to-rose-600',
        'sky' => 'from-sky-500 to-sky-600',
        'violet' => 'from-violet-500 to-violet-600',
        'teal' => 'from-teal-500 to-teal-600',
        'orange' => 'from-orange-500 to-orange-600',
    ];
    $gradient = $colors[$color] ?? $colors['indigo'];
@endphp

<div
    class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-5 hover:shadow-md transition-all duration-200">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-500 dark:text-slate-400">{{ $label }}</p>
            <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-slate-100">{{ $value }}</p>
            @if($trend)
                <p class="mt-1 text-xs {{ $trend > 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                    {{ $trend > 0 ? '↑' : '↓' }} {{ abs($trend) }}%
                </p>
            @endif
        </div>
        <div
            class="w-12 h-12 rounded-xl bg-gradient-to-br {{ $gradient }} flex items-center justify-center text-white shadow-lg shadow-{{ $color }}-500/20">
            @if($icon)
                {!! $icon !!}
            @else
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            @endif
        </div>
    </div>
</div>