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

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Church</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Group</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Officer</th>
                        <th class="px-6 py-3 text-center font-medium text-gray-500">First Timers</th>
                        <th class="px-6 py-3 text-center font-medium text-gray-500">Members</th>
                        <th class="px-6 py-3 text-right font-medium text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($churches as $church)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-6 py-3">
                                <div class="font-medium text-gray-900">{{ $church->name }}</div>
                                <div class="text-xs text-gray-400">{{ $church->address ?? 'No address' }}</div>
                            </td>
                            <td class="px-6 py-3 text-gray-500">{{ $church->group->name ?? '—' }}</td>
                            <td class="px-6 py-3 text-gray-500">{{ $church->retainingOfficer->name ?? 'Unassigned' }}</td>
                            <td class="px-6 py-3 text-center">{{ $church->first_timers_count }}</td>
                            <td class="px-6 py-3 text-center">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">{{ $church->members_count }}</span>
                            </td>
                            <td class="px-6 py-3 text-right space-x-2">
                                <a href="{{ route('admin.churches.show', $church) }}"
                                    class="text-sky-600 hover:text-sky-800 text-xs font-medium">View</a>
                                <a href="{{ route('admin.churches.edit', $church) }}"
                                    class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">Edit</a>
                                <form method="POST" action="{{ route('admin.churches.destroy', $church) }}" class="inline"
                                    onsubmit="return confirm('Delete this church?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="text-red-500 hover:text-red-700 text-xs font-medium">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400">No churches found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection