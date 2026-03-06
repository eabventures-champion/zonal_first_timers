@extends('layouts.app')
@section('title', 'My First Timers')
@section('page-title', 'My First Timers')

@section('content')
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <p class="text-sm text-gray-500">First-time visitors assigned to your church</p>
        </div>
        @if(auth()->user()->isOtherChurchRO())
            <div class="flex items-center gap-3">
                <a href="{{ route('ro.first-timers.import.form') }}"
                   class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-300 text-xs font-bold rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Import CSV
                </a>
                <a href="{{ route('ro.first-timers.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg shadow-sm transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Register First Timer
                </a>
            </div>
        @endif
    </div>

    @if(session('import_errors'))
        <div class="bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 rounded-xl p-4 mb-6 shadow-sm">
            <div class="flex items-center gap-2 mb-3 text-red-800 dark:text-red-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <p class="text-sm font-bold uppercase tracking-wider">Import Errors Found</p>
            </div>
            <ul class="space-y-1.5 list-none">
                @foreach(session('import_errors') as $err)
                    <li class="flex items-start gap-2 text-xs text-red-700 dark:text-red-300">
                        <span class="mt-1 w-1.5 h-1.5 rounded-full bg-red-400 shrink-0"></span>
                        <span class="font-medium">{{ $err }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div
        class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-slate-800/50">
                    <tr>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-slate-400">Name</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-slate-400">Contact</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-slate-400">Visit Date</th>
                        <th class="px-6 py-3 text-center font-medium text-gray-500 dark:text-slate-400">Status (Church attendance)</th>
                        <th class="px-6 py-3 text-right font-medium text-gray-500 dark:text-slate-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                    @forelse($firstTimers as $ft)
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors">
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $ft->full_name }}</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">
                                        {{ $ft->total_attended }} {{ Str::plural('Service', $ft->total_attended) }}
                                    </span>
                                </div>
                                <div class="text-xs text-gray-400 dark:text-slate-500">{{ $ft->email }}</div>
                            </td>
                            <td class="px-6 py-3 text-gray-500 dark:text-slate-400">{{ $ft->primary_contact }}</td>
                            <td class="px-6 py-3 text-gray-500 dark:text-slate-400">{{ $ft->date_of_visit?->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-3 text-center">
                                @php 
                                    $sc = [
                                        'New' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-500',
                                        'Developing' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
                                        'Retained' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400'
                                    ]; 
                                @endphp
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $sc[$ft->status] ?? 'bg-gray-100 dark:bg-slate-800' }}">{{ $ft->status }}</span>
                                </td>
                            <td class="px-6 py-3 text-right space-x-2">
                                <a href="{{ route('ro.first-timers.show', $ft) }}"

                                                           class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 text-xs font-medium">View</a>
                                </td>
                            </tr>
                    @empty
                        <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-400 dark:text-slate-500">No first timers assigned to your church.</td>
                        </tr>
                    @endforelse
            </tbody>
        </table>
        </div>
            @if($firstTimers->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-800">
                    {{ $firstTimers->links() }}
                </div>
            @endif
        </div>
@endsection