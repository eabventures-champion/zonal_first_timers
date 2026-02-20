@extends('layouts.app')
@section('title', 'Membership Approvals')
@section('page-title', 'Membership Approvals')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-slate-100">Membership Notifications</h3>
            <p class="text-sm text-gray-500 dark:text-slate-400">First timers who have automatically become members after 6
                attendances.
            </p>
        </div>
        <form action="{{ route('admin.membership-approvals.bulk-sync') }}" method="POST">
            @csrf
            <button type="submit"
                class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg text-sm font-semibold text-gray-700 dark:text-slate-200 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors shadow-sm">
                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Sync All Records
            </button>
        </form>
    </div>

    <div x-data="{ 
                    showHistory: false, 
                    historyName: '', 
                    historyDates: [],
                    selectedIds: [],
                    allSelected: false,
                    toggleAll() {
                        this.allSelected = !this.allSelected;
                        if (this.allSelected) {
                            this.selectedIds = Array.from(document.querySelectorAll('.member-checkbox')).map(cb => cb.value);
                        } else {
                            this.selectedIds = [];
                        }
                    },
                    toggleOne(id) {
                        if (this.selectedIds.includes(id)) {
                            this.selectedIds = this.selectedIds.filter(item => item !== id);
                            this.allSelected = false;
                        } else {
                            this.selectedIds.push(id);
                            if (this.selectedIds.length === document.querySelectorAll('.member-checkbox').length) {
                                this.allSelected = true;
                            }
                        }
                    }
                }"
        class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden">

        {{-- Bulk Actions Toolbar --}}
        <div x-show="selectedIds.length > 0" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            class="bg-indigo-50 dark:bg-indigo-900/10 px-6 py-3 border-b border-indigo-100 dark:border-indigo-900/20 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="text-sm font-bold text-indigo-700 dark:text-indigo-400">
                    <span x-text="selectedIds.length"></span> Selected
                </span>
            </div>
            <form action="{{ route('admin.membership-approvals.bulk-acknowledge') }}" method="POST">
                @csrf
                <template x-for="id in selectedIds" :key="id">
                    <input type="hidden" name="ids[]" :value="id">
                </template>
                <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg transition-all shadow-sm">
                    Acknowledge Selected
                </button>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr
                        class="bg-gray-50/50 dark:bg-slate-800/50 text-[11px] uppercase tracking-wider text-gray-500 dark:text-slate-400 font-bold">
                        <th class="px-6 py-4 w-10">
                            <input type="checkbox" @click="toggleAll()" :checked="allSelected"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </th>
                        <th class="px-6 py-4">New Member</th>
                        <th class="px-6 py-4">Church / Officer</th>
                        <th class="px-6 py-4 text-center">Attendance</th>
                        <th class="px-6 py-4 text-center">FS Level</th>
                        <th class="px-6 py-4">Became Member</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                    @forelse($notifications as $m)
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors group">
                            <td class="px-6 py-4">
                                <input type="checkbox" :value="'{{ $m->id }}'" :checked="selectedIds.includes('{{ $m->id }}')"
                                    @click="toggleOne('{{ $m->id }}')"
                                    class="member-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-full bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400 font-bold text-sm">
                                        {{ strtoupper(substr($m->full_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900 dark:text-slate-200">{{ $m->full_name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-slate-500">{{ $m->primary_contact }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-400">
                                <p class="font-medium text-gray-800 dark:text-slate-300">{{ $m->church->name }}</p>
                                <p class="text-xs opacity-75">{{ $m->retainingOfficer->name ?? 'Unassigned' }}</p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button type="button"
                                    @click="historyName = '{{ $m->full_name }}'; historyDates = {{ json_encode($m->attendance_dates) }}; showHistory = true"
                                    class="inline-flex items-center gap-1.5 px-2 py-1 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 rounded-md hover:bg-emerald-100 dark:hover:bg-emerald-500/20 transition cursor-pointer">
                                    <span class="text-xs font-bold">{{ count($m->attendance_dates) }}</span>
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </button>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="text-[10px] font-semibold text-gray-600 dark:text-slate-400 bg-gray-100 dark:bg-slate-800 px-2 py-0.5 rounded">
                                    {{ $m->current_foundation_level }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-400">
                                {{ $m->membership_approved_at?->format('M d, Y') ?? 'Recently' }}
                                <p class="text-[10px] opacity-75">{{ $m->membership_approved_at?->diffForHumans() }}</p>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('admin.membership-approvals.approve', $m) }}" method="POST"
                                    class="inline">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg transition-all shadow-sm shadow-indigo-200 dark:shadow-none hover:scale-105 active:scale-95">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                        Acknowledge
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-3 space-y-2 opacity-40">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-gray-500 dark:text-slate-400 font-medium">No new membership notifications at
                                        the moment.</p>
                                </div>
                            </td>
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
                                    <p class="text-[10px] text-emerald-600/70 dark:text-emerald-400/70 font-bold uppercase">
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
@endsection