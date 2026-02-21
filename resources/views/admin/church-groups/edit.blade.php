@extends('layouts.app')
@section('title', 'Edit Church Group')
@section('page-title', 'Edit Church Group')

@section('content')
    <div class="max-w-2xl">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form method="POST" action="{{ route('admin.church-groups.update', $churchGroup) }}">
                @csrf @method('PUT')

                <div class="mb-5">
                    <label for="church_category_id" class="block text-sm font-medium text-gray-700 mb-1">Category <span
                            class="text-red-500">*</span></label>
                    <select name="church_category_id" id="church_category_id" required
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('church_category_id', $churchGroup->church_category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('church_category_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="mb-5">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Group Name <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $churchGroup->name) }}" required
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                    <div>
                        <label for="pastor_name" class="block text-sm font-medium text-gray-700 mb-1">Name of Pastor</label>
                        <input type="text" name="pastor_name" id="pastor_name"
                            value="{{ old('pastor_name', $churchGroup->pastor_name) }}"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        @error('pastor_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="pastor_contact" class="block text-sm font-medium text-gray-700 mb-1">Contact of
                            Pastor</label>
                        <input type="text" name="pastor_contact" id="pastor_contact"
                            value="{{ old('pastor_contact', $churchGroup->pastor_contact) }}"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        @error('pastor_contact') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit"
                        class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">Update
                        Group</button>
                    <a href="{{ route('admin.church-groups.index') }}"
                        class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection