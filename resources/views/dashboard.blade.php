<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-900 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div x-data="{ showGlobalModal: false }" class="max-w-7xl mx-auto pb-12">

        @if(isset($filter) && $filter === 'Global Events')
        <!-- Global Events Top Header & Action -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4 animate-fade-in-up">
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Global Events & Observances</h1>
                <p class="text-gray-500 text-sm mt-1 font-medium">Manage worldwide events and special company-wide observances.</p>
            </div>
            <button type="button" @click="showGlobalModal = true" class="inline-flex items-center justify-center px-6 py-3 text-sm font-bold text-white rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-all shadow-md shadow-amber-500/20 hover:shadow-lg transform hover:-translate-y-0.5 group cursor-pointer">
                <svg class="w-5 h-5 mr-2 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add Global Event
            </button>
        </div>
        @endif

        @if(!isset($filter))
        <!-- Top Actions -->
        <div class="flex justify-end mb-6 animate-fade-in-up">
            <a href="{{ route('events.create', ['action' => 'create']) }}" class="inline-flex items-center justify-center px-6 py-3 text-sm font-bold text-white rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all shadow-md shadow-blue-500/20 hover:shadow-lg transform hover:-translate-y-0.5 group">
                <svg class="w-5 h-5 mr-2 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add New Event
            </a>
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
                <div class="bg-white rounded-2xl p-6 border border-gray-200/80 shadow-sm relative overflow-hidden group hover:-translate-y-0.5 transition-all duration-300">
                    <div class="absolute -right-12 -top-12 w-40 h-40 bg-indigo-500/10 rounded-full blur-3xl group-hover:bg-indigo-500/20 transition-all duration-500"></div>
                    <div class="relative z-10 flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Total Scheduled</p>
                            <h3 class="text-4xl font-black text-gray-900">{{ $total }}</h3>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 border border-indigo-100 shadow-xs">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                    </div>
                </div>
                
                <!-- Digital Team -->
                <div class="bg-white rounded-2xl p-6 border border-gray-200/80 shadow-sm relative overflow-hidden group hover:-translate-y-0.5 transition-all duration-300">
                    <div class="absolute -right-12 -top-12 w-40 h-40 bg-teal-500/10 rounded-full blur-3xl group-hover:bg-teal-500/20 transition-all duration-500"></div>
                    <div class="relative z-10 flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Digital Team</p>
                            <h3 class="text-4xl font-black text-teal-600">{{ $digital }}</h3>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-teal-50 flex items-center justify-center text-teal-600 border border-teal-100 shadow-xs">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Product Team -->
                <div class="bg-white rounded-2xl p-6 border border-gray-200/80 shadow-sm relative overflow-hidden group hover:-translate-y-0.5 transition-all duration-300">
                    <div class="absolute -right-12 -top-12 w-40 h-40 bg-blue-500/10 rounded-full blur-3xl group-hover:bg-blue-500/20 transition-all duration-500"></div>
                    <div class="relative z-10 flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Product Team</p>
                            <h3 class="text-4xl font-black text-blue-600">{{ $product }}</h3>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600 border border-blue-100 shadow-xs">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if(!isset($filter) || $filter !== 'Global Events')
        <!-- Dashboard Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4 animate-fade-in-up" style="animation-delay: 0.2s;">
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">{{ $filter ?? 'Schedule Overview' }}</h1>
                <p class="text-gray-500 text-sm mt-1 font-medium">Manage and track your upcoming content pipeline.</p>
            </div>
            
            <div class="flex items-center gap-3">
                @if(auth()->user()->role === 'super_admin')
                    <span class="px-3.5 py-1.5 bg-gray-100 text-gray-700 border border-gray-200 rounded-xl text-xs font-bold uppercase tracking-wider hidden sm:inline-flex items-center gap-2 shadow-xs">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Admin Mode
                    </span>
                @endif
            </div>
        </div>
        @endif

        <!-- FullCalendar Container -->
        <div class="bg-white border border-gray-200 rounded-3xl shadow-sm p-6 sm:p-8 mb-12 overflow-hidden animate-fade-in-up" style="animation-delay: 0.3s;">
            <div id="calendar"></div>
        </div>

        <!-- FullCalendar Dependencies -->
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
        
        @php
            $formattedEvents = $events->map(function($event) {
                $title = $event->team_type == 'product_team' ? $event->content_title : 'Post #'.$event->post_no;
                $userName = $event->user ? $event->user->name : 'Unknown User';
                $shootDate = $event->shoot_date ? $event->shoot_date->format('M d, Y') : null;
                
                return [
                    'id' => $event->id,
                    'title' => $title,
                    'start' => $event->event_date->format('Y-m-d'),
                    'allDay' => true,
                    'extendedProps' => [
                        'userName' => $userName,
                        'aipePillar' => $event->aipe_pillar ?? 'N/A',
                        'teamType' => $event->team_type,
                        'shootDate' => $shootDate
                    ]
                ];
            })->values();
        @endphp

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var calendarEl = document.getElementById('calendar');
                
                var eventsData = @json($formattedEvents);
                
                // Categorize dates by team type
                var dateTeamMap = {};
                eventsData.forEach(function(ev) {
                    var d = ev.start;
                    var t = ev.extendedProps.teamType;
                    if (!dateTeamMap[d]) {
                        dateTeamMap[d] = new Set();
                    }
                    dateTeamMap[d].add(t);
                });

                function applyDayCellClass(el, dateStr) {
                    if (!dateStr || !dateTeamMap[dateStr]) return;
                    var teams = dateTeamMap[dateStr];
                    if (teams.has('digital_team') && !teams.has('product_team')) {
                        el.classList.add('fc-has-digital-event-day');
                    } else if (teams.has('product_team') && !teams.has('digital_team')) {
                        el.classList.add('fc-has-product-event-day');
                    } else if (teams.has('digital_team') && teams.has('product_team')) {
                        el.classList.add('fc-has-mixed-event-day');
                    } else if (teams.has('global_team')) {
                        el.classList.add('fc-has-global-event-day');
                    } else {
                        el.classList.add('fc-has-product-event-day');
                    }
                }

                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    height: 'auto',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                    },
                    events: eventsData,
                    dayCellDidMount: function(arg) {
                        var dateStr = arg.el.getAttribute('data-date');
                        if (!dateStr && arg.date) {
                            var y = arg.date.getFullYear();
                            var m = String(arg.date.getMonth() + 1).padStart(2, '0');
                            var d = String(arg.date.getDate()).padStart(2, '0');
                            dateStr = y + '-' + m + '-' + d;
                        }
                        applyDayCellClass(arg.el, dateStr);
                    },
                    eventDidMount: function(info) {
                        var dateStr = info.event.startStr;
                        if (dateStr) {
                            var dayCell = document.querySelector('.fc-daygrid-day[data-date="' + dateStr + '"]');
                            if (dayCell) {
                                applyDayCellClass(dayCell, dateStr);
                            }
                        }
                    },
                    eventContent: function(arg) {
                        var teamType = arg.event.extendedProps.teamType;
                        var teamClass = '';
                        
                        if (teamType === 'digital_team') {
                            teamClass = 'bg-purple-50 text-purple-900 border-purple-200 shadow-xs';
                        } else if (teamType === 'product_team') {
                            teamClass = 'bg-amber-50 text-amber-900 border-amber-200 shadow-xs';
                        } else {
                            teamClass = 'bg-blue-50 text-blue-900 border-blue-200 shadow-xs';
                        }

                        var pillarHtml = '';
                        if (arg.event.extendedProps.aipePillar && arg.event.extendedProps.aipePillar !== 'N/A') {
                            var pillarBadge = teamType === 'digital_team' 
                                ? 'bg-purple-100 text-purple-800 border-purple-200' 
                                : 'bg-amber-100 text-amber-800 border-amber-200';
                            pillarHtml = `<span class="inline-flex items-center justify-center mt-1 px-2 py-0.5 rounded-md text-[9px] font-bold uppercase tracking-wider ${pillarBadge} border">
                                ${arg.event.extendedProps.aipePillar}
                            </span>`;
                        }

                        var shootHtml = '';
                        if (arg.event.extendedProps.shootDate) {
                            shootHtml = `
                                <div class="mt-1 px-2 py-0.5 rounded-md bg-rose-500 text-white font-extrabold text-[9px] flex items-center gap-1 shadow-xs tracking-tight">
                                    <svg class="w-2.5 h-2.5 inline-block shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    <span>Shoot: ${arg.event.extendedProps.shootDate}</span>
                                </div>
                            `;
                        }

                        var html = `
                            <div class="px-2.5 py-2 w-full border rounded-xl shadow-xs hover:shadow-md transition-all duration-200 hover:-translate-y-0.5 flex flex-col gap-0.5 ${teamClass}" style="white-space: normal; line-height: 1.4;">
                                <div class="font-extrabold text-[13px] leading-tight" style="word-break: break-word; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    ${arg.event.title}
                                </div>
                                <div class="text-[10px] text-gray-500 mt-0.5 flex items-center font-bold tracking-wide uppercase">
                                    <svg class="w-3 h-3 inline-block mr-1 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    ${arg.event.extendedProps.userName}
                                </div>
                                ${pillarHtml}
                                ${shootHtml}
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

            /* Ultra Modern styling for FullCalendar in Light Theme */
            .fc {
                --fc-border-color: #e2e8f0;
                --fc-button-bg-color: #ffffff;
                --fc-button-border-color: #e2e8f0;
                --fc-button-text-color: #1e293b;
                --fc-button-hover-bg-color: #f8fafc;
                --fc-button-hover-border-color: #cbd5e1;
                --fc-button-active-bg-color: #e2e8f0;
                --fc-button-active-border-color: #94a3b8;
                --fc-today-bg-color: transparent;
                --fc-page-bg-color: #ffffff;
                --fc-neutral-bg-color: #ffffff;
                font-family: inherit;
            }

            /* Toolbar Typography */
            .fc .fc-toolbar-title {
                font-size: 1.5rem;
                font-weight: 800;
                letter-spacing: -0.025em;
                color: #0f172a;
            }

            /* Modern Toolbar Buttons */
            .fc .fc-button-primary {
                border-radius: 0.75rem !important;
                font-weight: 700 !important;
                text-transform: capitalize;
                box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05) !important;
                transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
                padding: 0.5rem 1.1rem !important;
                margin-left: 0.4rem !important;
                border-width: 1px !important;
            }
            .fc .fc-button-primary:hover {
                transform: translateY(-1px);
            }
            .fc .fc-button-primary:focus {
                box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3) !important;
            }

            /* Day Cell & Frame - Default Pure White */
            .fc-theme-standard td, .fc-theme-standard th {
                border: 1px solid #f1f5f9 !important;
                background-color: #ffffff !important;
            }
            .fc .fc-scrollgrid {
                border: 1px solid #e2e8f0 !important;
                border-radius: 1.25rem;
                overflow: hidden;
                background-color: #ffffff !important;
            }
            .fc-daygrid-day {
                background-color: #ffffff !important;
            }
            .fc-daygrid-day-frame {
                background-color: #ffffff !important;
                transition: all 0.2s ease;
                border-radius: 0.75rem;
                margin: 2px;
                min-height: 110px;
                border: 1px solid transparent;
            }
            .fc-daygrid-day:hover .fc-daygrid-day-frame {
                background-color: #f8fafc !important;
            }

            /* PRODUCT TEAM DATES: LIGHT SOFT YELLOW */
            .fc-daygrid-day.fc-has-product-event-day,
            .fc-has-product-event-day,
            .fc-has-product-event-day .fc-daygrid-day-frame {
                background-color: #fefce8 !important; /* Tailwind yellow-50 */
            }
            .fc-has-product-event-day .fc-daygrid-day-frame {
                border: 1px solid #fef08a !important; /* Tailwind yellow-200 */
            }
            .fc-has-product-event-day:hover .fc-daygrid-day-frame {
                background-color: #fef9c3 !important; /* Tailwind yellow-100 */
                border-color: #fde047 !important;
            }

            /* DIGITAL TEAM DATES: LIGHT SOFT PURPLE */
            .fc-daygrid-day.fc-has-digital-event-day,
            .fc-has-digital-event-day,
            .fc-has-digital-event-day .fc-daygrid-day-frame {
                background-color: #faf5ff !important; /* Tailwind purple-50 */
            }
            .fc-has-digital-event-day .fc-daygrid-day-frame {
                border: 1px solid #e9d5ff !important; /* Tailwind purple-200 */
            }
            .fc-has-digital-event-day:hover .fc-daygrid-day-frame {
                background-color: #f3e8ff !important; /* Tailwind purple-100 */
                border-color: #d8b4fe !important;
            }

            /* MIXED DATES: DUAL PASTEL GRADIENT */
            .fc-daygrid-day.fc-has-mixed-event-day,
            .fc-has-mixed-event-day,
            .fc-has-mixed-event-day .fc-daygrid-day-frame {
                background: linear-gradient(135deg, #fefce8 50%, #faf5ff 50%) !important;
            }
            .fc-has-mixed-event-day .fc-daygrid-day-frame {
                border: 1px solid #e9d5ff !important;
            }
            .fc-has-mixed-event-day:hover .fc-daygrid-day-frame {
                background: linear-gradient(135deg, #fef9c3 50%, #f3e8ff 50%) !important;
            }

            /* GLOBAL EVENTS DATES: LIGHT BLUE */
            .fc-daygrid-day.fc-has-global-event-day,
            .fc-has-global-event-day,
            .fc-has-global-event-day .fc-daygrid-day-frame {
                background-color: #eff6ff !important;
            }
            .fc-has-global-event-day .fc-daygrid-day-frame {
                border: 1px solid #bfdbfe !important;
            }

            /* Day Headers */
            .fc .fc-col-header-cell {
                background-color: #f8fafc !important;
                border-bottom: 1px solid #e2e8f0 !important;
            }
            .fc .fc-col-header-cell-cushion {
                padding: 14px 8px;
                text-transform: uppercase;
                font-size: 0.75rem;
                font-weight: 800;
                letter-spacing: 0.05em;
                color: #475569;
            }

            /* Day Numbers */
            .fc .fc-daygrid-day-number {
                color: #1e293b;
                text-decoration: none;
                font-weight: 800;
                font-size: 0.875rem;
                padding: 10px 12px;
                transition: color 0.2s;
            }
            .fc .fc-daygrid-day-number:hover {
                color: #2563eb;
            }

            /* Today Cell Highlight */
            .fc .fc-day-today {
                background-color: transparent !important;
            }
            .fc .fc-day-today:not(.fc-has-product-event-day):not(.fc-has-digital-event-day):not(.fc-has-mixed-event-day):not(.fc-has-global-event-day) .fc-daygrid-day-frame {
                background-color: #ffffff !important;
                border: 2px solid #3b82f6 !important;
            }
            .fc .fc-day-today.fc-has-product-event-day .fc-daygrid-day-frame {
                background-color: #fefce8 !important;
                border: 2px solid #eab308 !important;
            }
            .fc .fc-day-today.fc-has-digital-event-day .fc-daygrid-day-frame {
                background-color: #faf5ff !important;
                border: 2px solid #9333ea !important;
            }
            .fc .fc-day-today.fc-has-mixed-event-day .fc-daygrid-day-frame {
                background: linear-gradient(135deg, #fefce8 50%, #faf5ff 50%) !important;
                border: 2px solid #9333ea !important;
            }
            .fc .fc-day-today .fc-daygrid-day-number {
                color: #2563eb !important;
                font-weight: 900 !important;
            }

            /* Event Cards Wrapper */
            .fc-event {
                cursor: pointer;
                border-radius: 0.75rem;
                overflow: visible !important;
                margin-top: 3px;
                margin-bottom: 3px;
                background: transparent !important;
                border: none !important;
            }
            
            /* Faded past days */
            .fc-day-past:not(.fc-has-product-event-day):not(.fc-has-digital-event-day):not(.fc-has-mixed-event-day):not(.fc-has-global-event-day) {
                opacity: 0.8;
            }
        </style>

        @php
            $totalEvents = $events->count();
            $upcomingEvents = $events->where('event_date', '>=', now()->startOfDay())->take(5);
        @endphp

        <!-- Upcoming Events Table -->
        @if(!isset($filter) && $upcomingEvents->count() > 0)
        <div class="bg-white border border-gray-200 rounded-3xl shadow-sm overflow-hidden mb-12 animate-fade-in-up" style="animation-delay: 0.4s;">
            <div class="px-8 py-5 border-b border-gray-100 flex justify-between items-center bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 border border-blue-100 shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 tracking-tight">Upcoming Events</h3>
                </div>
                <span class="px-3.5 py-1 bg-blue-50 text-blue-600 border border-blue-200 rounded-full text-xs font-bold uppercase tracking-wider">Next {{ $upcomingEvents->count() }} Events</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-gray-100">
                            <th class="px-8 py-4 text-[10px] font-extrabold text-gray-500 uppercase tracking-widest w-40">Date</th>
                            <th class="px-8 py-4 text-[10px] font-extrabold text-gray-500 uppercase tracking-widest w-32">Team</th>
                            <th class="px-8 py-4 text-[10px] font-extrabold text-gray-500 uppercase tracking-widest">Title / Objective</th>
                            <th class="px-8 py-4 text-[10px] font-extrabold text-gray-500 uppercase tracking-widest w-48">Tags</th>
                            <th class="px-8 py-4 text-[10px] font-extrabold text-gray-500 uppercase tracking-widest text-right w-16">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach($upcomingEvents as $event)
                        <tr class="hover:bg-slate-50 transition-all duration-150 group cursor-default">
                            <!-- Date Column -->
                            <td class="px-8 py-5 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">{{ $event->event_date->format('M d, Y') }}</div>
                                <div class="text-xs font-semibold text-blue-600 mt-0.5 uppercase tracking-wider">{{ $event->event_date->format('l') }}</div>
                                @if($event->shoot_date)
                                    <div class="mt-1.5 inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-rose-50 text-rose-700 border border-rose-200 shadow-xs">
                                        <svg class="w-3 h-3 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        <span>Shoot: {{ $event->shoot_date->format('M d, Y') }}</span>
                                    </div>
                                @endif
                            </td>
                            
                            <!-- Team Column -->
                            <td class="px-8 py-5 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-xl text-[10px] font-bold uppercase tracking-wider border
                                    {{ $event->team_type == 'product_team' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-purple-50 text-purple-700 border-purple-200' }}">
                                    {{ str_replace('_', ' ', $event->team_type) }}
                                </span>
                            </td>

                            <!-- Title & Objective Column -->
                            <td class="px-8 py-5">
                                <div class="text-base font-bold text-gray-900 group-hover:text-blue-600 transition-colors">
                                    {{ $event->team_type == 'product_team' ? $event->content_title : 'Post #'.$event->post_no }}
                                </div>
                                <div class="text-xs text-gray-500 mt-1 truncate max-w-md font-medium" title="{{ $event->content_objective }}">
                                    {{ $event->content_objective ?? 'No objective specified' }}
                                </div>
                            </td>

                            <!-- Tags Column -->
                            <td class="px-8 py-5">
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
                                </div>
                            </td>

                            <!-- Action Column -->
                            <td class="px-8 py-5 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('events.show', $event) }}" class="inline-flex items-center justify-center p-2 rounded-xl bg-gray-100 text-gray-600 hover:bg-blue-600 hover:text-white transition-all transform hover:-translate-y-0.5" title="View Event">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                    @if(auth()->id() === $event->user_id || auth()->user()->role === 'super_admin')
                                    <a href="{{ route('events.edit', $event) }}" class="inline-flex items-center justify-center p-2 rounded-xl bg-gray-100 text-gray-600 hover:bg-blue-600 hover:text-white transition-all transform hover:-translate-y-0.5" title="Edit Event">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    <form action="{{ route('events.destroy', $event) }}" method="POST" class="inline delete-form" data-confirm="Are you sure you want to delete this event?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center p-2 rounded-xl bg-gray-100 text-gray-600 hover:bg-red-600 hover:text-white transition-all transform hover:-translate-y-0.5" title="Delete Event">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Clean Linear-style Data Table -->
        <div class="bg-white border border-gray-200 rounded-3xl shadow-sm overflow-hidden mb-12 animate-fade-in-up" style="animation-delay: 0.5s;">
            <div class="px-8 py-5 border-b border-gray-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="text-lg font-bold text-gray-900 tracking-tight">Content Schedule</h3>
                <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-bold">{{ $totalEvents }} Events</span>
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
                                <span class="inline-flex items-center px-2 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider border
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
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                            {{ $event->product ?? $event->product_focus }}
                                        </span>
                                    @endif
                                    @if($event->format)
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                            {{ $event->format }}
                                        </span>
                                    @endif
                                    @if($event->platform)
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-semibold bg-gray-100 text-gray-700 border border-gray-200">
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
                            <td colspan="5" class="px-5 py-16 text-center">
                                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 text-gray-400 mb-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                </div>
                                <h3 class="text-sm font-semibold text-gray-800">No events found</h3>
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
        @if((!isset($filter) || $filter === 'Global Events') && $globalEventsRaw->count() > 0)
        <div class="mt-8">
            <h2 class="text-lg font-bold text-gray-900 mb-4 tracking-tight">Global Calendar & Observances</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($globalEventsRaw as $month => $eventsList)
                    <div class="bg-white border border-gray-200 rounded-2xl shadow-xs overflow-hidden flex flex-col hover:border-gray-300 transition-colors">
                        <div class="px-5 py-3 border-b border-gray-100 bg-slate-50">
                            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider">{{ $month }}</h3>
                        </div>
                        <div class="p-5 flex-1">
                            <ul class="space-y-3.5">
                                @foreach($eventsList as $eventItem)
                                    <li class="flex items-start">
                                        <div class="min-w-[4rem] shrink-0 pt-0.5">
                                            @php
                                                $dayStr = \Carbon\Carbon::parse($eventItem->event_date)->format('jS');
                                                preg_match('/(\d+)(st|nd|rd|th)?/', $dayStr, $matches);
                                                $dayNum = $matches[1] ?? \Carbon\Carbon::parse($eventItem->event_date)->format('j');
                                                $suffix = $matches[2] ?? '';
                                            @endphp
                                            <span class="text-sm font-bold text-gray-900">{{ $dayNum }}<sup class="text-[10px] text-gray-400 font-semibold">{{ $suffix }}</sup></span>
                                        </div>
                                        <div class="text-sm text-gray-600 font-medium pl-3 border-l-2 border-gray-200">
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

        <!-- Global Event Modal -->
        <div x-show="showGlobalModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div x-show="showGlobalModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-900/40 backdrop-blur-xs transition-opacity" @click="showGlobalModal = false"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="showGlobalModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
                     class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-xl border border-gray-200">
                    
                    <div class="bg-amber-50/70 px-8 py-6 border-b border-gray-100 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600 border border-amber-200 shadow-xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 tracking-tight">Add Global Event</h3>
                                <p class="text-xs text-gray-500 font-medium">Create a company-wide or observance event.</p>
                            </div>
                        </div>
                        <button @click="showGlobalModal = false" type="button" class="text-gray-400 hover:text-gray-700 bg-white hover:bg-gray-100 p-2 rounded-full border border-gray-200 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    
                    <div class="p-8 bg-white">
                        <form action="{{ route('events.global.store') }}" method="POST" class="space-y-5">
                            @csrf
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Event Date*</label>
                                <input type="date" name="event_date" required class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none transition-all shadow-xs font-medium">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Event Title*</label>
                                <input type="text" name="content_title" required class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none transition-all shadow-xs placeholder-gray-400 font-medium" placeholder="e.g. World Tourism Day">
                            </div>
                            
                            <div class="pt-6 flex items-center justify-end gap-3 mt-6 border-t border-gray-100">
                                <button type="button" @click="showGlobalModal = false" class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">
                                    Cancel
                                </button>
                                <button type="submit" class="px-6 py-2.5 text-sm font-bold text-white bg-amber-600 border border-transparent rounded-xl hover:bg-amber-700 shadow-md shadow-amber-500/20 transition-all focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2">
                                    Create Global Event
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
