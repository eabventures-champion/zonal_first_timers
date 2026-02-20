@extends('layouts.app')
@section('title', 'Edit Member')
@section('page-title', 'Edit: ' . $member->full_name)

@section('content')
    <div class="max-w-3xl">
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-6">
            <form method="POST" action="{{ route('admin.members.update', $member) }}" x-data="{
                        categories: {{ Js::from($categories) }},
                        selectedCategory: '{{ $member->church->group->category->id ?? '' }}',
                        selectedGroup: '{{ $member->church->group->id ?? '' }}',
                        selectedChurch: '{{ $member->church_id }}',
                        selectedOfficer: '{{ $member->retaining_officer_id }}',
                        groups: [],
                        churches: [],

                        init() {
                            this.updateGroups(true);
                        },

                        updateGroups(initial = false) {
                            const category = this.categories.find(c => c.id == this.selectedCategory);
                            this.groups = category ? category.groups : [];
                            if (!initial) {
                                this.selectedGroup = '';
                                this.churches = [];
                                this.selectedChurch = '';
                                this.selectedOfficer = '';
                            } else {
                                this.updateChurches(true);
                            }
                        },

                        updateChurches(initial = false) {
                            const group = this.groups.find(g => g.id == this.selectedGroup);
                            this.churches = group ? group.churches : [];
                            if (!initial) {
                                this.selectedChurch = '';
                                this.selectedOfficer = '';
                            }
                        },

                        updateOfficer() {
                            const church = this.churches.find(c => c.id == this.selectedChurch);
                            if (church && church.retaining_officer_id) {
                                this.selectedOfficer = church.retaining_officer_id;
                            } else {
                                this.selectedOfficer = '';
                            }
                        }
                    }">
                @csrf @method('PUT')

                <h3
                    class="text-sm font-semibold text-gray-700 dark:text-slate-300 mb-4 pb-2 border-b border-gray-100 dark:border-slate-800">
                    Personal Information</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Full Name <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="full_name" value="{{ old('full_name', $member->full_name) }}" required
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('full_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', $member->email) }}"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Primary Contact
                            <span class="text-red-500">*</span></label>
                        <input type="text" name="primary_contact"
                            value="{{ old('primary_contact', $member->primary_contact) }}" required
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('primary_contact') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Alternate
                            Contact</label>
                        <input type="text" name="alternate_contact"
                            value="{{ old('alternate_contact', $member->alternate_contact) }}"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Gender <span
                                class="text-red-500">*</span></label>
                        <select name="gender" required
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="Male" {{ old('gender', $member->gender) === 'Male' ? 'selected' : '' }}>Male
                            </option>
                            <option value="Female" {{ old('gender', $member->gender) === 'Female' ? 'selected' : '' }}>Female
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Date of Birth (Day & Month)</label>
                        <div class="grid grid-cols-2 gap-2">
                            <select name="dob_day"
                                class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Day</option>
                                @for ($i = 1; $i <= 31; $i++)
                                    @php $dayVal = str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                                    <option value="{{ $dayVal }}"
                                        {{ old('dob_day', $member->date_of_birth?->format('d')) == $dayVal ? 'selected' : '' }}>
                                        {{ $i }}
                                    </option>
                                @endfor
                            </select>
                            <select name="dob_month"
                                class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Month</option>
                                @foreach (['01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr', '05' => 'May', '06' => 'Jun', '07' => 'Jul', '08' => 'Aug', '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dec'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('dob_month', $member->date_of_birth?->format('m')) == $val ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Marital
                            Status</label>
                        <select name="marital_status"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select</option>
                            @foreach(['Single', 'Married', 'Divorced', 'Widowed'] as $ms)
                                <option value="{{ $ms }}" {{ old('marital_status', $member->marital_status) === $ms ? 'selected' : '' }}>{{ $ms }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Occupation</label>
                        <input type="text" name="occupation" value="{{ old('occupation', $member->occupation) }}"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Residential
                        Address</label>
                    <textarea name="residential_address" rows="2"
                        class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('residential_address', $member->residential_address) }}</textarea>
                </div>

                <h3
                    class="text-sm font-semibold text-gray-700 dark:text-slate-300 mb-4 pb-2 border-b border-gray-100 dark:border-slate-800">
                    Credentials</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="born_again" value="1" {{ old('born_again', $member->born_again) ? 'checked' : '' }}
                            class="rounded border-gray-300 dark:border-slate-700 dark:bg-slate-800 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm text-gray-700 dark:text-slate-300">Born Again</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="water_baptism" value="1" {{ old('water_baptism', $member->water_baptism) ? 'checked' : '' }}
                            class="rounded border-gray-300 dark:border-slate-700 dark:bg-slate-800 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm text-gray-700 dark:text-slate-300">Water Baptism</span>
                    </label>
                </div>

                <h3
                    class="text-sm font-semibold text-gray-700 dark:text-slate-300 mb-4 pb-2 border-b border-gray-100 dark:border-slate-800">
                    Church Assignment</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Category <span
                                class="text-red-500">*</span></label>
                        <select x-model="selectedCategory" @change="updateGroups()"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select Category</option>
                            <template x-for="category in categories" :key="category.id">
                                <option :value="category.id" x-text="category.name"
                                    :selected="category.id == selectedCategory"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Group <span
                                class="text-red-500">*</span></label>
                        <select x-model="selectedGroup" @change="updateChurches()" :disabled="!selectedCategory"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:opacity-50">
                            <option value="">Select Group</option>
                            <template x-for="group in groups" :key="group.id">
                                <option :value="group.id" x-text="group.name" :selected="group.id == selectedGroup">
                                </option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Church <span
                                class="text-red-500">*</span></label>
                        <select name="church_id" x-model="selectedChurch" required :disabled="!selectedGroup"
                            @change="updateOfficer()"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:opacity-50">
                            <option value="">Select Church</option>
                            <template x-for="church in churches" :key="church.id">
                                <option :value="church.id" x-text="church.name" :selected="church.id == selectedChurch">
                                </option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Retaining
                            Officer</label>
                        <select name="retaining_officer_id" x-model="selectedOfficer" :disabled="!selectedChurch"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:opacity-50">
                            <option value="">Auto-assign</option>
                            @foreach($officers as $officer)
                                <option value="{{ $officer->id }}" {{ old('retaining_officer_id', $member->retaining_officer_id) == $officer->id ? 'selected' : '' }}>{{ $officer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit"
                        class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">Update
                        Member</button>
                    <a href="{{ route('admin.members.index') }}"
                        class="text-sm text-gray-500 hover:text-gray-700 dark:text-slate-400 dark:hover:text-slate-300">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection