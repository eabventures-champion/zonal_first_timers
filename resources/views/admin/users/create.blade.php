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

                <div x-data="{ 
                        role: '{{ old('role') }}',
                        phone: '{{ old('phone') }}',
                        phoneError: '',
                        isCheckingContact: false,
                        lastCheckedPhone: '',

                        async checkContact() {
                            const phone = this.phone.trim();
                            if (phone.length === 0) {
                                this.phoneError = '';
                                return;
                            }

                            // Length validation
                            if (phone.length !== 10) {
                                this.phoneError = 'Phone number must be exactly 10 digits.';
                                return;
                            }

                            if (phone === this.lastCheckedPhone) return;

                            this.isCheckingContact = true;
                            try {
                                const response = await fetch('{{ route('admin.users.check-contact') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: JSON.stringify({ contact: phone })
                                });
                                const data = await response.json();
                                this.phoneError = data.exists ? data.message : '';
                                this.lastCheckedPhone = phone;
                            } catch (error) {
                                console.error('Error checking contact:', error);
                            } finally {
                                this.isCheckingContact = false;
                            }
                        }
                    }" x-init="$watch('phone', () => checkContact())">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Role <span
                                    class="text-red-500">*</span></label>
                            <select name="role" required x-model="role"
                                class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}">
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone <span
                                    class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="text" name="phone" x-model="phone" required
                                    class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="phoneError ? 'border-red-500 ring-red-500' : ''" placeholder="e.g. 024XXXXXXX">
                                <div x-show="isCheckingContact" class="absolute right-3 top-2.5">
                                    <svg class="animate-spin h-4 w-4 text-indigo-500" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                            <p x-show="phoneError" x-text="phoneError" class="mt-1 text-xs text-red-600"></p>
                            @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5" x-show="role !== 'Retaining Officer'">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password <span
                                    class="text-red-500">*</span></label>
                            <input type="password" name="password" :required="role !== 'Retaining Officer'"
                                class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password <span
                                    class="text-red-500">*</span></label>
                            <input type="password" name="password_confirmation" :required="role !== 'Retaining Officer'"
                                class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div class="mb-5 p-3 bg-indigo-50 rounded-lg border border-indigo-100"
                        x-show="role === 'Retaining Officer'">
                        <div class="flex gap-3">
                            <svg class="w-5 h-5 text-indigo-500 shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-xs text-indigo-700">
                                <strong>Default Password:</strong> For Retaining Officers, the user's <strong>Phone
                                    Number</strong> will be used as their default password. They can change it after logging
                                in.
                            </p>
                        </div>
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
                    <button type="submit" :disabled="phoneError || isCheckingContact"
                        class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition disabled:opacity-50 disabled:cursor-not-allowed">Create
                        User</button>
                    <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection