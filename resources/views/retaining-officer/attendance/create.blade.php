@extends('layouts.app')
@section('title', 'Record Attendance')
@section('page-title', 'Record Weekly Attendance')

@section('content')
    <div class="mb-4">
        <a href="{{ route('ro.attendance.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">← Back to
            Attendance</a>
    </div>

    <div class="max-w-3xl">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form method="POST" action="{{ route('ro.attendance.store') }}">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Week Number <span
                                class="text-red-500">*</span></label>
                        <select name="week_number" required
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @for($w = 1; $w <= 12; $w++)
                                <option value="{{ $w }}" {{ old('week_number') == $w ? 'selected' : '' }}>Week {{ $w }}</option>
                            @endfor
                        </select>
                        @error('week_number') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date <span
                                class="text-red-500">*</span></label>
                        <input type="date" name="attendance_date" value="{{ old('attendance_date', date('Y-m-d')) }}"
                            required
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('attendance_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <h3 class="text-sm font-semibold text-gray-700 mb-3">Mark attendance for each first timer:</h3>
                <div class="bg-gray-50 rounded-lg p-4 mb-6 space-y-3">
                    @forelse($firstTimers as $ft)
                        <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-white cursor-pointer transition">
                            <input type="checkbox" name="attended[{{ $ft->id }}]" value="1"
                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <div>
                                <span class="text-sm font-medium text-gray-900">{{ $ft->full_name }}</span>
                                @php $sc = ['New' => 'bg-amber-100 text-amber-700', 'In Progress' => 'bg-blue-100 text-blue-700', 'Member' => 'bg-emerald-100 text-emerald-700']; @endphp
                                <span
                                    class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $sc[$ft->status] ?? '' }}">{{ $ft->status }}</span>
                            </div>
                        </label>
                    @empty
                        <p class="text-sm text-gray-400">No first timers at your church.</p>
                    @endforelse
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea name="notes" rows="2"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit"
                        class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">Save
                        Attendance</button>
                    <a href="{{ route('ro.attendance.index') }}"
                        class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection