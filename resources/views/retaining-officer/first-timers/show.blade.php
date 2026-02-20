@extends('layouts.app')
@section('title', $firstTimer->full_name)
@section('page-title', $firstTimer->full_name)

@section('content')
    <div class="mb-4">
        <a href="{{ route('ro.first-timers.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">← Back to First
            Timers</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Info --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-700">Personal Information</h3>
                    @php $sc = ['New' => 'bg-amber-100 text-amber-700', 'Developing' => 'bg-blue-100 text-blue-700', 'Retained' => 'bg-emerald-100 text-emerald-700']; @endphp
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $sc[$firstTimer->status] ?? 'bg-gray-100 dark:bg-slate-800' }}">{{ $firstTimer->status }}</span>
                </div>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500">Email</dt>
                        <dd class="font-medium text-gray-900">{{ $firstTimer->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Primary Contact</dt>
                        <dd class="font-medium text-gray-900">{{ $firstTimer->primary_contact }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Gender</dt>
                        <dd class="font-medium text-gray-900">{{ $firstTimer->gender }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Date of Visit</dt>
                        <dd class="font-medium text-gray-900">{{ $firstTimer->date_of_visit?->format('M d, Y') }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-gray-500">Address</dt>
                        <dd class="font-medium text-gray-900">{{ $firstTimer->residential_address }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">Spiritual Info</h3>
                <div class="flex gap-4 mb-4">
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium {{ $firstTimer->born_again ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $firstTimer->born_again ? '✓' : '✗' }} Born Again
                    </span>
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium {{ $firstTimer->water_baptism ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $firstTimer->water_baptism ? '✓' : '✗' }} Water Baptism
                    </span>
                </div>
                @if($firstTimer->prayer_requests)
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Prayer Requests</p>
                        <p class="text-sm text-gray-900 bg-gray-50 rounded-lg p-3">{{ $firstTimer->prayer_requests }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            @if($firstTimer->status !== 'Retained')
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 space-y-2">
                    <a href="{{ route('ro.foundation-school.show', $firstTimer) }}"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Foundation
                        School</a>
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">Foundation Progress</h3>
                <div class="space-y-3">
                    @foreach($foundationProgress as $item)
                        <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors">
                            <div
                                class="w-6 h-6 shrink-0 rounded-full flex items-center justify-center text-xs font-bold
                                        {{ $item['completed'] ? 'bg-emerald-500 text-white' : ($item['attended'] ? 'bg-amber-400 text-white' : 'bg-gray-200 dark:bg-slate-800 text-gray-400') }}">
                                {{ $item['completed'] ? '✓' : $item['class']->class_number }}
                            </div>
                            <p class="text-sm {{ $item['completed'] ? 'text-emerald-700 dark:text-emerald-400 font-medium' : 'text-gray-700 dark:text-slate-300' }}">
                                {{ $item['class']->name }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">Weekly Attendance</h3>
                @if($firstTimer->weeklyAttendances->count())
                    <div class="space-y-1">
                        @foreach($firstTimer->weeklyAttendances->sortBy('week_number') as $wa)
                            <div class="flex items-center justify-between text-sm p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors">
                                <span class="text-gray-600 dark:text-slate-400">Week {{ $wa->week_number }}</span>
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $wa->attended ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400' }}">
                                    {{ $wa->attended ? 'Present' : 'Absent' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-400">No attendance yet.</p>
                @endif
            </div>
        </div>
    </div>
@endsection