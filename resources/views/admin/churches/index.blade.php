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

    <div
        class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-slate-800/50">
                    <tr>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-slate-400">Church</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-slate-400">Group</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-slate-400">Officer</th>
                        <th class="px-6 py-3 text-center font-medium text-gray-500 dark:text-slate-400">First Timers</th>
                        <th class="px-6 py-3 text-center font-medium text-gray-500 dark:text-slate-400">Members</th>
                        <th class="px-6 py-3 text-right font-medium text-gray-500 dark:text-slate-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                    @forelse($churches as $church)
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors">
                            <td class="px-6 py-3">
                                <div class="font-medium text-gray-900 dark:text-white">{{ $church->name }}</div>
                                <div class="text-xs text-gray-400 dark:text-slate-500">{{ $church->address ?? 'No address' }}
                                </div>
                            </td>
                            <td class="px-6 py-3 text-gray-500 dark:text-slate-400">{{ $church->group->name ?? '—' }}</td>
                            <td class="px-6 py-3 text-gray-500 dark:text-slate-400">
                                {{ $church->retainingOfficer->name ?? 'Unassigned' }}</td>
                            <td class="px-6 py-3 text-center text-gray-500 dark:text-slate-400">
                                {{ $church->first_timers_count }}</td>
                            <td class="px-6 py-3 text-center">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">{{ $church->members_count }}</span>
                            </td>
                            <td class="px-6 py-3 text-right space-x-2">
                                <a href="{{ route('admin.churches.show', $church) }}"
                                    class="text-sky-600 hover:text-sky-800 dark:text-sky-400 dark:hover:text-sky-300 text-xs font-medium">View</a>
                                <a href="{{ route('admin.churches.edit', $church) }}"
                                    class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 text-xs font-medium">Edit</a>
                                <form method="POST" action="{{ route('admin.churches.destroy', $church) }}" class="inline"
                                    onsubmit="return confirm('Delete this church?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 text-xs font-medium">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400 dark:text-slate-500">No churches found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection