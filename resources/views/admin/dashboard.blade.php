@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@section('content')
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
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Gender Distribution</h3>
            <div class="space-y-3">
                @php
                    $total = array_sum($genderDistribution) ?: 1;
                @endphp
                @foreach($genderDistribution as $gender => $count)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="font-medium text-gray-600">{{ $gender }}</span>
                            <span class="text-gray-500">{{ $count }} ({{ round($count / $total * 100, 1) }}%)</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5">
                            <div class="h-2.5 rounded-full {{ $gender === 'Male' ? 'bg-indigo-500' : 'bg-rose-400' }}"
                                style="width: {{ round($count / $total * 100) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Monthly Trend --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Monthly First Timers Trend</h3>
            @if(count($monthlyTrend) > 0)
                @php $maxCount = max($monthlyTrend) ?: 1; @endphp
                <div class="flex items-end gap-2 h-40">
                    @foreach($monthlyTrend as $month => $count)
                        <div class="flex-1 flex flex-col items-center gap-1">
                            <span class="text-xs font-medium text-gray-600">{{ $count }}</span>
                            <div class="w-full bg-gradient-to-t from-indigo-500 to-indigo-400 rounded-t-md"
                                style="height: {{ ($count / $maxCount) * 100 }}%"></div>
                            <span class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($month . '-01')->format('M') }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-400">No data available yet.</p>
            @endif
        </div>
    </div>

    {{-- Church Performance Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-700">Church Performance</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Church</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Category / Group</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Officer</th>
                        <th class="px-6 py-3 text-center font-medium text-gray-500">Total</th>
                        <th class="px-6 py-3 text-center font-medium text-gray-500">New</th>
                        <th class="px-6 py-3 text-center font-medium text-gray-500">In Progress</th>
                        <th class="px-6 py-3 text-center font-medium text-gray-500">Members</th>
                        <th class="px-6 py-3 text-center font-medium text-gray-500">Retention</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($churchPerformance as $church)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-6 py-3 font-medium text-gray-900">{{ $church['name'] }}</td>
                            <td class="px-6 py-3 text-gray-500">{{ $church['category'] }} / {{ $church['group'] }}</td>
                            <td class="px-6 py-3 text-gray-500">{{ $church['retaining_officer'] }}</td>
                            <td class="px-6 py-3 text-center">{{ $church['total_first_timers'] }}</td>
                            <td class="px-6 py-3 text-center">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">{{ $church['new'] }}</span>
                            </td>
                            <td class="px-6 py-3 text-center">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">{{ $church['in_progress'] }}</span>
                            </td>
                            <td class="px-6 py-3 text-center">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">{{ $church['members'] }}</span>
                            </td>
                            <td
                                class="px-6 py-3 text-center font-semibold {{ $church['retention_rate'] >= 50 ? 'text-emerald-600' : 'text-amber-600' }}">
                                {{ $church['retention_rate'] }}%
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-400">No churches found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection