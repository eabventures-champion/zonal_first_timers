@extends('layouts.app')
@section('title', 'Import First Timers')
@section('page-title', 'Import First Timers')

@section('content')
    <div class="max-w-2xl">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">CSV Import</h3>
                <p class="text-sm text-gray-500">Upload a CSV file to bulk register first timers. The CSV must include
                    headers matching the database fields.</p>
                <div class="mt-3 bg-gray-50 rounded-lg p-3">
                    <p class="text-xs font-medium text-gray-600 mb-1">Required columns:</p>
                    <code
                        class="text-xs text-gray-500">full_name, primary_contact, email, gender, residential_address, date_of_visit</code>
                    <p class="text-xs font-medium text-gray-600 mt-2 mb-1">Optional columns:</p>
                    <code
                        class="text-xs text-gray-500">alternate_contact, date_of_birth, occupation, marital_status, bringer_name, bringer_contact, born_again, water_baptism, church_event, prayer_requests</code>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.first-timers.import.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="mb-5">
                    <label for="church_id" class="block text-sm font-medium text-gray-700 mb-1">Church <span
                            class="text-red-500">*</span></label>
                    <select name="church_id" id="church_id" required
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select Church</option>
                        @foreach($churches as $church)
                            <option value="{{ $church->id }}">{{ $church->name }}</option>
                        @endforeach
                    </select>
                    @error('church_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

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