<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-900 leading-tight">
            {{ __('Events') }}
        </h2>
    </x-slot>

    <!-- Alpine Wrapper for Modal State -->
    <div x-data="{ 
            showModal: {{ $errors->any() || request()->query('action') === 'create' ? 'true' : 'false' }}
        }" class="max-w-7xl mx-auto pb-12 pt-4 px-4 sm:px-6 lg:px-8">



        <!-- Dashboard Header -->
        <div class="relative flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4 p-8 rounded-3xl bg-white border border-gray-200/80 shadow-sm overflow-hidden">
            <!-- Decorative blur -->
            <div class="absolute -top-32 -right-32 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-32 -left-32 w-64 h-64 bg-purple-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10">
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ $filter ?? 'Events' }}</h1>
                <p class="text-gray-500 text-sm mt-1 font-medium">Manage and track your content pipeline effortlessly.</p>
            </div>
            
            <div class="relative z-10 flex items-center gap-3">
                @if(auth()->check() && auth()->user()->role === 'super_admin')
                    <span class="px-4 py-2 bg-purple-50 text-purple-700 border border-purple-200 rounded-xl text-xs font-bold shadow-xs flex items-center">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        Super Admin
                    </span>
                @endif
                <button @click="showModal = true" class="inline-flex items-center justify-center px-6 py-3 text-sm font-bold text-white rounded-xl bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all shadow-md shadow-blue-500/20 transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Create New Event
                </button>
            </div>
        </div>

        @php
            $totalEvents = $events->count();
        @endphp

        <!-- Clean Linear-style Data Table -->
        <div class="bg-white border border-gray-200 rounded-3xl shadow-sm overflow-hidden mb-8">
            <div class="px-8 py-5 border-b border-gray-100 flex justify-between items-center bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-50 text-blue-600 rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 tracking-tight">Schedule Overview</h3>
                </div>
                <span class="px-3.5 py-1 text-xs font-bold text-blue-600 bg-blue-50 rounded-full border border-blue-200">{{ $totalEvents }} Events</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-gray-100">
                            <th class="px-6 py-4 text-[10px] font-extrabold text-gray-500 uppercase tracking-widest w-32">Date</th>
                            <th class="px-6 py-4 text-[10px] font-extrabold text-gray-500 uppercase tracking-widest w-32">Team</th>
                            <th class="px-6 py-4 text-[10px] font-extrabold text-gray-500 uppercase tracking-widest">Title / Objective</th>
                            <th class="px-6 py-4 text-[10px] font-extrabold text-gray-500 uppercase tracking-widest w-48">Tags</th>
                            <th class="px-6 py-4 text-[10px] font-extrabold text-gray-500 uppercase tracking-widest text-right w-16">Link</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($events as $event)
                        <tr class="hover:bg-slate-50 transition-colors duration-150 group">
                            <!-- Date Column -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">{{ $event->event_date->format('M d, Y') }}</div>
                                <div class="text-[11px] font-medium text-gray-500 mt-0.5">{{ $event->event_date->format('l') }}</div>
                                @if($event->shoot_date)
                                    <div class="mt-1.5 inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-rose-50 text-rose-700 border border-rose-200 shadow-xs">
                                        <svg class="w-3 h-3 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        <span>Shoot: {{ $event->shoot_date->format('M d, Y') }}</span>
                                    </div>
                                @endif
                            </td>
                            
                            <!-- Team Column -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider border
                                    {{ $event->team_type == 'product_team' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-purple-50 text-purple-700 border-purple-200' }}">
                                    {{ str_replace('_', ' ', $event->team_type) }}
                                </span>
                            </td>

                            <!-- Title & Objective Column -->
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900 group-hover:text-blue-600 transition-colors">
                                    {{ $event->team_type == 'product_team' ? $event->content_title : 'Post #'.$event->post_no }}
                                </div>
                                <div class="text-[12px] text-gray-500 mt-1 truncate max-w-sm font-medium" title="{{ $event->content_objective }}">
                                    {{ $event->content_objective ?? 'No objective specified' }}
                                </div>
                            </td>

                            <!-- Tags Column -->
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1.5">
                                    @if($event->product ?? $event->product_focus)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                            {{ $event->product ?? $event->product_focus }}
                                        </span>
                                    @endif
                                    @if($event->format)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                            {{ $event->format }}
                                        </span>
                                    @endif
                                    @if($event->platform)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                            {{ $event->platform }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Action Column -->
                            <td class="px-5 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end space-x-2">
                                     <a href="{{ route('events.show', $event) }}" class="inline-flex p-1.5 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-colors" title="View Event">
                                         <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                     </a>
                                     @if(auth()->id() === $event->user_id || auth()->user()->role === 'super_admin')
                                         <a href="{{ route('events.edit', $event) }}" class="inline-flex p-1.5 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-colors" title="Edit Event">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                        <form action="{{ route('events.destroy', $event) }}" method="POST" class="inline delete-form" data-confirm="Are you sure you want to delete this event?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors" title="Delete Event">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                     @endif
                                     
                                     @if($event->drive_link)
                                         <a href="{{ $event->drive_link }}" target="_blank" class="inline-flex p-1.5 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-colors" title="Open Link">
                                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                         </a>
                                     @else
                                         <span class="text-gray-300 p-1.5">-</span>
                                     @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-5 py-20 text-center">
                                <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-gray-100 text-gray-400 mb-3">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                </div>
                                <h3 class="text-base font-bold text-gray-900 tracking-wide">No events found</h3>
                                <p class="text-sm text-gray-500 mt-1 max-w-sm mx-auto">Get started by creating a new event to track your content pipeline.</p>
                                <button @click="showModal = true" class="mt-6 inline-flex items-center text-sm font-bold text-blue-600 hover:text-blue-700 transition-colors">
                                    Create Event <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </button>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- STYLISH & MODERN Add Event Modal Overlay (Light Theme) -->
        <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div x-show="showModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-900/40 backdrop-blur-xs transition-opacity" @click="showModal = false"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="showModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
                     class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-gray-200 ring-1 ring-black/5">
                    
                    @if($errors->any())
                        <div class="bg-red-50 border-b border-red-200 p-5" role="alert">
                            <div class="flex">
                                <svg class="w-6 h-6 text-red-500 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <ul class="list-disc list-inside text-red-600 text-sm font-medium">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <div x-data="{ selectedTeam: 'product_team' }" class="w-full">
                        <div class="bg-slate-50 px-8 py-6 border-b border-gray-200">
                            <div class="flex items-center justify-between mb-5">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 rounded-xl flex items-center justify-center transition-colors"
                                         :class="{
                                            'bg-blue-100 text-blue-600 shadow-sm shadow-blue-500/10': selectedTeam === 'product_team',
                                            'bg-teal-100 text-teal-600 shadow-sm shadow-teal-500/10': selectedTeam === 'digital_team',
                                            'bg-amber-100 text-amber-600 shadow-sm shadow-amber-500/10': selectedTeam === 'global_team'
                                         }">
                                        <svg x-show="selectedTeam === 'product_team'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                                        <svg x-show="selectedTeam === 'digital_team'" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                        <svg x-show="selectedTeam === 'global_team'" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900 tracking-tight">Create New Event</h3>
                                        <p class="text-sm text-gray-500 font-medium mt-0.5">Select the type of event you want to schedule</p>
                                    </div>
                                </div>
                                <button @click="showModal = false" type="button" class="text-gray-400 hover:text-gray-700 bg-white hover:bg-gray-100 p-2.5 rounded-full border border-gray-200 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                            
                            <!-- Segmented Tabs for Type Selection -->
                            <div class="flex items-center p-1 bg-gray-200/80 rounded-xl w-full">
                                <button type="button" @click="selectedTeam = 'product_team'" 
                                        :class="selectedTeam === 'product_team' ? 'bg-white text-blue-600 shadow-sm border border-gray-200 font-bold' : 'text-gray-600 hover:text-gray-900 border border-transparent font-medium'"
                                        class="flex-1 py-2 px-4 text-sm rounded-lg transition-all text-center">
                                    Product Event
                                </button>
                                <button type="button" @click="selectedTeam = 'digital_team'" 
                                        :class="selectedTeam === 'digital_team' ? 'bg-white text-teal-600 shadow-sm border border-gray-200 font-bold' : 'text-gray-600 hover:text-gray-900 border border-transparent font-medium'"
                                        class="flex-1 py-2 px-4 text-sm rounded-lg transition-all text-center">
                                    Digital Event
                                </button>
                                @if(auth()->check() && auth()->user()->role === 'super_admin')
                                <button type="button" @click="selectedTeam = 'global_team'" 
                                        :class="selectedTeam === 'global_team' ? 'bg-white text-amber-600 shadow-sm border border-gray-200 font-bold' : 'text-gray-600 hover:text-gray-900 border border-transparent font-medium'"
                                        class="flex-1 py-2 px-4 text-sm rounded-lg transition-all text-center">
                                    Global Event
                                </button>
                                @endif
                            </div>
                        </div>
                        
                        <div class="px-8 py-6 bg-white">
                            <!-- Product Form -->
                            <form x-show="selectedTeam === 'product_team'" action="{{ route('events.product.store') }}" method="POST" class="space-y-5">
                                @csrf
                                <div class="grid grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Publish Date*</label>
                                        <input type="date" name="event_date" required class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs font-medium">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Shoot Date</label>
                                        <input type="date" name="shoot_date" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs font-medium">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Content Title*</label>
                                    <input type="text" name="content_title" required class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs placeholder-gray-400 font-medium" placeholder="e.g. Life Style Review">
                                </div>
                                <div class="grid grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Product</label>
                                        <select name="product" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs font-medium">
                                            <option value="">Select Product</option>
                                            @if(isset($masterData['product']))
                                                @foreach($masterData['product'] as $item)
                                                    <option value="{{ $item->value }}">{{ $item->value }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Platform</label>
                                        <select name="platform" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs font-medium">
                                            <option value="">Select Platform</option>
                                            @if(isset($masterData['platform']))
                                                @foreach($masterData['platform'] as $item)
                                                    <option value="{{ $item->value }}">{{ $item->value }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Content Objective</label>
                                    <input type="text" name="content_objective" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs placeholder-gray-400 font-medium" placeholder="Briefly describe the goal">
                                </div>
                                <div class="grid grid-cols-3 gap-5">
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">A.I.P.E Pillar</label>
                                        <select name="aipe_pillar" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs font-medium">
                                            <option value="">Select Pillar</option>
                                            @if(isset($masterData['aipe_pillar']))
                                                @foreach($masterData['aipe_pillar'] as $item)
                                                    <option value="{{ $item->value }}">{{ $item->value }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Color</label>
                                        <input type="text" name="color_concern" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs font-medium">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Format</label>
                                        <select name="format" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs font-medium">
                                            <option value="">Select Format</option>
                                            @if(isset($masterData['format']))
                                                @foreach($masterData['format'] as $item)
                                                    <option value="{{ $item->value }}">{{ $item->value }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Boosting Budget</label>
                                        <input type="text" name="boosting_budget" value="{{ old('boosting_budget', '0') }}" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs font-medium">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Drive Link</label>
                                        <input type="text" name="drive_link" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs placeholder-gray-400 font-medium" placeholder="https://drive.google.com/...">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Remarks</label>
                                    <input type="text" name="remarks" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs font-medium">
                                </div>
                                
                                <div class="pt-6 flex items-center justify-end gap-3 mt-6 border-t border-gray-200">
                                    <button type="button" @click="showModal = false" class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-300">
                                        Cancel
                                    </button>
                                    <button type="submit" class="px-6 py-2.5 text-sm font-bold text-white bg-blue-600 border border-transparent rounded-xl hover:bg-blue-700 shadow-md shadow-blue-500/20 transition-all focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                        Create Event
                                    </button>
                                </div>
                            </form>

                            <!-- Digital Form -->
                            <form x-show="selectedTeam === 'digital_team'" x-cloak action="{{ route('events.digital.store') }}" method="POST" class="space-y-5">
                                @csrf
                                <div class="grid grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Event Date*</label>
                                        <input type="date" name="event_date" required class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none transition-all shadow-xs font-medium">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Post No.</label>
                                        <input type="text" name="post_no" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none transition-all shadow-xs placeholder-gray-400 font-medium" placeholder="e.g. 1">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Product Focus</label>
                                        <select name="product_focus" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none transition-all shadow-xs font-medium">
                                            <option value="">Select Product</option>
                                            @if(isset($masterData['product']))
                                                @foreach($masterData['product'] as $item)
                                                    <option value="{{ $item->value }}">{{ $item->value }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">A.I.P.E Pillar</label>
                                        <select name="aipe_pillar" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none transition-all shadow-xs font-medium">
                                            <option value="">Select Pillar</option>
                                            @if(isset($masterData['aipe_pillar']))
                                                @foreach($masterData['aipe_pillar'] as $item)
                                                    <option value="{{ $item->value }}">{{ $item->value }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Content Objective</label>
                                    <input type="text" name="content_objective" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none transition-all shadow-xs placeholder-gray-400 font-medium" placeholder="Briefly describe the goal">
                                </div>
                                <div class="grid grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Asset/Drive Link</label>
                                        <input type="text" name="drive_link" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none transition-all shadow-xs placeholder-gray-400 font-medium" placeholder="https://drive.google.com/...">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Format</label>
                                        <select name="format" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none transition-all shadow-xs font-medium">
                                            <option value="">Select Format</option>
                                            @if(isset($masterData['format']))
                                                @foreach($masterData['format'] as $item)
                                                    <option value="{{ $item->value }}">{{ $item->value }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Boosting Budget</label>
                                        <input type="text" name="boosting_budget" value="{{ old('boosting_budget', '0') }}" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none transition-all shadow-xs font-medium">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Remarks</label>
                                        <input type="text" name="remarks" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none transition-all shadow-xs font-medium">
                                    </div>
                                </div>
                                
                                <div class="pt-6 flex items-center justify-end gap-3 mt-6 border-t border-gray-200">
                                    <button type="button" @click="showModal = false" class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-300">
                                        Cancel
                                    </button>
                                    <button type="submit" class="px-6 py-2.5 text-sm font-bold text-white bg-teal-600 border border-transparent rounded-xl hover:bg-teal-700 shadow-md shadow-teal-500/20 transition-all focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2">
                                        Create Event
                                    </button>
                                </div>
                            </form>

                            <!-- Global Form -->
                            @if(auth()->check() && auth()->user()->role === 'super_admin')
                            <form x-show="selectedTeam === 'global_team'" x-cloak action="{{ route('events.global.store') }}" method="POST" class="space-y-5">
                                @csrf
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Event Date*</label>
                                    <input type="date" name="event_date" required class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none transition-all shadow-xs font-medium">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Event Title*</label>
                                    <input type="text" name="content_title" required class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none transition-all shadow-xs placeholder-gray-400 font-medium" placeholder="e.g. World Tourism Day">
                                </div>
                                
                                <div class="pt-6 flex items-center justify-end gap-3 mt-6 border-t border-gray-200">
                                    <button type="button" @click="showModal = false" class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-300">
                                        Cancel
                                    </button>
                                    <button type="submit" class="px-6 py-2.5 text-sm font-bold text-white bg-amber-600 border border-transparent rounded-xl hover:bg-amber-700 shadow-md shadow-amber-500/20 transition-all focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2">
                                        Create Global Event
                                    </button>
                                </div>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
