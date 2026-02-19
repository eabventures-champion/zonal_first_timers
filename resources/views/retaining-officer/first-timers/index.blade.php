@extends('layouts.app')
@section('title', 'My First Timers')
@section('page-title', 'My First Timers')

@section('content')
    <div class="mb-6">
        <p class="text-sm text-gray-500">First-time visitors assigned to your church</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
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
                    @forelse($firstTimers as $ft)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-6 py-3">
                                <div class="font-medium text-gray-900">{{ $ft->full_name }}</div>
                                <div class="text-xs text-gray-400">{{ $ft->email }}</div>
                            </td>
                            <td class="px-6 py-3 text-gray-500">{{ $ft->primary_contact }}</td>
                            <td class="px-6 py-3 text-gray-500">{{ $ft->date_of_visit?->format('M d, Y') }}</td>
                            <td class="px-6 py-3 text-center">
                                @php $sc = ['New' => 'bg-amber-100 text-amber-700', 'In Progress' => 'bg-blue-100 text-blue-700', 'Member' => 'bg-emerald-100 text-emerald-700']; @endphp
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $sc[$ft->status] ?? 'bg-gray-100' }}">{{ $ft->status }}</span>
                            </td>
                            <td class="px-6 py-3 text-right space-x-2">
                                <a href="{{ route('ro.first-timers.show', $ft) }}"
                                    class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400">No first timers assigned to your church.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($firstTimers->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $firstTimers->links() }}
            </div>
        @endif
    </div>
@endsection