@extends('layouts.app')
@section('title', 'Foundation School: ' . $firstTimer->full_name)
@section('page-title', 'Foundation School Progress')

@section('content')
    <div class="mb-4">
        <a href="{{ route('ro.foundation-school.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">← Back to
            Foundation School</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Student Info --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Student</h3>
            <div class="flex items-center gap-3 mb-4">
                <div
                    class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold">
                    {{ strtoupper(substr($firstTimer->full_name, 0, 2)) }}
                </div>
                <div>
                    <p class="font-semibold text-gray-900">{{ $firstTimer->full_name }}</p>
                    <p class="text-xs text-gray-500">{{ $firstTimer->church->name ?? '' }}</p>
                </div>
            </div>
            @php
                $completed = collect($progress)->where('completed', true)->count();
                $total = collect($progress)->count();
                $pct = $total > 0 ? round(($completed / $total) * 100) : 0;
            @endphp
            <div class="mb-2">
                <div class="flex justify-between text-xs text-gray-500 mb-1">
                    <span>Progress</span>
                    <span>{{ $completed }}/{{ $total }}</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2.5">
                    <div class="h-2.5 rounded-full bg-gradient-to-r from-indigo-500 to-emerald-500"
                        style="width: {{ $pct }}%"></div>
                </div>
            </div>
        </div>

        {{-- Class Progress --}}
        <div class="lg:col-span-2 space-y-4">
            @foreach($progress as $item)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-lg flex items-center justify-center text-sm font-bold
                                        {{ $item['completed'] ? 'bg-emerald-500 text-white' : ($item['attended'] ? 'bg-amber-400 text-white' : 'bg-gray-200 text-gray-500') }}">
                                {{ $item['completed'] ? '✓' : $item['class']->class_number }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $item['class']->name }}</p>
                                <p class="text-xs text-gray-400">
                                    @if($item['completed']) Completed
                                    @elseif($item['attended']) Attended
                                    @else Not attended
                                    @endif
                                </p>
                            </div>
                        </div>

                        @if(!$item['completed'] && $firstTimer->status !== 'Member')
                            <form method="POST" action="{{ route('ro.foundation-school.attendance', $firstTimer) }}"
                                class="flex items-center gap-2">
                                @csrf
                                <input type="hidden" name="foundation_class_id" value="{{ $item['class']->id }}">
                                <input type="hidden" name="attendance_date" value="{{ date('Y-m-d') }}">

                                @if(!$item['attended'])
                                    <input type="hidden" name="attended" value="1">
                                    <input type="hidden" name="completed" value="0">
                                    <button type="submit"
                                        class="px-3 py-1.5 text-xs font-medium text-amber-700 bg-amber-50 hover:bg-amber-100 rounded-lg border border-amber-200 transition">Mark
                                        Attended</button>
                                @endif

                                @if(!$item['completed'])
                                    <button type="submit" name="completed" value="1"
                                        class="px-3 py-1.5 text-xs font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-lg border border-emerald-200 transition"
                                        onclick="this.form.elements['attended'].value='1'">
                                        Mark Complete
                                    </button>
                                @endif
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection