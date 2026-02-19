@extends('layouts.app')
@section('title', $firstTimer->full_name)
@section('page-title', $firstTimer->full_name)

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.first-timers.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">← Back to
            First Timers</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Info --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Personal Details --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-700">Personal Information</h3>
                    @php
                        $sc = ['New' => 'bg-amber-100 text-amber-700', 'In Progress' => 'bg-blue-100 text-blue-700', 'Member' => 'bg-emerald-100 text-emerald-700'];
                    @endphp
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $sc[$firstTimer->status] ?? 'bg-gray-100' }}">{{ $firstTimer->status }}</span>
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
                        <dt class="text-gray-500">Marital Status</dt>
                        <dd class="font-medium text-gray-900">{{ $firstTimer->marital_status ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Date of Birth</dt>
                        <dd class="font-medium text-gray-900">{{ $firstTimer->date_of_birth?->format('M d, Y') ?? '—' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Occupation</dt>
                        <dd class="font-medium text-gray-900">{{ $firstTimer->occupation ?? '—' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-gray-500">Address</dt>
                        <dd class="font-medium text-gray-900">{{ $firstTimer->residential_address }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Visit Info --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">Visit & Church Details</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500">Church</dt>
                        <dd class="font-medium text-gray-900">{{ $firstTimer->church->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Date of Visit</dt>
                        <dd class="font-medium text-gray-900">{{ $firstTimer->date_of_visit?->format('M d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Church Event</dt>
                        <dd class="font-medium text-gray-900">{{ $firstTimer->church_event ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Retaining Officer</dt>
                        <dd class="font-medium text-gray-900">{{ $firstTimer->retainingOfficer->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Brought By</dt>
                        <dd class="font-medium text-gray-900">{{ $firstTimer->bringer_name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Bringer Contact</dt>
                        <dd class="font-medium text-gray-900">{{ $firstTimer->bringer_contact ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Spiritual --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">Spiritual Information</h3>
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
            {{-- Actions --}}
            @if($firstTimer->status !== 'Member')
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 space-y-2">
                    <a href="{{ route('admin.first-timers.edit', $firstTimer) }}"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Edit</a>
                    <a href="{{ route('admin.foundation-school.show', $firstTimer) }}"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-medium rounded-lg transition">Foundation
                        School</a>
                </div>
            @endif

            {{-- Foundation Progress --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">Foundation School Progress</h3>
                <div class="space-y-3">
                    @foreach($foundationProgress as $item)
                        <div class="flex items-center gap-3">
                            <div
                                class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold
                                        {{ $item['completed'] ? 'bg-emerald-500 text-white' : ($item['attended'] ? 'bg-amber-400 text-white' : 'bg-gray-200 text-gray-400') }}">
                                {{ $item['completed'] ? '✓' : $item['class']->class_number }}
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium {{ $item['completed'] ? 'text-emerald-700' : 'text-gray-700' }}">
                                    {{ $item['class']->name }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Weekly Attendance --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">Weekly Attendance</h3>
                @if($firstTimer->weeklyAttendances->count())
                    <div class="space-y-2">
                        @foreach($firstTimer->weeklyAttendances->sortBy('week_number') as $wa)
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">Week {{ $wa->week_number }}</span>
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $wa->attended ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $wa->attended ? 'Present' : 'Absent' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-400">No attendance records yet.</p>
                @endif
            </div>
        </div>
    </div>
@endsection