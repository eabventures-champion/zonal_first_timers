@extends('layouts.app')
@section('title', 'Edit Church Category')
@section('page-title', 'Edit Church Category')

@section('content')
    <div class="max-w-2xl">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form method="POST" action="{{ route('admin.church-categories.update', $churchCategory) }}">
                @csrf @method('PUT')

                <div class="mb-5">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Category Name <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $churchCategory->name) }}" required
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                    <div>
                        <label for="zonal_pastor_name" class="block text-sm font-medium text-gray-700 mb-1">Name of Zonal
                            Pastor</label>
                        <input type="text" name="zonal_pastor_name" id="zonal_pastor_name"
                            value="{{ old('zonal_pastor_name', $churchCategory->zonal_pastor_name) }}"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        @error('zonal_pastor_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="zonal_pastor_contact" class="block text-sm font-medium text-gray-700 mb-1">Contact of
                            Zonal Pastor</label>
                        <input type="text" name="zonal_pastor_contact" id="zonal_pastor_contact"
                            value="{{ old('zonal_pastor_contact', $churchCategory->zonal_pastor_contact) }}"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        @error('zonal_pastor_contact') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit"
                        class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                        Update Category
                    </button>
                    <a href="{{ route('admin.church-categories.index') }}"
                        class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection