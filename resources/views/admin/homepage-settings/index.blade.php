@extends('layouts.app')
@section('title', 'Homepage Settings')
@section('page-title', 'Homepage Settings')

@section('content')
    <div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1
                    class="text-2xl font-bold tracking-tight text-gray-900 bg-clip-text text-transparent bg-gradient-to-r from-gray-900 to-gray-600 dark:from-white dark:to-gray-400">
                    Homepage Settings
                </h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Manage the text and images displayed on the public
                    entry pages.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 mb-6 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-900/30 dark:text-green-400 border border-green-200 dark:border-green-800"
                role="alert">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.homepage-settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="space-y-10">
                <!-- App Branding -->
                <div class="bg-white dark:bg-slate-800 shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
                    <div class="px-4 py-6 sm:p-8">
                        <h2 class="text-lg font-semibold leading-7 text-gray-900 dark:text-white mb-6">App Branding</h2>
                        <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                            <div class="sm:col-span-4">
                                <label for="app_name"
                                    class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">App
                                    Name</label>
                                <div class="mt-2">
                                    <input type="text" name="app_name" id="app_name"
                                        value="{{ $settings['app_name']->value ?? '' }}"
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 dark:text-white dark:bg-slate-700 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hero Section -->
                <div class="bg-white dark:bg-slate-800 shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
                    <div class="px-4 py-6 sm:p-8">
                        <h2 class="text-lg font-semibold leading-7 text-gray-900 dark:text-white mb-6">Hero Section</h2>

                        <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

                            <div class="sm:col-span-full">
                                <label for="hero_background_image"
                                    class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Background
                                    Image</label>
                                @if(isset($settings['hero_background_image']))
                                    <img src="{{ $settings['hero_background_image']->value }}" alt="Current Background"
                                        class="mt-2 h-32 w-auto object-cover rounded-md mb-2">
                                @endif
                                <div class="mt-2">
                                    <input type="file" name="hero_background_image" id="hero_background_image"
                                        accept="image/*"
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:text-gray-400 dark:file:bg-slate-700 dark:file:text-gray-300">
                                    <p class="mt-1 text-sm text-gray-500">Leave blank to keep the current image. Recommended
                                        size: 1920x1080.</p>
                                </div>
                            </div>

                            <div class="sm:col-span-4">
                                <label for="hero_badge_text"
                                    class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Badge Text
                                    (Top)</label>
                                <div class="mt-2">
                                    <input type="text" name="hero_badge_text" id="hero_badge_text"
                                        value="{{ $settings['hero_badge_text']->value ?? '' }}"
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 dark:text-white dark:bg-slate-700 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                            </div>

                            <div class="sm:col-span-3">
                                <label for="hero_title_1"
                                    class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Title Part
                                    1</label>
                                <div class="mt-2">
                                    <input type="text" name="hero_title_1" id="hero_title_1"
                                        value="{{ $settings['hero_title_1']->value ?? '' }}"
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 dark:text-white dark:bg-slate-700 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                            </div>

                            <div class="sm:col-span-3">
                                <label for="hero_title_1_highlight"
                                    class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Title Part
                                    1 Highlight (Colored)</label>
                                <div class="mt-2">
                                    <input type="text" name="hero_title_1_highlight" id="hero_title_1_highlight"
                                        value="{{ $settings['hero_title_1_highlight']->value ?? '' }}"
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 dark:text-white dark:bg-slate-700 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                            </div>

                            <div class="sm:col-span-3">
                                <label for="hero_title_2"
                                    class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Title Part
                                    2</label>
                                <div class="mt-2">
                                    <input type="text" name="hero_title_2" id="hero_title_2"
                                        value="{{ $settings['hero_title_2']->value ?? '' }}"
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 dark:text-white dark:bg-slate-700 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                            </div>

                            <div class="sm:col-span-3">
                                <label for="hero_title_2_highlight"
                                    class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Title Part
                                    2 Highlight (Underlined)</label>
                                <div class="mt-2">
                                    <input type="text" name="hero_title_2_highlight" id="hero_title_2_highlight"
                                        value="{{ $settings['hero_title_2_highlight']->value ?? '' }}"
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 dark:text-white dark:bg-slate-700 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                            </div>

                            <div class="sm:col-span-full">
                                <label for="hero_subtitle"
                                    class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Subtitle</label>
                                <div class="mt-2">
                                    <textarea id="hero_subtitle" name="hero_subtitle" rows="3"
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 dark:text-white dark:bg-slate-700 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">{{ $settings['hero_subtitle']->value ?? '' }}</textarea>
                                </div>
                            </div>

                            <div class="sm:col-span-3">
                                <label for="hero_button_primary_text"
                                    class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Primary
                                    Button Text (Login)</label>
                                <div class="mt-2">
                                    <input type="text" name="hero_button_primary_text" id="hero_button_primary_text"
                                        value="{{ $settings['hero_button_primary_text']->value ?? '' }}"
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 dark:text-white dark:bg-slate-700 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                            </div>

                            <div class="sm:col-span-3">
                                <label for="hero_button_secondary_text"
                                    class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Secondary
                                    Button Text (Scroll to Mission)</label>
                                <div class="mt-2">
                                    <input type="text" name="hero_button_secondary_text" id="hero_button_secondary_text"
                                        value="{{ $settings['hero_button_secondary_text']->value ?? '' }}"
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 dark:text-white dark:bg-slate-700 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Mission Section -->
                <div class="bg-white dark:bg-slate-800 shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
                    <div class="px-4 py-6 sm:p-8">
                        <h2 class="text-lg font-semibold leading-7 text-gray-900 dark:text-white mb-6">Mission Section</h2>

                        <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 mb-8">
                            <div class="sm:col-span-3">
                                <label for="mission_heading_1"
                                    class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Heading
                                    Part 1</label>
                                <div class="mt-2">
                                    <input type="text" name="mission_heading_1" id="mission_heading_1"
                                        value="{{ $settings['mission_heading_1']->value ?? '' }}"
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 dark:text-white dark:bg-slate-700 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                            </div>

                            <div class="sm:col-span-3">
                                <label for="mission_heading_highlight"
                                    class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Heading
                                    Highlight</label>
                                <div class="mt-2">
                                    <input type="text" name="mission_heading_highlight" id="mission_heading_highlight"
                                        value="{{ $settings['mission_heading_highlight']->value ?? '' }}"
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 dark:text-white dark:bg-slate-700 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                            </div>

                            <div class="sm:col-span-full">
                                <label for="mission_subheading"
                                    class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Subheading</label>
                                <div class="mt-2">
                                    <textarea id="mission_subheading" name="mission_subheading" rows="2"
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 dark:text-white dark:bg-slate-700 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">{{ $settings['mission_subheading']->value ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>

                        <hr class="dark:border-slate-700 my-6">

                        <!-- Card 1 -->
                        <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 mb-8">
                            <div class="sm:col-span-full">
                                <label for="mission_card_1_title"
                                    class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Card 1
                                    Title</label>
                                <div class="mt-2">
                                    <input type="text" name="mission_card_1_title" id="mission_card_1_title"
                                        value="{{ $settings['mission_card_1_title']->value ?? '' }}"
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 dark:text-white dark:bg-slate-700 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                            </div>
                            <div class="sm:col-span-full">
                                <label for="mission_card_1_desc"
                                    class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Card 1
                                    Description</label>
                                <div class="mt-2">
                                    <textarea id="mission_card_1_desc" name="mission_card_1_desc" rows="2"
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 dark:text-white dark:bg-slate-700 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">{{ $settings['mission_card_1_desc']->value ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Card 2 -->
                        <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 mb-8">
                            <div class="sm:col-span-full">
                                <label for="mission_card_2_title"
                                    class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Card 2
                                    Title</label>
                                <div class="mt-2">
                                    <input type="text" name="mission_card_2_title" id="mission_card_2_title"
                                        value="{{ $settings['mission_card_2_title']->value ?? '' }}"
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 dark:text-white dark:bg-slate-700 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                            </div>
                            <div class="sm:col-span-full">
                                <label for="mission_card_2_desc"
                                    class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Card 2
                                    Description</label>
                                <div class="mt-2">
                                    <textarea id="mission_card_2_desc" name="mission_card_2_desc" rows="2"
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 dark:text-white dark:bg-slate-700 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">{{ $settings['mission_card_2_desc']->value ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Card 3 -->
                        <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                            <div class="sm:col-span-full">
                                <label for="mission_card_3_title"
                                    class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Card 3
                                    Title</label>
                                <div class="mt-2">
                                    <input type="text" name="mission_card_3_title" id="mission_card_3_title"
                                        value="{{ $settings['mission_card_3_title']->value ?? '' }}"
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 dark:text-white dark:bg-slate-700 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                            </div>
                            <div class="sm:col-span-full">
                                <label for="mission_card_3_desc"
                                    class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Card 3
                                    Description</label>
                                <div class="mt-2">
                                    <textarea id="mission_card_3_desc" name="mission_card_3_desc" rows="2"
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 dark:text-white dark:bg-slate-700 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">{{ $settings['mission_card_3_desc']->value ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="flex items-center justify-end gap-x-6">
                    <a href="{{ route('admin.dashboard') }}"
                        class="text-sm font-semibold leading-6 text-gray-900 dark:text-gray-300">Cancel</a>
                    <button type="submit"
                        class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Save
                        Settings</button>
                </div>
            </div>
        </form>
    </div>
@endsection