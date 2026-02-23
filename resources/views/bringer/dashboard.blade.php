@extends('layouts.app')

@section('title', 'My Impact Dashboard')

@section('content')
    <div class="px-4 py-6 sm:px-0">
        <!-- Header/Welcome -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Welcome, {{ $bringer->name }}</h1>
            <p class="text-gray-600 dark:text-slate-400 font-medium">Thank you for your impact. Here is a detailed overview of the souls you've brought.</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-10">
            <div class="bg-white dark:bg-slate-900 overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 dark:border-slate-800">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 dark:text-slate-400 truncate uppercase tracking-wider">Total Souls</dt>
                    <dd class="mt-1 text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ $stats['total_souls'] }}</dd>
                </div>
            </div>
            <div class="bg-white dark:bg-slate-900 overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 dark:border-slate-800">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 dark:text-slate-400 truncate uppercase tracking-wider">Retained</dt>
                    <dd class="mt-1 text-3xl font-bold text-green-600 dark:text-green-400">{{ $stats['retained'] }}</dd>
                </div>
            </div>
            <div class="bg-white dark:bg-slate-900 overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 dark:border-slate-800">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 dark:text-slate-400 truncate uppercase tracking-wider">Developing</dt>
                    <dd class="mt-1 text-3xl font-bold text-amber-600 dark:text-amber-400">{{ $stats['developing'] }}</dd>
                </div>
            </div>
            <div class="bg-white dark:bg-slate-900 overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 dark:border-slate-800">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 dark:text-slate-400 truncate uppercase tracking-wider">New Visitors</dt>
                    <dd class="mt-1 text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['new'] }}</dd>
                </div>
            </div>
        </div>

        <!-- First Timers List -->
        <div class="space-y-8">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                <svg class="h-6 w-6 mr-2 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Detailed Progress for Your Souls
            </h2>

            @forelse($firstTimers as $ft)
                <div class="bg-white dark:bg-slate-900 shadow-sm rounded-2xl border border-gray-100 dark:border-slate-800 overflow-hidden">
                    <!-- Top Info Bar -->
                    <div class="px-6 py-4 bg-gray-50/50 dark:bg-slate-800/30 border-b border-gray-100 dark:border-slate-800 flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $ft->full_name }}</h3>
                            <div class="flex items-center mt-1 space-x-3 text-sm text-gray-500 dark:text-slate-400">
                                <span class="flex items-center">
                                    <svg class="h-4 w-4 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                    {{ $ft->primary_contact }}
                                </span>
                                <span class="flex items-center">
                                    <svg class="h-4 w-4 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                    {{ $ft->church->name }}
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-medium text-gray-400 dark:text-slate-500 uppercase tracking-widest">
                                Joined {{ $ft->date_of_visit->format('M d, Y') }}
                            </span>
                            <span class="px-3 py-1 text-xs font-bold rounded-full 
                                {{ $ft->status === 'Retained' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 
                                   ($ft->status === 'Developing' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' : 
                                   'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400') }}">
                                {{ $ft->status }}
                            </span>
                        </div>
                    </div>

                    <!-- Details Sections -->
                    <div class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Foundation School Breakdown -->
                            <div>
                                <h4 class="text-xs font-bold text-gray-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-4 flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M12 14l9-5-9-5-9 5 9 5z" /><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm0 0V20" /></svg>
                                    Foundation School Progress 
                                    <span class="ml-2 text-indigo-600 dark:text-indigo-400">({{ $ft->foundationAttendances->where('completed', true)->count() }}/{{ $foundationClasses->count() }})</span>
                                </h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    @foreach($foundationClasses as $class)
                                        @php
                                            $attendance = $ft->foundationAttendances->where('foundation_class_id', $class->id)->first();
                                            $isCompleted = $attendance?->completed ?? false;
                                        @endphp
                                        <div class="flex items-center p-2.5 rounded-lg border {{ $isCompleted ? 'bg-green-50/30 border-green-100 dark:bg-green-900/10 dark:border-green-800/20' : 'bg-gray-50/30 border-gray-100 dark:bg-slate-800/20 dark:border-slate-700/30' }}">
                                            <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold {{ $isCompleted ? 'bg-green-500 text-white shadow-sm' : 'bg-gray-200 text-gray-400 dark:bg-slate-700 dark:text-slate-500' }}">
                                                @if($isCompleted)
                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                                @else
                                                    {{ $class->class_number }}
                                                @endif
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-xs font-bold {{ $isCompleted ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-slate-400' }}">{{ $class->name }}</p>
                                                <p class="text-[10px] {{ $isCompleted ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-slate-500' }} font-medium">
                                                    {{ $isCompleted ? 'Attended & Completed' : 'Not yet attended' }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Attendance History Breakdown -->
                            <div>
                                <h4 class="text-xs font-bold text-gray-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-4 flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    Church Attendance History
                                    <span class="ml-2 text-emerald-600 dark:text-emerald-400">({{ $ft->weeklyAttendances->where('attended', true)->count() }} Services)</span>
                                </h4>
                                <div class="bg-gray-50/50 dark:bg-slate-800/20 rounded-xl p-4 border border-gray-100 dark:border-slate-800/50">
                                    <div class="space-y-3 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
                                        @forelse($ft->weeklyAttendances->where('attended', true)->sortByDesc('service_date') as $att)
                                            <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-slate-700/30 last:border-0 hover:bg-white/50 dark:hover:bg-white/5 transition px-2 rounded-lg">
                                                <div class="flex items-center">
                                                    <div class="w-2 h-2 rounded-full bg-emerald-500 mr-3 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></div>
                                                    <span class="text-sm font-medium text-gray-700 dark:text-slate-300">{{ $att->service_date->format('l, F jS, Y') }}</span>
                                                </div>
                                                <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 px-2 py-0.5 rounded uppercase tracking-wider">Present</span>
                                            </div>
                                        @empty
                                            <div class="py-8 text-center">
                                                <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <p class="mt-2 text-xs text-gray-400 dark:text-slate-500 italic">No attendance records found yet.</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-slate-900 shadow-sm rounded-2xl border border-gray-100 dark:border-slate-800 p-12 text-center">
                    <div class="mx-auto w-16 h-16 bg-gray-100 dark:bg-slate-800 rounded-full flex items-center justify-center mb-4">
                        <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">No souls registered yet</h3>
                    <p class="text-gray-500 dark:text-slate-400 mt-1 max-w-xs mx-auto">Start spreading the word and bringing impact! Once you register souls, their progress will appear here.</p>
                </div>
            @endforelse
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.03);
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(0,0,0,0.1);
            border-radius: 10px;
        }
        .dark .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.02);
        }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.1);
        }
    </style>
@endsection