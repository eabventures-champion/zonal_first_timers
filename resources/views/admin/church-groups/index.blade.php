@extends('layouts.app')
@section('title', 'Church Groups')
@section('page-title', 'Church Groups')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <p class="text-sm text-gray-500">Manage church groups within categories</p>
        <a href="{{ route('admin.church-groups.create') }}"
            class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Group
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left font-medium text-gray-500">#</th>
                    <th class="px-6 py-3 text-left font-medium text-gray-500">Name</th>
                    <th class="px-6 py-3 text-left font-medium text-gray-500">Category</th>
                    <th class="px-6 py-3 text-left font-medium text-gray-500">Description</th>
                    <th class="px-6 py-3 text-center font-medium text-gray-500">Churches</th>
                    <th class="px-6 py-3 text-right font-medium text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($groups as $i => $group)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-3 text-gray-400">{{ $i + 1 }}</td>
                        <td class="px-6 py-3 font-medium text-gray-900">{{ $group->name }}</td>
                        <td class="px-6 py-3 text-gray-500">{{ $group->category->name ?? '—' }}</td>
                        <td class="px-6 py-3 text-gray-500">{{ Str::limit($group->description, 40) ?? '—' }}</td>
                        <td class="px-6 py-3 text-center">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">{{ $group->churches_count }}</span>
                        </td>
                        <td class="px-6 py-3 text-right space-x-2">
                            <a href="{{ route('admin.church-groups.edit', $group) }}"
                                class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">Edit</a>
                            <form method="POST" action="{{ route('admin.church-groups.destroy', $group) }}" class="inline"
                                onsubmit="return confirm('Delete this group?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="text-red-500 hover:text-red-700 text-xs font-medium">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-400">No groups found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection