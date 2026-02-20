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
        <x-stats-card label="Developing" :value="$stats['developing']" color="violet" />
        <x-stats-card label="Members" :value="$stats['total_members']" color="emerald" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Recent First Timers --}}
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-slate-300">Recent First Timers</h3>
                <a href="{{ route('ro.first-timers.index') }}"
                    class="text-xs text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium">View All →</a>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-slate-800">
                @forelse($recentFirstTimers as $ft)
                    <a href="{{ route('ro.first-timers.show', $ft) }}"
                        class="flex items-center justify-between px-6 py-3 hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $ft->full_name }}</p>
                            <p class="text-xs text-gray-400 dark:text-slate-500">{{ $ft->date_of_visit?->format('M d, Y') }}</p>
                        </div>
                        @php 
                            $sc = [
                                'New' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-500', 
                                'Developing' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400', 
                                'Retained' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400'
                            ]; 
                        @endphp
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $sc[$ft->status] ?? 'bg-gray-100 dark:bg-slate-800' }}">{{ $ft->status }}</span>
                    </a>
                @empty
                    <div class="px-6 py-8 text-center text-gray-400 dark:text-slate-500 text-sm">No first timers yet.</div>
                @endforelse
            </div>
        </div>

        {{-- Birthday Reminders --}}
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-slate-300">🎂 Birthday Reminders</h3>
                <span class="text-[10px] text-gray-400 dark:text-slate-500 uppercase tracking-wider font-bold">This Month & Next 30 Days</span>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-slate-800 max-h-[340px] overflow-y-auto">
                @forelse($upcomingBirthdays as $person)
                    <div class="flex items-center justify-between px-6 py-3 hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm
                                {{ $person->days_until === 0 ? 'bg-pink-100 dark:bg-pink-500/10' : ($person->already_passed ? 'bg-gray-100 dark:bg-slate-800' : 'bg-indigo-50 dark:bg-indigo-500/10') }}">
                                {{ $person->days_until === 0 ? '🎉' : '🎂' }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $person->full_name }}</p>
                                <p class="text-[10px] text-gray-400 dark:text-slate-500">
                                    {{ \Carbon\Carbon::parse($person->date_of_birth)->format('M d') }} · {{ $person->type }}
                                </p>
                            </div>
                        </div>
                        @if($person->already_passed)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-500 dark:bg-slate-800 dark:text-slate-400">
                                {{ abs($person->days_until) }} {{ abs($person->days_until) === 1 ? 'day' : 'days' }} ago
                            </span>
                        @elseif($person->days_until === 0)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-pink-100 text-pink-700 dark:bg-pink-500/10 dark:text-pink-400">
                                Today! 🎉
                            </span>
                        @elseif($person->days_until === 1)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400">
                                Tomorrow
                            </span>
                        @elseif($person->days_until <= 7)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400">
                                {{ $person->days_until }} days
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-500 dark:bg-slate-800 dark:text-slate-400">
                                {{ $person->days_until }} days
                            </span>
                        @endif
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-gray-400 dark:text-slate-500 text-sm">No upcoming birthdays in the next 30 days.</div>
                @endforelse
            </div>
        </div>
    </div>
@endsection