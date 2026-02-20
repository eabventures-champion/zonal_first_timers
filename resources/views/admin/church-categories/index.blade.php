@extends('layouts.app')
@section('title', 'Church Categories')
@section('page-title', 'Church Categories')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <p class="text-sm text-gray-500">Manage top-level church classifications</p>
        <a href="{{ route('admin.church-categories.create') }}"
            class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Category
        </a>
    </div>

    <div
        class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-slate-800/50">
                <tr>
                    <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-slate-400">#</th>
                    <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-slate-400">Name</th>
                    <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-slate-400">Description</th>
                    <th class="px-6 py-3 text-center font-medium text-gray-500 dark:text-slate-400">Groups</th>
                    <th class="px-6 py-3 text-right font-medium text-gray-500 dark:text-slate-400">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                @forelse($categories as $i => $category)
                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors">
                        <td class="px-6 py-3 text-gray-400 dark:text-slate-500">{{ $i + 1 }}</td>
                        <td class="px-6 py-3 font-medium text-gray-900 dark:text-white">{{ $category->name }}</td>
                        <td class="px-6 py-3 text-gray-500 dark:text-slate-400">
                            {{ Str::limit($category->description, 50) ?? '—' }}</td>
                        <td class="px-6 py-3 text-center">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400">
                                {{ $category->groups_count }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-right space-x-2">
                            <a href="{{ route('admin.church-categories.edit', $category) }}"
                                class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 text-xs font-medium">Edit</a>
                            <form method="POST" action="{{ route('admin.church-categories.destroy', $category) }}"
                                class="inline" onsubmit="return confirm('Delete this category?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 text-xs font-medium">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-400 dark:text-slate-500">No categories found.
                            Create one to get
                            started.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection