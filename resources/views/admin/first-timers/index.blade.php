@extends('layouts.app')
@section('title', 'First Timers')
@section('page-title', 'First Timers')

@section('content')
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <p class="text-sm text-gray-500">Manage all first-time visitors</p>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.first-timers.import') }}"
                class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-medium rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                Import CSV
            </a>
            <a href="{{ route('admin.first-timers.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add First Timer
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
        <form method="GET" action="{{ route('admin.first-timers.index') }}"
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                placeholder="Search name, email, phone..."
                class="rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">

            <select name="church_id"
                class="rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">All Churches</option>
                @foreach($churches as $church)
                    <option value="{{ $church->id }}" {{ ($filters['church_id'] ?? '') == $church->id ? 'selected' : '' }}>
                        {{ $church->name }}</option>
                @endforeach
            </select>

            <select name="status" class="rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">All Statuses</option>
                @foreach(['New', 'In Progress', 'Member'] as $s)
                    <option value="{{ $s }}" {{ ($filters['status'] ?? '') === $s ? 'selected' : '' }}>{{ $s }}</option>
                @endforeach
            </select>

            <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" placeholder="From"
                class="rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">

            <div class="flex gap-2">
                <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" placeholder="To"
                    class="flex-1 rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <button type="submit"
                    class="px-4 py-2 bg-gray-800 text-white text-sm rounded-lg hover:bg-gray-900 transition">Filter</button>
            </div>
        </form>
    </div>

    {{-- Import Errors --}}
    @if(session('import_errors'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <p class="text-sm font-medium text-red-800 mb-2">Import Errors:</p>
            <ul class="text-xs text-red-700 list-disc list-inside">
                @foreach(session('import_errors') as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Name</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Contact</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Church</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Visit Date</th>
                        <th class="px-6 py-3 text-center font-medium text-gray-500">Gender</th>
                        <th class="px-6 py-3 text-center font-medium text-gray-500">Status</th>
                        <th class="px-6 py-3 text-right font-medium text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($firstTimers as $ft)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-6 py-3">
                                <div class="font-medium text-gray-900">{{ $ft->full_name }}</div>
                                <div class="text-xs text-gray-400">{{ $ft->email }}</div>
                            </td>
                            <td class="px-6 py-3 text-gray-500">{{ $ft->primary_contact }}</td>
                            <td class="px-6 py-3 text-gray-500">{{ $ft->church->name ?? '—' }}</td>
                            <td class="px-6 py-3 text-gray-500">{{ $ft->date_of_visit?->format('M d, Y') }}</td>
                            <td class="px-6 py-3 text-center text-gray-500">{{ $ft->gender }}</td>
                            <td class="px-6 py-3 text-center">
                                @php
                                    $sc = ['New' => 'bg-amber-100 text-amber-700', 'In Progress' => 'bg-blue-100 text-blue-700', 'Member' => 'bg-emerald-100 text-emerald-700'];
                                @endphp
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $sc[$ft->status] ?? 'bg-gray-100' }}">{{ $ft->status }}</span>
                            </td>
                            <td class="px-6 py-3 text-right space-x-2">
                                <a href="{{ route('admin.first-timers.show', $ft) }}"
                                    class="text-sky-600 hover:text-sky-800 text-xs font-medium">View</a>
                                @if($ft->status !== 'Member')
                                    <a href="{{ route('admin.first-timers.edit', $ft) }}"
                                        class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">Edit</a>
                                @endif
                                <form method="POST" action="{{ route('admin.first-timers.destroy', $ft) }}" class="inline"
                                    onsubmit="return confirm('Delete this record?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="text-red-500 hover:text-red-700 text-xs font-medium">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-400">No first timers found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($firstTimers->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $firstTimers->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection