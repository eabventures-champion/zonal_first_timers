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

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                    <div>
                        <label for="group_id" class="block text-sm font-medium text-gray-700 mb-1">Group</label>
                        <select id="group_id"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                            onchange="filterChurches(this.value)">
                            <option value="">All Groups</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="church_id" class="block text-sm font-medium text-gray-700 mb-1">Church <span
                                class="text-red-500">*</span></label>
                        <select name="church_id" id="church_id" required
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select Church</option>
                            @foreach($churches as $church)
                                <option value="{{ $church->id }}" data-group-id="{{ $church->church_group_id }}">
                                    {{ $church->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('church_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <script>
                    function filterChurches(groupId) {
                        const churchSelect = document.getElementById('church_id');
                        const options = churchSelect.querySelectorAll('option');

                        churchSelect.value = ''; // Reset selection

                        options.forEach(option => {
                            if (option.value === '') return; // Skip "Select Church"

                            const optionGroupId = option.getAttribute('data-group-id');
                            if (!groupId || optionGroupId === groupId) {
                                option.style.display = '';
                                option.disabled = false;
                            } else {
                                option.style.display = 'none';
                                option.disabled = true;
                            }
                        });
                    }
                </script>

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