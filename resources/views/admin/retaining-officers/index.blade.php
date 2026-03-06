@extends('layouts.app')

@section('title', 'Retaining Officers')
@section('page-title', 'Retaining Officers')

@section('content')
    <div class="space-y-6" x-data="{ 
        search: '',
        shouldShowCategory(categoryName, members) {
            if (!this.search) return true;
            const s = this.search.toLowerCase();
            if (categoryName.toLowerCase().includes(s)) return true;
            return members.some(m => 
                (m.name && m.name.toLowerCase().includes(s)) || 
                (m.email && m.email.toLowerCase().includes(s)) || 
                (m.phone && m.phone.toLowerCase().includes(s)) ||
                (m.church && m.church.name && m.church.name.toLowerCase().includes(s)) ||
                (m.group_name && m.group_name.toLowerCase().includes(s))
            );
        },
        shouldShowOfficer(officer) {
            if (!this.search) return true;
            const s = this.search.toLowerCase();
            return (officer.name && officer.name.toLowerCase().includes(s)) || 
                   (officer.email && officer.email.toLowerCase().includes(s)) || 
                   (officer.phone && officer.phone.toLowerCase().includes(s)) ||
                   (officer.church && officer.church.name && officer.church.name.toLowerCase().includes(s)) ||
                   (officer.group_name && officer.group_name.toLowerCase().includes(s));
        }
    }">
        <!-- Search Header -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800">
            <div class="relative max-w-md">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" 
                    x-model="search"
                    placeholder="Search by name, contact or church..." 
                    class="block w-full pl-10 pr-3 py-2 border border-gray-200 dark:border-slate-700 rounded-xl leading-5 bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-all">
            </div>
        </div>

        @foreach($officers as $categoryName => $members)
            <div
                class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden"
                x-show="shouldShowCategory('{{ addslashes($categoryName) }}', @js($members->map(fn($m) => [
                    'name' => $m->name, 
                    'email' => $m->email, 
                    'phone' => $m->phone, 
                    'church' => ['name' => $m->church ? $m->church->name : ''],
                    'group_name' => $m->church?->group?->name ?? ''
                ])))">
                <div
                    class="px-6 py-4 bg-gray-50/50 dark:bg-slate-800/50 border-b border-gray-100 dark:border-slate-800 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">
                        {{ $categoryName }}
                    </h3>
                    <span
                        class="px-2.5 py-0.5 text-xs font-semibold bg-indigo-100 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 rounded-full">
                        {{ $members->count() }} Officers
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-slate-800">
                                <th
                                    class="py-3 px-6 text-[10px] font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-widest">
                                    Name</th>
                                <th
                                    class="py-3 px-6 text-[10px] font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-widest">
                                    Contact Info</th>
                                <th
                                    class="py-3 px-6 text-[10px] font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-widest">
                                    Church / Group</th>
                                <th
                                    class="py-3 px-6 text-[10px] font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-widest">
                                    Role</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-slate-800/50">
                            @foreach($members as $officer)
                                <tr class="hover:bg-gray-50/30 dark:hover:bg-slate-800/20 transition-colors"
                                    x-show="shouldShowOfficer(@js([
                                        'name' => $officer->name, 
                                        'email' => $officer->email, 
                                        'phone' => $officer->phone, 
                                        'church' => ['name' => $officer->church ? $officer->church->name : ''],
                                        'group_name' => $officer->church?->group?->name ?? ''
                                    ]))">
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-9 h-9 rounded-full bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold text-xs">
                                                {{ substr($officer->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                                    {{ $officer->name }}</div>
                                                <div class="text-[10px] text-gray-400 dark:text-slate-500 mt-0.5">Joined
                                                    {{ $officer->created_at->format('M d, Y') }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-slate-400">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                                {{ $officer->email }}
                                            </div>
                                            @if($officer->phone)
                                                <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-slate-400">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                    </svg>
                                                    {{ $officer->phone }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        @if($officer->church)
                                            <div class="text-sm font-medium text-gray-700 dark:text-slate-300">
                                                {{ $officer->church->name }}</div>
                                            @if($officer->church->group)
                                                <div
                                                    class="text-[10px] text-indigo-500 dark:text-indigo-400 font-semibold uppercase tracking-wider mt-1">
                                                    {{ $officer->church->group->name }}
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-xs text-gray-400 dark:text-slate-600 italic">No church assigned</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 uppercase tracking-widest border border-slate-200 dark:border-slate-700">
                                            Retaining Officer
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach

        @if($officers->isEmpty())
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-12 text-center border border-gray-100 dark:border-slate-800">
                <div class="w-16 h-16 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m9 5.197V21" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">No Retaining Officers found</h3>
                <p class="text-gray-500 dark:text-slate-400 max-w-sm mx-auto">There are currently no users assigned with the
                    Retaining Officer role in the system.</p>
            </div>
        @endif

        <!-- Empty search result message -->
        <template x-if="search && document.querySelectorAll('tbody tr[style*=\'display: none\']').length === document.querySelectorAll('tbody tr').length">
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-12 text-center border border-gray-100 dark:border-slate-800">
                <p class="text-gray-500 dark:text-slate-400">No officers match your search query.</p>
            </div>
        </template>
    </div>
@endsection