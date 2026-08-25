<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-white leading-tight">
            {{ __('Events') }}
        </h2>
    </x-slot>

    <!-- Alpine Wrapper for Modal State -->
    <div x-data="{ 
            showModal: {{ $errors->any() ? 'true' : 'false' }},
            editModal: false,
            eventData: null,
            openEdit(event) {
                this.eventData = event;
                this.editModal = true;
            }
        }" class="max-w-7xl mx-auto pb-12 pt-8 px-4 sm:px-6 lg:px-8">

        <!-- Alerts -->
        @if(session('success'))
            <div class="bg-white dark:bg-[#111] border border-green-500/30 p-4 mb-6 rounded-xl shadow-sm flex items-center" role="alert">
                <svg class="w-5 h-5 text-green-400 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <p class="text-green-400 text-sm font-medium">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Dashboard Header -->
        <div class="relative flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-4 p-8 rounded-3xl bg-white dark:bg-[#111] bg-gradient-to-br from-gray-50 to-white dark:from-white/5 dark:to-transparent border border-gray-200 dark:border-white/10 overflow-hidden shadow-xl dark:shadow-2xl">
            <!-- Decorative blur -->
            <div class="absolute -top-32 -right-32 w-64 h-64 bg-blue-500/10 dark:bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-32 -left-32 w-64 h-64 bg-purple-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10">
                <h1 class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-gray-900 via-gray-700 to-gray-500 dark:from-white dark:via-gray-200 dark:to-gray-400 tracking-tight">{{ $filter ?? 'Events' }}</h1>
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-2 font-medium">Manage and track your content pipeline effortlessly.</p>
            </div>
            
            @if(auth()->user()->role !== 'super_admin')
            <button @click="showModal = true" class="relative z-10 inline-flex items-center justify-center px-6 py-3 text-sm font-bold text-white rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-[#0a0a0a] focus:ring-indigo-500 transition-all duration-300 shadow-[0_0_15px_rgba(79,70,229,0.3)] dark:shadow-[0_0_20px_rgba(79,70,229,0.4)] hover:shadow-[0_0_25px_rgba(79,70,229,0.4)] dark:hover:shadow-[0_0_30px_rgba(79,70,229,0.6)] transform hover:-translate-y-1">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Create New Event
            </button>
            @else
                <div class="relative z-10 flex items-center gap-4">
                    <span class="px-5 py-2.5 bg-purple-100 dark:bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-200 dark:border-purple-500/20 rounded-xl text-sm font-bold shadow-sm dark:shadow-[0_0_15px_rgba(168,85,247,0.15)] flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        Super Admin
                    </span>
                    @if(isset($filter))
                    <button @click="showModal = true" class="inline-flex items-center justify-center px-6 py-3 text-sm font-bold text-white rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-[#0a0a0a] focus:ring-indigo-500 transition-all duration-300 shadow-[0_0_15px_rgba(79,70,229,0.3)] dark:shadow-[0_0_20px_rgba(79,70,229,0.4)] hover:shadow-[0_0_25px_rgba(79,70,229,0.4)] dark:hover:shadow-[0_0_30px_rgba(79,70,229,0.6)] transform hover:-translate-y-1">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        New {{ str_replace(' Events', '', str_replace(' Team', '', $filter)) }}
                    </button>
                    @endif
                </div>
            @endif
        </div>

        @php
            $totalEvents = $events->count();
        @endphp

        <!-- Clean Linear-style Data Table -->
        <div class="bg-white/80 dark:bg-[#111]/80 backdrop-blur-xl border border-gray-200 dark:border-white/10 rounded-2xl shadow-lg dark:shadow-2xl overflow-hidden mb-8 ring-1 ring-gray-900/5 dark:ring-white/5">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-white/10 flex justify-between items-center bg-gradient-to-r from-gray-50 dark:from-white/5 to-transparent">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-100 dark:bg-blue-500/10 rounded-lg">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white tracking-wide">Schedule Overview</h3>
                </div>
                <span class="px-3 py-1 text-xs font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-100 dark:bg-indigo-500/10 rounded-full border border-indigo-200 dark:border-indigo-500/20">{{ $totalEvents }} Events</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-[#111] border-b border-gray-200 dark:border-white/5">
                            <th class="px-5 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider w-32">Date</th>
                            <th class="px-5 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider w-32">Team</th>
                            <th class="px-5 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Title / Objective</th>
                            <th class="px-5 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider w-48">Tags</th>
                            <th class="px-5 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider text-right w-16">Link</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-white/5 bg-transparent">
                        @forelse($events as $event)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors duration-200 group">
                            <!-- Date Column -->
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-200">{{ $event->event_date->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $event->event_date->format('l') }}</div>
                            </td>
                            
                            <!-- Team Column -->
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider border
                                    {{ $event->team_type == 'product_team' ? 'bg-blue-500/10 text-blue-400 border-blue-500/20' : 'bg-teal-500/10 text-teal-400 border-teal-500/20' }}">
                                    {{ str_replace('_', ' ', $event->team_type) }}
                                </span>
                            </td>

                            <!-- Title & Objective Column -->
                            <td class="px-5 py-4">
                                <div class="text-sm font-semibold text-gray-900 dark:text-gray-200">
                                    {{ $event->team_type == 'product_team' ? $event->content_title : 'Post #'.$event->post_no }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate max-w-sm" title="{{ $event->content_objective }}">
                                    {{ $event->content_objective ?? 'No objective specified' }}
                                </div>
                            </td>

                            <!-- Tags Column -->
                            <td class="px-5 py-4">
                                <div class="flex flex-wrap gap-1.5">
                                    @if($event->product ?? $event->product_focus)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-gray-100 dark:bg-white/5 text-gray-800 dark:text-gray-300 border border-gray-200 dark:border-white/10">
                                            {{ $event->product ?? $event->product_focus }}
                                        </span>
                                    @endif
                                    @if($event->format)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-gray-100 dark:bg-white/5 text-gray-800 dark:text-gray-300 border border-gray-200 dark:border-white/10">
                                            {{ $event->format }}
                                        </span>
                                    @endif
                                    @if($event->platform)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-gray-100 dark:bg-white/5 text-gray-800 dark:text-gray-300 border border-gray-200 dark:border-white/10">
                                            {{ $event->platform }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Action Column -->
                            <td class="px-5 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end space-x-3">
                                     <a href="{{ route('events.show', $event) }}" class="inline-flex text-gray-400 hover:text-white transition-colors" title="View Event">
                                         <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                     </a>
                                     @if(auth()->id() === $event->user_id)
                                         <button @click="openEdit({{ htmlspecialchars(json_encode($event)) }})" class="inline-flex text-blue-400 hover:text-blue-300 transition-colors" title="Edit Event">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <form action="{{ route('events.destroy', $event) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this event?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex text-red-400 hover:text-red-300 transition-colors" title="Delete Event">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    @endif
                                    
                                    @if($event->drive_link)
                                        <a href="{{ $event->drive_link }}" target="_blank" class="inline-flex text-gray-500 hover:text-white transition-colors" title="Open Link">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        </a>
                                    @else
                                        <span class="text-gray-700">-</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-5 py-20 text-center">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-br from-gray-100 to-gray-50 dark:from-white/5 dark:to-white/10 border border-gray-200 dark:border-white/10 shadow-sm dark:shadow-[0_0_20px_rgba(255,255,255,0.05)] mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                </div>
                                <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-wide">No events found</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 max-w-sm mx-auto">Get started by creating a new event to track your content pipeline.</p>
                                <button @click="showModal = true" class="mt-6 inline-flex items-center text-sm font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 transition-colors">
                                    Create Event <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </button>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- STYLISH & MODERN Add Event Modal Overlay (Dark Theme) -->
        <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div x-show="showModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-50 dark:bg-[#0a0a0a]/80 backdrop-blur-sm transition-opacity" @click="showModal = false"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="showModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
                     class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-[#111] text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-gray-300 dark:border-white/10 ring-1 ring-white/5">
                    
                    @if($errors->any())
                        <div class="bg-red-500/10 border-b border-red-500/20 p-5" role="alert">
                            <div class="flex">
                                <svg class="w-6 h-6 text-red-400 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <ul class="list-disc list-inside text-red-400 text-sm font-medium">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <div x-data="{ selectedTeam: 'product_team' }" class="w-full">
                        <div class="bg-gray-50 dark:bg-[#161616] px-8 py-6 border-b border-gray-200 dark:border-white/5">
                            <div class="flex items-center justify-between mb-5">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 rounded-xl flex items-center justify-center transition-colors"
                                         :class="{
                                            'bg-blue-100 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 shadow-[0_0_15px_rgba(59,130,246,0.15)]': selectedTeam === 'product_team',
                                            'bg-teal-100 dark:bg-teal-500/10 text-teal-600 dark:text-teal-400 shadow-[0_0_15px_rgba(20,184,166,0.15)]': selectedTeam === 'digital_team',
                                            'bg-yellow-100 dark:bg-yellow-500/10 text-yellow-600 dark:text-yellow-400 shadow-[0_0_15px_rgba(234,179,8,0.15)]': selectedTeam === 'global_team'
                                         }">
                                        <svg x-show="selectedTeam === 'product_team'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                                        <svg x-show="selectedTeam === 'digital_team'" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                        <svg x-show="selectedTeam === 'global_team'" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight">Create New Event</h3>
                                        <p class="text-sm text-gray-500 font-medium mt-0.5">Select the type of event you want to schedule</p>
                                    </div>
                                </div>
                                <button @click="showModal = false" class="text-gray-500 hover:text-gray-900 dark:hover:text-white bg-gray-200/50 dark:bg-white/5 hover:bg-gray-300/50 dark:hover:bg-white/10 p-2.5 rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-white/20">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                            
                            <!-- Segmented Tabs for Type Selection -->
                            <div class="flex items-center p-1 bg-gray-200/50 dark:bg-black/20 rounded-xl w-full">
                                <button type="button" @click="selectedTeam = 'product_team'" 
                                        :class="selectedTeam === 'product_team' ? 'bg-white dark:bg-[#222] text-blue-600 dark:text-blue-400 shadow-sm border border-gray-200/50 dark:border-white/5' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 border border-transparent'"
                                        class="flex-1 py-2 px-4 text-sm font-bold rounded-lg transition-all text-center">
                                    Product Event
                                </button>
                                <button type="button" @click="selectedTeam = 'digital_team'" 
                                        :class="selectedTeam === 'digital_team' ? 'bg-white dark:bg-[#222] text-teal-600 dark:text-teal-400 shadow-sm border border-gray-200/50 dark:border-white/5' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 border border-transparent'"
                                        class="flex-1 py-2 px-4 text-sm font-bold rounded-lg transition-all text-center">
                                    Digital Event
                                </button>
                                @if(auth()->user()->role === 'super_admin')
                                <button type="button" @click="selectedTeam = 'global_team'" 
                                        :class="selectedTeam === 'global_team' ? 'bg-white dark:bg-[#222] text-yellow-600 dark:text-yellow-400 shadow-sm border border-gray-200/50 dark:border-white/5' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 border border-transparent'"
                                        class="flex-1 py-2 px-4 text-sm font-bold rounded-lg transition-all text-center">
                                    Global Event
                                </button>
                                @endif
                            </div>
                        </div>
                        
                        <div class="px-8 py-6 bg-white dark:bg-[#111]">
                            <!-- Product Form -->
                            <form x-show="selectedTeam === 'product_team'" action="{{ route('events.product.store') }}" method="POST" class="space-y-5">
                                @csrf
                                <div class="grid grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">Publish Date*</label>
                                        <input type="date" name="event_date" required class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-gray-900 dark:text-white rounded-lg px-4 py-2.5 focus:bg-gray-50 dark:focus:bg-[#222] focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all shadow-sm">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">Shoot Date</label>
                                        <input type="date" name="shoot_date" class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-gray-900 dark:text-white rounded-lg px-4 py-2.5 focus:bg-gray-50 dark:focus:bg-[#222] focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all shadow-sm">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">Content Title*</label>
                                    <input type="text" name="content_title" required class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-gray-900 dark:text-white rounded-lg px-4 py-2.5 focus:bg-gray-50 dark:focus:bg-[#222] focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all shadow-sm placeholder-gray-400 dark:placeholder-gray-600" placeholder="e.g. Life Style Review">
                                </div>
                                <div class="grid grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">Product</label>
                                        <select name="product" class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-gray-900 dark:text-white rounded-lg px-4 py-2.5 focus:bg-gray-50 dark:focus:bg-[#222] focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all shadow-sm">
                                            <option value="">Select Product</option>
                                            <option value="FZS V2">FZS V2</option>
                                            <option value="FZS V4">FZS V4</option>
                                            <option value="FZS FI Hybrid">FZS FI Hybrid</option>
                                            <option value="FZX">FZX</option>
                                            <option value="Fazer">Fazer</option>
                                            <option value="FZ 25">FZ 25</option>
                                            <option value="MT">MT</option>
                                            <option value="R15">R15</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">Platform</label>
                                        <select name="platform" class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-gray-900 dark:text-white rounded-lg px-4 py-2.5 focus:bg-gray-50 dark:focus:bg-[#222] focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all shadow-sm">
                                            <option value="">Select Platform</option>
                                            <option value="Facebook">Facebook</option>
                                            <option value="Option 2">Option 2</option>
                                            <option value="YRC Page">YRC Page</option>
                                            <option value="Yamaha Lovers BD">Yamaha Lovers BD</option>
                                        </select>
                                    </div>
                                </div>
                                <div><label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">Content Objective</label><input type="text" name="content_objective" class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-gray-900 dark:text-white rounded-lg px-4 py-2.5 focus:bg-gray-50 dark:focus:bg-[#222] focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all shadow-sm placeholder-gray-400 dark:placeholder-gray-600" placeholder="Briefly describe the goal"></div>
                                <div class="grid grid-cols-3 gap-5">
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">A.I.P.E Pillar</label>
                                        <select name="aipe_pillar" class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-gray-900 dark:text-white rounded-lg px-4 py-2.5 focus:bg-gray-50 dark:focus:bg-[#222] focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all shadow-sm">
                                            <option value="">Select Pillar</option>
                                            <option value="Awareness">Awareness</option>
                                            <option value="Awareness+Interest">Awareness+Interest</option>
                                            <option value="Interest">Interest</option>
                                            <option value="Interest+Experience">Interest+Experience</option>
                                            <option value="Experience">Experience</option>
                                        </select>
                                    </div>
                                    <div><label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">Color</label><input type="text" name="color_concern" class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-gray-900 dark:text-white rounded-lg px-4 py-2.5 focus:bg-gray-50 dark:focus:bg-[#222] focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all shadow-sm"></div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">Format</label>
                                        <select name="format" class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-gray-900 dark:text-white rounded-lg px-4 py-2.5 focus:bg-gray-50 dark:focus:bg-[#222] focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all shadow-sm">
                                            <option value="">Select Format</option>
                                            <option value="Product Review">Product Review</option>
                                            <option value="OVC">OVC</option>
                                            <option value="Special Content">Special Content</option>
                                            <option value="Get Together">Get Together</option>
                                            <option value="Reels">Reels</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-5">
                                    <div><label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">Budget</label><input type="text" name="boosting_budget" class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-gray-900 dark:text-white rounded-lg px-4 py-2.5 focus:bg-gray-50 dark:focus:bg-[#222] focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all shadow-sm"></div>
                                    <div><label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">Drive Link</label><input type="text" name="drive_link" class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-gray-900 dark:text-white rounded-lg px-4 py-2.5 focus:bg-gray-50 dark:focus:bg-[#222] focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all shadow-sm placeholder-gray-400 dark:placeholder-gray-600" placeholder="https://drive.google.com/..."></div>
                                </div>
                                <div><label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">Remarks</label><input type="text" name="remarks" class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-gray-900 dark:text-white rounded-lg px-4 py-2.5 focus:bg-gray-50 dark:focus:bg-[#222] focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all shadow-sm"></div>
                                
                                <div class="pt-6 flex items-center justify-end gap-3 mt-6 border-t border-gray-200 dark:border-white/5">
                                    <button type="button" @click="showModal = false" class="px-5 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-400 bg-transparent border border-gray-300 dark:border-white/10 rounded-lg hover:bg-gray-100 dark:hover:bg-white/5 hover:text-gray-900 dark:hover:text-white transition-colors focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-white/20">
                                        Cancel
                                    </button>
                                    <button type="submit" class="px-6 py-2.5 text-sm font-bold text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-500 shadow-[0_0_15px_rgba(59,130,246,0.3)] transition-all focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-[#111]">
                                        Create Event
                                    </button>
                                </div>
                            </form>

                            <!-- Digital Form -->
                            <form x-show="selectedTeam === 'digital_team'" x-cloak action="{{ route('events.digital.store') }}" method="POST" class="space-y-5">
                                @csrf
                                <div class="grid grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">Event Date*</label>
                                        <input type="date" name="event_date" required class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-gray-900 dark:text-white rounded-lg px-4 py-2.5 focus:bg-gray-50 dark:focus:bg-[#222] focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition-all shadow-sm">
                                    </div>
                                    <div><label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">Post No.</label><input type="text" name="post_no" class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-gray-900 dark:text-white rounded-lg px-4 py-2.5 focus:bg-gray-50 dark:focus:bg-[#222] focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition-all shadow-sm placeholder-gray-400 dark:placeholder-gray-600" placeholder="e.g. 1"></div>
                                </div>
                                <div class="grid grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">Product Focus</label>
                                        <select name="product_focus" class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-gray-900 dark:text-white rounded-lg px-4 py-2.5 focus:bg-gray-50 dark:focus:bg-[#222] focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition-all shadow-sm">
                                            <option value="">Select Product</option>
                                            <option value="FZS V2">FZS V2</option>
                                            <option value="FZS V4">FZS V4</option>
                                            <option value="FZS FI Hybrid">FZS FI Hybrid</option>
                                            <option value="FZX">FZX</option>
                                            <option value="Fazer">Fazer</option>
                                            <option value="FZ 25">FZ 25</option>
                                            <option value="MT">MT</option>
                                            <option value="R15">R15</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">A.I.P.E Pillar</label>
                                        <select name="aipe_pillar" class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-gray-900 dark:text-white rounded-lg px-4 py-2.5 focus:bg-gray-50 dark:focus:bg-[#222] focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition-all shadow-sm">
                                            <option value="">Select Pillar</option>
                                            <option value="Awareness">Awareness</option>
                                            <option value="Awareness+Interest">Awareness+Interest</option>
                                            <option value="Interest">Interest</option>
                                            <option value="Interest+Experience">Interest+Experience</option>
                                            <option value="Experience">Experience</option>
                                        </select>
                                    </div>
                                </div>
                                <div><label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">Content Objective</label><input type="text" name="content_objective" class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-gray-900 dark:text-white rounded-lg px-4 py-2.5 focus:bg-gray-50 dark:focus:bg-[#222] focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition-all shadow-sm placeholder-gray-400 dark:placeholder-gray-600" placeholder="Briefly describe the goal"></div>
                                <div class="grid grid-cols-2 gap-5">
                                    <div><label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">Asset/Drive Link</label><input type="text" name="drive_link" class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-gray-900 dark:text-white rounded-lg px-4 py-2.5 focus:bg-gray-50 dark:focus:bg-[#222] focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition-all shadow-sm placeholder-gray-400 dark:placeholder-gray-600" placeholder="https://drive.google.com/..."></div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">Format</label>
                                        <select name="format" class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-gray-900 dark:text-white rounded-lg px-4 py-2.5 focus:bg-gray-50 dark:focus:bg-[#222] focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition-all shadow-sm">
                                            <option value="">Select Format</option>
                                            <option value="Product Review">Product Review</option>
                                            <option value="OVC">OVC</option>
                                            <option value="Special Content">Special Content</option>
                                            <option value="Get Together">Get Together</option>
                                            <option value="Reels">Reels</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-5">
                                    <div><label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">Budget</label><input type="text" name="boosting_budget" class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-gray-900 dark:text-white rounded-lg px-4 py-2.5 focus:bg-gray-50 dark:focus:bg-[#222] focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition-all shadow-sm"></div>
                                    <div><label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">Remarks</label><input type="text" name="remarks" class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-gray-900 dark:text-white rounded-lg px-4 py-2.5 focus:bg-gray-50 dark:focus:bg-[#222] focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition-all shadow-sm"></div>
                                </div>
                                
                                <div class="pt-6 flex items-center justify-end gap-3 mt-6 border-t border-gray-200 dark:border-white/5">
                                    <button type="button" @click="showModal = false" class="px-5 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-400 bg-transparent border border-gray-300 dark:border-white/10 rounded-lg hover:bg-gray-100 dark:hover:bg-white/5 hover:text-gray-900 dark:hover:text-white transition-colors focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-white/20">
                                        Cancel
                                    </button>
                                    <button type="submit" class="px-6 py-2.5 text-sm font-bold text-white bg-teal-600 border border-transparent rounded-lg hover:bg-teal-500 shadow-[0_0_15px_rgba(20,184,166,0.3)] transition-all focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-[#111]">
                                        Create Event
                                    </button>
                                </div>
                            </form>

                            <!-- Global Form -->
                            @if(auth()->user()->role === 'super_admin')
                            <form x-show="selectedTeam === 'global_team'" x-cloak action="{{ route('events.global.store') }}" method="POST" class="space-y-5">
                                @csrf
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">Event Date*</label>
                                    <input type="date" name="event_date" required class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-gray-900 dark:text-white rounded-lg px-4 py-2.5 focus:bg-gray-50 dark:focus:bg-[#222] focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 outline-none transition-all shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">Event Title*</label>
                                    <input type="text" name="content_title" required class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-gray-900 dark:text-white rounded-lg px-4 py-2.5 focus:bg-gray-50 dark:focus:bg-[#222] focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 outline-none transition-all shadow-sm placeholder-gray-400 dark:placeholder-gray-600" placeholder="e.g. World Tourism Day">
                                </div>
                                
                                <div class="pt-6 flex items-center justify-end gap-3 mt-6 border-t border-gray-200 dark:border-white/5">
                                    <button type="button" @click="showModal = false" class="px-5 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-400 bg-transparent border border-gray-300 dark:border-white/10 rounded-lg hover:bg-gray-100 dark:hover:bg-white/5 hover:text-gray-900 dark:hover:text-white transition-colors focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-white/20">
                                        Cancel
                                    </button>
                                    <button type="submit" class="px-6 py-2.5 text-sm font-bold text-black bg-yellow-400 border border-transparent rounded-lg hover:bg-yellow-300 shadow-[0_0_15px_rgba(234,179,8,0.3)] transition-all focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-[#111]">
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

        <!-- Edit Modal -->
        <div x-show="editModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div x-show="editModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-50 dark:bg-[#0a0a0a]/80 backdrop-blur-sm transition-opacity" @click="editModal = false"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="editModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
                     class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-[#111] text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-gray-300 dark:border-white/10 ring-1 ring-white/5">
                    
                    <div class="bg-gray-50 dark:bg-[#161616] px-8 py-6 border-b border-gray-200 dark:border-white/5 flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-purple-500/10 border border-purple-500/20 rounded-xl flex items-center justify-center text-purple-400 shadow-[0_0_15px_rgba(168,85,247,0.15)]">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-white tracking-tight">Edit Event</h3>
                                <p class="text-sm text-gray-500 font-medium mt-0.5" x-text="eventData ? eventData.content_title || 'Post #'+eventData.post_no : ''"></p>
                            </div>
                        </div>
                        <button @click="editModal = false" class="text-gray-500 hover:text-white bg-white/5 hover:bg-white/10 p-2.5 rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-white/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    
                    <div class="px-8 py-6 bg-white dark:bg-[#111]">
                        <form x-bind:action="'/events/' + eventData?.id" method="POST" class="space-y-5">
                            @csrf
                            @method('PUT')
                            <div class="grid grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">Event Date*</label>
                                    <input type="date" name="event_date" x-bind:value="eventData?.event_date ? eventData.event_date.substring(0,10) : ''" required class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-white rounded-lg px-4 py-2.5 focus:bg-[#222] focus:border-purple-500 focus:ring-1 focus:ring-purple-500 outline-none transition-all shadow-sm">
                                </div>
                                <div x-show="eventData?.team_type === 'product_team'">
                                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">Shoot Date</label>
                                    <input type="date" name="shoot_date" x-bind:value="eventData?.shoot_date ? eventData.shoot_date.substring(0,10) : ''" class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-white rounded-lg px-4 py-2.5 focus:bg-[#222] focus:border-purple-500 focus:ring-1 focus:ring-purple-500 outline-none transition-all shadow-sm">
                                </div>
                            </div>
                            
                            <div class="pt-6 flex items-center justify-end gap-3 mt-6 border-t border-gray-200 dark:border-white/5">
                                <button type="button" @click="editModal = false" class="px-5 py-2.5 text-sm font-medium text-gray-500 dark:text-gray-400 bg-transparent border border-gray-300 dark:border-white/10 rounded-lg hover:bg-gray-100 dark:hover:bg-white/5 hover:text-white transition-colors focus:outline-none focus:ring-2 focus:ring-white/20">
                                    Cancel
                                </button>
                                <button type="submit" class="px-6 py-2.5 text-sm font-bold text-white bg-purple-600 border border-transparent rounded-lg hover:bg-purple-500 shadow-[0_0_15px_rgba(168,85,247,0.3)] transition-all focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-[#111]">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
