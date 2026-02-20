@extends('layouts.app')
@section('title', 'Foundation School')
@section('page-title', 'Foundation School')

@section('content')
    <div class="mb-6">
        <p class="text-sm text-gray-500">Track foundation school progress for first timers at your church</p>
    </div>

    <div x-data="{ expandedGroups: new Set(['in-progress']) }" class="space-y-4">
        @foreach($groupedData as $status => $students)
            <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden">
                <button @click="if(expandedGroups.has('{{ $status }}')) expandedGroups.delete('{{ $status }}'); else expandedGroups.add('{{ $status }}')"
                    class="w-full flex items-center justify-between px-6 py-4 bg-gray-50/50 dark:bg-slate-800/50 hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-gray-400 transition-transform" :class="expandedGroups.has('{{ $status }}') ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                        @php
                            $statusColors = [
                                'not yet' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-400',
                                'in-progress' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
                                'completed' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400'
                            ];
                        @endphp
                        <span class="font-bold text-[10px] uppercase tracking-widest px-2.5 py-1 rounded-full {{ $statusColors[$status] }}">{{ $status }}</span>
                        <span class="text-xs text-gray-400 dark:text-slate-500">({{ $students->count() }})</span>
                    </div>
                </button>

                <div x-show="expandedGroups.has('{{ $status }}')" x-collapse>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50/30 dark:bg-slate-800/20 border-b border-gray-100 dark:border-slate-800">
                                <tr>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-slate-400">Name</th>
                                    <th class="px-6 py-3 text-center font-medium text-gray-500 dark:text-slate-400">Progress</th>
                                    <th class="px-6 py-3 text-right font-medium text-gray-500 dark:text-slate-400">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                                @forelse($students as $ft)
                                    @php
                                        $completed = $ft->foundationAttendances->where('completed', true)->count();
                                        $totalClasses = $totalClassCount ?? 7;
                                        $pct = $totalClasses > 0 ? round(($completed / $totalClasses) * 100) : 0;
                                    @endphp
                                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors">
                                        <td class="px-6 py-3 font-medium text-gray-900 dark:text-white">{{ $ft->full_name }}</td>
                                        <td class="px-6 py-3">
                                            <div class="flex items-center justify-center gap-2">
                                                <div class="w-24 bg-gray-100 dark:bg-slate-800 rounded-full h-1.5">
                                                    <div class="h-1.5 rounded-full bg-indigo-500 transition-all" style="width: {{ $pct }}%">
                                                    </div>
                                                </div>
                                                <span class="text-[10px] text-gray-400 dark:text-slate-500 font-bold whitespace-nowrap">{{ $completed }}/{{ $totalClasses }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-3 text-right">
                                            <a href="{{ route('ro.foundation-school.show', ['id' => $ft->id, 'member' => $ft->is_member_record ? 1 : 0]) }}"
                                                class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 text-xs font-bold uppercase tracking-wider transition-colors">View Progress</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-8 text-center text-gray-400 dark:text-slate-500 italic">No students in this category.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection