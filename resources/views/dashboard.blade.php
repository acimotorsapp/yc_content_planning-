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
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Total Events -->
                <div class="bg-white dark:bg-[#111] rounded-2xl p-6 border border-gray-200 dark:border-white/10 shadow-sm relative overflow-hidden group">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-indigo-500/10 rounded-full blur-2xl group-hover:bg-indigo-500/20 transition-all"></div>
                    <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Total Scheduled Posts</p>
                    <h3 class="text-4xl font-black text-gray-900 dark:text-white">{{ $total }}</h3>
                </div>
                
                <!-- Digital Team -->
                <div class="bg-white dark:bg-[#111] rounded-2xl p-6 border border-gray-200 dark:border-white/10 shadow-sm relative overflow-hidden group">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-teal-500/10 rounded-full blur-2xl group-hover:bg-teal-500/20 transition-all"></div>
                    <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Digital Team Events</p>
                    <h3 class="text-4xl font-black text-teal-600 dark:text-teal-400">{{ $digital }}</h3>
                </div>

                <!-- Product Team -->
                <div class="bg-white dark:bg-[#111] rounded-2xl p-6 border border-gray-200 dark:border-white/10 shadow-sm relative overflow-hidden group">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-500/10 rounded-full blur-2xl group-hover:bg-blue-500/20 transition-all"></div>
                    <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Product Team Events</p>
                    <h3 class="text-4xl font-black text-blue-600 dark:text-blue-400">{{ $product }}</h3>
                </div>
            </div>
        @endif

        <!-- Dashboard Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">{{ $filter ?? 'Schedule Overview' }}</h1>
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-1.5 font-medium">Manage and track your upcoming content pipeline.</p>
            </div>
            
            <div class="flex items-center gap-3">
                @if(auth()->user()->role === 'super_admin')
                    <span class="px-4 py-2 bg-gradient-to-r from-purple-500/10 to-pink-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20 rounded-xl text-sm font-bold shadow-[0_0_20px_rgba(168,85,247,0.1)] hidden sm:inline-block">Super Admin Mode</span>
                @endif
                
                @if(isset($filter))
                <a href="{{ route('events.create', ['filter' => $filter, 'action' => 'create']) }}" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-bold text-white rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-[#0a0a0a] focus:ring-indigo-500 transition-all shadow-sm hover:shadow-md transform hover:-translate-y-0.5">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Add Event
                </a>
                @endif
            </div>
        </div>

        <!-- FullCalendar Container -->
        <div class="bg-white dark:bg-[#0a0a0a] border border-gray-100/80 dark:border-[#1f1f1f] rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.5)] p-6 mb-8 overflow-hidden">
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
                        var teamClass = arg.event.extendedProps.teamType === 'product_team' 
                            ? 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-500/20' 
                            : 'bg-teal-500/10 text-teal-600 dark:text-teal-400 border-teal-500/20';

                        var pillarHtml = '';
                        if (arg.event.extendedProps.aipePillar !== 'N/A') {
                            pillarHtml = `<span class="inline-block mt-1.5 px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-yellow-500/20 text-yellow-600 dark:text-yellow-400 border border-yellow-500/30 shadow-[0_0_10px_rgba(234,179,8,0.1)]">
                                ${arg.event.extendedProps.aipePillar}
                            </span>`;
                        }

                        var html = `
                            <div class="p-2 w-full border rounded-xl shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-0.5 backdrop-blur-sm flex flex-col gap-0.5 ${teamClass}" style="white-space: normal; line-height: 1.3;">
                                <div class="font-bold text-xs" style="word-break: break-word;">${arg.event.title}</div>
                                <div class="text-[10px] opacity-80 mt-0.5 flex items-center font-medium">
                                    <svg class="w-3 h-3 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
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
            /* Ultra Modern styling for FullCalendar */
            .fc {
                /* Light Mode Variables */
                --fc-border-color: #f1f5f9;
                --fc-button-bg-color: #f8fafc;
                --fc-button-border-color: #f1f5f9;
                --fc-button-text-color: #334155;
                --fc-button-hover-bg-color: #f1f5f9;
                --fc-button-hover-border-color: #e2e8f0;
                --fc-button-active-bg-color: #e2e8f0;
                --fc-button-active-border-color: #cbd5e1;
                --fc-today-bg-color: #f8fafc;
                --fc-page-bg-color: transparent;
                --fc-neutral-bg-color: transparent;
                font-family: inherit;
            }

            .dark .fc {
                /* Dark Mode Variables */
                color: #e2e8f0;
                --fc-border-color: #1e293b;
                --fc-button-bg-color: #0f172a;
                --fc-button-border-color: #1e293b;
                --fc-button-text-color: #e2e8f0;
                --fc-button-hover-bg-color: #1e293b;
                --fc-button-hover-border-color: #334155;
                --fc-button-active-bg-color: #334155;
                --fc-button-active-border-color: #475569;
                --fc-today-bg-color: rgba(59, 130, 246, 0.05);
            }

            /* Toolbar Typography */
            .fc .fc-toolbar-title {
                font-size: 1.5rem;
                font-weight: 800;
                letter-spacing: -0.025em;
                color: #0f172a;
            }
            .dark .fc .fc-toolbar-title {
                color: #f8fafc;
            }

            /* Modern Toolbar Buttons */
            .fc .fc-button-primary {
                border-radius: 0.75rem !important;
                font-weight: 600 !important;
                text-transform: capitalize;
                box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05) !important;
                transition: all 0.2s ease;
                padding: 0.5rem 1.25rem !important;
                margin-left: 0.25rem !important;
            }
            .dark .fc .fc-button-primary {
                box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.5) !important;
            }
            .fc .fc-button-primary:focus {
                box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3) !important;
            }

            /* Remove grid borders for an airy look, keep only horizontal borders */
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
                padding: 16px 8px;
                text-transform: uppercase;
                font-size: 0.75rem;
                font-weight: 800;
                letter-spacing: 0.05em;
                color: #64748b;
            }
            .dark .fc .fc-col-header-cell-cushion {
                color: #94a3b8;
            }

            /* Day Numbers */
            .fc .fc-daygrid-day-number {
                color: inherit;
                text-decoration: none;
                font-weight: 700;
                font-size: 0.875rem;
                padding: 12px;
                transition: color 0.2s;
                opacity: 0.7;
            }
            .fc .fc-daygrid-day-number:hover {
                color: #3b82f6;
                opacity: 1;
            }

            /* Today Cell Highlight */
            .fc .fc-day-today {
                background-color: var(--fc-today-bg-color) !important;
            }
            .fc .fc-day-today .fc-daygrid-day-number {
                color: #3b82f6;
                opacity: 1;
                position: relative;
            }
            .fc .fc-day-today .fc-daygrid-day-number::after {
                content: '';
                position: absolute;
                bottom: 6px;
                left: 50%;
                transform: translateX(-50%);
                width: 4px;
                height: 4px;
                border-radius: 50%;
                background-color: #3b82f6;
            }

            /* Event Cards */
            .fc-event {
                cursor: pointer;
                border-radius: 0.75rem;
                overflow: hidden;
                margin-top: 2px;
                margin-bottom: 2px;
            }
            
            /* Make past days slightly faded */
            .fc-day-past {
                background-color: #f8fafc;
            }
            .dark .fc-day-past {
                background-color: #0f172a;
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
