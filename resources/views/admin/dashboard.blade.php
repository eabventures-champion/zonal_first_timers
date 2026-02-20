@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    @if($stats['pending_approvals'] > 0)
        <div
            class="mb-8 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/50 rounded-xl p-4 flex items-center justify-between animate-pulse">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center text-amber-600 dark:text-amber-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-amber-800 dark:text-amber-200">Pending Membership Approvals</h4>
                    <p class="text-xs text-amber-700 dark:text-amber-400">There are {{ $stats['pending_approvals'] }} first
                        timers ready for migration to Member status.</p>
                </div>
            </div>
            <a href="{{ route('admin.membership-approvals.index') }}"
                class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-lg transition-colors shadow-sm shadow-amber-200 dark:shadow-none">
                Review Now
            </a>
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <x-stats-card label="Total Churches" :value="$stats['total_churches']" color="indigo" />
        <x-stats-card label="Total First Timers" :value="$stats['total_first_timers']" color="sky" />
        <x-stats-card label="New First Timers" :value="$stats['new_first_timers']" color="amber" />
        <x-stats-card label="Converted Members" :value="$stats['total_members']" color="emerald" />
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <x-stats-card label="In Progress" :value="$stats['in_progress']" color="violet" />
        <x-stats-card label="Categories" :value="$stats['total_categories']" color="orange" />
        <x-stats-card label="Groups" :value="$stats['total_groups']" color="teal" />
        <x-stats-card label="Retaining Officers" :value="$stats['total_retaining_officers']" color="rose" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- Gender Distribution --}}
        <div
            class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-6 flex flex-col h-full">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-slate-300 mb-4">Gender Distribution</h3>
            <div class="space-y-6 max-h-[160px] overflow-y-auto pr-2 custom-scrollbar">
                @foreach($genderDistribution as $label => $group)
                    <div class="space-y-2">
                        @if(count($genderDistribution) > 1)
                            <h4 class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider">
                                {{ $label }}
                            </h4>
                        @endif
                        <div class="space-y-3">
                            @foreach($group['data'] as $gender => $count)
                                <div>
                                    <div class="flex justify-between text-[11px] mb-1">
                                        <span class="font-medium text-gray-600 dark:text-slate-400">{{ $gender }}</span>
                                        <span class="text-gray-500 dark:text-slate-500">{{ $count }}
                                            ({{ round($count / $group['total'] * 100, 1) }}%)</span>
                                    </div>
                                    <div class="w-full bg-gray-100 dark:bg-slate-800 rounded-full h-1.5">
                                        <div class="h-1.5 rounded-full {{ $gender === 'Male' ? 'bg-indigo-500' : 'bg-rose-400' }}"
                                            style="width: {{ round($count / $group['total'] * 100) }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>



        {{-- Upcoming Birthdays --}}
        <div
            class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-6 flex flex-col h-full">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-slate-300 mb-4">Birthdays (This Month)</h3>
            <div class="space-y-4 max-h-[160px] overflow-y-auto pr-2 custom-scrollbar">
                @forelse($upcomingBirthdays as $birthday)
                    @php
                        $isPast = $birthday->date_of_birth->day < now()->day;
                    @endphp
                    <div
                        class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors {{ $isPast ? 'opacity-50' : '' }}">
                        <div
                            class="w-10 h-10 shrink-0 rounded-full {{ $isPast ? 'bg-gray-100 dark:bg-slate-800 text-gray-400' : 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400' }} flex items-center justify-center font-bold text-xs relative">
                            {{ $birthday->date_of_birth->format('d') }}
                            @if($isPast)
                                <span class="absolute -top-1 -right-1 bg-emerald-500 text-white rounded-full p-0.5">
                                    <svg class="w-2 h-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                </span>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-slate-200 truncate">
                                {{ $birthday->full_name }}
                                <span
                                    class="text-[10px] text-gray-400 dark:text-slate-500 ml-1 font-normal">({{ $birthday->primary_contact }})</span>
                            </p>
                            <p class="text-xs text-gray-500 dark:text-slate-400">
                                {{ $birthday->date_of_birth->format('M') }} {{ $isPast ? '(Passed)' : '' }}
                            </p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 dark:text-slate-600 text-center py-4">No upcoming birthdays.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Monthly Trend --}}
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-6 mb-8 overflow-hidden w-full"
        x-data="{ chartMode: localStorage.getItem('trendChartMode') || 'curve' }">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-lg bg-teal-500/10 flex items-center justify-center text-teal-600 dark:text-teal-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-800 dark:text-slate-200">Monthly Registration Trend</h3>
                    <p class="text-[10px] text-gray-400 dark:text-slate-500 uppercase tracking-widest font-semibold">
                        Registration Flow Overview</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                {{-- Chart Type Toggle --}}
                <div class="flex bg-gray-100 dark:bg-slate-800 p-1 rounded-lg">
                    <button
                        @click="chartMode = 'curve'; localStorage.setItem('trendChartMode', 'curve'); $dispatch('toggle-chart', 'area')"
                        :class="chartMode === 'curve' ? 'bg-white dark:bg-slate-700 shadow-sm text-teal-600 dark:text-teal-400' : 'text-gray-500 dark:text-slate-500 hover:text-gray-700'"
                        class="px-3 py-1 text-[11px] font-bold rounded-md transition-all flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4" />
                        </svg>
                        Curve
                    </button>
                    <button
                        @click="chartMode = 'bars'; localStorage.setItem('trendChartMode', 'bars'); $dispatch('toggle-chart', 'bar')"
                        :class="chartMode === 'bars' ? 'bg-white dark:bg-slate-700 shadow-sm text-teal-600 dark:text-teal-400' : 'text-gray-500 dark:text-slate-500 hover:text-gray-700'"
                        class="px-3 py-1 text-[11px] font-bold rounded-md transition-all flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Bars
                    </button>
                </div>

                <div class="flex items-center gap-3" x-data="{ editingTarget: false }">
                    {{-- Target Switcher --}}
                    <div class="relative flex items-center gap-2">
                        <template x-if="!editingTarget">
                            <button @click="editingTarget = true"
                                class="group px-3 py-1.5 bg-teal-50 dark:bg-teal-900/20 border border-teal-100 dark:border-teal-800/50 rounded-lg flex items-center gap-2 text-[11px] font-bold text-teal-700 dark:text-teal-400 hover:bg-teal-100 dark:hover:bg-teal-900/40 transition-all">
                                <span>Target: {{ $stats['monthly_target'] }}/mo</span>
                                <svg class="w-3 h-3 opacity-50 group-hover:opacity-100 transition-opacity" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </button>
                        </template>
                        <template x-if="editingTarget">
                            <form action="{{ route('admin.dashboard.update-target') }}" method="POST"
                                class="flex items-center gap-1 animate-in slide-in-from-right-2 duration-200">
                                @csrf
                                <input type="number" name="target" value="{{ $stats['monthly_target'] }}"
                                    class="w-16 px-2 py-1 text-[11px] font-bold border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-lg focus:ring-teal-500 focus:border-teal-500 dark:text-slate-200"
                                    required min="1">
                                <button type="submit" class="p-1.5 bg-teal-600 text-white rounded-lg hover:bg-teal-700">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>
                                <button type="button" @click="editingTarget = false"
                                    class="p-1.5 bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-slate-400 rounded-lg hover:bg-gray-200 dark:hover:bg-slate-600">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </form>
                        </template>
                    </div>

                    {{-- Period Dropdown --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false"
                            class="px-3 py-1.5 bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg flex items-center gap-2 text-[11px] font-bold text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                            <span>{{ collect(['last_6_months' => 'Last 6 Months', 'this_year' => 'This Year', 'last_year' => 'Last Year'])->get(request('trend_period', 'last_6_months')) }}</span>
                            <svg class="w-3 h-3 text-gray-400 transition-transform duration-200"
                                :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" x-transition
                            class="absolute right-0 mt-2 w-40 bg-white dark:bg-slate-800 border border-gray-100 dark:border-slate-700 rounded-xl shadow-xl z-50 overflow-hidden">
                            <a href="{{ route('admin.dashboard', ['trend_period' => 'last_6_months']) }}"
                                class="block px-4 py-2 text-[11px] font-semibold text-gray-600 dark:text-slate-400 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">Last
                                6 Months</a>
                            <a href="{{ route('admin.dashboard', ['trend_period' => 'this_year']) }}"
                                class="block px-4 py-2 text-[11px] font-semibold text-gray-600 dark:text-slate-400 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">This
                                Year</a>
                            <a href="{{ route('admin.dashboard', ['trend_period' => 'last_year']) }}"
                                class="block px-4 py-2 text-[11px] font-semibold text-gray-600 dark:text-slate-400 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">Last
                                Year</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(count($monthlyTrend) > 0)
            <div id="trendChart" class="w-full"></div>
        @else
            <div class="h-40 flex flex-col items-center justify-center text-gray-400 dark:text-slate-600">
                <svg class="w-12 h-12 mb-2 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <p class="text-sm font-medium">No registration data available yet</p>
            </div>
        @endif
    </div>

    @if(count($monthlyTrend) > 0)
        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const trendData = @json($monthlyTrend);
                    const targetValue = {{ \App\Models\Setting::get('monthly_registration_target', 30) }};

                    const months = trendData.labels;
                    const categorySeries = trendData.series;
                    const targetCounts = months.map(() => targetValue);

                    const initialMode = localStorage.getItem('trendChartMode') === 'bars' ? 'bar' : 'area';

                    // Colors for categories
                    const categoryColors = ['#10b981', '#3b82f6', '#f59e0b', '#8b5cf6', '#f43f5e', '#06b6d4'];

                    const getSeries = (mode) => {
                        const series = [...categorySeries];
                        if (mode === 'bar') {
                            series.push({
                                name: 'Target',
                                type: 'column',
                                data: targetCounts,
                                color: '#94a3b8'
                            });
                        } else {
                            // In area mode, maybe show target as a line?
                            series.push({
                                name: 'Target',
                                type: 'line',
                                data: targetCounts,
                                color: '#94a3b8'
                            });
                        }
                        return series;
                    };

                    const options = {
                        series: getSeries(initialMode),
                        chart: {
                            type: initialMode === 'bar' ? 'bar' : 'area',
                            height: 320,
                            stacked: true,
                            toolbar: { show: false },
                            zoom: { enabled: false },
                            animations: {
                                enabled: true,
                                easing: 'easeinout',
                                speed: 800,
                            }
                        },
                        stroke: {
                            width: initialMode === 'area' ? 3 : 0,
                            curve: 'smooth'
                        },
                        colors: [...categoryColors.slice(0, categorySeries.length), '#94a3b8'],
                        plotOptions: {
                            bar: {
                                horizontal: false,
                                columnWidth: '35%',
                                borderRadius: 4,
                            },
                        },
                        dataLabels: { enabled: false },
                        xaxis: {
                            categories: months,
                            axisBorder: { show: false },
                            axisTicks: { show: false },
                            labels: {
                                hideOverlappingLabels: true,
                                style: {
                                    colors: '#94a3b8',
                                    fontSize: '11px',
                                    fontWeight: 600,
                                }
                            }
                        },
                        yaxis: {
                            labels: {
                                style: {
                                    colors: '#94a3b8',
                                    fontSize: '11px',
                                    fontWeight: 600,
                                }
                            }
                        },
                        grid: {
                            borderColor: document.documentElement.classList.contains('dark') ? '#1e293b' : '#f1f5f9',
                            strokeDashArray: 4,
                            padding: { left: 10, right: 10 }
                        },
                        fill: {
                            type: initialMode === 'area' ? 'gradient' : 'solid',
                            opacity: 1
                        },
                        tooltip: {
                            theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
                            shared: true,
                            intersect: false,
                            y: {
                                formatter: function (val, { series, seriesIndex, dataPointIndex, w }) {
                                    if (w.config.series[seriesIndex].name === 'Target') {
                                        return val;
                                    }

                                    // Calculate total for this month (excluding target)
                                    let monthTotal = 0;
                                    for (let i = 0; i < categorySeries.length; i++) {
                                        monthTotal += series[i][dataPointIndex];
                                    }

                                    const percentage = monthTotal > 0 ? ((val / monthTotal) * 100).toFixed(1) : 0;
                                    const targetPercent = ((monthTotal / targetValue) * 100).toFixed(0);

                                    if (seriesIndex === categorySeries.length - 1) {
                                        // Add a summary line in the last category tooltip
                                        return `${val} (${percentage}%) <br><small class="text-gray-400">Total: ${monthTotal} (${targetPercent}% of target)</small>`;
                                    }

                                    return `${val} (${percentage}%)`;
                                }
                            }
                        },
                        legend: {
                            show: true,
                            position: 'top',
                            horizontalAlign: 'right',
                            labels: { colors: '#94a3b8' }
                        }
                    };

                    const chart = new ApexCharts(document.querySelector("#trendChart"), options);

                    setTimeout(() => {
                        chart.render();
                        window.dispatchEvent(new Event('resize'));
                    }, 100);

                    const resizeObserver = new ResizeObserver(() => {
                        if (chart && typeof chart.windowResize === 'function') {
                            chart.windowResize();
                        }
                    });
                    const chartImpactContainer = document.querySelector("#trendChart");
                    if (chartImpactContainer) {
                        resizeObserver.observe(chartImpactContainer);
                    }

                    window.addEventListener('toggle-chart', (e) => {
                        const newType = e.detail;
                        const newSeries = getSeries(newType);

                        chart.updateOptions({
                            chart: { type: newType === 'area' ? 'area' : 'bar' },
                            series: newSeries,
                            stroke: { width: newType === 'area' ? 3 : 0 },
                            fill: {
                                type: newType === 'area' ? 'gradient' : 'solid'
                            }
                        });

                        setTimeout(() => {
                            chart.windowResize();
                            window.dispatchEvent(new Event('resize'));
                        }, 50);
                    });

                    window.addEventListener('storage', (e) => {
                        if (e.key === 'darkMode') {
                            chart.updateOptions({
                                tooltip: { theme: e.newValue === 'true' ? 'dark' : 'light' },
                                grid: { borderColor: e.newValue === 'true' ? '#1e293b' : '#f1f5f9' }
                            });
                        }
                    });
                });
            </script>
        @endpush
    @endif


    {{-- Church Performance Hierarchy --}}
    <div
        class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden mt-20">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-800 flex justify-between items-center">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-slate-300">Church Performance Hierarchy</h3>
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Sorted by Retention Rate</span>
        </div>

        <div class="p-4 space-y-4">
            @forelse($churchPerformance as $category)
                <div x-data="{ expanded: false }"
                    class="border border-gray-100 dark:border-slate-800 rounded-lg overflow-hidden transition-all">
                    <button @click="expanded = !expanded"
                        class="w-full flex items-center justify-between px-4 py-3 bg-gray-50 dark:bg-slate-800/50 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="p-1 rounded-md bg-white dark:bg-slate-900 text-gray-400 shadow-sm">
                                <svg class="w-4 h-4 transition-transform" :class="expanded ? 'rotate-90' : ''" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </span>
                            <span
                                class="font-bold text-gray-900 dark:text-slate-200 text-sm tracking-tight">{{ $category['name'] }}</span>
                            <span class="text-[10px] text-gray-500 font-medium">({{ $category['total_churches'] ?? 0 }}
                                Churches)</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="text-[10px] text-gray-500 dark:text-slate-400 flex items-center gap-1">
                                <span class="font-semibold">AVG Retention:</span>
                                <span
                                    class="text-emerald-600 dark:text-emerald-400 font-bold">{{ $category['total_retention'] }}%</span>
                            </div>
                        </div>
                    </button>

                    <div x-show="expanded" x-collapse>
                        <div class="p-4 space-y-6">
                            @foreach($category['groups'] as $group)
                                <div x-data="{ expanded: true }" class="ml-4">
                                    <button @click="expanded = !expanded"
                                        class="flex items-center justify-between w-full mb-3 group/grp">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-3 h-3 text-gray-400 transition-transform group-hover/grp:text-indigo-500"
                                                :class="expanded ? 'rotate-90' : ''" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7" />
                                            </svg>
                                            <span
                                                class="text-sm font-semibold text-gray-700 dark:text-slate-300 group-hover/grp:text-indigo-600 transition-colors">{{ $group['name'] }}</span>
                                            <span class="text-[10px] text-gray-400 font-normal">({{ count($group['churches']) }}
                                                Churches)</span>
                                        </div>
                                        <div class="text-[10px] font-medium text-gray-500 dark:text-slate-400 italic">Group
                                            Performance: {{ $group['total_retention'] }}%</div>
                                    </button>

                                    <div x-show="expanded" x-collapse
                                        class="ml-5 border-l-2 border-gray-100 dark:border-slate-800 overflow-x-auto">
                                        <table class="w-full text-[13px]">
                                            <thead class="bg-gray-50/50 dark:bg-slate-800/30">
                                                <tr
                                                    class="text-left text-[11px] text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                                                    <th class="px-4 py-2 font-medium">Church</th>
                                                    <th class="px-4 py-2 font-medium">Officer</th>
                                                    <th class="px-4 py-2 text-center font-medium">Total</th>
                                                    <th class="px-4 py-2 text-center font-medium">New</th>
                                                    <th class="px-4 py-2 text-center font-medium">In Progress</th>
                                                    <th class="px-4 py-2 text-center font-medium">Members</th>
                                                    <th class="px-4 py-2 text-center font-medium">Retention</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-50 dark:divide-slate-800">
                                                @foreach($group['churches'] as $church)
                                                    <tr
                                                        class="hover:bg-gray-50/50 dark:hover:bg-slate-800/50 transition-colors group/item">
                                                        <td class="px-4 py-2.5 font-medium text-gray-900 dark:text-slate-200">
                                                            {{ $church['name'] }}
                                                        </td>
                                                        <td class="px-4 py-2.5 text-gray-500 dark:text-slate-400">
                                                            {{ $church['retaining_officer'] }}
                                                        </td>
                                                        <td
                                                            class="px-4 py-2.5 text-center text-gray-600 dark:text-slate-400 font-semibold">
                                                            {{ $church['total_first_timers'] }}
                                                        </td>
                                                        <td class="px-4 py-2.5 text-center">
                                                            <span
                                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">{{ $church['new'] }}</span>
                                                        </td>
                                                        <td class="px-4 py-2.5 text-center">
                                                            <span
                                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">{{ $church['in_progress'] }}</span>
                                                        </td>
                                                        <td class="px-4 py-2.5 text-center">
                                                            <span
                                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">{{ $church['members'] }}</span>
                                                        </td>
                                                        <td
                                                            class="px-4 py-2.5 text-center font-bold {{ $church['retention_rate'] >= 50 ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">
                                                            {{ $church['retention_rate'] }}%
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-12 text-center text-gray-400 dark:text-slate-600">
                    <p class="text-sm">No data found.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection