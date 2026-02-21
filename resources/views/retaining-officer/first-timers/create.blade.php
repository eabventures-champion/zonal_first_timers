@extends('layouts.app')
@section('title', 'Register First Timer')
@section('page-title', 'Register First Timer')

@section('content')
    <div class="max-w-3xl">
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-6">
            <form method="POST" action="{{ route('ro.first-timers.store') }}" x-data="{
                        primaryContact: '{{ old('primary_contact') }}',
                        contactError: '',
                        isValidating: false,

                        async checkContact() {
                            if (this.primaryContact.length < 5) {
                                this.contactError = '';
                                return;
                            }

                            this.isValidating = true;
                            try {
                                const response = await fetch('{{ route('admin.first-timers.check-contact') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: JSON.stringify({ contact: this.primaryContact })
                                });
                                const data = await response.json();
                                this.contactError = data.exists ? data.message : '';
                            } catch (e) {
                                console.error('Validation failed', e);
                            } finally {
                                this.isValidating = false;
                            }
                        }
                    }">
                @csrf

                <h3
                    class="text-sm font-semibold text-gray-700 dark:text-slate-300 mb-4 pb-2 border-b dark:border-slate-800">
                    Personal Information</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Full Name <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="full_name" value="{{ old('full_name') }}" required
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('full_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Primary Contact
                            <span class="text-red-500">*</span></label>
                        <input type="text" name="primary_contact" required x-model="primaryContact"
                            @input.debounce.500ms="checkContact()"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500"
                            :class="contactError ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''">
                        <p x-show="contactError" x-text="contactError" class="mt-1 text-xs text-red-600"
                            style="display: none;"></p>
                        @error('primary_contact') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Alternate
                            Contact</label>
                        <input type="text" name="alternate_contact" value="{{ old('alternate_contact') }}"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Gender <span
                                class="text-red-500">*</span></label>
                        <select name="gender" required
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select</option>
                            <option value="Male" {{ old('gender') === 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                        </select>
                        @error('gender') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Marital
                            Status</label>
                        <select name="marital_status"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select</option>
                            @foreach(['Single', 'Married', 'Divorced', 'Widowed'] as $ms)
                                <option value="{{ $ms }}" {{ old('marital_status') === $ms ? 'selected' : '' }}>{{ $ms }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Residential Address
                        <span class="text-red-500">*</span></label>
                    <textarea name="residential_address" rows="2" required
                        class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('residential_address') }}</textarea>
                    @error('residential_address') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <h3
                    class="text-sm font-semibold text-gray-700 dark:text-slate-300 mb-4 pb-2 border-b dark:border-slate-800">
                    Visit Details</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Date of Visit <span
                                class="text-red-500">*</span></label>
                        <input type="date" name="date_of_visit" value="{{ old('date_of_visit', date('Y-m-d')) }}" required
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('date_of_visit') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Church Event</label>
                        <input type="text" name="church_event" value="{{ old('church_event') }}"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="e.g., Sunday Service">
                    </div>
                </div>

                <h3
                    class="text-sm font-semibold text-gray-700 dark:text-slate-300 mb-4 pb-2 border-b dark:border-slate-800">
                    Extra Details</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="born_again" value="1" {{ old('born_again') ? 'checked' : '' }}
                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm text-gray-700 dark:text-slate-400">Born Again</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="water_baptism" value="1" {{ old('water_baptism') ? 'checked' : '' }}
                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm text-gray-700 dark:text-slate-400">Water Baptism</span>
                    </label>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Prayer Requests</label>
                    <textarea name="prayer_requests" rows="3"
                        class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('prayer_requests') }}</textarea>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit"
                        class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">Register
                        First Timer</button>
                    <a href="{{ route('ro.first-timers.index') }}"
                        class="text-sm text-gray-500 hover:text-gray-700 dark:text-slate-500 dark:hover:text-slate-300">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection