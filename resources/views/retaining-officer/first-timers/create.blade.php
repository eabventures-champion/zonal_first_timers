@extends('layouts.app')
@section('title', 'Register First Timer')
@section('page-title', 'Register First Timer')

@section('content')
    <div class="max-w-3xl">
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-6">
            <form method="POST" action="{{ route('ro.first-timers.store') }}"
                @submit.prevent="if(contactError || alternateContactError || (!selectedBringerId && bringerContactError) || isValidating) { return; } $el.submit();"
                x-data="{
                                                                        primaryContact: '{{ old('primary_contact') }}',
                                                                        contactError: '',
                                                                        alternateContact: '{{ old('alternate_contact') }}',
                                                                        alternateContactError: '',
                                                                        isValidating: false,
                                                                        bringers: [],
                                                                        selectedBringerId: '',
                                                                        bringerName: '{{ old('bringer_name') }}',
                                                                        bringerContact: '{{ old('bringer_contact') }}',
                                                                        bringerContactError: '',
                                                                        fullName: '{{ old('full_name') }}',
                                                                        bringerConfirmed: false,
                                                                    bringerCanConfirm: false,
                                                                    bringerExistsButCantAdd: false,
                                                                    bringerSourceType: '',
                                                                    bringerFoundName: '',
                                                                    bringerChurchName: '',
                                                                    churchEvent: '{{ old('church_event', 'Service service') }}',

                                                                        canSubmit() {
                                                                            const isCommonValid = this.fullName && this.fullName.trim() && this.primaryContact && this.primaryContact.trim() && !this.contactError && !this.alternateContactError && !this.isValidating;
                                                                            const isBringerValid = this.selectedBringerId || (this.bringerConfirmed && this.bringerName && this.bringerName.trim() && this.bringerContact && this.bringerContact.trim() && !this.bringerContactError);
                                                                            return isCommonValid && isBringerValid;
                                                                        },

                                                                        async init() {
                                                                            if ({{ auth()->user()->church_id ? 'true' : 'false' }}) {
                                                                                this.loadBringers();
                                                                            }
                                                                        },

                                                                        async loadBringers() {
                                                                            try {
                                                                                const response = await fetch('/admin/bringers/church/{{ auth()->user()->church_id }}');
                                                                                this.bringers = await response.json();
                                                                            } catch (e) {
                                                                                console.error('Failed to load bringers', e);
                                                                            }
                                                                        },

                                                                        async checkContact() {
                                                                            const phone = this.primaryContact.trim();
                                                                            if (phone.length === 0) {
                                                                                this.contactError = '';
                                                                                return;
                                                                            }

                                                                            // Length validation
                                                                            if (phone.length !== 10) {
                                                                                this.contactError = 'Phone number must be exactly 10 digits.';
                                                                                return;
                                                                            }

                                                                            this.contactError = '';
                                                                            this.isValidating = true;
                                                                            try {
                                                                                const response = await fetch('{{ route('admin.first-timers.check-contact') }}', {
                                                                                    method: 'POST',
                                                                                    headers: {
                                                                                        'Content-Type': 'application/json',
                                                                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                                                    },
                                                                                    body: JSON.stringify({
                                                                                        contact: phone,
                                                                                        type: 'first_timer'
                                                                                    })
                                                                                });
                                                                                const data = await response.json();
                                                                                this.contactError = data.exists ? data.message : '';
                                                                            } catch (e) {
                                                                                console.error('Validation failed', e);
                                                                            } finally {
                                                                                this.isValidating = false;
                                                                            }
                                                                        },

                                                                        checkAlternateContact() {
                                                                            const phone = this.alternateContact.trim();
                                                                            if (phone.length === 0) {
                                                                                this.alternateContactError = '';
                                                                                return;
                                                                            }
                                                                            if (phone.length !== 10) {
                                                                                this.alternateContactError = 'Phone number must be exactly 10 digits.';
                                                                                return;
                                                                            }
                                                                            this.alternateContactError = '';
                                                                        },

                                                                        async checkBringerContact() {
                                                                            const phone = this.bringerContact.trim();
                                                                            if (phone.length === 0) {
                                                                                this.bringerContactError = '';
                                                                                return;
                                                                            }
                                                                            if (phone.length !== 10) {
                                                                                this.bringerContactError = 'Phone number must be exactly 10 digits.';
                                                                                return;
                                                                            }
                                                                            this.bringerContactError = '';
                                                                            this.bringerCanConfirm = false;
                                                                            try {
                                                                                const response = await fetch('{{ route('admin.bringers.check-contact') }}', {
                                                                                    method: 'POST',
                                                                                    headers: {
                                                                                        'Content-Type': 'application/json',
                                                                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                                                    },
                                                                                    body: JSON.stringify({
                                                                                        contact: phone,
                                                                                        church_id: '{{ auth()->user()->church_id }}'
                                                                                    })
                                                                                });
                                                                                const data = await response.json();
                                                                                this.bringerContactError = data.exists ? data.message : '';
                                                                    if (data.exists) {
                                                                        if (data.can_confirm) {
                                                                            this.bringerCanConfirm = true;
                                                                            this.bringerExistsButCantAdd = false;
                                                                            // Auto-sync name if confirmable
                                                                            this.bringerName = data.name;
                                                                        } else {
                                                                            this.bringerCanConfirm = false;
                                                                            this.bringerExistsButCantAdd = true;
                                                                        }
                                                                        this.bringerSourceType = data.type;
                                                                        this.bringerFoundName = data.name;
                                                                        this.bringerChurchName = data.church_name;
                                                                    } else {
                                                                        this.bringerCanConfirm = false;
                                                                        this.bringerExistsButCantAdd = false;
                                                                    }
                                                                } catch (e) {
                                                                    console.error('Validation failed', e);
                                                                }
                                                            },
                                                                        confirmExistingAsBringer() {
                                                                            this.bringerName = this.bringerFoundName;
                                                                            this.bringerConfirmed = true;
                                                                            this.bringerCanConfirm = false;
                                                                            this.bringerContactError = '';
                                                                        }
                                                                    }">
                @csrf

                <div class="flex items-center gap-2 mb-4 pb-2 border-b dark:border-slate-800">
                    <div
                        class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-800 dark:text-slate-200">Personal Information</h3>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Full Name <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="full_name" x-model="fullName" required
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
                            @input.debounce.500ms="checkContact()" minlength="10" maxlength="20"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500"
                            :class="contactError ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''">
                        <p x-show="contactError" x-text="contactError" class="mt-1 text-xs text-red-600"
                            style="display: none;"></p>
                        @error('primary_contact') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Alternate
                            Contact</label>
                        <input type="text" name="alternate_contact" x-model="alternateContact"
                            @input.debounce.500ms="checkAlternateContact()" minlength="10" maxlength="20"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500"
                            :class="alternateContactError ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''">
                        <p x-show="alternateContactError" x-text="alternateContactError" class="mt-1 text-xs text-red-600"
                            style="display: none;"></p>
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
                        <select name="marital_status" required
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select</option>
                            @foreach(['Single', 'Married', 'Divorced', 'Widowed'] as $ms)
                                <option value="{{ $ms }}" {{ old('marital_status') === $ms ? 'selected' : '' }}>{{ $ms }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Date of
                            Birth</label>
                        <div class="grid grid-cols-2 gap-2">
                            <select name="dob_day"
                                class="rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Day</option>
                                @foreach(range(1, 31) as $day)
                                    <option value="{{ sprintf('%02d', $day) }}" {{ old('dob_day') == sprintf('%02d', $day) ? 'selected' : '' }}>{{ $day }}</option>
                                @endforeach
                            </select>
                            <select name="dob_month"
                                class="rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Month</option>
                                @foreach(range(1, 12) as $month)
                                    <option value="{{ sprintf('%02d', $month) }}" {{ old('dob_month') == sprintf('%02d', $month) ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $month, 1)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Occupation</label>
                        <input type="text" name="occupation" value="{{ old('occupation') }}"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Residential Address
                        <span class="text-red-500">*</span></label>
                    <textarea name="residential_address" rows="2" required
                        class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('residential_address') }}</textarea>
                    @error('residential_address') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-2 mb-4 pb-2 border-b dark:border-slate-800">
                    <div
                        class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center text-amber-600 dark:text-amber-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-800 dark:text-slate-200">Invited By</h3>
                </div>
                <div class="space-y-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Select
                            Person</label>
                        <select name="bringer_id" x-model="selectedBringerId"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">New Person / Not Listed (Use fields below)</option>
                            <template x-for="person in bringers" :key="person.id">
                                <option :value="person.id"
                                    x-text="`${person.name} (${person.contact})${person.is_ro ? ' (RO)' : ''}`"></option>
                            </template>
                        </select>
                        <p class="mt-1 text-[10px] text-gray-400 dark:text-slate-500">If the person is already in the
                            system, select them here. Otherwise, fill the details below.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" x-show="!selectedBringerId">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Bringer
                                Name</label>
                            <input type="text" name="bringer_name" x-model="bringerName"
                                class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Bringer
                                Contact</label>
                            <input type="text" name="bringer_contact" x-model="bringerContact"
                                @input.debounce.500ms="checkBringerContact()" minlength="10" maxlength="20"
                                class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                :class="bringerContactError ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''">
                            <div class="mt-1 flex items-center gap-2">
                                <p x-show="bringerContactError" x-text="bringerContactError" style="display: none;"
                                    class="text-[10px] text-red-600 dark:text-red-400 font-medium">
                                </p>
                                <span x-show="bringerExistsButCantAdd" style="display: none;"
                                    class="px-2 py-0.5 bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400 rounded text-[9px] font-bold uppercase">can't
                                    add</span>
                            </div>
                            <div x-show="!selectedBringerId && bringerName.trim() && bringerContact.trim() && (!bringerContactError || bringerCanConfirm)"
                                style="display: none;" class="mt-2">
                                <button type="button" @click="confirmExistingAsBringer()"
                                    x-show="bringerCanConfirm && !bringerConfirmed"
                                    class="text-[10px] px-2 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition font-bold uppercase shadow-sm">
                                    ADD AS BRINGER
                                </button>
                                <button type="button" @click="bringerConfirmed = true"
                                    x-show="!bringerConfirmed && !bringerCanConfirm"
                                    class="text-[10px] px-2 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition font-bold uppercase shadow-sm">
                                    ADD AS BRINGER
                                </button>
                                <p x-show="bringerConfirmed" class="text-[10px] text-green-600 flex items-center gap-1">
                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Bringer confirmed and ready.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div x-show="!selectedBringerId"
                        class="p-2 bg-amber-50 dark:bg-amber-500/10 rounded-lg border border-amber-100 dark:border-amber-500/20">
                        <p class="text-[10px] text-amber-700 dark:text-amber-400 italic">If left empty, this first timer
                            will be linked to your details as the Retaining Officer.</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 mb-4 pb-2 border-b dark:border-slate-800">
                    <div
                        class="w-8 h-8 rounded-lg bg-teal-50 dark:bg-teal-500/10 flex items-center justify-center text-teal-600 dark:text-teal-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-800 dark:text-slate-200">Visit Details</h3>
                </div>
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
                        <input type="text" name="church_event" x-model="churchEvent"
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
                            class="rounded border-gray-300 dark:border-slate-700 dark:bg-slate-800 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm text-gray-700 dark:text-slate-300">Born Again</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="water_baptism" value="1" {{ old('water_baptism') ? 'checked' : '' }}
                            class="rounded border-gray-300 dark:border-slate-700 dark:bg-slate-800 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm text-gray-700 dark:text-slate-300">Water Baptism</span>
                    </label>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Prayer Requests</label>
                    <textarea name="prayer_requests" rows="3"
                        class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('prayer_requests') }}</textarea>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" :disabled="!canSubmit()"
                        class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-show="!isValidating">Register First Timer</span>
                        <span x-show="isValidating">Validating...</span>
                    </button>
                    <a href="{{ route('ro.first-timers.index') }}"
                        class="text-sm text-gray-500 hover:text-gray-700 dark:text-slate-500 dark:hover:text-slate-300">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection