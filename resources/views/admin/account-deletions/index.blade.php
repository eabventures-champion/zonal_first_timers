@extends('layouts.app')
@section('title', 'Account Deletion Requests')
@section('page-title', 'Account Deletion Requests')

@section('content')
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800">
        <div class="p-6 border-b border-gray-100 dark:border-slate-800">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-slate-300">Pending Requests</h3>
            <p class="text-xs text-gray-500 mt-1">Review and action account deletion requests from users.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-slate-800/50">
                        <th
                            class="px-6 py-4 text-xs font-semibold text-gray-600 dark:text-slate-400 uppercase tracking-wider">
                            User</th>
                        <th
                            class="px-6 py-4 text-xs font-semibold text-gray-600 dark:text-slate-400 uppercase tracking-wider">
                            Role</th>
                        <th
                            class="px-6 py-4 text-xs font-semibold text-gray-600 dark:text-slate-400 uppercase tracking-wider">
                            Requested At</th>
                        <th
                            class="px-6 py-4 text-xs font-semibold text-gray-600 dark:text-slate-400 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                    @forelse($requests as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4 text-sm">
                                <div class="font-medium text-gray-900 dark:text-white">{{ $user->name }}</div>
                                <div class="text-gray-500 text-xs mb-1">{{ $user->email }}</div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                    {{ $user->phone }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-400">
                                {{ $user->roles->pluck('name')->implode(', ') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-400">
                                {{ $user->deletion_requested_at->format('M d, Y H:i') }}
                                <div class="text-[10px] text-gray-400">{{ $user->deletion_requested_at->diffForHumans() }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex items-center gap-2">
                                    <form action="{{ route('admin.account-deletions.approve', $user) }}" method="POST"
                                        onsubmit="return confirm('Are you sure you want to PERMANENTLY delete this account?')">
                                        @csrf
                                        <button type="submit"
                                            class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded shadow-sm transition">
                                            Approve Deletion
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.account-deletions.deny', $user) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded shadow-sm transition dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700">
                                            Deny
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 mb-3 text-gray-200" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    <p>No pending account deletion requests.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection