@extends('layouts.app')

@section('header')
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">System Trash</h2>
            <p class="text-sm text-gray-500 dark:text-slate-400">Recover deleted spiritual profiles and their history</p>
        </div>
        <div class="flex items-center gap-3">
            <div
                class="px-4 py-2 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 rounded-xl border border-indigo-100 dark:border-indigo-500/20 text-xs font-bold flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Unified Recycle Bin
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="space-y-6">
        @if(session('success'))
            <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400"
                role="alert">
                <span class="font-medium">Success!</span> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                <span class="font-medium">Error!</span> {{ session('error') }}
            </div>
        @endif

        <form id="bulk-trash-form" method="POST">
            @csrf
            <div class="flex items-center justify-between mb-4 px-2">
                <div id="bulk-actions"
                    class="flex items-center gap-3 opacity-0 pointer-events-none transition-all duration-300">
                    <span class="text-sm font-medium text-slate-500 dark:text-slate-400 mr-2 selected-count">0
                        selected</span>
                    <button type="submit" formaction="{{ route('admin.trash.bulk-restore') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-lg shadow-indigo-200 dark:shadow-none transition-all"
                        onclick="return confirm('Restore all selected records?')">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Restore Selected
                    </button>
                    <button type="submit" formaction="{{ route('admin.trash.bulk-force-delete') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-lg shadow-rose-200 dark:shadow-none transition-all"
                        onclick="return confirm('WARNING: This will permanently PURGE all selected records. This cannot be undone. Proceed?')">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Purge Selected
                    </button>
                </div>
            </div>

            <div
                class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 dark:bg-slate-800/50 border-b border-gray-100 dark:border-slate-800">
                                <th class="px-6 py-4 w-10">
                                    <input type="checkbox" id="select-all"
                                        class="rounded border-gray-300 dark:border-slate-700 text-indigo-600 focus:ring-indigo-500 bg-white dark:bg-slate-800">
                                </th>
                                <th
                                    class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                                    Name & Profile</th>
                                <th
                                    class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider text-center">
                                    Last Status</th>
                                <th
                                    class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                                    Church</th>
                                <th
                                    class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">
                                    Deleted At</th>
                                <th
                                    class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider text-right">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                            @forelse($items as $item)
                                <tr class="group hover:bg-gray-50/50 dark:hover:bg-slate-800/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <input type="checkbox" name="ids[]" value="{{ $item->trash_type }}:{{ $item->id }}"
                                            class="item-checkbox rounded border-gray-300 dark:border-slate-700 text-indigo-600 focus:ring-indigo-500 bg-white dark:bg-slate-800">
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-xs font-bold text-slate-500">
                                                {{ strtoupper(substr($item->full_name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-800 dark:text-slate-200">{{ $item->full_name }}
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-slate-500">
                                                    {{ $item->primary_contact }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($item->trash_type === 'member')
                                            <span
                                                class="px-2.5 py-1 text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 rounded-lg uppercase">
                                                {{ $item->display_status }}
                                            </span>
                                        @else
                                            <span
                                                class="px-2.5 py-1 text-[10px] font-bold bg-indigo-100 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400 rounded-lg uppercase">
                                                {{ $item->display_status }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="px-2.5 py-1 text-[10px] font-bold bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 rounded-lg uppercase whitespace-nowrap">
                                            {{ $item->church->name ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-xs text-gray-600 dark:text-slate-400 font-medium whitespace-nowrap">
                                            {{ $item->deleted_at->format('M d, Y') }}
                                            <p class="text-[10px] text-gray-400">{{ $item->deleted_at->diffForHumans() }}</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            <button type="button"
                                                onclick="submitRestore('{{ $item->trash_type }}', '{{ $item->id }}')"
                                                class="px-3 py-1.5 bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400 rounded-lg text-xs font-bold hover:bg-indigo-100 dark:hover:bg-indigo-500/20 transition-all flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                                Restore
                                            </button>

                                            <button type="button"
                                                onclick="submitPurge('{{ $item->trash_type }}', '{{ $item->id }}')"
                                                class="px-3 py-1.5 bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400 rounded-lg text-xs font-bold hover:bg-rose-100 dark:hover:bg-rose-500/20 transition-all flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Purge
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-slate-500">
                                        <div class="flex flex-col items-center gap-2">
                                            <svg class="w-12 h-12 text-gray-200 dark:text-slate-800" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            <p class="text-sm font-medium">The recycle bin is empty.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($items->hasPages())
                    <div class="px-6 py-4 bg-gray-50 dark:bg-slate-800/30 border-t border-gray-100 dark:border-slate-800">
                        {{ $items->links() }}
                    </div>
                @endif
            </div>
        </form>
    </div>

    {{-- Hidden individual action forms --}}
    <form id="individual-restore-form" method="POST" class="hidden">@csrf</form>
    <form id="individual-purge-form" method="POST" class="hidden">@csrf @method('DELETE')</form>

    @push('scripts')
        <script>
            function submitRestore(type, id) {
                if (confirm('Restore this record and its full history?')) {
                    const form = document.getElementById('individual-restore-form');
                    form.action = `/admin/trash/${type}/${id}/restore`;
                    form.submit();
                }
            }

            function submitPurge(type, id) {
                if (confirm('WARNING: This action is permanent and cannot be undone. Proceed?')) {
                    const form = document.getElementById('individual-purge-form');
                    form.action = `/admin/trash/${type}/${id}/force-delete`;
                    form.submit();
                }
            }

            document.addEventListener('DOMContentLoaded', function () {
                const selectAll = document.getElementById('select-all');
                const checkboxes = document.querySelectorAll('.item-checkbox');
                const bulkActions = document.getElementById('bulk-actions');
                const selectedCountLabel = document.querySelector('.selected-count');

                function updateBulkActions() {
                    const checkedCount = document.querySelectorAll('.item-checkbox:checked').length;
                    if (checkedCount > 0) {
                        bulkActions.classList.remove('opacity-0', 'pointer-events-none');
                        selectedCountLabel.textContent = `${checkedCount} selected`;
                    } else {
                        bulkActions.classList.add('opacity-0', 'pointer-events-none');
                    }
                }

                if (selectAll) {
                    selectAll.addEventListener('change', function () {
                        checkboxes.forEach(cb => cb.checked = selectAll.checked);
                        updateBulkActions();
                    });
                }

                checkboxes.forEach(cb => {
                    cb.addEventListener('change', function () {
                        const checkedCount = document.querySelectorAll('.item-checkbox:checked').length;
                        if (!this.checked && selectAll) selectAll.checked = false;
                        if (checkedCount === checkboxes.length && selectAll) selectAll.checked = true;
                        updateBulkActions();
                    });
                });
            });
        </script>
    @endpush
@endsection