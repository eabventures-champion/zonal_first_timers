@extends('layouts.app')
@section('title', 'First Timers')
@section('page-title', 'First Timers')

@section('content')
    <div x-data="{ 
                        filtersOpen: false,
                        search: '',
                        dateFrom: '',
                        dateTo: '',
                        selectedChurchId: '',
                        selectedStatus: '',
                        resetFilters() {
                            this.search = '';
                            this.dateFrom = '';
                            this.dateTo = '';
                            this.selectedChurchId = '';
                            this.selectedStatus = '';
                        },
                        expandedCategories: new Set(),
                        toggleCategory(name) {
                            if (this.expandedCategories.has(name)) {
                                this.expandedCategories.delete(name);
                            } else {
                                this.expandedCategories.add(name);
                            }
                        },
                        expandedChurches: new Set(),
                        toggleChurch(name) {
                            if (this.expandedChurches.has(name)) {
                                this.expandedChurches.delete(name);
                            } else {
                                this.expandedChurches.add(name);
                            }
                        },
                        shouldShowRecord(ft) {
                            const s = this.search.toLowerCase();
                            const matchesSearch = !this.search || 
                                (ft.name && ft.name.toLowerCase().includes(s)) || 
                                (ft.phone && ft.phone.toLowerCase().includes(s)) ||
                                (ft.church && ft.church.toLowerCase().includes(s)) ||
                                (ft.group_name && ft.group_name.toLowerCase().includes(s));

                            const matchesChurch = !this.selectedChurchId || ft.church_id == this.selectedChurchId;
                            const matchesStatus = !this.selectedStatus || ft.status == this.selectedStatus;

                            let matchesDate = true;
                            if (this.dateFrom || this.dateTo) {
                                const visitDate = new Date(ft.visit_date);
                                // Reset time for comparison
                                visitDate.setHours(0,0,0,0);

                                if (this.dateFrom) {
                                    const dFrom = new Date(this.dateFrom);
                                    dFrom.setHours(0,0,0,0);
                                    matchesDate = matchesDate && visitDate >= dFrom;
                                }
                                if (this.dateTo) {
                                    const dTo = new Date(this.dateTo);
                                    dTo.setHours(0,0,0,0);
                                    matchesDate = matchesDate && visitDate <= dTo;
                                }
                            }
                            return matchesSearch && matchesDate && matchesChurch && matchesStatus;
                        },
                        shouldShowChurch(churchName, groupedByChurch) {
                            if (!this.hasActiveFilters()) return true;
                            const s = this.search.toLowerCase();
                            const nameMatch = churchName.toLowerCase().includes(s);
                            const recordMatch = groupedByChurch.some(ft => this.shouldShowRecord(ft));
                            return nameMatch || recordMatch;
                        },
                        hasActiveFilters() {
                            return !!(this.search || this.selectedChurchId || this.selectedStatus || this.dateFrom || this.dateTo);
                        },
                        shouldShowCategory(categoryName, churches) {
                            if (!this.hasActiveFilters()) return true;
                            const s = this.search.toLowerCase();
                            if (categoryName.toLowerCase().includes(s)) return true;

                            // Check if any record in any of these churches matches
                            for (let churchName in churches) {
                                if (churches[churchName].some(ft => this.shouldShowRecord(ft))) return true;
                            }
                            return false;
                        },
                        showHistory: false, 
                        historyName: '', 
                        historyDates: [] 
                    }">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
            <p class="text-sm text-gray-500 hidden sm:block">Manage all first-time visitors</p>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.first-timers.import') }}"
                    class="inline-flex items-center gap-1.5 px-3 py-2 sm:gap-2 sm:px-4 border border-gray-300 text-gray-700 hover:bg-gray-50 text-xs sm:text-sm font-medium rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Import CSV
                </a>
                <a href="{{ route('admin.first-timers.create') }}"
                    class="inline-flex items-center gap-1.5 px-3 py-2 sm:gap-2 sm:px-4 bg-indigo-600 hover:bg-indigo-700 text-white text-xs sm:text-sm font-medium rounded-lg shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="hidden sm:inline">Register First Timer</span>
                    <span class="sm:hidden">Register</span>
                </a>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-4 mb-6">
            <div class="flex items-center justify-between lg:hidden mb-4" @click="filtersOpen = !filtersOpen">
                <h3
                    class="text-xs font-bold text-gray-400 uppercase tracking-widest flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Search & Filters
                </h3>
                <button type="button" class="text-indigo-600 font-bold text-[10px] uppercase tracking-wider">
                    <span x-text="filtersOpen ? 'Hide' : 'Show'"></span>
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 items-center gap-3"
                :class="filtersOpen ? 'grid' : 'hidden lg:grid'">
                <input type="text" x-model="search" placeholder="Search name, phone..."
                    class="rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500 w-full">

                <select x-model="selectedChurchId"
                    class="rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500 w-full">
                    <option value="">All Churches</option>
                    @foreach($churches as $church)
                        <option value="{{ $church->id }}">{{ $church->name }}</option>
                    @endforeach
                </select>

                <select x-model="selectedStatus"
                    class="rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500 w-full">
                    <option value="">All Statuses</option>
                    @foreach(['New', 'Developing', 'Retained'] as $s)
                        <option value="{{ $s }}">{{ $s }}</option>
                    @endforeach
                </select>

                <div class="sm:col-span-1 lg:col-span-1">
                    <label class="block lg:hidden text-[10px] font-bold text-gray-400 uppercase mb-1 ml-1">From</label>
                    <input type="date" x-model="dateFrom"
                        class="rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500 w-full">
                </div>

                <div class="sm:col-span-1 lg:col-span-1">
                    <label class="block lg:hidden text-[10px] font-bold text-gray-400 uppercase mb-1 ml-1">To</label>
                    <input type="date" x-model="dateTo"
                        class="rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500 w-full">
                </div>

                <div class="flex gap-2 lg:mt-0">
                    <button @click="resetFilters()"
                        class="flex-1 px-4 py-2 bg-gray-50 hover:bg-gray-100 dark:bg-slate-800 dark:hover:bg-slate-700 text-gray-600 dark:text-slate-400 text-sm rounded-lg transition font-medium border border-gray-200 dark:border-slate-700">
                        Reset
                    </button>
                </div>
            </div>
        </div>

        {{-- Import Errors --}}
        @if(session('import_errors'))
            <div
                class="bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 rounded-xl p-4 mb-6 shadow-sm">
                <div class="flex items-center gap-2 mb-3 text-red-800 dark:text-red-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <p class="text-sm font-bold uppercase tracking-wider">Import Errors Found</p>
                </div>
                <ul class="space-y-1.5 list-none">
                    @foreach(session('import_errors') as $err)
                        <li class="flex items-start gap-2 text-xs text-red-700 dark:text-red-300">
                            <span class="mt-1 w-1.5 h-1.5 rounded-full bg-red-400 shrink-0"></span>
                            <span class="font-medium">{{ $err }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Table --}}
        <div class="space-y-8">
            @forelse($groupedFirstTimers as $categoryName => $churches)
                <div class="space-y-4" x-show="shouldShowCategory('{{ addslashes($categoryName) }}', @js($churches->map(fn($c) => $c->map(fn($ft) => [
                    'name' => $ft->full_name,
                    'phone' => $ft->primary_contact,
                    'church' => $ft->church->name ?? '',
                    'church_id' => $ft->church_id,
                    'status' => $ft->status,
                    'visit_date' => $ft->date_of_visit?->format('Y-m-d'),
                    'group_name' => $ft->church->group->name ?? ''
                ]))))">
                    {{-- Category Header --}}
                    <div class="flex items-center gap-2 px-1">
                        <button @click="toggleCategory('{{ addslashes($categoryName) }}')"
                            class="flex items-center gap-2 hover:opacity-70 transition cursor-pointer">
                            <svg class="w-5 h-5 text-gray-400 transition-transform duration-200"
                                :class="{ '-rotate-90': !expandedCategories.has('{{ addslashes($categoryName) }}') }"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                            <h2 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-widest">
                                {{ $categoryName }}
                            </h2>
                            @php
                                $categoryTotal = $churches->sum(fn($churchItems) => $churchItems->count());
                            @endphp
                            <span
                                class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-orange-100 text-orange-700 dark:bg-orange-500/10 dark:text-orange-400">
                                {{ $categoryTotal }} {{ Str::plural('First Timer', $categoryTotal) }}
                            </span>
                        </button>
                        <div class="flex-1 border-t border-gray-200 dark:border-slate-700 ml-2"></div>
                    </div>

                    {{-- Churches List --}}
                    <div x-show="expandedCategories.has('{{ addslashes($categoryName) }}') || hasActiveFilters()" x-collapse
                        class="pl-4 space-y-8 border-l-2 border-gray-100 dark:border-slate-800 ml-3 py-2">
                        @foreach($churches as $churchName => $churchItems)
                            <div class="space-y-3" x-show="shouldShowChurch('{{ addslashes($churchName) }}', @js($churchItems->map(fn($ft) => [
                                'name' => $ft->full_name,
                                'phone' => $ft->primary_contact,
                                'church' => $ft->church->name ?? '',
                                'church_id' => $ft->church_id,
                                'status' => $ft->status,
                                'visit_date' => $ft->date_of_visit?->format('Y-m-d'),
                                'group_name' => $ft->church->group->name ?? ''
                            ])))">
                                {{-- Church Header --}}
                                <div class="flex items-center gap-2 px-1">
                                    <button @click="toggleChurch('{{ addslashes($categoryName . '_' . $churchName) }}')"
                                        class="flex items-center gap-2 hover:opacity-70 transition cursor-pointer">
                                        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200"
                                            :class="{ '-rotate-90': !expandedChurches.has('{{ addslashes($categoryName . '_' . $churchName) }}') }"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                        <h3 class="text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-widest">
                                            {{ $churchName }}
                                        </h3>
                                        <span
                                            class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                                            {{ $churchItems->count() }} {{ Str::plural('First Timer', $churchItems->count()) }}
                                        </span>
                                    </button>
                                    <div class="flex-1 border-t border-gray-100 dark:border-slate-800 ml-2"></div>
                                </div>

                                <div x-show="expandedChurches.has('{{ addslashes($categoryName . '_' . $churchName) }}') || hasActiveFilters()"
                                    x-collapse
                                    class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden">

                                    {{-- Desktop Table --}}
                                    <div class="hidden md:block overflow-x-auto">
                                        <table class="w-full text-sm">
                                            <thead class="bg-gray-50 dark:bg-slate-800/50">
                                                <tr>
                                                    <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-slate-400">
                                                        Name</th>
                                                    <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-slate-400">
                                                        Contact</th>
                                                    <!-- <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-slate-400">Church</th> -->
                                                    <th class="px-6 py-3 text-center font-medium text-gray-500 dark:text-slate-400">
                                                        Attendance</th>
                                                    <th class="px-6 py-3 text-center font-medium text-gray-500 dark:text-slate-400">
                                                        FS Level</th>
                                                    <th class="px-6 py-3 text-center font-medium text-gray-500 dark:text-slate-400">
                                                        Status</th>
                                                    <th class="px-6 py-3 text-right font-medium text-gray-500 dark:text-slate-400">
                                                        Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                                                @foreach($churchItems as $ft)
                                                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors" x-show="shouldShowRecord(@js([
                                                        'name' => $ft->full_name,
                                                        'phone' => $ft->primary_contact,
                                                        'church' => $ft->church->name ?? '',
                                                        'church_id' => $ft->church_id,
                                                        'status' => $ft->status,
                                                        'visit_date' => $ft->date_of_visit?->format('Y-m-d'),
                                                        'group_name' => $ft->church->group->name ?? ''
                                                    ]))">
                                                        <td class="px-6 py-3">
                                                            <div class="flex items-center gap-2">
                                                                <span
                                                                    class="font-medium text-gray-900 dark:text-white">{{ $ft->full_name }}</span>
                                                                <span
                                                                    class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">
                                                                    {{ $ft->total_attended }}
                                                                    {{ Str::plural('Service', $ft->total_attended) }}
                                                                </span>

                                                            </div>
                                                            <div class="text-[10px] text-gray-400 dark:text-slate-500">Joined:
                                                                {{ $ft->date_of_visit?->format('M d, Y') }}
                                                            </div>
                                                        </td>
                                                        <td class="px-6 py-3 text-gray-500 dark:text-slate-400">
                                                            {{ $ft->primary_contact }}
                                                        </td>
                                                        <!-- <td class="px-6 py-3 text-gray-500 dark:text-slate-400">
                                                                                        <div class="flex items-center gap-2">
                                                                                            <div class="w-1.5 h-1.5 rounded-full bg-indigo-400"></div>
                                                                                            <span>{{ $ft->church->name ?? '—' }}</span>
                                                                                        </div>
                                                                                    </td> -->
                                                        <td class="px-6 py-3 text-center">
                                                            <button type="button"
                                                                @click="historyName = '{{ $ft->full_name }}'; historyDates = {{ json_encode($ft->attendance_dates) }}; showHistory = true"
                                                                class="inline-flex items-center gap-1.5 px-2 py-1 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 rounded-md hover:bg-emerald-100 dark:hover:bg-emerald-500/20 transition cursor-pointer">
                                                                <span class="text-xs font-bold">{{ $ft->total_attended }}</span>
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                                </svg>
                                                            </button>
                                                        </td>
                                                        <td class="px-6 py-3 text-center">
                                                            <div class="flex flex-col items-center gap-1">
                                                                <span
                                                                    class="text-[10px] font-semibold px-2 py-0.5 rounded {{ $ft->foundation_school_status === 'in-progress' ? 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400' : 'text-gray-600 dark:text-slate-400 bg-gray-100 dark:bg-slate-800' }}">
                                                                    {{ $ft->foundation_school_status }}
                                                                </span>
                                                                @if($ft->foundation_school_status === 'in-progress')
                                                                    <span
                                                                        class="text-[10px] text-blue-600 dark:text-blue-400 font-medium italic">
                                                                        {{ $ft->current_foundation_level }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </td>
                                                        <td class="px-6 py-3 text-center">
                                                            @php
                                                                $sc = [
                                                                    'New' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-500',
                                                                    'Developing' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
                                                                    'Retained' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400',
                                                                ];
                                                            @endphp
                                                            <span
                                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $sc[$ft->status] ?? 'bg-gray-100 dark:bg-slate-800' }}">{{ $ft->status }}</span>
                                                        </td>
                                                        <td class="px-6 py-3 text-right space-x-1">
                                                            <a href="{{ route('admin.first-timers.show', $ft) }}"
                                                                class="text-sky-600 hover:text-sky-800 dark:text-sky-400 dark:hover:text-sky-300 text-xs font-medium px-2 py-1">View</a>
                                                            <a href="{{ route('admin.first-timers.edit', $ft) }}"
                                                                class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 text-xs font-medium px-2 py-1">Edit</a>
                                                            <form method="POST" action="{{ route('admin.first-timers.destroy', $ft) }}"
                                                                class="inline" onsubmit="return confirm('Delete this record?')">
                                                                @csrf @method('DELETE')
                                                                <button type="submit"
                                                                    class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 text-xs font-medium px-2 py-1">Delete</button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    {{-- Mobile Card List --}}
                                    <div class="md:hidden divide-y divide-gray-100 dark:divide-slate-800">
                                        @foreach($churchItems as $ft)
                                                        <div class="p-4 space-y-4" x-show="shouldShowRecord(@js([
                                                            'name' => $ft->full_name,
                                                            'phone' => $ft->primary_contact,
                                                            'church' => $ft->church->name ?? '',
                                                            'church_id' => $ft->church_id,
                                                            'status' => $ft->status,
                                                            'visit_date' => $ft->date_of_visit?->format('Y-m-d')
                                                        ]))">
                                                            <div class="flex items-start justify-between">
                                                                <div class="flex items-center gap-3">
                                                                    <div
                                                                        class="w-10 h-10 rounded-full bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold text-xs shrink-0">
                                                                        {{ strtoupper(substr($ft->full_name, 0, 2)) }}
                                                                    </div>
                                                                    <div>
                                                                        <h4 class="text-sm font-bold text-gray-900 dark:text-white">
                                                                            {{ $ft->full_name }}
                                                                        </h4>
                                                                        <p class="text-[11px] text-gray-500 dark:text-slate-400">
                                                                            {{ $ft->primary_contact }}
                                                                        </p>
                                                                    </div>
                                                                </div>
                                            @php
                                                $sc = [
                                                    'New' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-500',
                                                    'Developing' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
                                                    'Retained' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400',
                                                ];
                                            @endphp
                                                                <span
                                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider {{ $sc[$ft->status] ?? 'bg-gray-100 dark:bg-slate-800 text-gray-500' }}">
                                                                    {{ $ft->status }}
                                                                </span>
                                                            </div>

                                                            <div class="grid grid-cols-2 gap-3 pb-2">
                                                                <div
                                                                    class="bg-gray-50 dark:bg-slate-800/50 p-2 rounded-lg border border-gray-100 dark:border-slate-800/50">
                                                                    <p
                                                                        class="text-[9px] text-gray-400 dark:text-slate-500 uppercase tracking-widest font-bold mb-1">
                                                                        Attendance</p>
                                                                    <button type="button"
                                                                        @click="historyName = '{{ $ft->full_name }}'; historyDates = {{ json_encode($ft->attendance_dates) }}; showHistory = true"
                                                                        class="flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400">
                                                                        <span class="text-xs font-bold">{{ $ft->total_attended }} Services</span>
                                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                                            viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                                                        </svg>
                                                                    </button>
                                                                </div>
                                                                <div
                                                                    class="bg-gray-50 dark:bg-slate-800/50 p-2 rounded-lg border border-gray-100 dark:border-slate-800/50">
                                                                    <p
                                                                        class="text-[9px] text-gray-400 dark:text-slate-500 uppercase tracking-widest font-bold mb-1">
                                                                        FS Level</p>
                                                                    <span class="text-[11px] font-bold text-gray-700 dark:text-slate-300">
                                                                        {{ $ft->foundation_school_status === 'in-progress' ? ($ft->current_foundation_level ?: 'In Progress') : 'Not Started' }}
                                                                    </span>
                                                                </div>
                                                            </div>

                                                            <div
                                                                class="flex items-center justify-between pt-2 border-t border-gray-50 dark:border-slate-800/50">
                                                                <div
                                                                    class="flex items-center gap-1.5 text-[10px] text-gray-400 dark:text-slate-500 font-medium">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                                    </svg>
                                                                    {{ $ft->church->name ?? '—' }}
                                                                </div>
                                                                <div class="flex gap-2">
                                                                    <a href="{{ route('admin.first-timers.show', $ft) }}"
                                                                        class="p-2 bg-sky-50 dark:bg-sky-500/10 text-sky-600 dark:text-sky-400 rounded-lg transition-colors">
                                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                                        </svg>
                                                                    </a>
                                                                    <a href="{{ route('admin.first-timers.edit', $ft) }}"
                                                                        class="p-2 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 rounded-lg transition-colors">
                                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                                        </svg>
                                                                    </a>
                                                                    <form method="POST" action="{{ route('admin.first-timers.destroy', $ft) }}"
                                                                        class="inline" onsubmit="return confirm('Delete this record?')">
                                                                        @csrf @method('DELETE')
                                                                        <button type="submit"
                                                                            class="p-2 bg-red-50 dark:bg-red-500/10 text-red-500 dark:text-red-400 rounded-lg transition-colors">
                                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                                viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                                    stroke-width="2"
                                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                            </svg>
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
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
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-1">No first timers found</h3>
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
    </div>
@endsection