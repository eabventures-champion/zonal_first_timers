@extends('layouts.app')
@section('title', 'Churches')
@section('page-title', 'Churches')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <p class="text-sm text-gray-500">Manage individual churches</p>
        <a href="{{ route('admin.churches.create') }}"
            class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Church
        </a>
    </div>

    <div x-data="{ 
            expandedGroups: new Set(),
            toggleGroup(name) {
                if (this.expandedGroups.has(name)) {
                    this.expandedGroups.delete(name);
                } else {
                    this.expandedGroups.add(name);
                }
            }
        }" class="space-y-8">
        @forelse($groups as $group)
            @if($group->churches->count() > 0)
                <div class="space-y-3">
                    <div class="flex items-center gap-2 px-1 text-left">
                        <button @click="toggleGroup('{{ $group->name }}')"
                            class="flex items-center gap-2 hover:opacity-70 transition group">
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-indigo-500 transition-transform duration-200"
                                :class="{ '-rotate-90': !expandedGroups.has('{{ $group->name }}') }" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                            <h2 class="text-sm font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider">
                                {{ $group->name }}</h2>
                            <span
                                class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                                {{ $group->churches->count() }} {{ Str::plural('Church', $group->churches->count()) }}
                            </span>
                        </button>
                        <div class="flex-1 border-t border-gray-100 dark:border-slate-800 ml-2"></div>
                    </div>

                    <div x-show="expandedGroups.has('{{ $group->name }}')" x-collapse
                        class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 dark:bg-slate-800/50 border-b border-gray-100 dark:border-slate-800">
                                    <tr>
                                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-slate-400">Church</th>
                                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-slate-400">Officer</th>
                                        <th class="px-6 py-3 text-center font-medium text-gray-500 dark:text-slate-400">First Timers
                                        </th>
                                        <th class="px-6 py-3 text-center font-medium text-gray-500 dark:text-slate-400">Members</th>
                                        <th class="px-6 py-3 text-right font-medium text-gray-500 dark:text-slate-400">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                                    @foreach($group->churches as $church)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="font-semibold text-gray-900 dark:text-white">{{ $church->name }}</div>
                                                <div class="text-xs text-gray-400 dark:text-slate-500">
                                                    {{ $church->address ?? 'No address' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-gray-500 dark:text-slate-400">
                                                <div class="flex items-center gap-2">
                                                    <div
                                                        class="w-7 h-7 rounded-full bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold text-[10px]">
                                                        {{ strtoupper(substr($church->retainingOfficer->name ?? 'U', 0, 1)) }}
                                                    </div>
                                                    <span>{{ $church->retainingOfficer->name ?? 'Unassigned' }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <span
                                                    class="text-gray-900 dark:text-white font-medium">{{ $church->first_timers_count }}</span>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/20">
                                                    {{ $church->members_count }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-right space-x-2">
                                                <a href="{{ route('admin.churches.show', $church) }}"
                                                    class="inline-flex items-center px-2.5 py-1 rounded-lg bg-sky-50 text-sky-600 hover:bg-sky-100 dark:bg-sky-500/10 dark:text-sky-400 dark:hover:bg-sky-500/20 text-[11px] font-bold transition">View</a>
                                                <a href="{{ route('admin.churches.edit', $church) }}"
                                                    class="inline-flex items-center px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 dark:bg-indigo-500/10 dark:text-indigo-400 dark:hover:bg-indigo-500/20 text-[11px] font-bold transition">Edit</a>
                                                <form method="POST" action="{{ route('admin.churches.destroy', $church) }}"
                                                    class="inline" onsubmit="return confirm('Delete this church?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                        class="inline-flex items-center px-2.5 py-1 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 dark:bg-red-500/10 dark:text-red-400 dark:hover:bg-red-500/20 text-[11px] font-bold transition">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        @empty
            <div
                class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-12 text-center">
                <div
                    class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-50 dark:bg-slate-800 mb-4 text-gray-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-1">No churches found</h3>
                <p class="text-xs text-gray-500">Get started by creating your first church.</p>
            </div>
        @endforelse
    </div>
@endsection