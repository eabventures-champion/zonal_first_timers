@extends('layouts.app')

@section('content')
    <div class="p-6" x-data="{ 
            showModal: false, 
            selectedBringer: '', 
            firstTimers: [],
            openModal(bringer, list) {
                this.selectedBringer = bringer;
                this.firstTimers = list;
                this.showModal = true;
            }
        }">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Bringers in {{ $church->name }}</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">View first timers grouped by those who brought
                    them.</p>
            </div>
        </div>

        <div
            class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="bg-slate-50 dark:bg-slate-800/50 px-6 py-4 border-b border-slate-200 dark:border-slate-800">
                <h2 class="text-lg font-bold text-slate-800 dark:text-white">Church Bringers</h2>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-1 lg:grid-cols-1 gap-6">
                    <div
                        class="bg-slate-50 dark:bg-slate-800/50 rounded-lg p-4 border border-slate-200 dark:border-slate-800">
                        <div class="space-y-1">
                            @forelse($church->bringers as $bringer)
                                                <div
                                                    class="flex items-center justify-between py-2 px-2 hover:bg-white dark:hover:bg-slate-900 rounded-md transition-colors group">
                                                    <div class="flex items-center gap-2">
                                                        <span
                                                            class="font-semibold text-sm text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">{{ $bringer->name }}</span>
                                                        @if($bringer->is_ro)
                                                            <span
                                                                class="px-2 py-0.5 text-[9px] bg-indigo-100 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 rounded-full font-bold uppercase transition-all duration-300">RO &middot; Bringer</span>
                                                        @endif
                                                        @if($bringer->user && $bringer->user->hasRole('Member'))
                                                            <span
                                                                class="px-2 py-0.5 text-[9px] bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 rounded-full font-bold uppercase">Member</span>
                                                        @endif
                                                    </div>

                                                                                                        @php
                                                        $firstTimerCount = $bringer->firstTimers->count();
                                                        $memberCount = $bringer->members->count();
                                                    @endphp
                                                    <div class="flex items-center gap-1 cursor-pointer" @click="openModal('{{ addslashes($bringer->name) }}', {{ 
                                                        collect($bringer->firstTimers->map(fn($ft) => [
                                                            'name' => $ft->full_name,
                                                            'contact' => $ft->primary_contact ?? 'N/A',
                                                            'date' => $ft->date_of_visit ? $ft->date_of_visit->format('M d, Y') : '',
                                                            'status' => 'First Timer'
                                                        ]))->merge($bringer->members->map(fn($m) => [
                                                            'name' => $m->full_name,
                                                            'contact' => $m->primary_contact ?? 'N/A',
                                                            'date' => $m->migrated_at ? $m->migrated_at->format('M d, Y') : ($m->date_of_visit ? $m->date_of_visit->format('M d, Y') : ''),
                                                            'status' => 'Retained'
                                                        ]))->toJson() 
                                                    }})">
                                                        <span class="px-2.5 py-1 bg-black text-white text-[10px] font-bold rounded-full transition-colors shadow-sm" title="Members">
                                                            {{ $memberCount }} M
                                                        </span>
                                                        <span class="px-2.5 py-1 bg-slate-700 hover:bg-indigo-600 dark:bg-slate-700 dark:hover:bg-indigo-600 text-white text-[10px] font-bold rounded-full transition-colors shadow-sm" title="First Timers">
                                                            {{ $firstTimerCount }} FT
                                                        </span>
                                                    </div>
                                                </div>
                            @empty
                                <p class="text-xs text-slate-400 dark:text-slate-500 italic">No bringers registered yet in your
                                    church.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- First Timers Modal --}}
        <div x-show="showModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak>

            <div class="bg-white dark:bg-slate-900 w-full max-w-md rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden"
                @click.away="showModal = false">

                <div
                    class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50 dark:bg-slate-800/50">
                    <div>
                        <h3 class="font-bold text-slate-900 dark:text-white" x-text="selectedBringer"></h3>
                        <p class="text-[10px] text-slate-500 uppercase tracking-widest font-bold">First Timers List</p>
                    </div>
                    <button @click="showModal = false"
                        class="text-slate-400 hover:text-slate-600 dark:hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-6 max-h-[60vh] overflow-y-auto">
                    <div class="space-y-3">
                        <template x-for="(ft, index) in firstTimers" :key="index">
                            <div class="flex items-center justify-between p-3 rounded-xl border" :class="ft.status === 'Retained' ? 'bg-slate-900/5 dark:bg-slate-800/80 border-slate-200 dark:border-slate-700' : 'bg-slate-50 dark:bg-slate-800/30 border-slate-100 dark:border-slate-800/50'">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center"
                                        :class="ft.status === 'Retained' ? 'bg-black text-white dark:bg-black dark:text-white' : 'bg-indigo-100 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400'">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-slate-700 dark:text-slate-200" x-text="ft.name"></span>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="text-[10px] font-bold uppercase tracking-wider"
                                                :class="ft.status === 'Retained' ? 'text-slate-800 dark:text-white' : 'text-slate-400'"
                                                x-text="ft.status === 'Retained' ? 'Member' : 'First Timer'"></span>
                                            <span class="text-[10px] text-slate-500 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                                <span x-text="ft.contact"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase bg-white dark:bg-slate-900 px-2 py-1 rounded-md border border-slate-100 dark:border-slate-800 shadow-sm" x-text="ft.date"></span>
                            </div>
                                    <span class="text-sm font-bold text-slate-700 dark:text-slate-200"
                                        x-text="ft.name"></span>
                                </div>
                                <span
                                    class="text-[10px] font-bold text-slate-400 uppercase bg-white dark:bg-slate-900 px-2 py-1 rounded-md border border-slate-100 dark:border-slate-800 shadow-sm"
                                    x-text="ft.date"></span>
                            </div>
                        </template>
                    </div>

                    <div x-show="firstTimers.length === 0" class="text-center py-8 text-slate-400 italic">
                        No first timers found.
                    </div>
                </div>


            </div>
        </div>
    </div>
@endsection