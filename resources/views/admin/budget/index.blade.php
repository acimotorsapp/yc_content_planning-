<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-lg sm:text-2xl text-gray-900 leading-tight">
            Budget Provision
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto pb-12">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">Budget Provision</h1>
                <p class="text-gray-500 text-sm mt-1 font-medium">Every content item with its production and boosting budget. Sort by total amount.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.budget.index', ['sort' => 'low']) }}"
                   class="inline-flex items-center px-4 py-2.5 rounded-xl text-sm font-bold border transition-colors {{ ($sort ?? 'high') === 'low' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50' }}">
                    Low to High
                </a>
                <a href="{{ route('admin.budget.index', ['sort' => 'high']) }}"
                   class="inline-flex items-center px-4 py-2.5 rounded-xl text-sm font-bold border transition-colors {{ ($sort ?? 'high') === 'high' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50' }}">
                    High to Low
                </a>
            </div>
        </div>

        @php
            $grandTotal = $events->sum(fn ($event) => $event->budgetAmount());
            $financialTotal = $events->sum(fn ($event) => $event->budgetAmount('financial'));
            $boostTotal = $events->sum(fn ($event) => $event->budgetAmount('boosting'));
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mb-6">
            <div class="bg-white rounded-2xl p-4 border border-gray-200 shadow-sm">
                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Total Budget</p>
                <h3 class="text-2xl font-black text-gray-900 mt-1">৳ {{ number_format($grandTotal, 0) }}</h3>
            </div>
            <div class="bg-white rounded-2xl p-4 border border-gray-200 shadow-sm">
                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Financial / Production</p>
                <h3 class="text-2xl font-black text-gray-900 mt-1">৳ {{ number_format($financialTotal, 0) }}</h3>
            </div>
            <div class="bg-white rounded-2xl p-4 border border-gray-200 shadow-sm">
                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Boosting</p>
                <h3 class="text-2xl font-black text-teal-600 mt-1">$ {{ number_format($boostTotal, 0) }}</h3>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl sm:rounded-3xl shadow-sm overflow-hidden">
            <div class="px-4 sm:px-8 py-4 sm:py-5 border-b border-gray-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-base sm:text-lg font-bold text-gray-900 tracking-tight">All Content</h3>
                <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-[10px] sm:text-xs font-bold">{{ $events->count() }} items</span>
            </div>

            <div class="md:hidden divide-y divide-gray-100">
                @forelse($events as $event)
                    <div class="p-4">
                        <div class="text-sm font-bold text-gray-900">
                            <x-editable-title :event="$event" />
                        </div>
                        <div class="text-xs text-gray-500 mt-1">{{ $event->event_date->format('M d, Y') }} · {{ str_replace('_', ' ', $event->team_type) }}</div>
                        <div class="flex flex-wrap gap-2 mt-3">
                            <span class="px-2 py-1 rounded-lg text-[11px] font-bold bg-gray-100 text-gray-700">Financial ৳ {{ number_format($event->budgetAmount('financial'), 0) }}</span>
                            <span class="px-2 py-1 rounded-lg text-[11px] font-bold bg-teal-50 text-teal-700">Boost $ {{ number_format($event->budgetAmount('boosting'), 0) }}</span>
                            <span class="px-2 py-1 rounded-lg text-[11px] font-black bg-blue-50 text-blue-700">Total ৳ {{ number_format($event->budgetAmount(), 0) }}</span>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-12 text-center text-sm text-gray-500">No content with budget yet.</div>
                @endforelse
            </div>

            <div class="hidden md:block overflow-x-auto nice-scroll">
                <table class="w-full text-left border-collapse min-w-[820px]">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-gray-100">
                            <th class="px-6 py-4 text-[10px] font-extrabold text-gray-500 uppercase tracking-widest">Date</th>
                            <th class="px-6 py-4 text-[10px] font-extrabold text-gray-500 uppercase tracking-widest">Team</th>
                            <th class="px-6 py-4 text-[10px] font-extrabold text-gray-500 uppercase tracking-widest">Content</th>
                            <th class="px-6 py-4 text-[10px] font-extrabold text-gray-500 uppercase tracking-widest text-right">Financial</th>
                            <th class="px-6 py-4 text-[10px] font-extrabold text-gray-500 uppercase tracking-widest text-right">Boosting</th>
                            <th class="px-6 py-4 text-[10px] font-extrabold text-gray-500 uppercase tracking-widest text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($events as $event)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900">{{ $event->event_date->format('M d, Y') }}</div>
                                    <div class="text-[11px] text-gray-500">{{ $event->event_date->format('l') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider border
                                        {{ $event->teamBadgeClasses() }}">
                                        {{ $event->teamLabel() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-gray-900">
                                        <x-editable-title :event="$event" />
                                    </div>
                                    @if($event->content_objective)
                                        <div class="text-xs text-gray-500 mt-1 truncate max-w-md">{{ $event->content_objective }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-semibold text-gray-800">৳ {{ number_format($event->budgetAmount('financial'), 0) }}</td>
                                <td class="px-6 py-4 text-right text-sm font-semibold text-teal-700">$ {{ number_format($event->budgetAmount('boosting'), 0) }}</td>
                                <td class="px-6 py-4 text-right text-sm font-black text-gray-900">৳ {{ number_format($event->budgetAmount(), 0) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">No content with budget yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
