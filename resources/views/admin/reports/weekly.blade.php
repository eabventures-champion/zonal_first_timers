@extends('layouts.app')

@section('title', 'Weekly First Timers Report')
@section('page-title', 'Weekly First Timers Report')

@section('content')
    <div class="space-y-6">
        <!-- Header & Actions -->
        <div
            class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800">
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Reporting Overview</h3>
                <p class="text-sm text-gray-500 dark:text-slate-400">View and track first timers received weekly across all
                    churches.</p>
            </div>
            <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                <form method="GET" action="{{ route('admin.reports.weekly') }}"
                    class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                    <select name="month"
                        class="rounded-lg border-gray-300 dark:border-slate-700 bg-gray-50 dark:bg-slate-800 text-sm focus:ring-indigo-500 focus:border-indigo-500 dark:text-white shadow-sm">
                        @foreach($months as $m => $name)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    <select name="year"
                        class="rounded-lg border-gray-300 dark:border-slate-700 bg-gray-50 dark:bg-slate-800 text-sm focus:ring-indigo-500 focus:border-indigo-500 dark:text-white shadow-sm">
                        @foreach($years as $y)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>

                    <select name="group_id" id="group_id"
                        class="rounded-lg border-gray-300 dark:border-slate-700 bg-gray-50 dark:bg-slate-800 text-sm focus:ring-indigo-500 focus:border-indigo-500 dark:text-white shadow-sm">
                        <option value="">All Groups</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" {{ $groupId == $group->id ? 'selected' : '' }}>
                                {{ $group->name }}
                            </option>
                        @endforeach
                    </select>

                    <select name="church_id" id="church_id"
                        class="rounded-lg border-gray-300 dark:border-slate-700 bg-gray-50 dark:bg-slate-800 text-sm focus:ring-indigo-500 focus:border-indigo-500 dark:text-white shadow-sm">
                        <option value="">All Churches</option>
                        @foreach($churches as $church)
                            <option value="{{ $church->id }}" data-group="{{ $church->church_group_id }}"
                                {{ $churchId == $church->id ? 'selected' : '' }}>
                                {{ $church->name }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit"
                        class="px-4 py-2 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 text-sm font-medium rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-500/20 transition-colors shadow-sm">
                        Filter
                    </button>
                    <a href="{{ route('admin.reports.weekly') }}" 
                        class="px-3 py-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200" title="Reset Filters">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </a>
                </form>

                <div class="h-6 w-px bg-gray-200 dark:bg-slate-700 hidden sm:block"></div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.reports.weekly.excel', ['month' => $month, 'year' => $year, 'group_id' => $groupId, 'church_id' => $churchId]) }}"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-green-500 text-white text-sm font-medium rounded-lg hover:bg-green-600 transition-colors shadow-sm shadow-green-500/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        Excel
                    </a>
                    <a href="{{ route('admin.reports.weekly.pdf', ['month' => $month, 'year' => $year, 'group_id' => $groupId, 'church_id' => $churchId]) }}"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-red-500 text-white text-sm font-medium rounded-lg hover:bg-red-600 transition-colors shadow-sm shadow-red-500/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        PDF
                    </a>
                </div>
            </div>
        </div>

        <!-- Report Table -->
        <div
            class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-slate-800/50 border-b border-gray-100 dark:border-slate-800">
                            <th
                                class="py-3 px-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                                Category</th>
                            <th
                                class="py-3 px-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                                Group</th>
                            <th
                                class="py-3 px-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                                Church</th>
                            @foreach($weeksInMonth as $week)
                                <th
                                    class="py-3 px-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider text-center">
                                    {{ strtoupper($week['start']) }}
                                </th>
                            @endforeach
                            <th
                                class="py-3 px-4 text-xs font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider text-center bg-indigo-50 border-l border-indigo-100 dark:bg-indigo-500/10 dark:border-indigo-500/20">
                                Total</th>
                        </tr>
                    </thead>
                    @forelse($reportData as $catName => $catData)
                        @foreach($catData['groups'] as $groupName => $groupData)
                            <tbody x-data="{ expanded: true }" class="divide-y divide-gray-100 dark:divide-slate-800">
                                <tr class="bg-gray-50/30 dark:bg-slate-800/20">
                                    <td class="py-3 px-4 text-sm font-bold text-gray-900 dark:text-white">{{ $catName }}</td>
                                    <td class="py-3 px-4 text-sm font-bold text-indigo-600 dark:text-indigo-400 cursor-pointer flex items-center gap-2" @click="expanded = !expanded">
                                        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                        {{ $groupName }}
                                    </td>
                                    <td class="py-3 px-4 text-xs font-semibold text-gray-400 dark:text-slate-500 uppercase italic">Group Total</td>
                                    
                                    @foreach($groupData['weeks'] as $weekCount)
                                        <td class="py-3 px-4 text-sm text-center font-bold text-gray-900 dark:text-white">
                                            {{ $weekCount }}
                                        </td>
                                    @endforeach

                                    <td class="py-3 px-4 text-sm font-black text-center text-gray-900 dark:text-white bg-gray-100/50 dark:bg-slate-800/50 border-l border-gray-100 dark:border-slate-800">
                                        {{ $groupData['total'] }}
                                    </td>
                                </tr>

                                {{-- Church Rows --}}
                                @foreach($groupData['churches'] as $churchName => $stats)
                                    <tr x-show="expanded" x-transition class="hover:bg-gray-50/50 dark:hover:bg-slate-800/50 transition-colors">
                                        <td class="py-3 px-4 text-sm text-gray-400 dark:text-slate-600">{{ $catName }}</td>
                                        <td class="py-3 px-4 text-sm text-gray-400 dark:text-slate-600 pl-8">{{ $groupName }}</td>
                                        <td class="py-3 px-4 text-sm text-gray-500 dark:text-slate-400 font-medium">{{ $churchName }}</td>

                                        @foreach($stats['weeks'] as $weekCount)
                                            <td class="py-3 px-4 text-sm text-center font-medium {{ $weekCount > 0 ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/30 dark:bg-indigo-500/5' : 'text-gray-400 dark:text-slate-600' }}">
                                                {{ $weekCount }}
                                            </td>
                                        @endforeach

                                        <td class="py-3 px-4 text-sm font-bold text-center text-gray-900 dark:text-white bg-gray-50/30 dark:bg-slate-800/30 border-l border-gray-100 dark:border-slate-800">
                                            {{ $stats['total'] }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        @endforeach

                        <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                            {{-- Category Grand Total Row --}}
                            <tr class="bg-indigo-50/50 dark:bg-indigo-900/10 border-t-2 border-indigo-100 dark:border-indigo-900/50">
                                <td colspan="3" class="py-4 px-4 text-sm font-black text-indigo-700 dark:text-indigo-300 uppercase tracking-wider text-right">
                                    {{ $catName }} GRAND TOTAL
                                </td>
                                @foreach($catData['weeks'] as $weekCount)
                                    <td class="py-4 px-4 text-sm text-center font-black text-indigo-700 dark:text-indigo-300">
                                        {{ $weekCount }}
                                    </td>
                                @endforeach
                                <td class="py-4 px-4 text-sm font-black text-center text-white bg-indigo-600 dark:bg-indigo-500">
                                    {{ $catData['total'] }}
                                </td>
                            </tr>
                        </tbody>
                    @empty
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                            <tr>
                                <td colspan="{{ 4 + count($weeksInMonth) }}"
                                    class="py-8 px-4 text-center text-gray-500 dark:text-slate-400">
                                    No report data available for the selected period.
                                </td>
                            </tr>
                        </tbody>
                    @endforelse
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const groupSelect = document.getElementById('group_id');
                const churchSelect = document.getElementById('church_id');
                if (!groupSelect || !churchSelect) return;

                const originalOptions = Array.from(churchSelect.options);

                function filterChurches() {
                    const selectedGroupId = groupSelect.value;
                    const currentValue = churchSelect.value;
                    
                    // Clear current options
                    churchSelect.innerHTML = '';
                    
                    // Add back relevant options
                    originalOptions.forEach(option => {
                        if (!selectedGroupId || !option.dataset.group || option.dataset.group === selectedGroupId || option.value === "") {
                            churchSelect.appendChild(option.cloneNode(true));
                        }
                    });

                    // Set value back if it still exists in the filtered list
                    if (Array.from(churchSelect.options).some(opt => opt.value === currentValue)) {
                        churchSelect.value = currentValue;
                    } else {
                        churchSelect.value = "";
                    }
                }

                groupSelect.addEventListener('change', filterChurches);
                
                // Run once on load if group is already selected
                if (groupSelect.value) {
                    const savedChurchId = "{{ $churchId }}";
                    filterChurches();
                    if (savedChurchId) {
                        churchSelect.value = savedChurchId;
                    }
                }
            });
        </script>
    @endpush
@endsection