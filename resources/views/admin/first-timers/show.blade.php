@extends('layouts.app')
@section('title', $firstTimer->full_name)
@section('page-title', $firstTimer->full_name)
@section('back-link', route('admin.first-timers.index'))

@section('content')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Info --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Personal Details --}}
            <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-slate-300">Personal Information</h3>
                    @php
                        $sc = [
                            'New' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-500',
                            'Developing' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
                            'Retained' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400'
                        ];
                    @endphp
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $sc[$firstTimer->status] ?? 'bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300' }}">{{ $firstTimer->status }}</span>
                </div>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500 dark:text-slate-500">Email</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ $firstTimer->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-slate-500">Primary Contact</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ $firstTimer->primary_contact }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-slate-500">Gender</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ $firstTimer->gender }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-slate-500">Marital Status</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ $firstTimer->marital_status ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-slate-500">Date of Birth</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">
                            {{ $firstTimer->date_of_birth?->format('M d, Y') ?? '—' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-slate-500">Occupation</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ $firstTimer->occupation ?? '—' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-gray-500 dark:text-slate-500">Address</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ $firstTimer->residential_address }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Visit Info --}}
            <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-6">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-slate-300 mb-4">Visit & Church Details</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500 dark:text-slate-500">Church</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ $firstTimer->church->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-slate-500">Date of Visit</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">
                            {{ $firstTimer->date_of_visit?->format('M d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-slate-500">Church Event</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ $firstTimer->church_event ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-slate-500">Retaining Officer</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">
                            {{ $firstTimer->retainingOfficer->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-slate-500">Brought By</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ $firstTimer->bringer_name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-slate-500">Bringer Contact</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ $firstTimer->bringer_contact ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Spiritual --}}
            <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-6">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-slate-300 mb-4">Credentials</h3>
                <div class="flex gap-4 mb-4">
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium {{ $firstTimer->born_again ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-gray-100 dark:bg-slate-800 text-gray-500 dark:text-slate-400' }}">
                        {{ $firstTimer->born_again ? '✓' : '✗' }} Born Again
                    </span>
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium {{ $firstTimer->water_baptism ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-gray-100 dark:bg-slate-800 text-gray-500 dark:text-slate-400' }}">
                        {{ $firstTimer->water_baptism ? '✓' : '✗' }} Water Baptism
                    </span>
                </div>
                @if($firstTimer->prayer_requests)
                    <div>
                        <p class="text-sm text-gray-500 dark:text-slate-500 mb-1">Prayer Requests</p>
                        <p
                            class="text-sm text-gray-900 dark:text-slate-200 bg-gray-50 dark:bg-slate-800 p-3 rounded-xl border border-gray-100 dark:border-slate-700/50">
                            {{ $firstTimer->prayer_requests }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Actions --}}
            @if($firstTimer->status !== 'Retained')
                <div
                    class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-4 space-y-2">
                    <a href="{{ route('admin.first-timers.edit', $firstTimer) }}"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition shadow-sm shadow-indigo-500/20">Edit</a>
                    <a href="{{ route('admin.foundation-school.show', $firstTimer) }}"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 border border-gray-200 dark:border-slate-800 text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-800 text-sm font-medium rounded-lg transition">Foundation
                        School</a>
                </div>
            @endif

            {{-- Foundation Progress --}}
            <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-6">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-slate-300 mb-4">Foundation Progress</h3>
                <div class="space-y-3">
                    @foreach($foundationProgress as $item)
                        <div
                            class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors">
                            <div
                                class="w-6 h-6 shrink-0 rounded-full flex items-center justify-center text-[10px] font-bold
                                                        {{ $item['completed'] ? 'bg-emerald-500 text-white' : ($item['attended'] ? 'bg-amber-400 text-white' : 'bg-gray-200 dark:bg-slate-800 text-gray-400 dark:text-slate-600') }}">
                                {{ $item['completed'] ? '✓' : $item['class']->class_number }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p
                                    class="text-sm font-medium truncate {{ $item['completed'] ? 'text-emerald-700 dark:text-emerald-400' : 'text-gray-700 dark:text-slate-300' }}">
                                    {{ $item['class']->name }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Weekly Attendance --}}
            <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-6">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-slate-300 mb-4">Weekly Attendance</h3>
                @if($firstTimer->weeklyAttendances->count())
                    <div class="space-y-1">
                        @foreach($firstTimer->weeklyAttendances->sort(function($a, $b) {
                            if (($a->year ?? 0) != ($b->year ?? 0)) return ($b->year ?? 0) <=> ($a->year ?? 0);
                            if (($a->month ?? 0) != ($b->month ?? 0)) return ($b->month ?? 0) <=> ($a->month ?? 0);
                            return ($b->week_number ?? 0) <=> ($a->week_number ?? 0);
                        }) as $wa)
                            <div
                                class="flex items-center justify-between text-sm p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors">
                                <span class="text-gray-600 dark:text-slate-400 font-medium">
                                    {{ date('M Y', mktime(0, 0, 0, $wa->month ?? 1, 1)) }} - Week {{ $wa->week_number }}
                                </span>
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $wa->attended ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400' }}">
                                    {{ $wa->attended ? 'Present' : 'Absent' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-400 dark:text-slate-500 py-2">No attendance records yet.</p>
                @endif
            </div>
        </div>
    </div>
@endsection