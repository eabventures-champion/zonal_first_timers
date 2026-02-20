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
        {{-- <a href="{{ route('ro.attendance.create') }}"
            class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Record Attendance
        </a> --}}
    </div>

    <div x-data="{ 
            month: {{ $month }},
            year: {{ $year }},
            loading: {},
            async toggle(ftId, weekNum, serviceDate, currentAttended) {
                const key = `${ftId}-${weekNum}`;
                if (this.loading[key]) return;
                
                this.loading[key] = true;
                try {
                    const response = await fetch('{{ route('ro.attendance.toggle') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=&quot;csrf-token&quot;]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            first_timer_id: ftId,
                            month: this.month,
                            year: this.year,
                            week_number: weekNum,
                            service_date: serviceDate,
                            attended: !currentAttended
                        })
                    });
                    
                    const data = await response.json();
                    if (data.success) {
                        // Update local row state if needed, or just reload/refresh via simple feedback
                        window.location.reload(); // Simple approach for consistency, or we could update DOM elements
                    }
                } catch (e) {
                    console.error(e);
                } finally {
                    this.loading[key] = false;
                }
            }
        }"
        class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-slate-800/50">
                    <tr>
                        <th class="px-6 py-4 text-left font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider text-[11px]">First Timer</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider text-[11px]">Date Joined</th>
                        @foreach($sundays as $sunday)
                            <th class="px-3 py-4 text-center">
                                <span class="block text-[11px] font-bold text-gray-500 dark:text-slate-400 uppercase tracking-tight">{{ $sunday['label'] }}</span>
                                <span class="block text-[9px] text-gray-400 dark:text-slate-500 mt-0.5">{{ \Carbon\Carbon::parse($sunday['date'])->format('M d') }}</span>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                    @forelse($groupedAttendance as $joinMonth => $firstTimers)
                        <tr class="bg-gray-50/10 dark:bg-slate-800/20">
                            <td colspan="{{ 2 + count($sundays) }}" class="px-6 py-2">
                                <span
                                    class="text-[10px] font-bold uppercase tracking-wider text-indigo-500/80 dark:text-indigo-400/80">JOINED IN {{ strtoupper($joinMonth) }}</span>
                            </td>
                        </tr>
                        @foreach($firstTimers as $data)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-indigo-50 dark:bg-indigo-950/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold text-xs">
                                            {{ strtoupper(substr($data['name'], 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900 dark:text-slate-200 text-sm">{{ $data['name'] }}</p>
                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[9px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">
                                                {{ $data['total_attended'] }} {{ Str::plural('Service', $data['total_attended']) }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-500 dark:text-slate-400 whitespace-nowrap text-xs">
                                    {{ $data['join_date'] }}
                                </td>
                                @foreach($sundays as $sunday)
                                    @php
                                        $attended = $data['weeks'][$sunday['week_number']] ?? false;
                                        $exists = isset($data['weeks'][$sunday['week_number']]);
                                        $key = $data['id'] . '-' . $sunday['week_number'];
                                    @endphp
                                    <td class="px-3 py-4 text-center">
                                        <button 
                                            @click="toggle({{ $data['id'] }}, {{ $sunday['week_number'] }}, '{{ $sunday['date'] }}', {{ $attended ? 'true' : 'false' }})"
                                            :class="loading['{{ $key }}'] ? 'opacity-50' : 'hover:scale-110'"
                                            class="inline-flex items-center justify-center w-7 h-7 rounded-lg transition-all duration-200 relative group/btn"
                                            title="{{ \Carbon\Carbon::parse($sunday['date'])->format('l, M d, Y') }}">
                                            
                                            @if($attended)
                                                <div class="bg-emerald-500 dark:bg-emerald-600 text-white p-1 rounded-md shadow-sm shadow-emerald-200 dark:shadow-none">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </div>
                                            @elseif($exists && !$attended)
                                                <div class="bg-red-500 dark:bg-red-600 text-white p-1 rounded-md shadow-sm shadow-red-200 dark:shadow-none">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </div>
                                            @else
                                                <div class="border-2 border-dashed border-gray-200 dark:border-slate-700 text-gray-300 dark:text-slate-600 p-1 rounded-md group-hover/btn:border-indigo-300 dark:group-hover/btn:border-indigo-500 group-hover/btn:text-indigo-400">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                    </svg>
                                                </div>
                                            @endif

                                            {{-- Spinner --}}
                                            <div x-show="loading['{{ $key }}']" class="absolute inset-0 flex items-center justify-center bg-white/50 dark:bg-slate-900/50 rounded-lg">
                                                <svg class="animate-spin h-4 w-4 text-indigo-600" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                            </div>
                                        </button>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="{{ 2 + count($sundays) }}" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-4 opacity-30">
                                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <p class="text-lg font-medium text-gray-500 dark:text-slate-400">No attendance records for newcomers in this period.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection