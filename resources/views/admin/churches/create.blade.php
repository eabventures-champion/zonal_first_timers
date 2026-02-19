@extends('layouts.app')
@section('title', 'Create Church')
@section('page-title', 'Create Church')

@section('content')
    <div class="max-w-2xl">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form method="POST" action="{{ route('admin.churches.store') }}">
                @csrf

                <div class="mb-5">
                    <label for="church_group_id" class="block text-sm font-medium text-gray-700 mb-1">Group <span
                            class="text-red-500">*</span></label>
                    <select name="church_group_id" id="church_group_id" required
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">Select a group</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" {{ old('church_group_id') == $group->id ? 'selected' : '' }}>
                                {{ $group->category->name ?? '' }} → {{ $group->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('church_group_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="mb-5">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Church Name <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="mb-5">
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <textarea name="address" id="address" rows="2"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('address') }}</textarea>
                </div>

                <div class="mb-6">
                    <label for="retaining_officer_id" class="block text-sm font-medium text-gray-700 mb-1">Retaining
                        Officer</label>
                    <select name="retaining_officer_id" id="retaining_officer_id"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">None</option>
                        @foreach($officers as $officer)
                            <option value="{{ $officer->id }}" {{ old('retaining_officer_id') == $officer->id ? 'selected' : '' }}>{{ $officer->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit"
                        class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">Create
                        Church</button>
                    <a href="{{ route('admin.churches.index') }}"
                        class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection