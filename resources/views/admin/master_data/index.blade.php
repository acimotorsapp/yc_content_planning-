<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-900 leading-tight">
            {{ __('Category & Events Explorer') }}
        </h2>
    </x-slot>

    @php
        $hasActiveFilter = request()->filled('category_type') || request()->filled('category_value') || request()->filled('platform') || request()->filled('format') || request()->filled('aipe_pillar') || request()->filled('product') || request()->filled('team_type') || request()->filled('search') || request()->filled('month');
        $initialTab = $hasActiveFilter ? 'events' : (request()->query('tab', 'events'));
        $categoryLabels = ['platform' => 'Platforms', 'format' => 'Formats', 'aipe_pillar' => 'AIPE Pillars', 'product' => 'Products'];
    @endphp

    <div x-data="{ 
            mainTab: '{{ $initialTab }}',
            categoryTab: 'platform',
            showAddModal: {{ $errors->any() ? 'true' : 'false' }},
            selectedCatType: '{{ request('category_type', '') }}',
            selectedCatVal: '{{ request('category_value', '') }}',
            masterDataMap: {{ json_encode($masterData->map(fn($group) => $group->pluck('value'))) }}
         }" class="max-w-7xl mx-auto pb-16 pt-4 px-4 sm:px-6 lg:px-8 space-y-8">

        <!-- Top Header Card -->
        <div class="relative flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 p-8 rounded-3xl bg-white border border-gray-200/80 shadow-sm overflow-hidden">
            <div class="absolute -top-32 -right-32 w-72 h-72 bg-gradient-to-br from-teal-500/15 via-blue-500/10 to-indigo-500/15 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="relative z-10 space-y-1">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-50 border border-teal-200 text-teal-700 text-xs font-bold uppercase tracking-wider mb-2">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    Master Data & Filtering
                </div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Category Wise Events</h1>
                <p class="text-gray-500 text-sm font-medium max-w-2xl">
                    Filter and view all calendar events by Platform, Format, AIPE Pillar, and Product Focus, or manage Master Data categories.
                </p>
            </div>

            <!-- Quick Summary Badges -->
            <div class="relative z-10 flex flex-wrap lg:flex-col gap-2 shrink-0 bg-slate-50 p-4 rounded-2xl border border-gray-200">
                <div class="text-xs font-semibold text-gray-500 flex items-center justify-between gap-4">
                    <span>Total Events in Database:</span>
                    <span class="font-extrabold text-gray-900 bg-white px-2.5 py-0.5 rounded-lg border border-gray-200 shadow-2xs">{{ $stats['total_events'] }}</span>
                </div>
                <div class="text-xs font-semibold text-gray-500 flex items-center justify-between gap-4">
                    <span>Matched Filter Results:</span>
                    <span class="font-extrabold text-teal-600 bg-white px-2.5 py-0.5 rounded-lg border border-teal-200 shadow-2xs">{{ $stats['filtered_count'] }}</span>
                </div>
            </div>
        </div>

        <!-- Notification Alerts -->
        @if (session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center gap-3 shadow-xs">
                <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <div class="text-sm font-semibold">{{ session('success') }}</div>
            </div>
        @endif

        <!-- Main Tab Switcher -->
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-gray-200 pb-4">
            <div class="flex space-x-3">
                <button @click="mainTab = 'events'" 
                    :class="mainTab === 'events' ? 'bg-teal-600 text-white shadow-md shadow-teal-500/20 font-bold' : 'bg-white text-gray-600 hover:bg-gray-50 font-medium border border-gray-200'"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl text-sm transition-all duration-150 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    <span>Category Events Explorer</span>
                    <span class="ml-1 px-2 py-0.5 rounded-full text-xs font-extrabold" :class="mainTab === 'events' ? 'bg-teal-700 text-teal-100' : 'bg-gray-100 text-gray-600'">{{ $stats['filtered_count'] }}</span>
                </button>

                <button @click="mainTab = 'categories'" 
                    :class="mainTab === 'categories' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-500/20 font-bold' : 'bg-white text-gray-600 hover:bg-gray-50 font-medium border border-gray-200'"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl text-sm transition-all duration-150 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    <span>Manage Categories</span>
                </button>
            </div>

            <button x-show="mainTab === 'categories'" @click="showAddModal = true" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-bold text-white rounded-xl bg-teal-600 hover:bg-teal-700 transition-all shadow-sm cursor-pointer">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add Category Entry
            </button>
        </div>

        <!-- ================= TAB 1: CATEGORY EVENTS EXPLORER ================= -->
        <div x-show="mainTab === 'events'" class="space-y-6">

            <!-- Interactive Filter Card -->
            <div class="bg-white rounded-3xl border border-gray-200 shadow-sm p-6 sm:p-8 space-y-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">Filter Events by Category</h3>
                    </div>

                    @if($hasActiveFilter)
                        <a href="{{ route('admin.master_data.index') }}" class="inline-flex items-center text-xs font-bold text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 px-3 py-1.5 rounded-xl transition-colors">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            Clear All Filters
                        </a>
                    @endif
                </div>

                <form method="GET" action="{{ route('admin.master_data.index') }}" class="space-y-4">
                    <input type="hidden" name="tab" value="events">

                    <!-- Filter Form Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Category Type -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Category Type</label>
                            <select 
                                name="category_type" 
                                x-model="selectedCatType"
                                @change="selectedCatVal = ''"
                                class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-3.5 py-2.5 focus:bg-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 font-medium text-sm">
                                <option value="">All Categories</option>
                                <option value="platform">Platform ({{ $masterData['platform']->count() ?? 0 }})</option>
                                <option value="format">Format ({{ $masterData['format']->count() ?? 0 }})</option>
                                <option value="aipe_pillar">AIPE Pillar ({{ $masterData['aipe_pillar']->count() ?? 0 }})</option>
                                <option value="product">Product Focus ({{ $masterData['product']->count() ?? 0 }})</option>
                            </select>
                        </div>

                        <!-- Category Value (Dynamic options) -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Category Value</label>
                            <select 
                                name="category_value" 
                                x-model="selectedCatVal"
                                class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-3.5 py-2.5 focus:bg-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 font-medium text-sm">
                                <option value="">All Values</option>
                                <template x-if="selectedCatType && masterDataMap[selectedCatType]">
                                    <template x-for="val in masterDataMap[selectedCatType]" :key="val">
                                        <option :value="val" x-text="val" :selected="selectedCatVal === val"></option>
                                    </template>
                                </template>
                                <template x-if="!selectedCatType">
                                    <optgroup label="Select a Category Type above">
                                        @foreach($masterData as $catKey => $items)
                                            <optgroup label="{{ ucfirst(str_replace('_', ' ', $catKey)) }}">
                                                @foreach($items as $item)
                                                    <option value="{{ $item->value }}" {{ request('category_value') == $item->value ? 'selected' : '' }}>{{ $item->value }}</option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </optgroup>
                                </template>
                            </select>
                        </div>

                        <!-- Team Type -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Team Type</label>
                            <select name="team_type" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-3.5 py-2.5 focus:bg-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 font-medium text-sm">
                                <option value="">All Teams</option>
                                <option value="product_team" {{ request('team_type') == 'product_team' ? 'selected' : '' }}>Product Team</option>
                                <option value="digital_team" {{ request('team_type') == 'digital_team' ? 'selected' : '' }}>Digital Team</option>
                                <option value="global_team" {{ request('team_type') == 'global_team' ? 'selected' : '' }}>Global Events</option>
                            </select>
                        </div>

                        <!-- Search Text -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Keyword Search</label>
                            <input 
                                type="text" 
                                name="search" 
                                value="{{ request('search') }}" 
                                placeholder="Search title, product, remarks..." 
                                class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-3.5 py-2.5 focus:bg-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 font-medium text-sm placeholder-gray-400">
                        </div>
                    </div>

                    <!-- Filter Action Buttons -->
                    <div class="flex items-center justify-between pt-2">
                        <!-- Quick Category Pills -->
                        <div class="hidden xl:flex items-center gap-2 text-xs text-gray-500 font-medium overflow-x-auto">
                            <span class="font-bold text-gray-700">Quick Filters:</span>
                            <a href="{{ route('admin.master_data.index', ['category_type' => 'platform', 'category_value' => 'Facebook']) }}" class="px-2.5 py-1 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg border border-blue-200 transition-colors">Facebook</a>
                            <a href="{{ route('admin.master_data.index', ['category_type' => 'format', 'category_value' => 'Reel']) }}" class="px-2.5 py-1 bg-purple-50 text-purple-700 hover:bg-purple-100 rounded-lg border border-purple-200 transition-colors">Reels</a>
                            <a href="{{ route('admin.master_data.index', ['category_type' => 'aipe_pillar', 'category_value' => 'Awareness']) }}" class="px-2.5 py-1 bg-amber-50 text-amber-700 hover:bg-amber-100 rounded-lg border border-amber-200 transition-colors">Awareness</a>
                            <a href="{{ route('admin.master_data.index', ['category_type' => 'product', 'category_value' => 'FZS V4']) }}" class="px-2.5 py-1 bg-teal-50 text-teal-700 hover:bg-teal-100 rounded-lg border border-teal-200 transition-colors">FZS V4</a>
                            <a href="{{ route('admin.master_data.index', ['category_type' => 'product', 'category_value' => 'R15']) }}" class="px-2.5 py-1 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-lg border border-indigo-200 transition-colors">R15</a>
                        </div>

                        <div class="flex items-center gap-3 ml-auto">
                            @if($hasActiveFilter)
                                <a href="{{ route('admin.master_data.index') }}" class="px-4 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">
                                    Reset
                                </a>
                            @endif
                            <button type="submit" class="inline-flex items-center justify-center px-6 py-2.5 text-sm font-bold text-white rounded-xl bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 transition-all shadow-md shadow-teal-500/20 cursor-pointer">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                Filter Events
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Filtered Events Table Card -->
            <div class="bg-white border border-gray-200 rounded-3xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-slate-50/50">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-bold text-gray-900">Events List</span>
                        <span class="px-2.5 py-0.5 bg-teal-100 text-teal-800 rounded-full text-xs font-bold">{{ $events->total() }} events</span>
                    </div>

                    <div class="text-xs text-gray-400 font-medium">
                        Page {{ $events->currentPage() }} of {{ $events->lastPage() ?: 1 }}
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-gray-100">
                                <th class="px-6 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Content & Title</th>
                                <th class="px-6 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Team</th>
                                <th class="px-6 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Product / Focus</th>
                                <th class="px-6 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Category Details</th>
                                <th class="px-6 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Boosting Budget</th>
                                <th class="px-6 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse($events as $event)
                                <tr class="hover:bg-slate-50 transition-colors duration-150 group">
                                    <!-- Event Date -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-extrabold text-gray-900">{{ $event->event_date->format('M d, Y') }}</span>
                                            <span class="text-xs text-gray-400 font-medium">{{ $event->event_date->format('l') }}</span>
                                        </div>
                                    </td>

                                    <!-- Content Title & Objective -->
                                    <td class="px-6 py-4 max-w-xs sm:max-w-md">
                                        <div class="text-sm font-bold text-gray-900 line-clamp-1">
                                            {{ $event->content_title ?: ($event->post_no ? "Post #{$event->post_no}" : 'Untitled Event') }}
                                        </div>
                                        @if($event->content_objective)
                                            <div class="text-xs text-gray-500 line-clamp-1 mt-0.5">{{ $event->content_objective }}</div>
                                        @endif
                                    </td>

                                    <!-- Team Type Badge -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-xs font-bold uppercase tracking-wider border
                                            {{ $event->team_type == 'product_team' ? 'bg-amber-50 text-amber-700 border-amber-200' : ($event->team_type == 'digital_team' ? 'bg-purple-50 text-purple-700 border-purple-200' : 'bg-blue-50 text-blue-700 border-blue-200') }}">
                                            {{ str_replace('_', ' ', $event->team_type) }}
                                        </span>
                                    </td>

                                    <!-- Product / Focus -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($event->product || $event->product_focus)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-teal-50 text-teal-700 border border-teal-200">
                                                {{ $event->product ?: $event->product_focus }}
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400 font-medium">-</span>
                                        @endif
                                    </td>

                                    <!-- Category Details (Platform, Format, Pillar) -->
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-1.5">
                                            @if($event->platform)
                                                <span class="px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                                                    {{ $event->platform }}
                                                </span>
                                            @endif
                                            @if($event->format)
                                                <span class="px-2 py-0.5 rounded-md text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                                    {{ $event->format }}
                                                </span>
                                            @endif
                                            @if($event->aipe_pillar)
                                                <span class="px-2 py-0.5 rounded-md text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-100">
                                                    {{ $event->aipe_pillar }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Boosting Budget -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-extrabold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                            ৳ {{ $event->boosting_budget ?? '0' }}
                                        </span>
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('events.show', $event) }}" class="p-1.5 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-colors" title="View Event">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            </a>
                                            <a href="{{ route('events.edit', $event) }}" class="p-1.5 rounded-lg text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors" title="Edit Event">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </a>
                                            <form action="{{ route('events.destroy', $event) }}" method="POST" class="inline delete-form" data-confirm="Are you sure you want to delete this event?">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors" title="Delete Event">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                        <div class="w-12 h-12 mx-auto rounded-2xl bg-gray-100 text-gray-400 flex items-center justify-center mb-3">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <div class="text-base font-bold text-gray-700">No events matched the selected filter criteria</div>
                                        <p class="text-xs text-gray-400 mt-1 max-w-sm mx-auto">Try clearing one or more filters or search terms above to see more events.</p>
                                        @if($hasActiveFilter)
                                            <a href="{{ route('admin.master_data.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 mt-4 text-xs font-bold text-teal-700 bg-teal-50 border border-teal-200 rounded-xl hover:bg-teal-100 transition-colors">
                                                Reset All Filters
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Links -->
                @if($events->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 bg-slate-50/50">
                        {{ $events->links() }}
                    </div>
                @endif
            </div>

        </div>

        <!-- ================= TAB 2: MANAGE CATEGORIES ================= -->
        <div x-show="mainTab === 'categories'" style="display: none;" class="space-y-6">

            <!-- Category Sub-Tabs -->
            <div class="flex space-x-2 overflow-x-auto pb-2">
                @foreach($categoryLabels as $key => $label)
                    <button @click="categoryTab = '{{ $key }}'" 
                            :class="categoryTab === '{{ $key }}' ? 'bg-indigo-50 text-indigo-700 border-indigo-500 shadow-xs font-bold' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50 font-medium'"
                            class="px-5 py-2.5 rounded-xl border text-sm transition-all cursor-pointer flex items-center gap-2">
                        <span>{{ $label }}</span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-extrabold" :class="categoryTab === '{{ $key }}' ? 'bg-indigo-200/80 text-indigo-800' : 'bg-gray-100 text-gray-500'">
                            {{ isset($masterData[$key]) ? $masterData[$key]->count() : 0 }}
                        </span>
                    </button>
                @endforeach
            </div>

            <!-- Category Items Table -->
            <div class="bg-white border border-gray-200 rounded-3xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-gray-100">
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Category Item Name</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Associated Events</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach($categoryLabels as $catKey => $catLabel)
                                @if(isset($masterData[$catKey]) && $masterData[$catKey]->count() > 0)
                                    @foreach($masterData[$catKey] as $item)
                                        @php
                                            $evCount = $categoryCounts[$catKey][$item->value] ?? 0;
                                        @endphp
                                        <tr x-show="categoryTab === '{{ $catKey }}'" class="hover:bg-slate-50 transition-colors duration-150 group">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-bold text-gray-900">{{ $item->value }}</div>
                                            </td>

                                            <!-- Associated Events Count & Filter Link -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <a href="{{ route('admin.master_data.index', ['category_type' => $catKey, 'category_value' => $item->value, 'tab' => 'events']) }}" 
                                                   class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold transition-all border {{ $evCount > 0 ? 'bg-teal-50 text-teal-700 border-teal-200 hover:bg-teal-100' : 'bg-gray-50 text-gray-500 border-gray-200 hover:bg-gray-100' }}"
                                                   title="View all events with this category">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                    <span>{{ $evCount }} {{ Str::plural('Event', $evCount) }}</span>
                                                    <svg class="w-3 h-3 text-teal-600 opacity-60 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                                </a>
                                            </td>

                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <form action="{{ route('admin.master_data.destroy', $item) }}" method="POST" class="inline delete-form" data-confirm="Are you sure you want to delete this category entry?">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors" title="Delete">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr x-show="categoryTab === '{{ $catKey }}'">
                                        <td colspan="3" class="px-6 py-10 text-center text-gray-500 text-sm">
                                            No {{ strtolower($catLabel) }} found. Click 'Add Category Entry' to create one.
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Add Category Entry Modal -->
        <div x-show="showAddModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-xs transition-opacity" @click="showAddModal = false"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-md transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all border border-gray-200 ring-1 ring-black/5 p-6 sm:p-8">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-xl font-bold text-gray-900">Add New Category Entry</h3>
                        <button @click="showAddModal = false" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <form action="{{ route('admin.master_data.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Category Type</label>
                                <select name="category" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-3.5 py-2.5 focus:bg-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 font-medium">
                                    <option value="platform">Platform</option>
                                    <option value="format">Format</option>
                                    <option value="aipe_pillar">AIPE Pillar</option>
                                    <option value="product">Product</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Value Name</label>
                                <input type="text" name="value" required placeholder="e.g., Facebook, Reel, Awareness, FZS V4" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-3.5 py-2.5 focus:bg-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 placeholder-gray-400 font-medium">
                            </div>
                        </div>
                        <div class="mt-6 flex items-center justify-end gap-3">
                            <button type="button" @click="showAddModal = false" class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">
                                Cancel
                            </button>
                            <button type="submit" class="px-6 py-2.5 border border-transparent rounded-xl shadow-md shadow-teal-500/20 text-sm font-bold text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500">
                                Save Entry
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
