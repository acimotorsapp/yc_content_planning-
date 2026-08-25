<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-white leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <!-- Alpine Wrapper for Modal State -->
    <div x-data="{ 
            editModal: false,
            eventData: null,
            openEdit(event) {
                this.eventData = event;
                this.editModal = true;
            }
        }" 
        id="dashboard-modal-wrapper"
        class="max-w-7xl mx-auto pb-12">

        <!-- Alerts -->
        @if(session('success'))
            <div class="bg-white dark:bg-[#111] border border-green-500/30 p-4 mb-6 rounded-xl shadow-sm flex items-center" role="alert">
                <svg class="w-5 h-5 text-green-400 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <p class="text-green-400 text-sm font-medium">{{ session('success') }}</p>
            </div>
        @endif

        @if(auth()->user()->role === 'super_admin' && !isset($filter))
            @php
                $totalCount = clone $events;
                $total = $totalCount->count();
                $digital = $totalCount->where('team_type', 'digital_team')->count();
                $product = $totalCount->where('team_type', 'product_team')->count();
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10 animate-fade-in-up" style="animation-delay: 0.1s;">
                <!-- Total Events -->
                <div class="bg-white/40 dark:bg-[#111]/40 backdrop-blur-xl rounded-2xl p-6 border border-white/20 dark:border-white/5 shadow-xl relative overflow-hidden group hover:-translate-y-1 transition-all duration-300">
                    <div class="absolute -right-12 -top-12 w-40 h-40 bg-gradient-to-br from-indigo-500/20 to-purple-500/10 rounded-full blur-3xl group-hover:from-indigo-500/30 group-hover:to-purple-500/20 transition-all duration-500"></div>
                    <div class="absolute inset-0 bg-gradient-to-br from-white/40 to-white/0 dark:from-white/5 dark:to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="relative z-10 flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-1">Total Scheduled</p>
                            <h3 class="text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-gray-900 to-gray-600 dark:from-white dark:to-gray-400">{{ $total }}</h3>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-indigo-500/10 flex items-center justify-center text-indigo-500 border border-indigo-500/20 shadow-[0_0_15px_rgba(99,102,241,0.2)]">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                    </div>
                </div>
                
                <!-- Digital Team -->
                <div class="bg-white/40 dark:bg-[#111]/40 backdrop-blur-xl rounded-2xl p-6 border border-white/20 dark:border-white/5 shadow-xl relative overflow-hidden group hover:-translate-y-1 transition-all duration-300">
                    <div class="absolute -right-12 -top-12 w-40 h-40 bg-gradient-to-br from-teal-500/20 to-emerald-500/10 rounded-full blur-3xl group-hover:from-teal-500/30 group-hover:to-emerald-500/20 transition-all duration-500"></div>
                    <div class="absolute inset-0 bg-gradient-to-br from-white/40 to-white/0 dark:from-white/5 dark:to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="relative z-10 flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-1">Digital Team</p>
                            <h3 class="text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-teal-600 to-emerald-500 dark:from-teal-400 dark:to-emerald-300">{{ $digital }}</h3>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-teal-500/10 flex items-center justify-center text-teal-500 border border-teal-500/20 shadow-[0_0_15px_rgba(20,184,166,0.2)]">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Product Team -->
                <div class="bg-white/40 dark:bg-[#111]/40 backdrop-blur-xl rounded-2xl p-6 border border-white/20 dark:border-white/5 shadow-xl relative overflow-hidden group hover:-translate-y-1 transition-all duration-300">
                    <div class="absolute -right-12 -top-12 w-40 h-40 bg-gradient-to-br from-blue-500/20 to-cyan-500/10 rounded-full blur-3xl group-hover:from-blue-500/30 group-hover:to-cyan-500/20 transition-all duration-500"></div>
                    <div class="absolute inset-0 bg-gradient-to-br from-white/40 to-white/0 dark:from-white/5 dark:to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="relative z-10 flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-1">Product Team</p>
                            <h3 class="text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-cyan-500 dark:from-blue-400 dark:to-cyan-300">{{ $product }}</h3>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-blue-500/10 flex items-center justify-center text-blue-500 border border-blue-500/20 shadow-[0_0_15px_rgba(59,130,246,0.2)]">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Dashboard Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-4 animate-fade-in-up" style="animation-delay: 0.2s;">
            <div>
                <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight">{{ $filter ?? 'Schedule Overview' }}</h1>
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-2 font-medium tracking-wide">Manage and track your upcoming content pipeline.</p>
            </div>
            
            <div class="flex items-center gap-4">
                @if(auth()->user()->role === 'super_admin')
                    <span class="px-4 py-2 bg-[#111] text-white border border-gray-800 dark:bg-white/5 dark:text-white dark:border-white/10 rounded-xl text-xs font-bold uppercase tracking-widest shadow-2xl hidden sm:inline-flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                        Admin Mode
                    </span>
                @endif
                
                @if(isset($filter))
                <a href="{{ route('events.create', ['filter' => $filter, 'action' => 'create']) }}" class="inline-flex items-center justify-center px-6 py-3 text-sm font-bold text-white rounded-xl bg-gray-900 hover:bg-gray-800 dark:bg-white dark:text-black dark:hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-[#0a0a0a] focus:ring-gray-900 dark:focus:ring-white transition-all shadow-xl hover:shadow-2xl transform hover:-translate-y-1 group">
                    <svg class="w-5 h-5 mr-2 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    New Event
                </a>
                @endif
            </div>
        </div>

        <!-- FullCalendar Container -->
        <div class="bg-white/40 dark:bg-[#0a0a0a]/60 backdrop-blur-xl border border-gray-200/50 dark:border-white/10 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_40px_rgb(0,0,0,0.8)] p-8 mb-12 overflow-hidden animate-fade-in-up" style="animation-delay: 0.3s;">
            <div id="calendar"></div>
        </div>

        <!-- FullCalendar Dependencies -->
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
        
        @php
            $formattedEvents = $events->map(function($event) {
                $title = $event->team_type == 'product_team' ? $event->content_title : 'Post #'.$event->post_no;
                $userName = $event->user ? $event->user->name : 'Unknown User';
                
                return [
                    'id' => $event->id,
                    'title' => $title,
                    'start' => $event->event_date->format('Y-m-d'),
                    'allDay' => true,
                    'extendedProps' => [
                        'userName' => $userName,
                        'aipePillar' => $event->aipe_pillar ?? 'N/A',
                        'teamType' => $event->team_type
                    ]
                ];
            })->values();
        @endphp

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var calendarEl = document.getElementById('calendar');
                
                var eventsData = @json($formattedEvents);

                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    height: 'auto',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                    },
                    events: eventsData,
                    eventContent: function(arg) {
                        var isProduct = arg.event.extendedProps.teamType === 'product_team';
                        var teamClass = isProduct
                            ? 'bg-gradient-to-r from-blue-500/10 to-indigo-500/10 text-blue-700 dark:text-blue-300 border-blue-500/20' 
                            : 'bg-gradient-to-r from-teal-500/10 to-emerald-500/10 text-teal-700 dark:text-teal-300 border-teal-500/20';

                        var pillarHtml = '';
                        if (arg.event.extendedProps.aipePillar !== 'N/A') {
                            pillarHtml = `<span class="inline-flex items-center justify-center mt-1.5 px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-widest bg-yellow-500/10 text-yellow-600 dark:text-yellow-400 border border-yellow-500/20">
                                ${arg.event.extendedProps.aipePillar}
                            </span>`;
                        }

                        var html = `
                            <div class="px-2.5 py-2 w-full border rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-0.5 backdrop-blur-md flex flex-col gap-0.5 ${teamClass}" style="white-space: normal; line-height: 1.4;">
                                <div class="font-extrabold text-[11px] leading-tight" style="word-break: break-word; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    ${arg.event.title}
                                </div>
                                <div class="text-[9px] opacity-75 mt-0.5 flex items-center font-bold tracking-wide uppercase">
                                    <svg class="w-3 h-3 inline-block mr-1 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    ${arg.event.extendedProps.userName}
                                </div>
                                ${pillarHtml}
                            </div>
                        `;

                        return { html: html };
                    },
                    eventClassNames: function(arg) {
                        return ['!bg-transparent', '!border-none', '!p-0', 'hover:opacity-95', 'transition-all'];
                    },
                    eventClick: function(info) {
                        window.location.href = '/events/' + info.event.id;
                    }
                });
                calendar.render();
            });
        </script>

        <style>
            /* Entrance Animations */
            @keyframes fadeInUp {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .animate-fade-in-up {
                animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
                opacity: 0;
            }

            /* Ultra Modern styling for FullCalendar */
            .fc {
                /* Light Mode Variables */
                --fc-border-color: rgba(226, 232, 240, 0.5);
                --fc-button-bg-color: #f8fafc;
                --fc-button-border-color: rgba(226, 232, 240, 0.8);
                --fc-button-text-color: #334155;
                --fc-button-hover-bg-color: #f1f5f9;
                --fc-button-hover-border-color: #cbd5e1;
                --fc-button-active-bg-color: #e2e8f0;
                --fc-button-active-border-color: #94a3b8;
                --fc-today-bg-color: rgba(59, 130, 246, 0.03);
                --fc-page-bg-color: transparent;
                --fc-neutral-bg-color: transparent;
                font-family: inherit;
            }

            .dark .fc {
                /* Dark Mode Variables */
                color: #e2e8f0;
                --fc-border-color: rgba(255, 255, 255, 0.05);
                --fc-button-bg-color: rgba(255, 255, 255, 0.03);
                --fc-button-border-color: rgba(255, 255, 255, 0.05);
                --fc-button-text-color: #f8fafc;
                --fc-button-hover-bg-color: rgba(255, 255, 255, 0.08);
                --fc-button-hover-border-color: rgba(255, 255, 255, 0.1);
                --fc-button-active-bg-color: rgba(255, 255, 255, 0.12);
                --fc-button-active-border-color: rgba(255, 255, 255, 0.2);
                --fc-today-bg-color: rgba(59, 130, 246, 0.08);
            }

            /* Toolbar Typography */
            .fc .fc-toolbar-title {
                font-size: 1.75rem;
                font-weight: 900;
                letter-spacing: -0.025em;
                color: #0f172a;
            }
            .dark .fc .fc-toolbar-title {
                color: #ffffff;
            }

            /* Modern Toolbar Buttons */
            .fc .fc-button-primary {
                border-radius: 0.75rem !important;
                font-weight: 700 !important;
                text-transform: capitalize;
                box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05) !important;
                transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
                padding: 0.6rem 1.25rem !important;
                margin-left: 0.5rem !important;
                border-width: 1px !important;
                backdrop-filter: blur(10px);
            }
            .dark .fc .fc-button-primary {
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.5), 0 2px 4px -1px rgba(0, 0, 0, 0.3) !important;
            }
            .fc .fc-button-primary:hover {
                transform: translateY(-1px);
            }
            .fc .fc-button-primary:focus {
                box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5) !important;
            }

            /* Remove grid borders for an airy look, keep only subtle horizontal borders */
            .fc-theme-standard td, .fc-theme-standard th {
                border-right: none !important;
                border-left: none !important;
                border-bottom: 1px solid var(--fc-border-color) !important;
                border-top: none !important;
                background-color: transparent !important;
            }
            .fc-scrollgrid {
                border: none !important;
            }
            
            /* Day Headers */
            .fc .fc-col-header-cell-cushion {
                padding: 20px 8px;
                text-transform: uppercase;
                font-size: 0.7rem;
                font-weight: 800;
                letter-spacing: 0.1em;
                color: #64748b;
            }
            .dark .fc .fc-col-header-cell-cushion {
                color: #94a3b8;
            }

            /* Day Numbers */
            .fc .fc-daygrid-day-number {
                color: inherit;
                text-decoration: none;
                font-weight: 800;
                font-size: 0.9rem;
                padding: 16px;
                transition: color 0.2s;
                opacity: 0.5;
            }
            .fc .fc-daygrid-day-number:hover {
                color: #3b82f6;
                opacity: 1;
            }

            /* Today Cell Highlight */
            .fc .fc-day-today {
                background-color: var(--fc-today-bg-color) !important;
                border-radius: 1.5rem;
            }
            .fc .fc-day-today .fc-daygrid-day-number {
                color: #3b82f6;
                opacity: 1;
                position: relative;
            }
            .fc .fc-day-today .fc-daygrid-day-number::after {
                content: '';
                position: absolute;
                bottom: 8px;
                left: 50%;
                transform: translateX(-50%);
                width: 6px;
                height: 6px;
                border-radius: 50%;
                background-color: #3b82f6;
                box-shadow: 0 0 10px rgba(59,130,246,0.5);
            }

            /* Event Cards Wrapper */
            .fc-event {
                cursor: pointer;
                border-radius: 0.75rem;
                overflow: visible !important; /* Allow shadow to pop out */
                margin-top: 3px;
                margin-bottom: 3px;
                background: transparent !important;
                border: none !important;
            }
            
            /* Faded past days */
            .fc-day-past {
                opacity: 0.6;
            }
        </style>

        @php
            $totalEvents = $events->count();
        @endphp

        <!-- Clean Linear-style Data Table -->
        <div class="bg-white dark:bg-[#0a0a0a] border border-gray-100/80 dark:border-[#1f1f1f] rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.5)] overflow-hidden mb-12">
            <div class="px-6 py-5 border-b border-gray-100/80 dark:border-[#1f1f1f] flex justify-between items-center bg-transparent">
                <h3 class="text-lg font-extrabold text-gray-900 dark:text-white tracking-tight">Content Schedule</h3>
                <span class="px-3 py-1 bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-400 rounded-full text-xs font-bold">{{ $totalEvents }} Events</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-[#0f0f0f] border-b border-gray-100/80 dark:border-[#1f1f1f]">
                            <th class="px-6 py-4 text-[10px] font-extrabold text-gray-400 uppercase tracking-widest w-32">Date</th>
                            <th class="px-6 py-4 text-[10px] font-extrabold text-gray-400 uppercase tracking-widest w-32">Team</th>
                            <th class="px-6 py-4 text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">Title / Objective</th>
                            <th class="px-6 py-4 text-[10px] font-extrabold text-gray-400 uppercase tracking-widest w-48">Tags</th>
                            <th class="px-6 py-4 text-[10px] font-extrabold text-gray-400 uppercase tracking-widest text-right w-16">Link</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100/80 dark:divide-[#1f1f1f] bg-transparent">
                        @forelse($events as $event)
                        <tr class="hover:bg-blue-50/30 dark:hover:bg-[#141414] transition-colors duration-200 group">
                            <!-- Date Column -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $event->event_date->format('M d, Y') }}</div>
                                <div class="text-[11px] font-medium text-gray-500 mt-0.5">{{ $event->event_date->format('l') }}</div>
                            </td>
                            
                            <!-- Team Column -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-1 rounded-lg text-[10px] font-extrabold uppercase tracking-wider border
                                    {{ $event->team_type == 'product_team' ? 'bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-200 dark:border-blue-500/20' : 'bg-teal-50 dark:bg-teal-500/10 text-teal-600 dark:text-teal-400 border-teal-200 dark:border-teal-500/20' }}">
                                    {{ str_replace('_', ' ', $event->team_type) }}
                                </span>
                            </td>

                            <!-- Title & Objective Column -->
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                    {{ $event->team_type == 'product_team' ? $event->content_title : 'Post #'.$event->post_no }}
                                </div>
                                <div class="text-[12px] text-gray-500 dark:text-gray-400 mt-1 truncate max-w-sm font-medium" title="{{ $event->content_objective }}">
                                    {{ $event->content_objective ?? 'No objective specified' }}
                                </div>
                            </td>

                            <!-- Tags Column -->
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1.5">
                                    @if($event->product ?? $event->product_focus)
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-bold bg-gray-100 dark:bg-[#1a1a1a] text-gray-700 dark:text-gray-300 border border-transparent dark:border-white/5">
                                            {{ $event->product ?? $event->product_focus }}
                                        </span>
                                    @endif
                                    @if($event->format)
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-bold bg-gray-100 dark:bg-[#1a1a1a] text-gray-700 dark:text-gray-300 border border-transparent dark:border-white/5">
                                            {{ $event->format }}
                                        </span>
                                    @endif
                                    @if($event->platform)
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-bold bg-gray-100 dark:bg-[#1a1a1a] text-gray-700 dark:text-gray-300 border border-transparent dark:border-white/5">
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
                                     @if(auth()->id() === $event->user_id || auth()->user()->role === 'super_admin')
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
                            <td colspan="5" class="px-5 py-16 text-center">
                                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-white dark:bg-[#1a1a1a] mb-3">
                                    <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                </div>
                                <h3 class="text-sm font-medium text-gray-800 dark:text-gray-300">No events found</h3>
                                <p class="text-xs text-gray-500 mt-1">Get started by creating a new event.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @php
            $globalEventsRaw = \App\Models\CalendarEvent::where('team_type', 'global_team')
                ->orderBy('event_date', 'asc')
                ->get()
                ->groupBy(function($event) {
                    return \Carbon\Carbon::parse($event->event_date)->format('F');
                });
        @endphp

        <!-- Global Calendar & Observances from Database -->
        @if($globalEventsRaw->count() > 0)
        <div class="mt-8">
            <h2 class="text-lg font-bold text-gray-900 dark:text-gray-200 mb-4 tracking-tight">Global Calendar & Observances</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($globalEventsRaw as $month => $eventsList)
                    <div class="bg-white dark:bg-[#111] border border-gray-200 dark:border-white/5 rounded-xl shadow-sm overflow-hidden flex flex-col hover:border-gray-300 dark:border-white/10 transition-colors">
                        <div class="px-5 py-3 border-b border-gray-200 dark:border-white/5 bg-gray-50 dark:bg-[#161616]">
                            <h3 class="text-sm font-bold text-gray-800 dark:text-gray-300 uppercase tracking-widest">{{ $month }}</h3>
                        </div>
                        <div class="p-5 flex-1">
                            <ul class="space-y-4">
                                @foreach($eventsList as $eventItem)
                                    <li class="flex items-start">
                                        <div class="min-w-[4rem] shrink-0 pt-0.5">
                                            @php
                                                $dayStr = \Carbon\Carbon::parse($eventItem->event_date)->format('jS');
                                                preg_match('/(\d+)(st|nd|rd|th)?/', $dayStr, $matches);
                                                $dayNum = $matches[1] ?? \Carbon\Carbon::parse($eventItem->event_date)->format('j');
                                                $suffix = $matches[2] ?? '';
                                            @endphp
                                            <span class="text-sm font-bold text-gray-800 dark:text-gray-300">{{ $dayNum }}<sup class="text-[10px] text-gray-500">{{ $suffix }}</sup></span>
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400 font-medium pl-3 border-l-2 border-gray-200 dark:border-white/5">
                                            {{ $eventItem->content_title }}
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

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
                            
                            <!-- Additional edit fields can be added here. Minimal needed for this feature is Date. -->
                            
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
