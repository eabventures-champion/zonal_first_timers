@extends('layouts.app')
@section('title', 'Create User')
@section('page-title', 'Create User')
@section('back-link', route('admin.users.index'))

@section('content')
    <div class="max-w-2xl">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password <span
                                class="text-red-500">*</span></label>
                        <input type="password" name="password" value="password" required
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password <span
                                class="text-red-500">*</span></label>
                        <input type="password" name="password_confirmation" value="password" required
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Role <span
                                class="text-red-500">*</span></label>
                        <select name="role" required
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ old('role') === $role->name ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('role') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                <div x-data="{ 
                                categories: {{ $categories->toJson() }},
                                selectedCategory: '{{ old('category_id') }}',
                                selectedGroup: '{{ old('group_id') }}',
                                selectedChurch: '{{ old('church_id') }}',
                                get groups() {
                                    if (!this.selectedCategory) return [];
                                    const cat = this.categories.find(c => c.id == this.selectedCategory);
                                    return cat ? cat.groups : [];
                                },
                                get churches() {
                                    if (!this.selectedGroup) return [];
                                    const group = this.groups.find(g => g.id == this.selectedGroup);
                                    return group ? group.churches : [];
                                }
                            }" class="space-y-4 mb-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Church Category</label>
                            <select name="category_id" x-model="selectedCategory"
                                @change="selectedGroup = ''; selectedChurch = ''"
                                class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select Category</option>
                                <template x-for="category in categories" :key="category.id">
                                    <option :value="category.id" x-text="category.name"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Church Group</label>
                            <select name="group_id" x-model="selectedGroup" @change="selectedChurch = ''"
                                :disabled="!selectedCategory"
                                class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-50 disabled:text-gray-400">
                                <option value="">Select Group</option>
                                <template x-for="group in groups" :key="group.id">
                                    <option :value="group.id" x-text="group.name"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Assign to Church</label>
                        <select name="church_id" x-model="selectedChurch" :disabled="!selectedGroup"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-50 disabled:text-gray-400">
                            <option value="">None</option>
                            <template x-for="church in churches" :key="church.id">
                                <option :value="church.id" x-text="church.name"></option>
                            </template>
                        </select>
                        <p class="mt-1 text-xs text-gray-400">Required for Retaining Officers</p>
                        @error('church_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit"
                        class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">Create
                        User</button>
                    <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection