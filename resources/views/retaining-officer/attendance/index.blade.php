@extends('layouts.app')
@section('title', 'Weekly Attendance')
@section('page-title', 'Weekly Attendance')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <p class="text-sm text-gray-500 mb-2">Track weekly church attendance for your first timers</p>
            <form action="{{ route('ro.attendance.index') }}" method="GET" class="flex items-center gap-2">
                <select name="month" onchange="this.form.submit()"
                    class="rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 text-xs focus:border-indigo-500 focus:ring-indigo-500">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                        </option>
                    @endfor
                </select>
                <select name="year" onchange="this.form.submit()"
                    class="rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 text-xs focus:border-indigo-500 focus:ring-indigo-500">
                    @for($y = date('Y'); $y >= date('Y') - 1; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </form>
        </div>
        <a href="{{ route('ro.attendance.create') }}"
            class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Record Attendance
        </a>
    </div>

    <div
        class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-slate-800/50">
                    <tr>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-slate-400">First Timer</th>
                        @for($w = 1; $w <= 5; $w++)
                            <th class="px-3 py-3 text-center font-medium text-gray-500 dark:text-slate-400">W{{ $w }}</th>
                        @endfor
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                    @forelse($attendanceData as $data)
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors">
                            <td class="px-6 py-3 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                {{ $data['name'] }}</td>
                            @for($w = 1; $w <= 5; $w++)
                                <td class="px-3 py-3 text-center">
                                    @if(isset($data['weeks'][$w]))
                                        @if($data['weeks'][$w])
                                            <span
                                                class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 text-xs">✓</span>
                                        @else
                                            <span
                                                class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-red-100 text-red-500 text-xs">✗</span>
                                        @endif
                                    @else
                                        <span class="text-gray-200 dark:text-slate-700">—</span>
                                    @endif
                                </td>
                            @endfor
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" class="px-6 py-8 text-center text-gray-400 dark:text-slate-500">No attendance
                                records yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection