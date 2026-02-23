@extends('layouts.app')
@section('title', 'My Dashboard')
@section('page-title', 'Welcome, ' . explode(' ', $record->full_name)[0])

@section('content')
    <div class="space-y-6">
        {{-- Status Header --}}
        <div class="bg-indigo-600 rounded-2xl p-6 text-white shadow-lg overflow-hidden relative">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <h2 class="text-2xl font-bold mb-1">Hello, {{ $record->full_name }}!</h2>
                    <p class="text-indigo-100 text-sm">Welcome to your personal church dashboard.</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="px-4 py-2 bg-white/10 backdrop-blur-md rounded-xl border border-white/20">
                        <p class="text-[10px] uppercase tracking-widest text-indigo-200 font-bold mb-0.5">Current Status</p>
                        <p class="text-lg font-bold">{{ $record->status }}</p>
                    </div>
                    @if($member && $record->migrated_at)
                        <div class="px-4 py-2 bg-emerald-500/20 backdrop-blur-md rounded-xl border border-emerald-500/30">
                            <p class="text-[10px] uppercase tracking-widest text-emerald-200 font-bold mb-0.5">Member Since</p>
                            <p class="text-lg font-bold">{{ $record->migrated_at->format('M d, Y') }}</p>
                        </div>
                    @endif
                </div>
            </div>
            {{-- Decorative pattern --}}
            <div class="absolute top-0 right-0 -translate-y-12 translate-x-12 opacity-10">
                <svg class="w-64 h-64" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2L1 21h22L12 2zm0 3.45l8.2 14.1H3.8L12 5.45z" />
                </svg>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left Column: Stats & Progress --}}
            <div class="space-y-6 lg:col-span-1">
                {{-- Foundation School Progress --}}
                <div
                    class="bg-white dark:bg-slate-900 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-slate-800">
                    <h3 class="text-sm font-bold text-gray-700 dark:text-slate-300 mb-4 uppercase tracking-wider">Foundation
                        School</h3>
                    <div class="flex flex-col items-start gap-1 mb-2">
                        <span
                            class="text-xs font-semibold text-gray-500 uppercase">{{ $record->foundation_school_status }}</span>
                        @if($record->foundation_school_status === 'in-progress')
                            <span class="text-[10px] text-indigo-600 dark:text-indigo-400 font-medium italic">
                                {{ $record->current_foundation_level }}
                            </span>
                        @endif
                    </div>
                    <div class="w-full bg-gray-100 dark:bg-slate-800 rounded-full h-2 mb-4">
                        <div class="bg-indigo-500 h-2 rounded-full transition-all duration-1000"
                            style="width: {{ $record->foundation_progress }}%"></div>
                    </div>
                    <p class="text-[11px] text-gray-400 dark:text-slate-500 italic text-center">
                        Keep attending classes to complete your discipleship journey.
                    </p>
                </div>

                {{-- Personal Info --}}
                <div
                    class="bg-white dark:bg-slate-900 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-slate-800">
                    <h3 class="text-sm font-bold text-gray-700 dark:text-slate-300 mb-4 uppercase tracking-wider">My Profile
                    </h3>
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="p-2 bg-slate-50 dark:bg-slate-800 rounded-lg text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold">Church</p>
                                <p class="text-sm text-gray-700 dark:text-slate-200">{{ $record->church->name }}</p>
                                <p class="text-[10px] text-gray-400">{{ $record->church->group->name }}
                                    ({{ $record->church->group->category->name }})</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="p-2 bg-slate-50 dark:bg-slate-800 rounded-lg text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.948V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold">Contact Info</p>
                                <p class="text-sm text-gray-700 dark:text-slate-200">{{ $record->primary_contact }}</p>
                                <p class="text-[10px] text-gray-400">{{ $record->email }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Attendance Report --}}
            <div class="lg:col-span-2 space-y-6">
                <div
                    class="bg-white dark:bg-slate-900 rounded-xl overflow-hidden shadow-sm border border-gray-100 dark:border-slate-800">
                    <div class="p-6 border-b border-gray-50 dark:border-slate-800 flex items-center justify-between">
                        <h3 class="text-sm font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider">Service
                            Attendance Report</h3>
                        <div class="flex items-center gap-2">
                            <span
                                class="px-3 py-1 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-bold rounded-lg border border-emerald-100 dark:border-emerald-500/20">
                                {{ $record->total_attended }} Total Attendances
                            </span>
                        </div>
                    </div>
                    <div class="p-0">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50/50 dark:bg-slate-800/50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-[11px] font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                                            Service Date</th>
                                        <th
                                            class="px-6 py-3 text-left text-[11px] font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                                            Status</th>
                                        <th
                                            class="px-6 py-3 text-left text-[11px] font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                                            Week</th>
                                        <th
                                            class="px-6 py-3 text-left text-[11px] font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                                            Notes</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                                    @foreach($record->weeklyAttendances as $attendance)
                                        <tr>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-700 dark:text-slate-300">
                                                {{ $attendance->service_date->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($attendance->attended)
                                                    <span
                                                        class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                                d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        <span class="text-[11px] font-bold">Present</span>
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 text-red-500 dark:text-red-400">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                                d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                        <span class="text-[11px] font-bold">Absent</span>
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500 dark:text-slate-500">
                                                Week {{ $attendance->week_number }}
                                            </td>
                                            <td class="px-6 py-4 text-xs text-gray-500 dark:text-slate-500">
                                                {{ $attendance->notes ?? '—' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    @if($record->weeklyAttendances->isEmpty())
                                        <tr>
                                            <td colspan="4"
                                                class="px-6 py-12 text-center text-gray-400 dark:text-slate-500 italic">
                                                No attendance records found yet.
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection