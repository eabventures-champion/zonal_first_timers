@extends('layouts.app')
@section('title', 'My Dashboard')
@section('page-title', 'Welcome, ' . explode(' ', $record->full_name)[0])

@section('content')
    <div class="space-y-6" x-data="{ 
            selectedSoul: null,
            showSoulModal: false,
            viewSoul(soul) {
                this.selectedSoul = soul;
                this.showSoulModal = true;
            }
        }">
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
                            <p class="text-lg font-bold">{{ $record->date_of_visit?->format('M d, Y') ?? '—' }}</p>
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

            {{-- Right Column: Attendance Report & Bringer Stats --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Souls I Brought Section (If Bringer) --}}
                @if($isBringer && $bringer)
                    <div
                        class="bg-white dark:bg-slate-900 rounded-xl overflow-hidden shadow-sm border border-gray-100 dark:border-slate-800">
                        <div class="p-6 border-b border-gray-50 dark:border-slate-800">
                            <h3 class="text-sm font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider">Souls I
                                Brought</h3>
                        </div>
                        <div class="p-6">
                            {{-- Soul Stats --}}
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                                <div
                                    class="p-3 bg-indigo-50 dark:bg-indigo-500/10 rounded-xl border border-indigo-100 dark:border-indigo-500/20">
                                    <p class="text-[10px] uppercase font-bold text-indigo-400 mb-1">Total</p>
                                    <p class="text-xl font-bold text-indigo-600 dark:text-indigo-400">
                                        {{ $bringerStats['total_souls'] }}</p>
                                </div>
                                <div
                                    class="p-3 bg-emerald-50 dark:bg-emerald-500/10 rounded-xl border border-emerald-100 dark:border-emerald-500/20">
                                    <p class="text-[10px] uppercase font-bold text-emerald-400 mb-1">Retained</p>
                                    <p class="text-xl font-bold text-emerald-600 dark:text-emerald-400">
                                        {{ $bringerStats['retained'] }}</p>
                                </div>
                                <div
                                    class="p-3 bg-blue-50 dark:bg-blue-500/10 rounded-xl border border-blue-100 dark:border-blue-500/20">
                                    <p class="text-[10px] uppercase font-bold text-blue-400 mb-1">Developing</p>
                                    <p class="text-xl font-bold text-blue-600 dark:text-blue-400">
                                        {{ $bringerStats['developing'] }}</p>
                                </div>
                                <div
                                    class="p-3 bg-amber-50 dark:bg-amber-500/10 rounded-xl border border-amber-100 dark:border-amber-500/20">
                                    <p class="text-[10px] uppercase font-bold text-amber-400 mb-1">New</p>
                                    <p class="text-xl font-bold text-amber-600 dark:text-amber-400">{{ $bringerStats['new'] }}
                                    </p>
                                </div>
                            </div>

                            {{-- Mini Souls Table --}}
                            @if($firstTimers->isNotEmpty())
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm">
                                        <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                                            @foreach($firstTimers->take(5) as $ft)
                                                <tr class="group hover:bg-gray-50/50 dark:hover:bg-slate-800/50">
                                                    <td class="py-3 px-1">
                                                        <div class="flex items-center gap-3">
                                                            <div
                                                                class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-[10px] font-bold text-slate-500">
                                                                {{ strtoupper(substr($ft->full_name, 0, 2)) }}
                                                            </div>
                                                            <div>
                                                                <div class="flex items-center gap-2">
                                                                    <p class="text-xs font-bold text-gray-700 dark:text-slate-300">
                                                                        {{ $ft->full_name }}</p>
                                                                    @if($ft->status === 'Retained')
                                                                        <span
                                                                            class="px-1.5 py-0.5 text-[8px] font-bold bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400 rounded uppercase tracking-wider">Retained</span>
                                                                    @endif
                                                                </div>
                                                                <p class="text-[10px] text-gray-400">{{ $ft->status }} • Joined
                                                                    {{ $ft->date_of_visit->format('M Y') }}</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="py-3 px-1 text-right flex items-center justify-end gap-2">
                                                        <span
                                                            class="text-[10px] text-gray-400 italic hidden sm:inline">{{ $ft->church->name }}</span>
                                                        <button @click="viewSoul({{ $ft->toJson() }})"
                                                            class="p-1.5 text-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-lg transition-colors"
                                                            title="View Details">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                            </svg>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if($firstTimers->count() > 5)
                                    <div class="mt-4 pt-4 border-t border-gray-50 dark:border-slate-800 text-center">
                                        <a href="{{ route('bringer.dashboard') }}"
                                            class="text-[11px] font-bold text-indigo-500 hover:text-indigo-600 uppercase tracking-wider">
                                            View All Souls →
                                        </a>
                                    </div>
                                @endif
                            @else
                                <p class="text-center py-4 text-xs text-gray-400 italic">You haven't registered any first timers
                                    yet.</p>
                            @endif
                        </div>
                    </div>
                @endif

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
        @if($isBringer && $bringer)
            {{-- Soul Details Modal --}}
            <div x-show="showSoulModal" class="fixed inset-0 z-[60] overflow-y-auto" x-cloak
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                    <div class="fixed inset-0 transition-opacity bg-slate-900/75 backdrop-blur-sm"
                        @click="showSoulModal = false"></div>

                    <div
                        class="inline-block w-full max-w-4xl my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-gray-100 dark:border-slate-800">
                        <template x-if="selectedSoul">
                            <div class="relative">
                                {{-- Modal Header --}}
                                <div
                                    class="px-6 py-4 bg-gray-50/50 dark:bg-slate-800/50 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between">
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-white"
                                            x-text="selectedSoul.full_name"></h3>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-[10px] uppercase font-bold text-slate-400"
                                                x-text="selectedSoul.church ? selectedSoul.church.name : ''"></span>
                                            <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                            <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded-full"
                                                :class="selectedSoul.status === 'Retained' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : (selectedSoul.status === 'Developing' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400')"
                                                x-text="selectedSoul.status"></span>
                                        </div>
                                    </div>
                                    <button @click="showSoulModal = false"
                                        class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-white transition-colors">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>

                                {{-- Modal Body --}}
                                <div class="p-6">
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                        {{-- Foundation School --}}
                                        <div>
                                            <h4
                                                class="text-xs font-bold text-gray-400 dark:text-slate-500 uppercase tracking-widest mb-4 flex items-center">
                                                <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 14l9-5-9-5-9 5 9 5z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                                </svg>
                                                Foundation School Progress
                                            </h4>
                                            <div
                                                class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                                                @foreach($foundationClasses as $class)
                                                    @php $classId = $class->id; @endphp
                                                    <div class="flex items-center p-3 rounded-xl border transition-colors"
                                                        :class="selectedSoul.foundation_attendances && selectedSoul.foundation_attendances.find(a => a.foundation_class_id == {{ $classId }} && a.completed) 
                                                                        ? 'bg-emerald-50/50 border-emerald-100 dark:bg-emerald-900/10 dark:border-emerald-800/20' 
                                                                        : 'bg-gray-50/20 border-gray-100 dark:bg-slate-800/20 dark:border-slate-700/30'">

                                                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-[10px] font-bold shrink-0"
                                                            :class="selectedSoul.foundation_attendances && selectedSoul.foundation_attendances.find(a => a.foundation_class_id == {{ $classId }} && a.completed)
                                                                            ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/20'
                                                                            : 'bg-gray-200 text-gray-400 dark:bg-slate-700 dark:text-slate-500'">
                                                            <template
                                                                x-if="selectedSoul.foundation_attendances && selectedSoul.foundation_attendances.find(a => a.foundation_class_id == {{ $classId }} && a.completed)">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="3" d="M5 13l4 4L19 7" />
                                                                </svg>
                                                            </template>
                                                            <template
                                                                x-if="!(selectedSoul.foundation_attendances && selectedSoul.foundation_attendances.find(a => a.foundation_class_id == {{ $classId }} && a.completed))">
                                                                <span>{{ $class->class_number }}</span>
                                                            </template>
                                                        </div>

                                                        <div class="ml-3">
                                                            <p class="text-[11px] font-bold"
                                                                :class="selectedSoul.foundation_attendances && selectedSoul.foundation_attendances.find(a => a.foundation_class_id == {{ $classId }} && a.completed) ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-slate-400'">
                                                                {{ $class->name }}</p>
                                                            <p class="text-[10px]"
                                                                :class="selectedSoul.foundation_attendances && selectedSoul.foundation_attendances.find(a => a.foundation_class_id == {{ $classId }} && a.completed) ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-400 dark:text-slate-500'">
                                                                <span
                                                                    x-text="selectedSoul.foundation_attendances && selectedSoul.foundation_attendances.find(a => a.foundation_class_id == {{ $classId }} && a.completed) ? 'Completed' : 'Not yet attended'"></span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        {{-- Attendance History --}}
                                        <div>
                                            <h4
                                                class="text-xs font-bold text-gray-400 dark:text-slate-500 uppercase tracking-widest mb-4 flex items-center">
                                                <svg class="w-4 h-4 mr-2 text-emerald-500" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                Church Attendance History
                                            </h4>
                                            <div
                                                class="bg-gray-50/50 dark:bg-slate-800/20 rounded-2xl p-4 border border-gray-100 dark:border-slate-800/50">
                                                <div class="space-y-2 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                                                    <template
                                                        x-for="att in (selectedSoul.weekly_attendances || []).filter(a => a.attended).sort((a, b) => new Date(b.service_date) - new Date(a.service_date))"
                                                        :key="att.id">
                                                        <div
                                                            class="flex items-center justify-between p-3 bg-white dark:bg-slate-900 rounded-xl border border-gray-100 dark:border-slate-800 shadow-sm">
                                                            <div class="flex items-center">
                                                                <div
                                                                    class="w-2 h-2 rounded-full bg-emerald-500 mr-3 shadow-[0_0_8px_rgba(16,185,129,0.5)]">
                                                                </div>
                                                                <span
                                                                    class="text-xs font-medium text-gray-700 dark:text-slate-300"
                                                                    x-text="new Date(att.service_date).toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' })"></span>
                                                            </div>
                                                            <span
                                                                class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Present</span>
                                                        </div>
                                                    </template>
                                                    <template
                                                        x-if="!(selectedSoul.weekly_attendances || []).filter(a => a.attended).length">
                                                        <div class="py-12 text-center">
                                                            <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-slate-700 mb-2"
                                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="1"
                                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                            <p class="text-[10px] text-gray-400 dark:text-slate-500 italic">No
                                                                attendance records found yet.</p>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.03);
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .dark .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.02);
        }

        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
        }
    </style>
@endsection