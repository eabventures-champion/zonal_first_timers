@extends('layouts.app')
@section('title', 'Foundation School: ' . $firstTimer->full_name)
@section('page-title', 'Foundation School Progress')
@section('back-link', route('admin.foundation-school.index'))

@section('content')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Student Info --}}
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-6 h-fit">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-slate-300 mb-4">Student Info</h3>
            <div class="flex items-center gap-3 mb-6">
                <div
                    class="w-12 h-12 rounded-full bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold text-lg">
                    {{ strtoupper(substr($firstTimer->full_name, 0, 2)) }}
                </div>
                <div>
                    <p class="font-bold text-gray-900 dark:text-white">{{ $firstTimer->full_name }}</p>
                    <p class="text-xs text-gray-500 dark:text-slate-500 font-medium">
                        {{ $firstTimer->church->name ?? 'No church assigned' }}
                    </p>
                </div>
            </div>
            @php
                $completed = $progress->where('completed', true)->count();
                $total = $progress->count();
                $pct = $total > 0 ? round(($completed / $total) * 100) : 0;
            @endphp
            <div class="mb-4">
                <div
                    class="flex justify-between text-[10px] text-gray-500 dark:text-slate-500 mb-1.5 uppercase font-bold tracking-wider">
                    <span>Course Progress</span>
                    <span>{{ $completed }}/{{ $total }} Sessions</span>
                </div>
                <div class="w-full bg-gray-100 dark:bg-slate-800 rounded-full h-2">
                    <div class="h-2 rounded-full bg-gradient-to-r from-indigo-500 to-emerald-500 transition-all duration-700 shadow-sm shadow-indigo-500/20"
                        style="width: {{ $pct }}%"></div>
                </div>
                <p class="text-right text-[10px] mt-1 text-emerald-600 dark:text-emerald-400 font-bold">{{ $pct }}% Complete
                </p>
            </div>
            @php
                $sc = [
                    'not yet' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-400',
                    'in-progress' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
                    'completed' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400'
                ];
                $fsStatus = $firstTimer->foundation_school_status;
            @endphp
            <span
                class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $sc[$fsStatus] ?? 'bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300' }}">{{ $fsStatus }}</span>
        </div>

        {{-- Class Progress & Recording --}}
        <div class="lg:col-span-2 space-y-4">
            @foreach($progress as $item)
                <div
                    class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-5 hover:border-indigo-200 dark:hover:border-indigo-500/30 transition-all">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 rounded-xl flex items-center justify-center text-sm font-bold shadow-sm
                                                                                {{ $item['completed'] ? 'bg-emerald-500 text-white' : ($item['attended'] ? 'bg-amber-400 text-white' : 'bg-gray-100 dark:bg-slate-800 text-gray-500 dark:text-slate-500') }}">
                                {{ $item['completed'] ? '✓' : $item['class']->class_number }}
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $item['class']->name }}</p>
                                <p class="text-[10px] uppercase font-bold tracking-wider mt-0.5
                                                                            @if($item['completed']) text-emerald-600 dark:text-emerald-400 
                                                                            @elseif($item['attended']) text-amber-600 dark:text-amber-400 
                                                                            @else text-gray-400 dark:text-slate-500
                                                                            @endif">
                                    @if($item['completed']) Completed
                                    @elseif($item['attended']) Attended — Pending Completion
                                    @else Not Started
                                    @endif
                                </p>
                            </div>
                        </div>

                        @php
                            $isMember = request()->query('member') === '1';
                        @endphp
                        @if (!$item['attended'])
                            <form
                                action="{{ route('admin.foundation-school.attendance', ['id' => $firstTimer->id, 'member' => $isMember ? 1 : 0]) }}"
                                method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="foundation_class_id" value="{{ $item['class']->id }}">
                                <input type="hidden" name="attended" value="1">
                                <input type="hidden" name="completed" value="0">
                                <button type="submit"
                                    class="px-3 py-1.5 bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 text-[10px] font-bold rounded-lg hover:bg-amber-100 dark:hover:bg-amber-500/20 transition-colors uppercase tracking-wider">
                                    Mark Attended
                                </button>
                            </form>
                        @endif

                        @if (!$item['completed'])
                            <form
                                action="{{ route('admin.foundation-school.attendance', ['id' => $firstTimer->id, 'member' => $isMember ? 1 : 0]) }}"
                                method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="foundation_class_id" value="{{ $item['class']->id }}">
                                <input type="hidden" name="attended" value="1">
                                <input type="hidden" name="completed" value="1">
                                <button type="submit"
                                    class="px-3 py-1.5 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-bold rounded-lg hover:bg-emerald-100 dark:hover:bg-emerald-500/20 transition-colors uppercase tracking-wider">
                                    Mark Done
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection