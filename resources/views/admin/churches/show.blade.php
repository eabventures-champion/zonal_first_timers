@extends('layouts.app')
@section('title', $church->name)
@section('page-title', $church->name)

@section('content')
    {{-- Church Info --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Church Details</h3>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="text-gray-500">Category</dt>
                    <dd class="font-medium text-gray-900">{{ $church->group->category->name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Group</dt>
                    <dd class="font-medium text-gray-900">{{ $church->group->name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Address</dt>
                    <dd class="font-medium text-gray-900">{{ $church->address ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Retaining Officer</dt>
                    <dd class="font-medium text-gray-900">{{ $church->retainingOfficer->name ?? 'Unassigned' }}</dd>
                </div>
            </dl>
        </div>

        <div class="space-y-4">
            <x-stats-card label="Total First Timers" :value="$stats['total_first_timers']" color="sky" />
            <x-stats-card label="Retention Rate" :value="$stats['retention_rate'] . '%'" color="emerald" />
        </div>
    </div>

    {{-- Status Metrics --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-8">
        <x-stats-card label="New" :value="$stats['new_first_timers']" color="amber" />
        <x-stats-card label="In Progress" :value="$stats['in_progress']" color="violet" />
        <x-stats-card label="Members" :value="$stats['total_members']" color="emerald" />
        <x-stats-card label="Foundation Rate" :value="$stats['foundation_completion_rate'] . '%'" color="teal" />
    </div>

    {{-- First Timers List --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-700">First Timers at this Church</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Name</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Contact</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Visit Date</th>
                        <th class="px-6 py-3 text-center font-medium text-gray-500">Status</th>
                        <th class="px-6 py-3 text-right font-medium text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($church->firstTimers as $ft)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-6 py-3 font-medium text-gray-900">{{ $ft->full_name }}</td>
                            <td class="px-6 py-3 text-gray-500">{{ $ft->primary_contact }}</td>
                            <td class="px-6 py-3 text-gray-500">{{ $ft->date_of_visit?->format('M d, Y') }}</td>
                            <td class="px-6 py-3 text-center">
                                @php
                                    $statusColors = ['New' => 'bg-amber-100 text-amber-700', 'In Progress' => 'bg-blue-100 text-blue-700', 'Member' => 'bg-emerald-100 text-emerald-700'];
                                @endphp
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$ft->status] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ $ft->status }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <a href="{{ route('admin.first-timers.show', $ft) }}"
                                    class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400">No first timers registered yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection