@extends('layouts.app')
@section('title', 'First Timers')
@section('page-title', 'First Timers')

@section('content')
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <p class="text-sm text-gray-500">Manage all first-time visitors</p>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.first-timers.import') }}"
                class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-medium rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                Import CSV
            </a>
            <a href="{{ route('admin.first-timers.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add First Timer
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-4 mb-6">
        <form method="GET" action="{{ route('admin.first-timers.index') }}"
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                placeholder="Search name, email, phone..."
                class="rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">

            <select name="church_id"
                class="rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">All Churches</option>
                @foreach($churches as $church)
                    <option value="{{ $church->id }}" {{ ($filters['church_id'] ?? '') == $church->id ? 'selected' : '' }}>
                        {{ $church->name }}
                    </option>
                @endforeach
            </select>

            <select name="status"
                class="rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">All Statuses</option>
                @foreach(['New', 'In Progress'] as $s)
                    <option value="{{ $s }}" {{ ($filters['status'] ?? '') === $s ? 'selected' : '' }}>{{ $s }}</option>
                @endforeach
            </select>

            <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" placeholder="From"
                class="rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">

            <div class="flex gap-2">
                <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" placeholder="To"
                    class="flex-1 rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <button type="submit"
                    class="px-4 py-2 bg-gray-800 dark:bg-indigo-600 text-white text-sm rounded-lg hover:bg-gray-900 dark:hover:bg-indigo-700 transition">Filter</button>
            </div>
        </form>
    </div>

    {{-- Import Errors --}}
    @if(session('import_errors'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <p class="text-sm font-medium text-red-800 mb-2">Import Errors:</p>
            <ul class="text-xs text-red-700 list-disc list-inside">
                @foreach(session('import_errors') as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Table --}}
    <div x-data="{ 
        showHistory: false, 
        historyName: '', 
        historyDates: [] 
    }" class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-slate-800/50">
                    <tr>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-slate-400">Name</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-slate-400">Contact</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-slate-400">Church</th>
                        <th class="px-6 py-3 text-center font-medium text-gray-500 dark:text-slate-400">Attendance</th>
                        <th class="px-6 py-3 text-center font-medium text-gray-500 dark:text-slate-400">FS Level</th>
                        <th class="px-6 py-3 text-center font-medium text-gray-500 dark:text-slate-400">Status</th>
                        <th class="px-6 py-3 text-right font-medium text-gray-500 dark:text-slate-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                    @forelse($firstTimers as $ft)
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors">
                            <td class="px-6 py-3">
                                <div class="font-medium text-gray-900 dark:text-white">{{ $ft->full_name }}</div>
                                <div class="text-[10px] text-gray-400 dark:text-slate-500">Joined:
                                    {{ $ft->date_of_visit?->format('M d, Y') }}</div>
                            </td>
                            <td class="px-6 py-3 text-gray-500 dark:text-slate-400">{{ $ft->primary_contact }}</td>
                            <td class="px-6 py-3 text-gray-500 dark:text-slate-400">{{ $ft->church->name ?? '—' }}</td>
                            <td class="px-6 py-3 text-center">
                                <button type="button"
                                    @click="historyName = '{{ $ft->full_name }}'; historyDates = {{ json_encode($ft->attendance_dates) }}; showHistory = true"
                                    class="inline-flex items-center gap-1.5 px-2 py-1 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 rounded-md hover:bg-emerald-100 dark:hover:bg-emerald-500/20 transition cursor-pointer">
                                    <span class="text-xs font-bold">{{ count($ft->attendance_dates) }}</span>
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </button>
                            </td>
                            <td class="px-6 py-3 text-center">
                                <span
                                    class="text-[10px] font-semibold text-gray-600 dark:text-slate-400 bg-gray-100 dark:bg-slate-800 px-2 py-0.5 rounded">
                                    {{ $ft->current_foundation_level }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-center">
                                @php
                                    $sc = [
                                        'New' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-500',
                                        'In Progress' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
                                        'Member' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400'
                                    ];
                                @endphp
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $sc[$ft->status] ?? 'bg-gray-100 dark:bg-slate-800' }}">{{ $ft->status }}</span>
                            </td>
                            <td class="px-6 py-3 text-right space-x-1">
                                <a href="{{ route('admin.first-timers.show', $ft) }}"
                                    class="text-sky-600 hover:text-sky-800 dark:text-sky-400 dark:hover:text-sky-300 text-xs font-medium">View</a>
                                <a href="{{ route('admin.first-timers.edit', $ft) }}"
                                    class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 text-xs font-medium">Edit</a>
                                <form method="POST" action="{{ route('admin.first-timers.destroy', $ft) }}" class="inline"
                                    onsubmit="return confirm('Delete this record?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 text-xs font-medium">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-400 dark:text-slate-500">No first timers
                                found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Attendance History Modal --}}
        <div x-show="showHistory"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" x-cloak
            @click.self="showHistory = false" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">

            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-gray-100 dark:border-slate-800 w-full max-w-sm overflow-hidden"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0">

                <div
                    class="px-6 py-4 border-b border-gray-100 dark:border-slate-800 flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white" x-text="historyName"></h3>
                        <p class="text-[10px] text-gray-500 uppercase tracking-widest font-bold">Attendance History
                        </p>
                    </div>
                    <button @click="showHistory = false"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l18 18" />
                        </svg>
                    </button>
                </div>

                <div class="p-6 max-h-[60vh] overflow-y-auto">
                    <template x-if="historyDates.length === 0">
                        <div class="text-center py-4">
                            <p class="text-sm text-gray-500">No recorded attendances yet.</p>
                        </div>
                    </template>

                    <div class="space-y-3">
                        <template x-for="(date, index) in historyDates" :key="index">
                            <div
                                class="flex items-center gap-3 p-3 bg-emerald-50 dark:bg-emerald-500/5 rounded-xl border border-emerald-100/50 dark:border-emerald-500/10">
                                <div
                                    class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900 dark:text-slate-200" x-text="date">
                                    </p>
                                    <p
                                        class="text-[10px] text-emerald-600/70 dark:text-emerald-400/70 font-bold uppercase">
                                        Sunday Service</p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div
                    class="px-6 py-4 border-t border-gray-100 dark:border-slate-800 bg-gray-50 dark:bg-slate-800/50">
                    <button @click="showHistory = false"
                        class="w-full py-2 bg-gray-900 dark:bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-black dark:hover:bg-indigo-700 transition">
                        Close
                    </button>
                </div>
            </div>
        </div>

        @if($firstTimers->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-800">
                {{ $firstTimers->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection