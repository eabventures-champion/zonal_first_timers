@extends('layouts.app')
@section('title', 'Members')
@section('page-title', 'Members')

@section('content')
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <p class="text-sm text-gray-500">Manage all church members</p>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-4 mb-6">
        <form method="GET" action="{{ route('admin.members.index') }}"
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 items-center gap-3">
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search name, phone..."
                class="rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500 w-full">

            <select name="church_id"
                class="rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500 w-full">
                <option value="">All Churches</option>
                @foreach($churches as $church)
                    <option value="{{ $church->id }}" {{ ($filters['church_id'] ?? '') == $church->id ? 'selected' : '' }}>
                        {{ $church->name }}
                    </option>
                @endforeach
            </select>

            <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"
                class="rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500 w-full lg:w-36">

            <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"
                class="rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500 w-full lg:w-36">

            <div class="flex gap-2">
                <button type="submit"
                    class="px-4 py-2 bg-gray-800 dark:bg-indigo-600 text-white text-sm rounded-lg hover:bg-gray-900 dark:hover:bg-indigo-700 transition font-medium">Filter</button>
                @if(count(array_filter($filters)) > 0)
                    <a href="{{ route('admin.members.index') }}"
                        class="px-4 py-2 bg-red-50 hover:bg-red-100 dark:bg-red-500/10 dark:hover:bg-red-500/20 text-red-600 dark:text-red-400 text-sm rounded-lg transition flex items-center justify-center font-medium"
                        title="Clear all filters">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div x-data="{ 
                                        expandedGroups: new Set(),
                                        toggleGroup(name) {
                                            if (this.expandedGroups.has(name)) {
                                                this.expandedGroups.delete(name);
                                            } else {
                                                this.expandedGroups.add(name);
                                            }
                                        },
                                        showHistory: false, 
                                        historyName: '', 
                                        historyDates: [] 
                                    }" class="space-y-8">
        @forelse($groupedMembers as $groupName => $groupItems)
            <div class="space-y-3">
                <div class="flex items-center gap-2 px-1">
                    <button @click="toggleGroup('{{ $groupName }}')"
                        class="flex items-center gap-2 hover:opacity-70 transition text-left">
                        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200"
                            :class="{ '-rotate-90': !expandedGroups.has('{{ $groupName }}') }" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                        <h2 class="text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-widest">
                            {{ $groupName }}
                        </h2>
                        <span
                            class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                            {{ $groupItems->count() }} {{ Str::plural('Member', $groupItems->count()) }}
                        </span>
                    </button>
                    <div class="flex-1 border-t border-gray-100 dark:border-slate-800 ml-2"></div>
                </div>

                <div x-show="expandedGroups.has('{{ $groupName }}')" x-collapse
                    class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-slate-800/50">
                                <tr>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-slate-400">Name</th>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-slate-400">Contact</th>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-slate-400">Church</th>
                                    <th class="px-6 py-3 text-center font-medium text-gray-500 dark:text-slate-400">Attendance
                                    </th>
                                    <th class="px-6 py-3 text-center font-medium text-gray-500 dark:text-slate-400">Foundation School Status
                                    </th>
                                    <th class="px-6 py-3 text-center font-medium text-gray-500 dark:text-slate-400">Approved On
                                    </th>
                                    <th class="px-6 py-3 text-right font-medium text-gray-500 dark:text-slate-400">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                                @foreach($groupItems as $m)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors">
                                        <td class="px-6 py-3">
                                            <div class="flex items-center gap-2">
                                                <span class="font-medium text-gray-900 dark:text-white">{{ $m->full_name }}</span>
                                                <span
                                                    class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">
                                                    {{ $m->total_attended }} {{ Str::plural('Service', $m->total_attended) }}
                                                </span>
                                            </div>
                                            <div
                                                class="text-[10px] text-gray-400 dark:text-slate-500 uppercase tracking-wider font-bold">
                                                {{ $m->status }}
                                            </div>
                                            <div class="text-[10px] text-gray-400 dark:text-slate-500">First Visit:
                                                {{ $m->date_of_visit?->format('M d, Y') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-3 text-gray-500 dark:text-slate-400">{{ $m->primary_contact }}</td>
                                        <td class="px-6 py-3 text-gray-500 dark:text-slate-400">
                                            <div class="flex items-center gap-2">
                                                <div class="w-1.5 h-1.5 rounded-full bg-emerald-400"></div>
                                                <span>{{ $m->church->name ?? '—' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-3 text-center">
                                            <button type="button"
                                                @click="historyName = '{{ $m->full_name }}'; historyDates = {{ json_encode($m->attendance_dates) }}; showHistory = true"
                                                class="inline-flex items-center gap-1.5 px-2 py-1 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 rounded-md hover:bg-emerald-100 dark:hover:bg-emerald-500/20 transition cursor-pointer">
                                                <span class="text-xs font-bold">{{ $m->total_attended }}</span>
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </button>
                                        </td>
                                        <td class="px-6 py-3 text-center">
                                            @php
                                                $fsColors = [
                                                    'not yet' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-400',
                                                    'in-progress' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
                                                    'completed' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400'
                                                ];
                                                $fsStatus = $m->foundation_school_status;
                                            @endphp
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $fsColors[$fsStatus] ?? 'bg-gray-50 text-gray-700 dark:bg-gray-500/10 dark:text-gray-400' }}">
                                                {{ $fsStatus }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3 text-center text-gray-500 dark:text-slate-400 text-xs">
                                            {{ $m->membership_approved_at?->format('M d, Y') ?? '—' }}
                                        </td>
                                        <td class="px-6 py-3 text-right space-x-1">
                                            <a href="{{ route('admin.members.show', $m) }}"
                                                class="text-sky-600 hover:text-sky-800 dark:text-sky-400 dark:hover:text-sky-300 text-xs font-medium">View</a>
                                            <a href="{{ route('admin.members.edit', $m) }}"
                                                class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 text-xs font-medium">Edit</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @empty
            <div
                class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-12 text-center">
                <div
                    class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-50 dark:bg-slate-800 mb-4 text-gray-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 8.292m-4-8.292a4 4 0 110 8.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-1">No members found</h3>
                <p class="text-xs text-gray-500">No records found matching your filters.</p>
            </div>
        @endforelse

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

                <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-800 flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white" x-text="historyName"></h3>
                        <p class="text-[10px] text-gray-500 uppercase tracking-widest font-bold">Attendance History</p>
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
                    <template x-for="(date, index) in historyDates" :key="index">
                        <div
                            class="flex items-center gap-3 p-3 bg-emerald-50 dark:bg-emerald-500/5 rounded-xl border border-emerald-100/50 dark:border-emerald-500/10 mb-3 last:mb-0">
                            <div
                                class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900 dark:text-slate-200" x-text="date"></p>
                                <p class="text-[10px] text-emerald-600/70 dark:text-emerald-400/70 font-bold uppercase">
                                    Sunday Service</p>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-800 bg-gray-50 dark:bg-slate-800/50">
                    <button @click="showHistory = false"
                        class="w-full py-2 bg-gray-900 dark:bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-black dark:hover:bg-indigo-700 transition">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection