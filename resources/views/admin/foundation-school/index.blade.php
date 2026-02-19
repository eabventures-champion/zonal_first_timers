@extends('layouts.app')
@section('title', 'Foundation School')
@section('page-title', 'Foundation School')

@section('content')
    <div class="mb-6">
        <p class="text-sm text-gray-500">Foundation School classes and progression tracking</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
        @foreach($classes as $class)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3 mb-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center text-white font-bold text-sm shadow-lg">
                        {{ $class->class_number }}
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">{{ $class->name }}</h3>
                    </div>
                </div>
                @if($class->description)
                    <p class="text-sm text-gray-500">{{ $class->description }}</p>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Search First Timers to view their progress --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">View First Timer Progress</h3>
        <p class="text-sm text-gray-500 mb-4">Select a first timer from the list below to view or update their foundation
            school progress.</p>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left font-medium text-gray-500">Name</th>
                            <th class="px-6 py-3 text-left font-medium text-gray-500">Church</th>
                            <th class="px-6 py-3 text-center font-medium text-gray-500">Status</th>
                            <th class="px-6 py-3 text-right font-medium text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @php
                            $firstTimers = \App\Models\FirstTimer::whereIn('status', ['New', 'In Progress'])->with('church')->latest()->limit(50)->get();
                        @endphp
                        @forelse($firstTimers as $ft)
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-6 py-3 font-medium text-gray-900">{{ $ft->full_name }}</td>
                                <td class="px-6 py-3 text-gray-500">{{ $ft->church->name ?? '—' }}</td>
                                <td class="px-6 py-3 text-center">
                                    @php $sc = ['New' => 'bg-amber-100 text-amber-700', 'In Progress' => 'bg-blue-100 text-blue-700']; @endphp
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $sc[$ft->status] ?? 'bg-gray-100' }}">{{ $ft->status }}</span>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <a href="{{ route('admin.foundation-school.show', $ft) }}"
                                        class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">View Progress</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-400">No first timers in progress.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection