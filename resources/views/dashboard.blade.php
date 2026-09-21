<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-lg sm:text-2xl text-gray-900 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    @php
        $dateCounts = $dateCounts ?? (\App\Models\CalendarEvent::selectRaw('event_date, count(*) as count')
            ->groupBy('event_date')
            ->pluck('count', 'event_date')
            ->mapWithKeys(fn($count, $date) => [\Carbon\Carbon::parse($date)->format('Y-m-d') => (int)$count])
            ->all());
    @endphp

    <div x-data="{ 
        showGlobalModal: false, 
        globalDate: '', 
        dateCounts: {{ json_encode($dateCounts) }}, 
        getCount(d) { return (d && this.dateCounts[d]) ? Number(this.dateCounts[d]) : 0; },
        isFullyBooked(d) { return this.getCount(d) >= 6; } 
    }" class="max-w-7xl mx-auto pb-12">

        @if(isset($filter) && $filter === 'Global Events')
        <!-- Global Events Top Header & Action -->
        <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center mb-6 sm:mb-8 gap-3 sm:gap-4 animate-fade-in-up">
            <div class="min-w-0">
                <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">Global Events &amp; Observances</h1>
                <p class="text-gray-500 text-xs sm:text-sm mt-1 font-medium">Manage worldwide events and special company-wide observances.</p>
            </div>
            <button type="button" @click="showGlobalModal = true" class="w-full sm:w-auto shrink-0 inline-flex items-center justify-center px-5 sm:px-6 py-3 text-sm font-bold text-white rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-all shadow-md shadow-amber-500/20 hover:shadow-lg sm:transform sm:hover:-translate-y-0.5 group cursor-pointer">
                <svg class="w-5 h-5 mr-2 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add Global Event
            </button>
        </div>
        @endif

        @if(!isset($filter))
        <!-- Month filter (Super Admin) + Add Event -->
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 sm:gap-4 mb-4 sm:mb-6 animate-fade-in-up">
            @if(auth()->user()->role === 'super_admin')
            @php $monthKey = $month ?? now()->format('Y-m'); @endphp
            <form method="GET" action="{{ route('dashboard') }}" class="w-full sm:w-auto flex flex-col sm:flex-row sm:items-end gap-2 sm:gap-3 bg-white border border-gray-200 rounded-2xl p-3 sm:p-3.5 shadow-sm">
                <div class="min-w-0 flex-1 sm:flex-none">
                    <label for="month-filter" class="block text-[10px] font-extrabold text-gray-500 uppercase tracking-widest mb-1.5">Month filter</label>
                    <input id="month-filter"
                           type="month"
                           name="month"
                           value="{{ $monthKey }}"
                           class="w-full sm:w-52 rounded-xl border-gray-200 text-sm font-bold text-gray-900 focus:border-blue-500 focus:ring-blue-500"
                           onchange="this.form.submit()">
                </div>
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-bold text-white rounded-xl bg-gray-900 hover:bg-gray-800 transition-colors">
                    Apply
                </button>
            </form>
            @endif
            <a href="{{ route('events.create', ['action' => 'create']) }}" class="w-full sm:w-auto inline-flex items-center justify-center px-5 sm:px-6 py-3 text-sm font-bold text-white rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all shadow-md shadow-blue-500/20 hover:shadow-lg sm:transform sm:hover:-translate-y-0.5 group">
                <svg class="w-5 h-5 mr-2 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add New Event
            </a>
        </div>
        @endif



        <div id="month-calendar-print" data-month="{{ $month ?? now()->format('Y-m') }}">
        @if(auth()->user()->role === 'super_admin' && !isset($filter))
            @php
                $monthKey = $month ?? now()->format('Y-m');
                $monthLabel = \Carbon\Carbon::createFromFormat('Y-m', $monthKey)->format('M');
                $currentMonthEvents = $monthEvents ?? $events->filter(function($e) {
                    return $e->event_date && $e->event_date->isCurrentMonth() && $e->event_date->isCurrentYear();
                });
                $total = $currentMonthEvents->count();
                $doneCount = $currentMonthEvents->where('status', 'done')->count();
                $notDoneCount = $total - $doneCount;
                $digital = $currentMonthEvents->where('team_type', 'digital_team')->count();
                $product = $currentMonthEvents->where('team_type', 'product_team')->count();
            @endphp
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 sm:gap-4 mb-6 sm:mb-10 animate-fade-in-up" style="animation-delay: 0.1s;">
                <!-- Total Events -->
                <a href="{{ route('dashboard', ['month' => $monthKey]) }}" class="block bg-white rounded-2xl p-4 border border-gray-200/80 shadow-sm relative overflow-hidden group sm:hover:-translate-y-0.5 transition-all duration-300">
                    <div class="flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5 mb-0.5">
                                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Total</p>
                                <span class="stat-month-badge text-[9px] font-bold text-indigo-500">({{ $monthLabel }})</span>
                            </div>
                            <h3 id="stat-total" class="text-2xl sm:text-3xl font-black text-gray-900 transition-all duration-300">{{ $total }}</h3>
                        </div>
                        <div class="w-9 h-9 sm:w-10 sm:h-10 shrink-0 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 border border-indigo-100 shadow-xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                    </div>
                </a>

                <!-- Done Events -->
                <a href="{{ route('admin.events.done') }}" class="block bg-white rounded-2xl p-4 border border-emerald-200/80 shadow-sm relative overflow-hidden group sm:hover:-translate-y-0.5 transition-all duration-300">
                    <div class="flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5 mb-0.5">
                                <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest">Done</p>
                                <span class="stat-month-badge text-[9px] font-bold text-emerald-500">({{ $monthLabel }})</span>
                            </div>
                            <h3 id="stat-done" class="text-2xl sm:text-3xl font-black text-emerald-600 transition-all duration-300">{{ $doneCount }}</h3>
                        </div>
                        <div class="w-9 h-9 sm:w-10 sm:h-10 shrink-0 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 border border-emerald-100 shadow-xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                    </div>
                </a>

                <!-- Not Done Events -->
                <a href="{{ route('admin.events.not_done') }}" class="block bg-white rounded-2xl p-4 border border-rose-200/80 shadow-sm relative overflow-hidden group sm:hover:-translate-y-0.5 transition-all duration-300">
                    <div class="flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5 mb-0.5">
                                <p class="text-[10px] font-bold text-rose-600 uppercase tracking-widest">Not Done</p>
                                <span class="stat-month-badge text-[9px] font-bold text-rose-500">({{ $monthLabel }})</span>
                            </div>
                            <h3 id="stat-not-done" class="text-2xl sm:text-3xl font-black text-rose-600 transition-all duration-300">{{ $notDoneCount }}</h3>
                        </div>
                        <div class="w-9 h-9 sm:w-10 sm:h-10 shrink-0 rounded-xl bg-rose-50 flex items-center justify-center text-rose-600 border border-rose-100 shadow-xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                </a>

                <!-- Digital Team -->
                <a href="{{ route('admin.events.digital') }}" class="block bg-white rounded-2xl p-4 border border-gray-200/80 shadow-sm relative overflow-hidden group sm:hover:-translate-y-0.5 transition-all duration-300">
                    <div class="flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5 mb-0.5">
                                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Digital</p>
                                <span class="stat-month-badge text-[9px] font-bold text-teal-500">({{ $monthLabel }})</span>
                            </div>
                            <h3 id="stat-digital" class="text-2xl sm:text-3xl font-black text-teal-600 transition-all duration-300">{{ $digital }}</h3>
                        </div>
                        <div class="w-9 h-9 sm:w-10 sm:h-10 shrink-0 rounded-xl bg-teal-50 flex items-center justify-center text-teal-600 border border-teal-100 shadow-xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                        </div>
                    </div>
                </a>

                <!-- Product Team -->
                <a href="{{ route('admin.events.product') }}" class="block bg-white rounded-2xl p-4 border border-gray-200/80 shadow-sm relative overflow-hidden group sm:hover:-translate-y-0.5 transition-all duration-300">
                    <div class="flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5 mb-0.5">
                                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Product</p>
                                <span class="stat-month-badge text-[9px] font-bold text-blue-500">({{ $monthLabel }})</span>
                            </div>
                            <h3 id="stat-product" class="text-2xl sm:text-3xl font-black text-blue-600 transition-all duration-300">{{ $product }}</h3>
                        </div>
                        <div class="w-9 h-9 sm:w-10 sm:h-10 shrink-0 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 border border-blue-100 shadow-xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        </div>
                    </div>
                </a>
            </div>
        @endif

        @if(!isset($filter) || $filter !== 'Global Events')
        <!-- Dashboard Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-5 sm:mb-8 gap-3 sm:gap-4 animate-fade-in-up" style="animation-delay: 0.2s;">
            <div class="min-w-0">
                <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">{{ $filter ?? 'Final Content Calendar' }}</h1>
                <p class="text-gray-500 text-xs sm:text-sm mt-1 font-medium">{{ isset($filter) ? 'Manage and track your upcoming content pipeline.' : 'Plan, prioritize, and finalize content entirely in the platform.' }}</p>
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
        <div class="bg-white border border-gray-200 rounded-2xl sm:rounded-3xl shadow-sm p-3 sm:p-6 lg:p-8 mb-6 sm:mb-12">
            <!-- Colour legend (most useful on small screens where badges are compact) -->
            <div class="flex flex-wrap items-center gap-x-3 gap-y-2 mb-3 sm:mb-4 text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-gray-500">
                <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-400 ring-1 ring-amber-200"></span>Product</span>
                <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-purple-400 ring-1 ring-purple-200"></span>Digital</span>
                <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-sky-400 ring-1 ring-sky-200"></span>Brand</span>
                <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-400 ring-1 ring-emerald-200"></span>Service</span>
                <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-rose-400 ring-1 ring-rose-200"></span>Global</span>
                <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-gradient-to-r from-amber-400 to-purple-400 ring-1 ring-gray-200"></span>Mixed</span>
                <span class="hidden sm:inline text-gray-400 font-semibold normal-case tracking-normal">· Drag an event onto another day to reschedule</span>
            </div>
            <div id="calendar"></div>
        </div>
        </div>

        <!-- FullCalendar Dependencies -->
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
        
        @php
            $formattedEvents = $events->map(function($event) {
                $title = $event->displayTitle();
                $userName = $event->user ? $event->user->name : 'Global Event';
                $shootDate = $event->shoot_date ? $event->shoot_date->format('M d, Y') : null;
                
                return [
                    'id' => $event->id,
                    'title' => $title,
                    'start' => $event->event_date->format('Y-m-d'),
                    'allDay' => true,
                    'editable' => auth()->user()->role === 'super_admin' || auth()->id() === $event->user_id,
                    'extendedProps' => [
                        'userName' => $userName,
                        'aipePillar' => $event->aipe_pillar ?? 'N/A',
                        'teamType' => $event->team_type,
                        'shootDate' => $shootDate,
                        'financialBudget' => $event->financial_budget ?? '0',
                        'boostingBudget' => $event->boosting_budget ?? '0',
                        'status' => $event->status ?? 'not_done'
                    ]
                ];
            })->values();
        @endphp

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var calendarEl = document.getElementById('calendar');
                
                var eventsData = @json($formattedEvents);
                var dateCounts = @json($dateCounts ?? []);
                var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                function isoDate(value) {
                    if (!value) return '';
                    if (typeof value === 'string') return value.slice(0, 10);
                    var y = value.getFullYear();
                    var m = String(value.getMonth() + 1).padStart(2, '0');
                    var d = String(value.getDate()).padStart(2, '0');
                    return y + '-' + m + '-' + d;
                }

                function formatCardDate(iso) {
                    var parts = String(iso).split('-');
                    if (parts.length < 3) return iso;
                    var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                    return months[parseInt(parts[1], 10) - 1] + ' ' + parts[2];
                }

                function countOnDate(iso, exceptId) {
                    var total = dateCounts[iso] ? Number(dateCounts[iso]) : 0;
                    var match = eventsData.find(function(ev) { return String(ev.id) === String(exceptId); });
                    if (match && match.start === iso) total = Math.max(0, total - 1);
                    return total;
                }

                function applyMovedDate(eventId, oldDate, newDate) {
                    if (oldDate && dateCounts[oldDate]) {
                        dateCounts[oldDate] = Math.max(0, Number(dateCounts[oldDate]) - 1);
                    }
                    dateCounts[newDate] = (dateCounts[newDate] ? Number(dateCounts[newDate]) : 0) + 1;

                    eventsData.forEach(function(ev) {
                        if (String(ev.id) === String(eventId)) ev.start = newDate;
                    });

                    var card = document.querySelector('.js-board-card[data-event-id="' + eventId + '"]');
                    if (card) {
                        card.setAttribute('data-date', newDate);
                        var label = card.querySelector('.js-card-date');
                        if (label) label.textContent = formatCardDate(newDate);
                    }

                    if (window.contentCalendar) {
                        var calEvent = window.contentCalendar.getEventById(String(eventId));
                        if (calEvent && calEvent.startStr.slice(0, 10) !== newDate) {
                            calEvent.setStart(newDate, { maintainDuration: true });
                        }
                    }
                }

                window.rescheduleEventDate = function(eventId, newDate, oldDate) {
                    return fetch('/events/' + eventId + '/reschedule', {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ event_date: newDate })
                    }).then(function(response) {
                        return response.json().then(function(data) {
                            if (!response.ok || !data.success) {
                                var message = data.message;
                                if (!message && data.errors && data.errors.event_date) {
                                    message = data.errors.event_date[0];
                                }
                                throw new Error(message || 'Could not reschedule');
                            }
                            applyMovedDate(eventId, oldDate || '', newDate);
                            return data;
                        });
                    });
                };
                
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
                    if (teams.size > 1) {
                        el.classList.add('fc-has-mixed-event-day');
                    } else if (teams.has('digital_team')) {
                        el.classList.add('fc-has-digital-event-day');
                    } else if (teams.has('product_team')) {
                        el.classList.add('fc-has-product-event-day');
                    } else if (teams.has('brand_team')) {
                        el.classList.add('fc-has-brand-event-day');
                    } else if (teams.has('service_team')) {
                        el.classList.add('fc-has-service-event-day');
                    } else if (teams.has('global_team')) {
                        el.classList.add('fc-has-global-event-day');
                    }
                }

                // --- Responsive breakpoints -------------------------------------------------
                function isSmall()  { return window.innerWidth < 768; }   // phones
                function isMedium() { return window.innerWidth < 1024; }  // small tablets

                function toolbarFor(small) {
                    return small
                        ? { left: 'prev,next', center: 'title', right: 'today' }
                        : { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek' };
                }
                function footerFor(small) {
                    return small ? { center: 'dayGridMonth,listMonth' } : false;
                }

                var startedSmall = isSmall();

                function updateMonthStats(currentDate) {
                    if (!currentDate) return;
                    var year = currentDate.getFullYear();
                    var month = currentDate.getMonth(); // 0 to 11

                    var monthEvents = eventsData.filter(function(ev) {
                        if (!ev.start) return false;
                        var parts = ev.start.split('-');
                        if (parts.length < 2) return false;
                        var evY = parseInt(parts[0], 10);
                        var evM = parseInt(parts[1], 10) - 1;
                        return evY === year && evM === month;
                    });

                    var total = monthEvents.length;
                    var done = monthEvents.filter(function(ev) {
                        return ev.extendedProps && ev.extendedProps.status === 'done';
                    }).length;
                    var notDone = total - done;
                    var digital = monthEvents.filter(function(ev) {
                        return ev.extendedProps && ev.extendedProps.teamType === 'digital_team';
                    }).length;
                    var product = monthEvents.filter(function(ev) {
                        return ev.extendedProps && ev.extendedProps.teamType === 'product_team';
                    }).length;

                    var elTotal = document.getElementById('stat-total');
                    var elDone = document.getElementById('stat-done');
                    var elNotDone = document.getElementById('stat-not-done');
                    var elDigital = document.getElementById('stat-digital');
                    var elProduct = document.getElementById('stat-product');

                    if (elTotal) elTotal.textContent = total;
                    if (elDone) elDone.textContent = done;
                    if (elNotDone) elNotDone.textContent = notDone;
                    if (elDigital) elDigital.textContent = digital;
                    if (elProduct) elProduct.textContent = product;

                    var monthName = currentDate.toLocaleString('default', { month: 'short' });
                    var monthBadges = document.querySelectorAll('.stat-month-badge');
                    monthBadges.forEach(function(badge) {
                        badge.textContent = '(' + monthName + ')';
                    });
                }

                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: startedSmall ? 'listMonth' : 'dayGridMonth',
                    @if(!isset($filter) && !empty($month))
                    initialDate: '{{ $month }}-01',
                    @endif
                    height: 'auto',
                    expandRows: true,
                    handleWindowResize: true,
                    dayMaxEvents: startedSmall ? 2 : 6,
                    headerToolbar: toolbarFor(startedSmall),
                    footerToolbar: footerFor(startedSmall),
                    titleFormat: startedSmall
                        ? { year: 'numeric', month: 'short' }
                        : { year: 'numeric', month: 'long' },
                    dayHeaderFormat: { weekday: startedSmall ? 'narrow' : 'short' },
                    buttonText: {
                        today: 'Today',
                        month: 'Month',
                        week: 'Week',
                        day: 'Day',
                        list: 'List'
                    },
                    views: {
                        listMonth: { buttonText: 'List', noEventsContent: 'No events scheduled this month' }
                    },
                    noEventsContent: 'No events scheduled this month',
                    events: eventsData,
                    editable: true,
                    eventStartEditable: true,
                    eventDurationEditable: false,
                    eventDragMinDistance: 6,
                    dragScroll: true,
                    eventDrop: function(info) {
                        var newDate = isoDate(info.event.startStr);
                        var oldDate = isoDate(info.oldEvent ? info.oldEvent.startStr : '');
                        if (!newDate || newDate === oldDate) return;

                        if (countOnDate(newDate, info.event.id) >= 6) {
                            info.revert();
                            Swal.fire({
                                icon: 'error',
                                title: 'Date fully booked',
                                text: 'A maximum of 6 events can be scheduled on ' + newDate + '.',
                                confirmButtonColor: '#2563eb',
                                customClass: { popup: 'rounded-2xl shadow-xl' }
                            });
                            return;
                        }

                        window.rescheduleEventDate(info.event.id, newDate, oldDate).then(function() {
                            var monthKey = newDate.slice(0, 7);
                            @if(!isset($filter) && !empty($month))
                            if (monthKey !== '{{ $month }}') {
                                var url = new URL(window.location.href);
                                url.searchParams.set('month', monthKey);
                                window.location.href = url.toString();
                            }
                            @endif
                        }).catch(function(err) {
                            info.revert();
                            Swal.fire({
                                icon: 'error',
                                title: 'Could not reschedule',
                                text: err.message || 'Please try another date.',
                                confirmButtonColor: '#2563eb',
                                customClass: { popup: 'rounded-2xl shadow-xl' }
                            });
                        });
                    },
                    datesSet: function(dateInfo) {
                        var mid = new Date((dateInfo.start.getTime() + dateInfo.end.getTime()) / 2);
                        var viewType = dateInfo.view.type || '';
                        @if(!isset($filter) && !empty($month))
                        if (viewType === 'dayGridMonth' || viewType === 'listMonth') {
                            var key = mid.getFullYear() + '-' + String(mid.getMonth() + 1).padStart(2, '0');
                            if (key !== '{{ $month }}') {
                                var url = new URL(window.location.href);
                                url.searchParams.set('month', key);
                                url.searchParams.delete('page');
                                window.location.href = url.toString();
                                return;
                            }
                        }
                        @endif
                        updateMonthStats(mid);
                    },
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
                        var props = arg.event.extendedProps;
                        var viewType = arg.view.type;

                        function esc(v) {
                            return String(v == null ? '' : v)
                                .replace(/&/g, '&amp;').replace(/</g, '&lt;')
                                .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
                        }

                        var dotColor = teamType === 'digital_team' ? 'bg-purple-500'
                                     : teamType === 'product_team' ? 'bg-amber-500'
                                     : teamType === 'brand_team'   ? 'bg-sky-500'
                                     : teamType === 'service_team' ? 'bg-emerald-500'
                                     : teamType === 'global_team'  ? 'bg-rose-500'
                                     : 'bg-blue-500';

                        var chipClass = teamType === 'digital_team' ? 'bg-purple-50 text-purple-900 border-purple-200'
                                      : teamType === 'product_team' ? 'bg-amber-50 text-amber-900 border-amber-200'
                                      : teamType === 'brand_team'   ? 'bg-sky-50 text-sky-900 border-sky-200'
                                      : teamType === 'service_team' ? 'bg-emerald-50 text-emerald-900 border-emerald-200'
                                      : teamType === 'global_team'  ? 'bg-rose-50 text-rose-900 border-rose-200'
                                      : 'bg-blue-50 text-blue-900 border-blue-200';

                        var teamLabel = teamType === 'digital_team' ? 'Digital'
                                      : teamType === 'product_team' ? 'Product'
                                      : teamType === 'brand_team'   ? 'Brand'
                                      : teamType === 'service_team' ? 'Service'
                                      : teamType === 'global_team'  ? 'Global'
                                      : 'Event';

                        // List view — the default on phones: one readable, tappable row per event
                        if (viewType.indexOf('list') === 0) {
                            var meta = ['<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-white border border-gray-200 text-gray-700">' + teamLabel + '</span>'];
                            if (props.userName) {
                                meta.push('<span class="text-[11px] font-semibold opacity-80">' + esc(props.userName) + '</span>');
                            }
                            if (props.aipePillar && props.aipePillar !== 'N/A') {
                                meta.push('<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-white border border-gray-200 text-gray-700">' + esc(props.aipePillar) + '</span>');
                            }
                            if (props.shootDate) {
                                meta.push('<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-rose-50 text-rose-700 border border-rose-200">Shoot: ' + esc(props.shootDate) + '</span>');
                            }
                            if (teamType !== 'global_team') {
                                if (props.status === 'done') {
                                    meta.push('<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">✓ Done</span>');
                                } else {
                                    meta.push('<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-rose-100 text-rose-800 border border-rose-300">✕ Not Done</span>');
                                }
                                if (props.financialBudget && props.financialBudget !== '0') {
                                    meta.push('<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-blue-50 text-blue-800 border border-blue-200">Fin: ৳ ' + esc(props.financialBudget) + '</span>');
                                }
                                meta.push('<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-emerald-50 text-emerald-800 border border-emerald-200">Boost: ৳ ' + esc(props.boostingBudget || '0') + '</span>');
                            }
                            return { html:
                                '<div class="py-2 px-3 min-w-0 rounded-xl shadow-xs border ' + chipClass + '" style="margin: -2px 0;">' +
                                    '<div class="font-extrabold text-[14px] leading-snug" style="word-break: break-word;">' + esc(arg.event.title) + '</div>' +
                                    '<div class="flex flex-wrap items-center gap-1.5 mt-1.5">' + meta.join('') + '</div>' +
                                '</div>'
                            };
                        }

                        // Month grid on phones: compact chip so all 7 columns stay legible
                        if (isSmall()) {
                            return { html:
                                '<div class="flex items-center gap-1 px-1 py-0.5 rounded-md border ' + chipClass + '" style="white-space:nowrap; overflow:hidden;">' +
                                    '<span class="w-1.5 h-1.5 rounded-full shrink-0 ' + dotColor + '"></span>' +
                                    '<span class="text-[9px] font-bold leading-tight" style="overflow:hidden; text-overflow:ellipsis;">' + esc(arg.event.title) + '</span>' +
                                '</div>'
                            };
                        }

                        var teamClass = '';
                        var teamBadge = '';

                        if (teamType === 'digital_team') {
                            teamClass = 'bg-purple-50 text-purple-900 border-purple-200 shadow-xs';
                            teamBadge = '<span class="px-1.5 py-0.5 rounded text-[8px] font-bold uppercase tracking-wider bg-purple-100 text-purple-800 border border-purple-200">Digital</span>';
                        } else if (teamType === 'product_team') {
                            teamClass = 'bg-amber-50 text-amber-900 border-amber-200 shadow-xs';
                            teamBadge = '<span class="px-1.5 py-0.5 rounded text-[8px] font-bold uppercase tracking-wider bg-amber-100 text-amber-800 border border-amber-200">Product</span>';
                        } else if (teamType === 'brand_team') {
                            teamClass = 'bg-sky-50 text-sky-900 border-sky-200 shadow-xs';
                            teamBadge = '<span class="px-1.5 py-0.5 rounded text-[8px] font-bold uppercase tracking-wider bg-sky-100 text-sky-800 border border-sky-200">Brand</span>';
                        } else if (teamType === 'service_team') {
                            teamClass = 'bg-emerald-50 text-emerald-900 border-emerald-200 shadow-xs';
                            teamBadge = '<span class="px-1.5 py-0.5 rounded text-[8px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200">Service</span>';
                        } else if (teamType === 'global_team') {
                            teamClass = 'bg-rose-50 text-rose-900 border-rose-200 shadow-xs';
                            teamBadge = '<span class="px-1.5 py-0.5 rounded text-[8px] font-bold uppercase tracking-wider bg-rose-100 text-rose-800 border border-rose-200">Global</span>';
                        } else {
                            teamClass = 'bg-blue-50 text-blue-900 border-blue-200 shadow-xs';
                        }

                        var statusBadge = '';
                        if (teamType !== 'global_team') {
                            if (arg.event.extendedProps.status === 'done') {
                                statusBadge = '<span class="px-1.5 py-0.5 rounded text-[8px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">✓ Done</span>';
                            } else {
                                statusBadge = '<span class="px-1.5 py-0.5 rounded text-[8px] font-extrabold bg-rose-100 text-rose-800 border border-rose-300">✕ Not Done</span>';
                            }
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

                        var budgetHtml = '';
                        if (teamType !== 'global_team') {
                            var bVal = arg.event.extendedProps.boostingBudget || '0';
                            var fVal = arg.event.extendedProps.financialBudget || '0';
                            var fHtml = (fVal && fVal !== '0') ? `
                                <div class="mt-1 px-2 py-0.5 rounded-md bg-blue-50 text-blue-800 border border-blue-200 font-bold text-[9px] flex items-center justify-between shadow-2xs">
                                    <span>Fin Budget:</span>
                                    <span class="font-extrabold text-blue-700">৳ ${fVal}</span>
                                </div>` : '';
                            budgetHtml = `
                                ${fHtml}
                                <div class="mt-1 px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-800 border border-emerald-200 font-bold text-[9px] flex items-center justify-between shadow-2xs">
                                    <span>Boost:</span>
                                    <span class="font-extrabold text-emerald-700">৳ ${bVal}</span>
                                </div>
                            `;
                        }

                        var html = `
                            <div class="px-2.5 py-2 w-full border rounded-xl shadow-xs hover:shadow-md transition-all duration-200 hover:-translate-y-0.5 flex flex-col gap-0.5 ${teamClass}" style="white-space: normal; line-height: 1.4;">
                                <div class="flex items-center justify-between gap-1 mb-0.5">
                                    <div class="flex items-center gap-1">${teamBadge} ${statusBadge}</div>
                                </div>
                                <div class="font-extrabold text-[13px] leading-tight" style="word-break: break-word; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    ${arg.event.title}
                                </div>
                                <div class="text-[10px] text-gray-500 mt-0.5 flex items-center font-bold tracking-wide uppercase">
                                    <svg class="w-3 h-3 inline-block mr-1 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    ${arg.event.extendedProps.userName}
                                </div>
                                ${pillarHtml}
                                ${shootHtml}
                                ${budgetHtml}
                            </div>
                        `;

                        return { html: html };
                    },
                    eventClassNames: function(arg) {
                        var classes = ['!bg-transparent', '!border-none', '!p-0', 'hover:opacity-95', 'transition-all'];
                        if (arg.event.startEditable !== false) classes.push('fc-event-draggable');
                        return classes;
                    },
                    eventClick: function(info) {
                        window.location.href = '/events/' + info.event.id;
                    }
                });
                calendar.render();
                window.contentCalendar = calendar;

                // Re-shape the calendar when the viewport crosses the phone breakpoint
                var wasSmall = startedSmall;
                var resizeTimer = null;

                window.addEventListener('resize', function() {
                    clearTimeout(resizeTimer);
                    resizeTimer = setTimeout(function() {
                        var nowSmall = isSmall();
                        if (nowSmall === wasSmall) return;
                        wasSmall = nowSmall;

                        calendar.setOption('headerToolbar', toolbarFor(nowSmall));
                        calendar.setOption('footerToolbar', footerFor(nowSmall));
                        calendar.setOption('dayMaxEvents', nowSmall ? 2 : 6);
                        calendar.setOption('dayHeaderFormat', { weekday: nowSmall ? 'narrow' : 'short' });
                        calendar.setOption('titleFormat', nowSmall
                            ? { year: 'numeric', month: 'short' }
                            : { year: 'numeric', month: 'long' });

                        var current = calendar.view.type;
                        if (nowSmall && current === 'dayGridMonth') {
                            calendar.changeView('listMonth');
                        } else if (!nowSmall && (current === 'listMonth' || current.indexOf('list') === 0)) {
                            calendar.changeView('dayGridMonth');
                        }
                        calendar.updateSize();
                    }, 180);
                });
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
            .fc-event-draggable,
            .fc-event-draggable .fc-event-main { cursor: grab; }
            .fc-event-dragging,
            .fc-event-dragging .fc-event-main { cursor: grabbing !important; }
            .fc-daygrid-day.board-date-target {
                outline: 2px dashed #60a5fa;
                outline-offset: -4px;
                background-color: #eff6ff !important;
            }
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

            /* BRAND EVENTS DATES: LIGHT SKY */
            .fc-daygrid-day.fc-has-brand-event-day,
            .fc-has-brand-event-day,
            .fc-has-brand-event-day .fc-daygrid-day-frame {
                background-color: #f0f9ff !important;
            }
            .fc-has-brand-event-day .fc-daygrid-day-frame {
                border: 1px solid #bae6fd !important;
            }
            .fc-has-brand-event-day:hover .fc-daygrid-day-frame {
                background-color: #e0f2fe !important;
                border-color: #7dd3fc !important;
            }

            /* SERVICE EVENTS DATES: LIGHT EMERALD */
            .fc-daygrid-day.fc-has-service-event-day,
            .fc-has-service-event-day,
            .fc-has-service-event-day .fc-daygrid-day-frame {
                background-color: #ecfdf5 !important;
            }
            .fc-has-service-event-day .fc-daygrid-day-frame {
                border: 1px solid #a7f3d0 !important;
            }
            .fc-has-service-event-day:hover .fc-daygrid-day-frame {
                background-color: #d1fae5 !important;
                border-color: #6ee7b7 !important;
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
            .fc .fc-day-today:not(.fc-has-product-event-day):not(.fc-has-digital-event-day):not(.fc-has-mixed-event-day):not(.fc-has-global-event-day):not(.fc-has-brand-event-day):not(.fc-has-service-event-day) .fc-daygrid-day-frame {
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
            .fc-day-past:not(.fc-has-product-event-day):not(.fc-has-digital-event-day):not(.fc-has-mixed-event-day):not(.fc-has-global-event-day):not(.fc-has-brand-event-day):not(.fc-has-service-event-day) {
                opacity: 0.8;
            }

            /* ---------- List view (the default on phones) ---------- */
            .fc .fc-list {
                border: 1px solid #e2e8f0 !important;
                border-radius: 1rem;
                overflow: hidden;
                background: #ffffff;
            }
            .fc .fc-list-day-cushion {
                background-color: #f8fafc !important;
                padding: 10px 14px !important;
            }
            .fc .fc-list-day-text,
            .fc .fc-list-day-side-text {
                font-weight: 800;
                font-size: 0.78rem;
                color: #0f172a;
                text-decoration: none;
            }
            .fc .fc-list-event { cursor: pointer; }
            .fc .fc-list-event:hover td { background-color: #f8fafc !important; }
            .fc .fc-list-event-time { display: none; }
            .fc .fc-list-event-graphic { padding-left: 14px !important; padding-right: 6px !important; vertical-align: top; }
            .fc .fc-list-event-dot { border-width: 4px; margin-top: 8px; }
            .fc .fc-list-event-title { padding: 8px 14px 8px 4px !important; }
            .fc .fc-list-empty {
                background: #ffffff;
                color: #64748b;
                font-weight: 600;
                padding: 2.5rem 1rem;
            }

            /* The mini popover shown by the "+N more" link */
            .fc .fc-popover {
                max-width: 92vw;
                border-radius: 1rem;
                border: 1px solid #e2e8f0;
                box-shadow: 0 20px 40px -12px rgb(0 0 0 / 0.25);
                z-index: 45;
            }
            .fc .fc-popover-header { border-radius: 1rem 1rem 0 0; background: #f8fafc; font-weight: 800; }
            .fc .fc-popover-body { max-height: 55vh; overflow-y: auto; }

            /* ---------- Tablet ---------- */
            @media (max-width: 1023px) {
                .fc .fc-toolbar-title { font-size: 1.2rem; }
                .fc .fc-button-primary { padding: 0.45rem 0.85rem !important; font-size: 0.8rem !important; }
                .fc-daygrid-day-frame { min-height: 92px; }
            }

            /* ---------- Phones ---------- */
            @media (max-width: 767px) {
                .fc .fc-toolbar.fc-header-toolbar {
                    display: flex;
                    flex-wrap: nowrap;
                    align-items: center;
                    justify-content: space-between;
                    gap: 0.4rem;
                    margin-bottom: 0.75rem !important;
                }
                .fc .fc-toolbar.fc-footer-toolbar {
                    margin-top: 0.75rem !important;
                    justify-content: center;
                }
                .fc .fc-toolbar-chunk { display: flex; align-items: center; min-width: 0; }
                .fc .fc-toolbar-title {
                    font-size: 0.98rem;
                    text-align: center;
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                }
                .fc .fc-button-primary {
                    padding: 0.4rem 0.6rem !important;
                    font-size: 0.72rem !important;
                    margin-left: 0.2rem !important;
                    border-radius: 0.6rem !important;
                }
                .fc .fc-button-primary:hover { transform: none; }
                .fc .fc-icon { font-size: 1rem; }

                .fc .fc-scrollgrid { border-radius: 0.85rem !important; }
                .fc-daygrid-day-frame {
                    min-height: 62px;
                    margin: 1px;
                    border-radius: 0.4rem;
                }
                .fc .fc-daygrid-day-number {
                    padding: 4px 5px;
                    font-size: 0.7rem;
                    font-weight: 700;
                }
                .fc .fc-col-header-cell-cushion {
                    padding: 8px 1px;
                    font-size: 0.6rem;
                    letter-spacing: 0;
                }
                .fc .fc-daygrid-day-events { margin-bottom: 2px; }
                .fc .fc-daygrid-event-harness { margin-top: 1px !important; }
                .fc-event { margin-top: 1px; margin-bottom: 1px; border-radius: 0.35rem; }
                .fc .fc-daygrid-more-link {
                    font-size: 0.58rem;
                    font-weight: 800;
                    padding: 0 2px;
                    color: #2563eb;
                }
                /* Today ring is thinner so it doesn't eat the tiny cell */
                .fc .fc-day-today .fc-daygrid-day-frame { border-width: 1.5px !important; }
            }

            /* Very small phones (≤360px) */
            @media (max-width: 380px) {
                .fc .fc-toolbar-title { font-size: 0.88rem; }
                .fc .fc-button-primary { padding: 0.35rem 0.5rem !important; font-size: 0.66rem !important; }
                .fc-daygrid-day-frame { min-height: 54px; }
            }
        </style>

        @php
            $totalEvents = (isset($monthEvents) && !isset($filter)) ? $monthEvents->count() : $events->count();
            $upcomingEvents = $events->where('event_date', '>=', now()->startOfDay())->take(5);
        @endphp

        @if(!isset($filter))
            @include('dashboard.content-board')
        @endif

        <!-- Upcoming Events Table is replaced by the Trello board on the main calendar -->
        @if(false && !isset($filter) && $upcomingEvents->count() > 0)
        <div class="bg-white border border-gray-200 rounded-2xl sm:rounded-3xl shadow-sm overflow-hidden mb-6 sm:mb-12 animate-fade-in-up" style="animation-delay: 0.4s;">
            <div class="px-4 sm:px-8 py-4 sm:py-5 border-b border-gray-100 flex flex-wrap justify-between items-center gap-2 sm:gap-3 bg-slate-50/50">
                <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 shrink-0 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 border border-blue-100 shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <h3 class="text-base sm:text-xl font-bold text-gray-900 tracking-tight truncate">Upcoming Events</h3>
                </div>
                <span class="px-3 sm:px-3.5 py-1 bg-blue-50 text-blue-600 border border-blue-200 rounded-full text-[10px] sm:text-xs font-bold uppercase tracking-wider shrink-0">Next {{ $upcomingEvents->count() }} Events</span>
            </div>

            <!-- Mobile: stacked cards -->
            <div class="md:hidden divide-y divide-gray-100">
                @foreach($upcomingEvents as $event)
                <div class="p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-1.5 mb-1.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[9px] font-bold uppercase tracking-wider border
                                    {{ $event->teamBadgeClasses() }}">
                                    {{ $event->teamLabel() }}
                                </span>
                                <span class="text-[11px] font-bold text-gray-900">{{ $event->event_date->format('M d, Y') }}</span>
                                <span class="text-[10px] font-semibold text-blue-600 uppercase tracking-wider">{{ $event->event_date->format('D') }}</span>
                            </div>
                            <div class="text-sm font-bold text-gray-900 leading-snug break-words">
                                <x-editable-title :event="$event" />
                            </div>
                            <div class="text-xs text-gray-500 mt-1 font-medium line-clamp-2">
                                {{ $event->content_objective ?? 'No objective specified' }}
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0">
                            <a href="{{ route('events.show', $event) }}" class="inline-flex items-center justify-center p-2 rounded-xl bg-gray-100 text-gray-600 active:bg-blue-600 active:text-white transition-colors" title="View Event">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </a>
                            @if(auth()->id() === $event->user_id || auth()->user()->role === 'super_admin')
                            <a href="{{ route('events.edit', $event) }}" class="inline-flex items-center justify-center p-2 rounded-xl bg-gray-100 text-gray-600 active:bg-blue-600 active:text-white transition-colors" title="Edit Event">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </a>
                            <form action="{{ route('events.destroy', $event) }}" method="POST" class="inline delete-form" data-confirm="Are you sure you want to delete this event?">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center justify-center p-2 rounded-xl bg-gray-100 text-gray-600 active:bg-red-600 active:text-white transition-colors" title="Delete Event">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-1.5 mt-2.5">
                        @if($event->shoot_date)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-rose-50 text-rose-700 border border-rose-200">
                                Shoot: {{ $event->shoot_date->format('M d, Y') }}
                            </span>
                        @endif
                        @if($event->product ?? $event->product_focus)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-gray-100 text-gray-700 border border-gray-200">{{ $event->product ?? $event->product_focus }}</span>
                        @endif
                        @if($event->format)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-gray-100 text-gray-700 border border-gray-200">{{ $event->format }}</span>
                        @endif
                    </div>

                    <!-- Status row -->
                    <div class="flex items-center justify-between gap-2 mt-3 pt-2.5 border-t border-gray-100">
                        <div class="flex items-center gap-1.5">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Status:</span>
                            @if(auth()->user()->role === 'super_admin')
                                <div class="inline-flex items-center p-0.5 rounded-lg bg-gray-100 border border-gray-200/80">
                                    <form action="{{ route('events.update_status', $event) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="done">
                                        <button type="submit" 
                                                class="px-2.5 py-0.5 text-[9px] font-extrabold rounded transition-all {{ $event->status === 'done' ? 'bg-emerald-600 text-white shadow-2xs' : 'text-gray-500 hover:text-emerald-700' }}"
                                                title="Mark as Done">
                                            ✓ Done
                                        </button>
                                    </form>
                                    <form action="{{ route('events.update_status', $event) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="not_done">
                                        <button type="submit" 
                                                class="px-2.5 py-0.5 text-[9px] font-extrabold rounded transition-all {{ $event->status !== 'done' ? 'bg-rose-600 text-white shadow-2xs' : 'text-gray-500 hover:text-rose-700' }}"
                                                title="Mark as Not Done">
                                            ✕ Not Done
                                        </button>
                                    </form>
                                </div>
                            @else
                                @if($event->status === 'done')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Done
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        Not Done
                                    </span>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Desktop / tablet: table -->
            <div class="hidden md:block overflow-x-auto nice-scroll">
                <table class="w-full text-left border-collapse min-w-[820px]">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-gray-100">
                            <th class="px-8 py-4 text-[10px] font-extrabold text-gray-500 uppercase tracking-widest w-40">Date</th>
                            <th class="px-8 py-4 text-[10px] font-extrabold text-gray-500 uppercase tracking-widest w-32">Team</th>
                            <th class="px-8 py-4 text-[10px] font-extrabold text-gray-500 uppercase tracking-widest">Title / Objective</th>
                            <th class="px-8 py-4 text-[10px] font-extrabold text-gray-500 uppercase tracking-widest w-48">Tags</th>
                            <th class="px-8 py-4 text-[10px] font-extrabold text-gray-500 uppercase tracking-widest text-center w-36">Status</th>
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
                                    {{ $event->teamBadgeClasses() }}">
                                    {{ $event->teamLabel() }}
                                </span>
                            </td>

                            <!-- Title & Objective Column -->
                            <td class="px-8 py-5">
                                <div class="text-base font-bold text-gray-900 group-hover:text-blue-600 transition-colors">
                                    <x-editable-title :event="$event" />
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

                            <!-- Status Column -->
                            <td class="px-8 py-5 whitespace-nowrap text-center">
                                @if(auth()->user()->role === 'super_admin')
                                    <div class="inline-flex items-center p-0.5 rounded-xl bg-gray-100 border border-gray-200/80 shadow-2xs">
                                        <form action="{{ route('events.update_status', $event) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="done">
                                            <button type="submit" 
                                                    class="px-2.5 py-1 text-[10px] font-extrabold rounded-lg transition-all {{ $event->status === 'done' ? 'bg-emerald-600 text-white shadow-xs' : 'text-gray-500 hover:text-emerald-700 hover:bg-emerald-50 cursor-pointer' }}"
                                                    title="Mark as Done">
                                                ✓ Done
                                            </button>
                                        </form>
                                        <form action="{{ route('events.update_status', $event) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="not_done">
                                            <button type="submit" 
                                                    class="px-2.5 py-1 text-[10px] font-extrabold rounded-lg transition-all {{ $event->status !== 'done' ? 'bg-rose-600 text-white shadow-xs' : 'text-gray-500 hover:text-rose-700 hover:bg-rose-50 cursor-pointer' }}"
                                                    title="Mark as Not Done">
                                                ✕ Not Done
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    @if($event->status === 'done')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Done
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-rose-50 text-rose-700 border border-rose-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            Not Done
                                        </span>
                                    @endif
                                @endif
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

        @if(isset($filter))
        <!-- Clean Linear-style Data Table -->
        <div id="schedule" class="bg-white border border-gray-200 rounded-2xl sm:rounded-3xl shadow-sm overflow-hidden mb-6 sm:mb-12 animate-fade-in-up scroll-mt-24" style="animation-delay: 0.5s;">
            <div class="px-4 sm:px-8 py-4 sm:py-5 border-b border-gray-100 flex flex-wrap justify-between items-center gap-2 bg-slate-50/50">
                <h3 class="text-base sm:text-lg font-bold text-gray-900 tracking-tight">Content Schedule{{ !empty($month) && !isset($filter) ? ' · '.\Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') : '' }}</h3>
                <span id="schedule-count" class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-[10px] sm:text-xs font-bold shrink-0">{{ $totalEvents }} Events</span>
            </div>

            <!-- Mobile: stacked cards -->
            <div class="md:hidden divide-y divide-gray-100">
                @forelse($tableEvents as $event)
                <div class="p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-1.5 mb-1.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[9px] font-bold uppercase tracking-wider border
                                    {{ $event->teamBadgeClasses() }}">
                                    {{ $event->teamLabel() }}
                                </span>
                                <span class="text-[11px] font-bold text-gray-900">{{ $event->event_date->format('M d, Y') }}</span>
                                <span class="text-[10px] font-medium text-gray-500">{{ $event->event_date->format('D') }}</span>
                            </div>
                            <div class="text-sm font-bold text-gray-900 leading-snug break-words">
                                <x-editable-title :event="$event" />
                            </div>
                            <div class="text-xs text-gray-500 mt-1 font-medium line-clamp-2">
                                {{ $event->content_objective ?? 'No objective specified' }}
                            </div>
                        </div>
                        <div class="flex items-center gap-1 shrink-0">
                            <a href="{{ route('events.show', $event) }}" class="inline-flex p-2 rounded-lg text-gray-400 active:text-blue-600 active:bg-blue-50 transition-colors" title="View Event">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </a>
                            @if(auth()->id() === $event->user_id || auth()->user()->role === 'super_admin')
                            <a href="{{ route('events.edit', $event) }}" class="inline-flex p-2 rounded-lg text-gray-400 active:text-blue-600 active:bg-blue-50 transition-colors" title="Edit Event">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </a>
                            <form action="{{ route('events.destroy', $event) }}" method="POST" class="inline delete-form" data-confirm="Are you sure you want to delete this event?">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex p-2 rounded-lg text-gray-400 active:text-red-600 active:bg-red-50 transition-colors" title="Delete Event">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                            @endif
                            @if($event->drive_link)
                            <a href="{{ $event->drive_link }}" target="_blank" class="inline-flex p-2 rounded-lg text-gray-400 active:text-blue-600 active:bg-blue-50 transition-colors" title="Open Link">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                            </a>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-1.5 mt-2.5">
                        @if($event->shoot_date)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-rose-50 text-rose-700 border border-rose-200">
                                Shoot: {{ $event->shoot_date->format('M d, Y') }}
                            </span>
                        @endif
                        @if($event->product ?? $event->product_focus)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-gray-100 text-gray-700 border border-gray-200">{{ $event->product ?? $event->product_focus }}</span>
                        @endif
                        @if($event->format)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-gray-100 text-gray-700 border border-gray-200">{{ $event->format }}</span>
                        @endif
                        @if($event->platform)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-gray-100 text-gray-700 border border-gray-200">{{ $event->platform }}</span>
                        @endif
                    </div>

                    <!-- Status row -->
                    <div class="flex items-center justify-between gap-2 mt-3 pt-2.5 border-t border-gray-100">
                        <div class="flex items-center gap-1.5">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Status:</span>
                            @if(auth()->user()->role === 'super_admin')
                                <div class="inline-flex items-center p-0.5 rounded-lg bg-gray-100 border border-gray-200/80">
                                    <form action="{{ route('events.update_status', $event) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="done">
                                        <button type="submit" 
                                                class="px-2.5 py-0.5 text-[9px] font-extrabold rounded transition-all {{ $event->status === 'done' ? 'bg-emerald-600 text-white shadow-2xs' : 'text-gray-500 hover:text-emerald-700' }}"
                                                title="Mark as Done">
                                            ✓ Done
                                        </button>
                                    </form>
                                    <form action="{{ route('events.update_status', $event) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="not_done">
                                        <button type="submit" 
                                                class="px-2.5 py-0.5 text-[9px] font-extrabold rounded transition-all {{ $event->status !== 'done' ? 'bg-rose-600 text-white shadow-2xs' : 'text-gray-500 hover:text-rose-700' }}"
                                                title="Mark as Not Done">
                                            ✕ Not Done
                                        </button>
                                    </form>
                                </div>
                            @else
                                @if($event->status === 'done')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Done
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        Not Done
                                    </span>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-5 py-12 text-center">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 text-gray-400 mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-800">No events found</h3>
                    <p class="text-xs text-gray-500 mt-1">Get started by creating a new event.</p>
                </div>
                @endforelse
            </div>

            <!-- Desktop / tablet: table -->
            <div class="hidden md:block overflow-x-auto nice-scroll">
                <table class="w-full text-left border-collapse min-w-[860px]">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-gray-100">
                            <th class="px-6 py-4 text-[10px] font-extrabold text-gray-500 uppercase tracking-widest w-32">Date</th>
                            <th class="px-6 py-4 text-[10px] font-extrabold text-gray-500 uppercase tracking-widest w-32">Team</th>
                            <th class="px-6 py-4 text-[10px] font-extrabold text-gray-500 uppercase tracking-widest">Title / Objective</th>
                            <th class="px-6 py-4 text-[10px] font-extrabold text-gray-500 uppercase tracking-widest w-48">Tags</th>
                            <th class="px-6 py-4 text-[10px] font-extrabold text-gray-500 uppercase tracking-widest text-center w-36">Status</th>
                            <th class="px-6 py-4 text-[10px] font-extrabold text-gray-500 uppercase tracking-widest text-right w-16">Link</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($tableEvents as $event)
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
                                    {{ $event->teamBadgeClasses() }}">
                                    {{ $event->teamLabel() }}
                                </span>
                            </td>

                            <!-- Title & Objective Column -->
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900 group-hover:text-blue-600 transition-colors">
                                    <x-editable-title :event="$event" />
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

                            <!-- Status Column -->
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if(auth()->user()->role === 'super_admin')
                                    <div class="inline-flex items-center p-0.5 rounded-xl bg-gray-100 border border-gray-200/80 shadow-2xs">
                                        <form action="{{ route('events.update_status', $event) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="done">
                                            <button type="submit" 
                                                    class="px-2.5 py-1 text-[10px] font-extrabold rounded-lg transition-all {{ $event->status === 'done' ? 'bg-emerald-600 text-white shadow-xs' : 'text-gray-500 hover:text-emerald-700 hover:bg-emerald-50 cursor-pointer' }}"
                                                    title="Mark as Done">
                                                ✓ Done
                                            </button>
                                        </form>
                                        <form action="{{ route('events.update_status', $event) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="not_done">
                                            <button type="submit" 
                                                    class="px-2.5 py-1 text-[10px] font-extrabold rounded-lg transition-all {{ $event->status !== 'done' ? 'bg-rose-600 text-white shadow-xs' : 'text-gray-500 hover:text-rose-700 hover:bg-rose-50 cursor-pointer' }}"
                                                    title="Mark as Not Done">
                                                ✕ Not Done
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    @if($event->status === 'done')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Done
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-amber-50 text-amber-700 border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            Not Done
                                        </span>
                                    @endif
                                @endif
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

            @if($tableEvents->hasPages())
                <div class="px-4 sm:px-8 py-4 border-t border-gray-100 bg-slate-50/50">
                    {{ $tableEvents->links() }}
                </div>
            @endif
        </div>
        @endif

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
            <h2 class="text-base sm:text-lg font-bold text-gray-900 mb-3 sm:mb-4 tracking-tight">Global Calendar &amp; Observances</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
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

            <div class="flex min-h-full items-center justify-center p-3 text-center sm:p-4">
                <div x-show="showGlobalModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
                     class="relative transform w-full overflow-hidden rounded-2xl sm:rounded-3xl bg-white text-left shadow-2xl transition-all my-4 sm:my-8 sm:max-w-xl border border-gray-200 max-h-[92vh] overflow-y-auto nice-scroll">

                    <div class="bg-amber-50/70 px-5 sm:px-8 py-4 sm:py-6 border-b border-gray-100 flex items-center justify-between gap-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600 border border-amber-200 shadow-xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-base sm:text-xl font-bold text-gray-900 tracking-tight">Add Global Event</h3>
                                <p class="hidden sm:block text-xs text-gray-500 font-medium">Create a company-wide or observance event.</p>
                            </div>
                        </div>
                        <button @click="showGlobalModal = false" type="button" class="text-gray-400 hover:text-gray-700 bg-white hover:bg-gray-100 p-2 rounded-full border border-gray-200 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    
                    <div class="p-5 sm:p-8 bg-white">
                        <form action="{{ route('events.global.store') }}" method="POST" class="space-y-5">
                            @csrf
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest">Event Date*</label>
                                    <span x-show="globalDate && !isFullyBooked(globalDate)" x-cloak class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                                        <span x-text="getCount(globalDate)"></span>/6 slots used
                                    </span>
                                </div>
                                <input type="date" name="event_date" x-model="globalDate" required class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none transition-all shadow-xs font-medium" :class="isFullyBooked(globalDate) ? '!border-rose-500 !bg-rose-50/40' : (getCount(globalDate) > 0 ? '!border-amber-400 !bg-amber-50/20' : '')">
                                
                                <div x-show="isFullyBooked(globalDate)" x-cloak class="mt-1.5 p-2.5 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    <span>⚠️ Maximum 6 events already scheduled on this date. Please select another date.</span>
                                </div>

                                <div x-show="globalDate && !isFullyBooked(globalDate) && getCount(globalDate) > 0" x-cloak class="mt-1.5 p-2 rounded-lg bg-amber-50 border border-amber-200 text-amber-700 text-xs font-semibold flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>ℹ️ <span class="font-bold" x-text="getCount(globalDate)"></span> event(s) on this date. You can add <span class="font-bold" x-text="6 - getCount(globalDate)"></span> more.</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Event Title*</label>
                                <input type="text" name="content_title" required class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none transition-all shadow-xs placeholder-gray-400 font-medium" placeholder="e.g. World Tourism Day">
                            </div>
                            
                            <div class="pt-5 sm:pt-6 flex flex-col-reverse sm:flex-row items-stretch sm:items-center sm:justify-end gap-2.5 sm:gap-3 mt-5 sm:mt-6 border-t border-gray-100">
                                <button type="button" @click="showGlobalModal = false" class="w-full sm:w-auto px-5 py-3 sm:py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors cursor-pointer">
                                    Cancel
                                </button>
                                <button type="submit" :disabled="isFullyBooked(globalDate)" :class="isFullyBooked(globalDate) ? 'opacity-40 cursor-not-allowed bg-gray-400' : 'bg-amber-600 hover:bg-amber-700 shadow-md shadow-amber-500/20 cursor-pointer'" class="w-full sm:w-auto px-6 py-3 sm:py-2.5 text-sm font-bold text-white border border-transparent rounded-xl transition-all focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2">
                                    Create Global Event
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-10 sm:mt-14 flex justify-center no-print">
            <button type="button"
                    id="print-month-calendar"
                    class="inline-flex items-center justify-center gap-2.5 px-6 sm:px-8 py-3.5 text-sm font-bold text-white rounded-xl bg-gray-900 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-all shadow-md shadow-gray-900/20 cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Print
            </button>
        </div>

        <script>
            (function() {
                var printBtn = document.getElementById('print-month-calendar');
                if (!printBtn) return;

                function loadHtml2Pdf() {
                    if (window.html2pdf) return Promise.resolve();
                    return new Promise(function(resolve, reject) {
                        var existing = document.querySelector('script[data-html2pdf]');
                        if (existing) {
                            existing.addEventListener('load', resolve);
                            existing.addEventListener('error', function() { reject(new Error('Could not load PDF library')); });
                            return;
                        }
                        var script = document.createElement('script');
                        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js';
                        script.setAttribute('data-html2pdf', '1');
                        script.onload = resolve;
                        script.onerror = function() { reject(new Error('Could not load PDF library')); };
                        document.head.appendChild(script);
                    });
                }

                function calendarMonthLabel() {
                    if (window.contentCalendar) {
                        var date = window.contentCalendar.getDate();
                        return date.toLocaleString('en-US', { month: 'long', year: 'numeric' });
                    }
                    var source = document.getElementById('month-calendar-print');
                    var key = source && source.getAttribute('data-month');
                    if (key && /^\d{4}-\d{2}$/.test(key)) {
                        var parts = key.split('-');
                        return new Date(Number(parts[0]), Number(parts[1]) - 1, 1)
                            .toLocaleString('en-US', { month: 'long', year: 'numeric' });
                    }
                    return new Date().toLocaleString('en-US', { month: 'long', year: 'numeric' });
                }

                function resetButton(label) {
                    printBtn.disabled = false;
                    printBtn.innerHTML = label || printBtn.dataset.originalHtml;
                }

                printBtn.dataset.originalHtml = printBtn.innerHTML;

                printBtn.addEventListener('click', function() {
                    var source = document.getElementById('month-calendar-print');
                    if (!source) return;

                    printBtn.disabled = true;
                    printBtn.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg> Preparing PDF…';

                    if (window.contentCalendar) {
                        try {
                            window.contentCalendar.changeView('dayGridMonth');
                            window.contentCalendar.updateSize();
                        } catch (err) {}
                    }

                    var monthLabel = calendarMonthLabel();
                    var filename = 'YC-Content-Calendar-' + monthLabel.replace(/\s+/g, '-') + '.pdf';

                    loadHtml2Pdf()
                        .then(function() {
                            return new Promise(function(resolve) { setTimeout(resolve, 200); });
                        })
                        .then(function() {
                            var options = {
                                margin: [8, 8, 8, 8],
                                filename: filename,
                                image: { type: 'jpeg', quality: 0.95 },
                                html2canvas: {
                                    scale: 2,
                                    useCORS: true,
                                    logging: false,
                                    backgroundColor: '#ffffff',
                                    windowWidth: 1400,
                                    onclone: function(clonedDoc) {
                                        var root = clonedDoc.getElementById('month-calendar-print');
                                        if (!root) return;
                                        root.style.width = '1400px';
                                        root.style.maxWidth = '1400px';
                                        root.style.background = '#ffffff';
                                        root.style.padding = '8px';
                                        clonedDoc.querySelectorAll('.no-print').forEach(function(el) { el.remove(); });
                                        clonedDoc.querySelectorAll('.fc-button').forEach(function(el) {
                                            el.style.display = 'none';
                                        });
                                        clonedDoc.querySelectorAll('[class*="animate-"]').forEach(function(el) {
                                            el.style.animation = 'none';
                                            el.style.transform = 'none';
                                            el.style.opacity = '1';
                                        });
                                        var heading = clonedDoc.createElement('div');
                                        heading.style.cssText = 'margin:0 0 16px 0;padding-bottom:12px;border-bottom:1px solid #e2e8f0;';
                                        heading.innerHTML = '<div style="font-size:11px;font-weight:800;letter-spacing:.12em;text-transform:uppercase;color:#64748b;">YC Content Planning</div>' +
                                            '<div style="font-size:22px;font-weight:900;color:#0f172a;margin-top:4px;">Final Content Calendar · ' + monthLabel + '</div>';
                                        root.insertBefore(heading, root.firstChild);
                                    }
                                },
                                jsPDF: { unit: 'mm', format: 'a3', orientation: 'landscape' },
                                pagebreak: { mode: ['css', 'legacy'] }
                            };
                            return window.html2pdf().set(options).from(source).save();
                        })
                        .then(function() {
                            resetButton();
                        })
                        .catch(function(err) {
                            resetButton();
                            if (window.Swal) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Could not create PDF',
                                    text: (err && err.message) ? err.message : 'Please try again.',
                                    confirmButtonColor: '#111827',
                                    customClass: { popup: 'rounded-2xl shadow-xl' }
                                });
                            }
                        });
                });
            })();
        </script>

    </div>
</x-app-layout>
