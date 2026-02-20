@extends('layouts.app')
@section('title', 'Foundation School')
@section('page-title', 'Foundation School')

@section('content')
    <div class="mb-6">
        <p class="text-sm text-gray-500">Track foundation school progress for first timers at your church</p>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-slate-800/50">
                    <tr>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-slate-400">Name</th>
                        <th class="px-6 py-3 text-center font-medium text-gray-500 dark:text-slate-400">Status</th>
                        <th class="px-6 py-3 text-center font-medium text-gray-500 dark:text-slate-400">Progress</th>
                        <th class="px-6 py-3 text-right font-medium text-gray-500 dark:text-slate-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                    @forelse($firstTimers as $ft)
                        @php
                            $completed = $ft->foundationAttendances->where('completed', true)->count();
                            $totalClasses = $totalClassCount ?? 4;
                            $pct = $totalClasses > 0 ? round(($completed / $totalClasses) * 100) : 0;
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors">
                            <td class="px-6 py-3 font-medium text-gray-900 dark:text-white">{{ $ft->full_name }}</td>
                            <td class="px-6 py-3 text-center">
                                @php 
                                    $sc = [
                                        'New' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-500', 
                                        'In Progress' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400', 
                                        'Member' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400'
                                    ]; 
                                @endphp
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $sc[$ft->status] ?? 'bg-gray-100 dark:bg-slate-800' }}">{{ $ft->status }}</span>
                            </td>
                            <td class="px-6 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    <div class="w-24 bg-gray-100 dark:bg-slate-800 rounded-full h-2">
                                        <div class="h-2 rounded-full bg-indigo-500 transition-all" style="width: {{ $pct }}%">
                                        </div>
                                    </div>
                                    <span class="text-xs text-gray-400 dark:text-slate-500">{{ $completed }}/{{ $totalClasses }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <a href="{{ route('ro.foundation-school.show', $ft) }}"
                                    class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 text-xs font-medium">View Progress</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-400 dark:text-slate-500">No first timers at your church.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection