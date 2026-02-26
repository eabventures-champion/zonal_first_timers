@extends('layouts.app')
@section('title', 'Church Groups')
@section('page-title', 'Church Groups')

@section('content')
    <div x-data="{ 
                modalOpen: false, 
                currentGroup: '',
                churches: [],
                openChurchesModal(groupName, groupChurches) {
                    this.currentGroup = groupName;
                    this.churches = groupChurches;
                    this.modalOpen = true;
                }
            }">
        <div class="flex items-center justify-between mb-6">
            <p class="text-sm text-gray-500 hidden sm:block">Manage church groups within categories</p>
            <a href="{{ route('admin.church-groups.create') }}"
                class="inline-flex items-center gap-1.5 px-3 py-2 sm:gap-2 sm:px-4 bg-indigo-600 hover:bg-indigo-700 text-white text-xs sm:text-sm font-medium rounded-lg shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Group
            </a>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800">
            <div class="overflow-x-auto">
                <table class="w-full text-sm min-w-[700px]">
                    <thead class="bg-gray-50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-3 sm:px-6 py-3 text-left font-medium text-gray-500 dark:text-slate-400">#</th>
                            <th class="px-3 sm:px-6 py-3 text-left font-medium text-gray-500 dark:text-slate-400">Name</th>
                            <th class="px-3 sm:px-6 py-3 text-left font-medium text-gray-500 dark:text-slate-400">Category
                            </th>
                            <th class="px-3 sm:px-6 py-3 text-left font-medium text-gray-500 dark:text-slate-400">Pastor
                                Details</th>
                            <th class="px-3 sm:px-6 py-3 text-center font-medium text-gray-500 dark:text-slate-400">Churches
                            </th>
                            <th class="px-3 sm:px-6 py-3 text-right font-medium text-gray-500 dark:text-slate-400">Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                        @forelse($groups as $i => $group)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors">
                                <td class="px-3 sm:px-6 py-3 text-gray-400 dark:text-slate-500">{{ $i + 1 }}</td>
                                <td class="px-3 sm:px-6 py-3 font-medium text-gray-900 dark:text-white">{{ $group->name }}</td>
                                <td class="px-3 sm:px-6 py-3 text-gray-500 dark:text-slate-400">
                                    {{ $group->category->name ?? '—' }}</td>
                                <td class="px-3 sm:px-6 py-3">
                                    <div class="text-gray-900 dark:text-white font-medium">{{ $group->pastor_name ?? '—' }}
                                    </div>
                                    @if($group->pastor_contact)
                                        <span
                                            class="inline-flex items-center mt-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/20">
                                            {{ $group->pastor_contact }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-3 sm:px-6 py-3 text-center">
                                    <button type="button"
                                        @click="openChurchesModal('{{ addslashes($group->name) }}', {{ Js::from($group->churches) }})"
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400 hover:bg-indigo-200 dark:hover:bg-indigo-500/20 transition-colors cursor-pointer border-none"
                                        title="View Churches">
                                        {{ $group->churches_count }}
                                    </button>
                                </td>
                                <td class="px-3 sm:px-6 py-3 text-right space-x-2">
                                    <a href="{{ route('admin.church-groups.edit', $group) }}"
                                        class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 text-xs font-medium">Edit</a>
                                    <form method="POST" action="{{ route('admin.church-groups.destroy', $group) }}"
                                        class="inline" onsubmit="return confirm('Delete this group?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 text-xs font-medium">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-3 sm:px-6 py-8 text-center text-gray-400 dark:text-slate-500">No
                                    groups found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Churches Modal --}}
        <div x-show="modalOpen" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="modalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                    class="fixed inset-0 transition-opacity bg-gray-500/75 dark:bg-slate-900/80 backdrop-blur-sm"
                    @click="modalOpen = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div x-show="modalOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block w-full max-w-2xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-2xl shadow-xl">

                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white" x-text="'Churches in ' + currentGroup">
                        </h3>
                        <button @click="modalOpen = false"
                            class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l18 18" />
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-4 max-h-[65vh] overflow-y-auto pr-2 custom-scrollbar">
                        <template x-for="church in churches" :key="church.id">
                            <div
                                class="p-5 rounded-2xl border border-gray-100 dark:border-slate-800 bg-gray-50 dark:bg-slate-800/50 hover:border-indigo-200 dark:hover:border-indigo-500/30 transition-all">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                                    <div class="font-bold text-gray-900 dark:text-white text-lg" x-text="church.name"></div>
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="px-3 py-1 rounded-full bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 text-xs font-bold border border-indigo-100 dark:border-indigo-500/20">
                                            Retaining Officer: <span
                                                x-text="church.retaining_officer ? church.retaining_officer.name : 'Unassigned'"></span>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-gray-200/50 dark:border-slate-700/50">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-blue-600 dark:text-blue-400 shadow-sm">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-[10px] text-gray-400 uppercase font-black tracking-widest">
                                                Church Leader</div>
                                            <div class="text-sm font-bold text-gray-800 dark:text-slate-200"
                                                x-text="church.leader_name || 'Not Listed'"></div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shadow-sm">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-[10px] text-gray-400 uppercase font-black tracking-widest">
                                                Leader Contact</div>
                                            <div class="text-sm font-bold text-gray-800 dark:text-slate-200"
                                                x-text="church.leader_contact || 'No Contact'"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <div x-show="churches.length === 0" class="text-center py-16">
                            <div
                                class="w-20 h-20 bg-gray-50 dark:bg-slate-800/50 rounded-full flex items-center justify-center mx-auto mb-6">
                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <p class="text-gray-500 dark:text-slate-400 font-medium">No churches assigned to this group yet.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection