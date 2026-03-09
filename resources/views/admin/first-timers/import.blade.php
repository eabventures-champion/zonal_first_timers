@extends('layouts.app')
@section('title', 'Import First Timers')
@section('page-title', 'Import First Timers')

@section('content')
    <div class="max-w-2xl">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="mb-6">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-semibold text-gray-700">CSV Import</h3>
                    <a href="{{ route('admin.first-timers.template') }}"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 text-[11px] font-bold rounded-lg transition duration-200">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download Template
                    </a>
                </div>
                <p class="text-sm text-gray-500">Upload a CSV file to bulk register first timers. The CSV must include
                    headers matching the database fields.</p>
                <div
                    class="mt-3 bg-gray-50 rounded-lg p-4 border border-gray-100 dark:bg-slate-800/50 dark:border-slate-700">
                    <p class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-2 uppercase tracking-wider">Required
                        columns (12):</p>
                    <div class="flex flex-wrap gap-1.5 mb-3">
                        @foreach(['date_of_visit', 'group_church', 'church', 'full_name', 'primary_contact', 'birthday', 'occupation', 'marital_status', 'church_event', 'residential_address', 'bringer_name', 'bringer_contact'] as $col)
                            <span
                                class="px-2 py-1 bg-white dark:bg-slate-700 text-gray-600 dark:text-gray-400 text-[10px] rounded border border-gray-200 dark:border-slate-600 font-mono">{{ $col }}</span>
                        @endforeach
                    </div>

                    <p class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-2 uppercase tracking-wider">Optional
                        columns (6):</p>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach(['email', 'alternate_contact', 'gender', 'born_again', 'water_baptism', 'prayer_requests'] as $col)
                            <span
                                class="px-2 py-1 bg-white dark:bg-slate-700 text-gray-600 dark:text-gray-400 text-[10px] rounded border border-gray-200 dark:border-slate-600 font-mono">{{ $col }}</span>
                        @endforeach
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.first-timers.import.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="mb-6">
                    <label for="csv_file" class="block text-sm font-medium text-gray-700 mb-1">CSV File <span
                            class="text-red-500">*</span></label>
                    <input type="file" name="csv_file" id="csv_file" accept=".csv" required
                        class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    @error('csv_file') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit"
                        class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">Import</button>
                    <a href="{{ route('admin.first-timers.index') }}"
                        class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection