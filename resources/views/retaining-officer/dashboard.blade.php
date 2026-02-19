@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    {{-- Church Info Banner --}}
    <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 text-white rounded-xl p-6 mb-8 shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-indigo-200 text-sm">Your Church</p>
                <h2 class="text-2xl font-bold">{{ $church->name ?? 'Not Assigned' }}</h2>
                <p class="text-indigo-200 text-sm mt-1">{{ $church->group->name ?? '' }} ·
                    {{ $church->group->category->name ?? '' }}</p>
            </div>
            <div class="hidden sm:block w-16 h-16 rounded-full bg-white/10 flex items-center justify-center">
                <svg class="w-8 h-8 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <x-stats-card label="Total First Timers" :value="$stats['total_first_timers']" color="sky" />
        <x-stats-card label="New" :value="$stats['new_first_timers']" color="amber" />
        <x-stats-card label="In Progress" :value="$stats['in_progress']" color="violet" />
        <x-stats-card label="Members" :value="$stats['total_members']" color="emerald" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Recent First Timers --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-700">Recent First Timers</h3>
                <a href="{{ route('ro.first-timers.index') }}"
                    class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">View All →</a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($recentFirstTimers as $ft)
                    <a href="{{ route('ro.first-timers.show', $ft) }}"
                        class="flex items-center justify-between px-6 py-3 hover:bg-gray-50/50 transition">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $ft->full_name }}</p>
                            <p class="text-xs text-gray-400">{{ $ft->date_of_visit?->format('M d, Y') }}</p>
                        </div>
                        @php $sc = ['New' => 'bg-amber-100 text-amber-700', 'In Progress' => 'bg-blue-100 text-blue-700', 'Member' => 'bg-emerald-100 text-emerald-700']; @endphp
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $sc[$ft->status] ?? 'bg-gray-100' }}">{{ $ft->status }}</span>
                    </a>
                @empty
                    <div class="px-6 py-8 text-center text-gray-400 text-sm">No first timers yet.</div>
                @endforelse
            </div>
        </div>

        {{-- Foundation School Overview --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-700">Foundation School Status</h3>
                <a href="{{ route('ro.foundation-school.index') }}"
                    class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">View All →</a>
            </div>
            <div class="p-6">
                @if(isset($foundationStats) && ($foundationStats['enrolled'] ?? 0) > 0)
                    <div class="grid grid-cols-2 gap-4 text-center">
                        <div class="bg-sky-50 rounded-lg p-4">
                            <p class="text-2xl font-bold text-sky-700">{{ $foundationStats['enrolled'] ?? 0 }}</p>
                            <p class="text-xs text-sky-500">Enrolled</p>
                        </div>
                        <div class="bg-emerald-50 rounded-lg p-4">
                            <p class="text-2xl font-bold text-emerald-700">{{ $foundationStats['completed'] ?? 0 }}</p>
                            <p class="text-xs text-emerald-500">Completed</p>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-gray-400 text-center">No foundation school data yet.</p>
                @endif
            </div>
        </div>
    </div>
@endsection