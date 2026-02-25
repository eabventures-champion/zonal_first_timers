@extends('layouts.app')
@section('title', 'Users')
@section('page-title', 'User Management')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <p class="text-sm text-gray-500">Manage system users and roles</p>
        <div class="flex items-center gap-3">
            <button type="button" id="bulk-delete-btn" disabled
                class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 disabled:bg-red-400 disabled:cursor-not-allowed text-white text-sm font-medium rounded-lg shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Delete Selected
            </button>
            <a href="{{ route('admin.users.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add User
            </a>
        </div>
    </div>

    <form id="bulk-delete-form" method="POST" action="{{ route('admin.users.bulk-delete') }}">
        @csrf
        <div
            class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-6 py-3 text-left w-10">
                                <input type="checkbox" id="select-all"
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            </th>
                            <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-slate-400">Name</th>
                            <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-slate-400">Email</th>
                            <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-slate-400">Role</th>
                            <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-slate-400">Church</th>
                            <th class="px-6 py-3 text-right font-medium text-gray-500 dark:text-slate-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors">
                                <td class="px-6 py-3">
                                    @if($user->id !== auth()->id())
                                        <input type="checkbox" name="user_ids[]" value="{{ $user->id }}"
                                            class="user-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    @endif
                                </td>
                                <td class="px-6 py-3">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $user->name }}</div>
                                    <div class="text-xs text-gray-400 dark:text-slate-500">{{ $user->phone ?? '' }}</div>
                                </td>
                                <td class="px-6 py-3 text-gray-500 dark:text-slate-400">{{ $user->email }}</td>
                                <td class="px-6 py-3">
                                    @foreach($user->roles as $role)
                                        @php
                                            $rc = [
                                                'Super Admin' => 'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-500',
                                                'Admin' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400',
                                                'Bringer' => 'bg-orange-100 text-orange-700 dark:bg-orange-500/10 dark:text-orange-400',
                                                'Retaining Officer' => 'bg-teal-100 text-teal-700 dark:bg-teal-500/10 dark:text-teal-400'
                                            ];
                                        @endphp
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $rc[$role->name] ?? 'bg-gray-100 dark:bg-slate-800 dark:text-slate-300' }}">
                                            {{ $role->name }}
                                        </span>
                                    @endforeach
                                    @if($user->isBringer() && $user->hasRole('Retaining Officer') && !$user->hasRole('Bringer'))
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700 dark:bg-orange-500/10 dark:text-orange-400">
                                            Bringer
                                        </span>
                                    @endif
                                    @if($user->hasRole('Member') && $user->firstTimer && !$user->member)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400 uppercase">
                                            FT
                                        </span>
                                    @endif
                                    @if($user->hasRole('Member') && $user->member)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 uppercase">
                                            Retained
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-gray-500 dark:text-slate-400">{{ $user->church->name ?? '—' }}</td>
                                <td class="px-6 py-3 text-right space-x-2">
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                        class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 text-xs font-medium">Edit</a>
                                    @if($user->id !== auth()->id())
                                        <button type="button"
                                            onclick="confirm('Delete this user?') ? document.getElementById('delete-form-{{ $user->id }}').submit() : false;"
                                            class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 text-xs font-medium">Delete</button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-400 dark:text-slate-500">No users found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-800">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </form>

    {{-- Hidden individual delete forms moved outside the main bulk-delete form --}}
    <div id="individual-delete-forms" class="hidden" aria-hidden="true">
        @foreach($users as $user)
            @if($user->id !== auth()->id())
                <form id="delete-form-{{ $user->id }}" method="POST" action="{{ route('admin.users.destroy', $user) }}">
                    @csrf @method('DELETE')
                </form>
            @endif
        @endforeach
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const selectAll = document.getElementById('select-all');
                const checkboxes = document.querySelectorAll('.user-checkbox');
                const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
                const bulkDeleteForm = document.getElementById('bulk-delete-form');

                function updateBulkDeleteBtn() {
                    const checkedCount = document.querySelectorAll('.user-checkbox:checked').length;
                    bulkDeleteBtn.disabled = checkedCount === 0;

                    if (checkedCount > 0) {
                        bulkDeleteBtn.innerHTML = `
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                </svg>
                                                                Delete Selected (${checkedCount})
                                                            `;
                    } else {
                        bulkDeleteBtn.innerHTML = `
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                </svg>
                                                                Delete Selected
                                                            `;
                    }
                }

                if (selectAll) {
                    selectAll.addEventListener('change', function () {
                        checkboxes.forEach(cb => cb.checked = selectAll.checked);
                        updateBulkDeleteBtn();
                    });
                }

                checkboxes.forEach(cb => {
                    cb.addEventListener('change', function () {
                        if (!this.checked && selectAll) {
                            selectAll.checked = false;
                        } else if (document.querySelectorAll('.user-checkbox:checked').length === checkboxes.length && selectAll) {
                            selectAll.checked = true;
                        }
                        updateBulkDeleteBtn();
                    });
                });

                if (bulkDeleteBtn) {
                    bulkDeleteBtn.addEventListener('click', function () {
                        if (confirm('Are you sure you want to delete the selected users? This action cannot be undone.')) {
                            bulkDeleteForm.submit();
                        }
                    });
                }
            });
        </script>
    @endpush
@endsection