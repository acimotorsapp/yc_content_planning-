<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-white leading-tight">
            {{ __('Dashboard') }}
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
        }" class="max-w-7xl mx-auto pb-12">

        <!-- Alerts -->
        @if(session('success'))
            <div class="bg-white dark:bg-[#111] border border-green-500/30 p-4 mb-6 rounded-xl shadow-sm flex items-center" role="alert">
                <svg class="w-5 h-5 text-green-400 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <p class="text-green-400 text-sm font-medium">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Dashboard Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">{{ $filter ?? 'Overview' }}</h1>
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Manage and track your content pipeline.</p>
            </div>
            
            @if(auth()->user()->role !== 'super_admin')
            <button @click="showModal = true" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-bold text-black bg-white rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-[#0a0a0a] focus:ring-white transition-all shadow-[0_0_15px_rgba(255,255,255,0.1)]">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                New Event
            </button>
            @else
                <div class="flex items-center gap-3">
                    <span class="px-4 py-2 bg-purple-500/10 text-purple-400 border border-purple-500/20 rounded-lg text-sm font-bold shadow-[0_0_15px_rgba(168,85,247,0.15)]">Super Admin Mode</span>
                    @if(isset($filter))
                    <button @click="showModal = true" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-bold text-black bg-white rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-[#0a0a0a] focus:ring-white transition-all shadow-[0_0_15px_rgba(255,255,255,0.1)]">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        New {{ str_replace(' Events', '', str_replace(' Team', '', $filter)) }}
                    </button>
                    @endif
                </div>
            @endif
        </div>

        <!-- FullCalendar Container -->
        <div class="bg-white dark:bg-[#111] border border-gray-200 dark:border-white/5 rounded-xl shadow-sm p-5 mb-8 overflow-hidden">
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
                            ? 'bg-blue-500/10 text-blue-400 border-blue-500/20' 
                            : 'bg-teal-500/10 text-teal-400 border-teal-500/20';

                        var pillarHtml = '';
                        if (arg.event.extendedProps.aipePillar !== 'N/A') {
                            pillarHtml = `<span class="inline-block mt-1 px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-yellow-500/20 text-yellow-500 border border-yellow-500/30">
                                ${arg.event.extendedProps.aipePillar}
                            </span>`;
                        }

                        var html = `
                            <div class="p-1.5 w-full text-xs border rounded-md shadow-sm overflow-hidden flex flex-col gap-0.5 ${teamClass}" style="white-space: normal; line-height: 1.2;">
                                <div class="font-bold" style="word-break: break-word;">${arg.event.title}</div>
                                <div class="text-[10px] opacity-80 mt-0.5">
                                    <svg class="w-3 h-3 inline-block mr-0.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    ${arg.event.extendedProps.userName}
                                </div>
                                ${pillarHtml}
                            </div>
                        `;

                        return { html: html };
                    },
                    eventClassNames: function(arg) {
                        return ['!bg-transparent', '!border-none', '!p-0', 'hover:opacity-90', 'transition-opacity'];
                    }
                });
                calendar.render();
            });
        </script>

        <style>
            /* Custom styling for FullCalendar to match dark/light theme */
            .fc {
                --fc-border-color: rgba(255, 255, 255, 0.05);
                --fc-button-bg-color: #3b82f6;
                --fc-button-border-color: #3b82f6;
                --fc-button-hover-bg-color: #2563eb;
                --fc-button-hover-border-color: #2563eb;
                --fc-button-active-bg-color: #1d4ed8;
                --fc-button-active-border-color: #1d4ed8;
                --fc-today-bg-color: rgba(59, 130, 246, 0.1);
            }
            .fc .fc-toolbar-title {
                font-size: 1.25rem;
                font-weight: 700;
                color: inherit;
            }
            .fc .fc-daygrid-day-number, .fc .fc-col-header-cell-cushion {
                color: inherit;
                text-decoration: none;
                font-weight: 600;
            }
            .fc .fc-col-header-cell-cushion {
                padding: 12px 4px;
                text-transform: uppercase;
                font-size: 0.75rem;
                letter-spacing: 0.05em;
                color: #6b7280;
            }
            .dark .fc .fc-col-header-cell-cushion {
                color: #9ca3af;
            }
            .dark .fc {
                color: #e5e7eb;
                --fc-border-color: rgba(255, 255, 255, 0.1);
            }
            .fc-event {
                cursor: pointer;
            }
        </style>

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

                    @if(auth()->user()->role === 'product_team' || (auth()->user()->role === 'super_admin' && isset($filter) && $filter === 'Product Team Events'))
                        <div class="bg-gray-50 dark:bg-[#161616] px-8 py-6 border-b border-gray-200 dark:border-white/5 flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-blue-500/10 border border-blue-500/20 rounded-xl flex items-center justify-center text-blue-400 shadow-[0_0_15px_rgba(59,130,246,0.15)]">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-white tracking-tight">New Product Event</h3>
                                    <p class="text-sm text-gray-500 font-medium mt-0.5">Schedule new product content</p>
                                </div>
                            </div>
                            <button @click="showModal = false" class="text-gray-500 hover:text-white bg-white/5 hover:bg-white/10 p-2.5 rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-white/20">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                        
                        <div class="px-8 py-6 bg-white dark:bg-[#111]">
                            <form action="{{ route('events.product.store') }}" method="POST" class="space-y-5">
                                @csrf
                                <div class="grid grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">Publish Date*</label>
                                        <input type="date" name="event_date" required class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-white rounded-lg px-4 py-2.5 focus:bg-[#222] focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all shadow-sm">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">Shoot Date</label>
                                        <input type="date" name="shoot_date" class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-white rounded-lg px-4 py-2.5 focus:bg-[#222] focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all shadow-sm">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">Content Title*</label>
                                    <input type="text" name="content_title" required class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-white rounded-lg px-4 py-2.5 focus:bg-[#222] focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all shadow-sm placeholder-gray-600" placeholder="e.g. Life Style Review">
                                </div>
                                <div class="grid grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">Product</label>
                                        <select name="product" class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-white rounded-lg px-4 py-2.5 focus:bg-[#222] focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all shadow-sm">
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
                                        <select name="platform" class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-white rounded-lg px-4 py-2.5 focus:bg-[#222] focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all shadow-sm">
                                            <option value="">Select Platform</option>
                                            <option value="Facebook">Facebook</option>
                                            <option value="Option 2">Option 2</option>
                                            <option value="YRC Page">YRC Page</option>
                                            <option value="Yamaha Lovers BD">Yamaha Lovers BD</option>
                                        </select>
                                    </div>
                                </div>
                                <div><label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">Content Objective</label><input type="text" name="content_objective" class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-white rounded-lg px-4 py-2.5 focus:bg-[#222] focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all shadow-sm placeholder-gray-600" placeholder="Briefly describe the goal"></div>
                                <div class="grid grid-cols-3 gap-5">
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">A.I.P.E Pillar</label>
                                        <select name="aipe_pillar" class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-white rounded-lg px-4 py-2.5 focus:bg-[#222] focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all shadow-sm">
                                            <option value="">Select Pillar</option>
                                            <option value="Awareness">Awareness</option>
                                            <option value="Awareness+Interest">Awareness+Interest</option>
                                            <option value="Interest">Interest</option>
                                            <option value="Interest+Experience">Interest+Experience</option>
                                            <option value="Experience">Experience</option>
                                        </select>
                                    </div>
                                    <div><label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">Color</label><input type="text" name="color_concern" class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-white rounded-lg px-4 py-2.5 focus:bg-[#222] focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all shadow-sm"></div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">Format</label>
                                        <select name="format" class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-white rounded-lg px-4 py-2.5 focus:bg-[#222] focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all shadow-sm">
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
                                    <div><label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">Budget</label><input type="text" name="boosting_budget" class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-white rounded-lg px-4 py-2.5 focus:bg-[#222] focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all shadow-sm"></div>
                                    <div><label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">Drive Link</label><input type="text" name="drive_link" class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-white rounded-lg px-4 py-2.5 focus:bg-[#222] focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all shadow-sm placeholder-gray-600" placeholder="https://drive.google.com/..."></div>
                                </div>
                                <div><label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">Remarks</label><input type="text" name="remarks" class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-white rounded-lg px-4 py-2.5 focus:bg-[#222] focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all shadow-sm"></div>
                                
                                <div class="pt-6 flex items-center justify-end gap-3 mt-6 border-t border-gray-200 dark:border-white/5">
                                    <button type="button" @click="showModal = false" class="px-5 py-2.5 text-sm font-medium text-gray-500 dark:text-gray-400 bg-transparent border border-gray-300 dark:border-white/10 rounded-lg hover:bg-gray-100 dark:hover:bg-white/5 hover:text-white transition-colors focus:outline-none focus:ring-2 focus:ring-white/20">
                                        Cancel
                                    </button>
                                    <button type="submit" class="px-6 py-2.5 text-sm font-bold text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-500 shadow-[0_0_15px_rgba(59,130,246,0.3)] transition-all focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-[#111]">
                                        Create Event
                                    </button>
                                </div>
                            </form>
                        </div>

                    @elseif(auth()->user()->role === 'digital_team' || (auth()->user()->role === 'super_admin' && isset($filter) && $filter === 'Digital Team Events'))
                        <div class="bg-gray-50 dark:bg-[#161616] px-8 py-6 border-b border-gray-200 dark:border-white/5 flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-teal-500/10 border border-teal-500/20 rounded-xl flex items-center justify-center text-teal-400 shadow-[0_0_15px_rgba(20,184,166,0.15)]">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-white tracking-tight">New Digital Event</h3>
                                    <p class="text-sm text-gray-500 font-medium mt-0.5">Schedule digital campaigns</p>
                                </div>
                            </div>
                            <button @click="showModal = false" class="text-gray-500 hover:text-white bg-white/5 hover:bg-white/10 p-2.5 rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-white/20">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                        
                        <div class="px-8 py-6 bg-white dark:bg-[#111]">
                            <form action="{{ route('events.digital.store') }}" method="POST" class="space-y-5">
                                @csrf
                                <div class="grid grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">Event Date*</label>
                                        <input type="date" name="event_date" required class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-white rounded-lg px-4 py-2.5 focus:bg-[#222] focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition-all shadow-sm">
                                    </div>
                                    <div><label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">Post No.</label><input type="text" name="post_no" class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-white rounded-lg px-4 py-2.5 focus:bg-[#222] focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition-all shadow-sm placeholder-gray-600" placeholder="e.g. 1"></div>
                                </div>
                                <div class="grid grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">Product Focus</label>
                                        <select name="product_focus" class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-white rounded-lg px-4 py-2.5 focus:bg-[#222] focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition-all shadow-sm">
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
                                        <select name="aipe_pillar" class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-white rounded-lg px-4 py-2.5 focus:bg-[#222] focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition-all shadow-sm">
                                            <option value="">Select Pillar</option>
                                            <option value="Awareness">Awareness</option>
                                            <option value="Awareness+Interest">Awareness+Interest</option>
                                            <option value="Interest">Interest</option>
                                            <option value="Interest+Experience">Interest+Experience</option>
                                            <option value="Experience">Experience</option>
                                        </select>
                                    </div>
                                </div>
                                <div><label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">Content Objective</label><input type="text" name="content_objective" class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-white rounded-lg px-4 py-2.5 focus:bg-[#222] focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition-all shadow-sm placeholder-gray-600" placeholder="Briefly describe the goal"></div>
                                <div class="grid grid-cols-2 gap-5">
                                    <div><label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">Asset/Drive Link</label><input type="text" name="drive_link" class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-white rounded-lg px-4 py-2.5 focus:bg-[#222] focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition-all shadow-sm placeholder-gray-600" placeholder="https://drive.google.com/..."></div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">Format</label>
                                        <select name="format" class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-white rounded-lg px-4 py-2.5 focus:bg-[#222] focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition-all shadow-sm">
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
                                    <div><label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">Budget</label><input type="text" name="boosting_budget" class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-white rounded-lg px-4 py-2.5 focus:bg-[#222] focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition-all shadow-sm"></div>
                                    <div><label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">Remarks</label><input type="text" name="remarks" class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-white rounded-lg px-4 py-2.5 focus:bg-[#222] focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition-all shadow-sm"></div>
                                </div>
                                
                                <div class="pt-6 flex items-center justify-end gap-3 mt-6 border-t border-gray-200 dark:border-white/5">
                                    <button type="button" @click="showModal = false" class="px-5 py-2.5 text-sm font-medium text-gray-500 dark:text-gray-400 bg-transparent border border-gray-300 dark:border-white/10 rounded-lg hover:bg-gray-100 dark:hover:bg-white/5 hover:text-white transition-colors focus:outline-none focus:ring-2 focus:ring-white/20">
                                        Cancel
                                    </button>
                                    <button type="submit" class="px-6 py-2.5 text-sm font-bold text-white bg-teal-600 border border-transparent rounded-lg hover:bg-teal-500 shadow-[0_0_15px_rgba(20,184,166,0.3)] transition-all focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-[#111]">
                                        Create Event
                                    </button>
                                </div>
                            </form>
                        </div>
                    @elseif(auth()->user()->role === 'super_admin' && isset($filter) && $filter === 'Global Events')
                        <div class="bg-gray-50 dark:bg-[#161616] px-8 py-6 border-b border-gray-200 dark:border-white/5 flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-yellow-500/10 border border-yellow-500/20 rounded-xl flex items-center justify-center text-yellow-400 shadow-[0_0_15px_rgba(234,179,8,0.15)]">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-white tracking-tight">New Global Event</h3>
                                    <p class="text-sm text-gray-500 font-medium mt-0.5">Schedule a global observance or holiday</p>
                                </div>
                            </div>
                            <button @click="showModal = false" class="text-gray-500 hover:text-white bg-white/5 hover:bg-white/10 p-2.5 rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-white/20">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                        
                        <div class="px-8 py-6 bg-white dark:bg-[#111]">
                            <form action="{{ route('events.global.store') }}" method="POST" class="space-y-5">
                                @csrf
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">Event Date*</label>
                                    <input type="date" name="event_date" required class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-white rounded-lg px-4 py-2.5 focus:bg-[#222] focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 outline-none transition-all shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">Event Title*</label>
                                    <input type="text" name="content_title" required class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-white rounded-lg px-4 py-2.5 focus:bg-[#222] focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 outline-none transition-all shadow-sm placeholder-gray-600" placeholder="e.g. World Tourism Day">
                                </div>
                                
                                <div class="pt-6 flex items-center justify-end gap-3 mt-6 border-t border-gray-200 dark:border-white/5">
                                    <button type="button" @click="showModal = false" class="px-5 py-2.5 text-sm font-medium text-gray-500 dark:text-gray-400 bg-transparent border border-gray-300 dark:border-white/10 rounded-lg hover:bg-gray-100 dark:hover:bg-white/5 hover:text-white transition-colors focus:outline-none focus:ring-2 focus:ring-white/20">
                                        Cancel
                                    </button>
                                    <button type="submit" class="px-6 py-2.5 text-sm font-bold text-black bg-yellow-400 border border-transparent rounded-lg hover:bg-yellow-300 shadow-[0_0_15px_rgba(234,179,8,0.3)] transition-all focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-[#111]">
                                        Create Global Event
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
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
