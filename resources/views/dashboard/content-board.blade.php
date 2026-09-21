@php
    $boardEvents = $monthEvents ?? $events;
    $canDragBoard = auth()->user()->role === 'super_admin';
    $monthLabel = !empty($month)
        ? \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y')
        : now()->format('F Y');
    $plannedCards = $boardEvents
        ->filter(fn ($e) => ($e->status ?? 'not_done') !== 'done' && ($e->status ?? 'not_done') !== 'in_progress')
        ->sortBy([['sort_order', 'asc'], ['event_date', 'asc']])
        ->values();
    $progressCards = $boardEvents->where('status', 'in_progress')->sortBy([['sort_order', 'asc'], ['event_date', 'asc']])->values();
    $doneCards = $boardEvents->where('status', 'done')->sortBy([['sort_order', 'asc'], ['event_date', 'asc']])->values();
    $boardColumns = [
        [
            'status' => 'not_done',
            'label' => 'Planned',
            'hint' => 'Ready to produce',
            'countId' => 'board-count-not_done',
            'header' => 'bg-slate-100 border-slate-200 text-slate-700',
            'dot' => 'bg-slate-400',
            'list' => 'bg-slate-50/80',
            'cards' => $plannedCards,
        ],
        [
            'status' => 'in_progress',
            'label' => 'In Progress',
            'hint' => 'Currently in production',
            'countId' => 'board-count-in_progress',
            'header' => 'bg-amber-50 border-amber-200 text-amber-800',
            'dot' => 'bg-amber-500',
            'list' => 'bg-amber-50/40',
            'cards' => $progressCards,
        ],
        [
            'status' => 'done',
            'label' => 'Done',
            'hint' => 'Published / complete',
            'countId' => 'board-count-done',
            'header' => 'bg-emerald-50 border-emerald-200 text-emerald-800',
            'dot' => 'bg-emerald-500',
            'list' => 'bg-emerald-50/40',
            'cards' => $doneCards,
        ],
    ];
@endphp

<style>
    .js-board-column:empty::before {
        content: attr(data-empty-label);
        display: block;
        text-align: center;
        font-size: 11px;
        font-weight: 600;
        color: #9ca3af;
        padding: 2.5rem 0.75rem;
    }
    .js-board-card { user-select: none; -webkit-user-select: none; }
    .js-board-card.is-dragging { opacity: 0.25; }
    .board-card-ghost {
        position: fixed;
        z-index: 90;
        pointer-events: none;
        transform: rotate(2deg);
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.22);
        margin: 0 !important;
    }
    .board-card-placeholder {
        border: 2px dashed #93c5fd;
        background: #eff6ff;
        border-radius: 0.75rem;
        min-height: 88px;
        margin: 0;
    }
</style>

<div id="content-board"
     class="mb-6 sm:mb-12"
     x-data="{
        open: false,
        saving: false,
        form: { id: null, title: '', date: '', status: 'not_done', url: '' },
        originalDate: '',
        dateCounts: {{ json_encode($dateCounts ?? []) }},
        otherCount() {
            if (!this.form.date) return 0;
            var total = this.dateCounts[this.form.date] ? Number(this.dateCounts[this.form.date]) : 0;
            return this.form.date === this.originalDate ? Math.max(0, total - 1) : total;
        },
        isFullyBooked() { return this.otherCount() >= 6; },
        openEdit(el) {
            this.form.id = el.dataset.eventId;
            this.form.title = el.dataset.title || '';
            this.form.date = el.dataset.date || '';
            this.form.status = el.dataset.status || 'not_done';
            this.form.url = el.dataset.rescheduleUrl;
            this.originalDate = el.dataset.date || '';
            this.open = true;
        },
        save() {
            if (this.saving || this.isFullyBooked() || !this.form.url) return;
            this.saving = true;
            var self = this;
            fetch(this.form.url, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify({
                    event_date: this.form.date,
                    content_title: this.form.title,
                    status: this.form.status
                })
            })
            .then(function(r) { return r.json().then(function(data) { return { ok: r.ok, data: data }; }); })
            .then(function(result) {
                if (!result.ok || !result.data.success) {
                    throw new Error((result.data && (result.data.message || result.data.errors)) || 'Could not reschedule');
                }
                window.location.reload();
            })
            .catch(function(err) {
                self.saving = false;
                Swal.fire({
                    icon: 'error',
                    title: 'Could not reschedule',
                    text: err.message || 'Please try another date.',
                    confirmButtonColor: '#2563eb',
                    customClass: { popup: 'rounded-2xl shadow-xl' }
                });
            });
        }
     }">
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-2 mb-4">
        <div class="min-w-0">
            <h2 class="text-lg sm:text-xl font-black text-gray-900 tracking-tight">Final Content Calendar</h2>
            <p class="text-xs sm:text-sm text-gray-500 font-medium mt-0.5">
                {{ $monthLabel }} · managed in-platform
                @if($canDragBoard)
                    · drag cards to change status, or drop them on a calendar date
                @endif
            </p>
        </div>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-100 text-[10px] sm:text-xs font-bold uppercase tracking-wider shrink-0">
            {{ $boardEvents->count() }} cards
        </span>
    </div>

    <div class="flex lg:grid lg:grid-cols-3 gap-3 sm:gap-4 overflow-x-auto lg:overflow-visible pb-2 items-start nice-scroll">
        @foreach($boardColumns as $column)
            <section class="w-[280px] sm:w-[300px] lg:w-auto shrink-0 lg:min-w-0 rounded-2xl border border-gray-200 bg-white shadow-sm flex flex-col max-h-[70vh]">
                <header class="px-4 py-3 border-b {{ $column['header'] }} rounded-t-2xl flex items-center justify-between gap-2">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full {{ $column['dot'] }}"></span>
                            <h3 class="text-sm font-black tracking-tight">{{ $column['label'] }}</h3>
                        </div>
                        <p class="text-[10px] font-semibold opacity-70 mt-0.5">{{ $column['hint'] }}</p>
                    </div>
                    <span id="{{ $column['countId'] }}" class="text-xs font-black tabular-nums px-2 py-0.5 rounded-lg bg-white/80 border border-black/5">{{ $column['cards']->count() }}</span>
                </header>
                <div class="js-board-column flex-1 overflow-y-auto p-2.5 space-y-2.5 min-h-[140px] {{ $column['list'] }} rounded-b-2xl {{ $canDragBoard ? 'js-board-sortable' : '' }}"
                     data-status="{{ $column['status'] }}"
                     data-empty-label="{{ $canDragBoard ? 'Drop cards here' : 'No cards in this column' }}">
                    @foreach($column['cards'] as $event)
                        @php
                            $canEditCard = auth()->id() === $event->user_id || auth()->user()->role === 'super_admin';
                        @endphp
                        <article class="js-board-card group bg-white border border-gray-200 rounded-xl shadow-xs hover:border-blue-200 hover:shadow-md transition-shadow p-3 {{ $canDragBoard ? 'cursor-grab active:cursor-grabbing' : '' }}"
                                 data-event-id="{{ $event->id }}"
                                 data-title="{{ $event->displayTitle() }}"
                                 data-date="{{ $event->event_date?->format('Y-m-d') }}"
                                 data-status="{{ $event->status ?? 'not_done' }}"
                                 data-reschedule-url="{{ route('events.reschedule', $event) }}">
                            @if($canDragBoard)
                            <div class="flex items-center justify-center gap-1 text-gray-300 group-hover:text-gray-500 mb-2 -mt-0.5" title="Drag this card">
                                <span class="w-8 h-1 rounded-full bg-current opacity-70"></span>
                                <span class="w-8 h-1 rounded-full bg-current opacity-70"></span>
                            </div>
                            @endif
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-1.5 mb-1.5">
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[9px] font-bold uppercase tracking-wider border {{ $event->teamBadgeClasses() }}">
                                            {{ $event->teamLabel() }}
                                        </span>
                                        <span class="js-card-date text-[10px] font-bold text-gray-500">{{ $event->event_date?->format('M d') }}</span>
                                    </div>
                                    <h4 class="js-card-title text-sm font-bold text-gray-900 leading-snug break-words">{{ $event->displayTitle() }}</h4>
                                    @if($event->content_objective)
                                        <p class="text-[11px] text-gray-500 mt-1 line-clamp-2 font-medium">{{ $event->content_objective }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="mt-2.5 flex items-center justify-between gap-2">
                                <span class="text-[10px] font-semibold text-gray-400 truncate">{{ $event->user?->name ?? 'Unassigned' }}</span>
                                <div class="flex items-center gap-1 shrink-0 js-no-drag">
                                    @if($canEditCard)
                                        <button type="button"
                                                @click.stop="openEdit($event.currentTarget.closest('.js-board-card'))"
                                                class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-100 transition-colors">
                                            Edit
                                        </button>
                                    @endif
                                    <a href="{{ route('events.show', $event) }}" class="inline-flex p-1 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-colors" title="View">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endforeach
    </div>

    <template x-teleport="body">
    <div x-show="open" x-cloak class="relative z-[80]" aria-modal="true" @keydown.escape.window="open = false">
        <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-xs" @click="open = false"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="w-full max-w-md rounded-2xl bg-white shadow-2xl border border-gray-200 overflow-hidden" @click.stop>
                <div class="px-5 py-4 border-b border-gray-100 bg-slate-50/80 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-black text-gray-900">Edit &amp; reschedule</h3>
                        <p class="text-xs text-gray-500 font-medium mt-0.5">Change the publish date, title, or column.</p>
                    </div>
                    <button type="button" @click="open = false" class="p-2 rounded-full text-gray-400 hover:bg-gray-100 hover:text-gray-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form class="p-5 space-y-4" @submit.prevent="save()">
                    <div>
                        <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-1.5">Content title</label>
                        <input type="text" x-model="form.title" required class="w-full rounded-xl border-gray-300 bg-slate-50 text-sm font-semibold text-gray-900 focus:bg-white focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest">Publish date</label>
                            <span x-show="form.date && !isFullyBooked()" x-cloak class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                                <span x-text="otherCount()"></span>/6 slots used
                            </span>
                        </div>
                        <input type="date" x-model="form.date" required class="w-full rounded-xl border-gray-300 bg-slate-50 text-sm font-semibold text-gray-900 focus:bg-white focus:border-blue-500 focus:ring-blue-500">
                        <p x-show="isFullyBooked()" x-cloak class="mt-1.5 text-xs font-bold text-rose-600">This date already has 6 events. Pick another day.</p>
                    </div>
                    @if($canDragBoard)
                    <div>
                        <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-1.5">Column</label>
                        <select x-model="form.status" class="w-full rounded-xl border-gray-300 bg-slate-50 text-sm font-semibold text-gray-900 focus:bg-white focus:border-blue-500 focus:ring-blue-500">
                            <option value="not_done">Planned</option>
                            <option value="in_progress">In Progress</option>
                            <option value="done">Done</option>
                        </select>
                    </div>
                    @endif
                    <div class="pt-2 flex flex-col-reverse sm:flex-row sm:justify-end gap-2">
                        <button type="button" @click="open = false" class="px-4 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50">Cancel</button>
                        <button type="submit" :disabled="saving || isFullyBooked()" :class="(saving || isFullyBooked()) ? 'opacity-40 cursor-not-allowed' : ''" class="px-5 py-2.5 text-sm font-bold text-white bg-blue-600 rounded-xl hover:bg-blue-700">
                            Save changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </template>
</div>

@if($canDragBoard)
<script>
    (function () {
        var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        var reorderUrl = @json(route('events.reorder_board'));
        var ignore = '.js-no-drag, a, button, input, select, textarea';

        function columnIds(list) {
            return Array.from(list.querySelectorAll('.js-board-card')).map(function (card) {
                return Number(card.getAttribute('data-event-id'));
            });
        }

        function refreshCounts() {
            document.querySelectorAll('.js-board-sortable').forEach(function (list) {
                var countEl = document.getElementById('board-count-' + list.getAttribute('data-status'));
                if (countEl) countEl.textContent = list.querySelectorAll('.js-board-card').length;
            });
        }

        function persist(list) {
            return fetch(reorderUrl, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify({
                    status: list.getAttribute('data-status'),
                    ordered_ids: columnIds(list)
                })
            }).then(function (response) {
                if (!response.ok) throw new Error('Reorder failed');
                return response.json();
            });
        }

        function boot() {
            var columns = Array.from(document.querySelectorAll('.js-board-sortable'));
            if (!columns.length) return;

            columns.forEach(function (col) {
                col.addEventListener('pointerdown', onPointerDown);
            });
        }

        function onPointerDown(e) {
            if (e.button !== 0) return;
            if (e.target.closest(ignore)) return;
            var card = e.target.closest('.js-board-card');
            if (!card) return;

            var from = card.closest('.js-board-sortable');
            var startX = e.clientX;
            var startY = e.clientY;
            var started = false;
            var ghost = null;
            var placeholder = null;
            var offsetX = 0;
            var offsetY = 0;

            function beginDrag(ev) {
                started = true;
                var rect = card.getBoundingClientRect();
                offsetX = ev.clientX - rect.left;
                offsetY = ev.clientY - rect.top;

                placeholder = document.createElement('div');
                placeholder.className = 'board-card-placeholder';
                placeholder.style.height = rect.height + 'px';
                card.after(placeholder);

                ghost = card.cloneNode(true);
                ghost.classList.add('board-card-ghost');
                ghost.style.width = rect.width + 'px';
                ghost.style.left = rect.left + 'px';
                ghost.style.top = rect.top + 'px';
                document.body.appendChild(ghost);

                card.classList.add('is-dragging');
                document.body.style.cursor = 'grabbing';
                document.body.style.userSelect = 'none';
                try { card.setPointerCapture(ev.pointerId); } catch (err) {}
            }

            function clearDateTargets() {
                document.querySelectorAll('.fc-daygrid-day.board-date-target').forEach(function (el) {
                    el.classList.remove('board-date-target');
                });
            }

            function placeAt(ev) {
                ghost.style.left = (ev.clientX - offsetX) + 'px';
                ghost.style.top = (ev.clientY - offsetY) + 'px';

                ghost.style.visibility = 'hidden';
                var under = document.elementFromPoint(ev.clientX, ev.clientY);
                ghost.style.visibility = 'visible';
                if (!under) return;

                clearDateTargets();
                var dayCell = under.closest('.fc-daygrid-day[data-date]');
                if (dayCell) {
                    dayCell.classList.add('board-date-target');
                    return;
                }

                var col = under.closest('.js-board-sortable');
                if (!col) return;

                var overCard = under.closest('.js-board-card');
                if (overCard && overCard !== card && col.contains(overCard)) {
                    var box = overCard.getBoundingClientRect();
                    if (ev.clientY < box.top + box.height / 2) {
                        col.insertBefore(placeholder, overCard);
                    } else {
                        col.insertBefore(placeholder, overCard.nextSibling);
                    }
                } else if (!col.querySelector('.js-board-card:not(.is-dragging)')) {
                    col.appendChild(placeholder);
                } else if (!overCard) {
                    col.appendChild(placeholder);
                }
            }

            function onMove(ev) {
                if (!started) {
                    if (Math.abs(ev.clientX - startX) + Math.abs(ev.clientY - startY) < 6) return;
                    beginDrag(ev);
                }
                ev.preventDefault();
                placeAt(ev);
            }

            function onUp(ev) {
                window.removeEventListener('pointermove', onMove);
                window.removeEventListener('pointerup', onUp);
                window.removeEventListener('pointercancel', onUp);
                if (!started) return;

                var dropDay = null;
                if (ghost) ghost.style.visibility = 'hidden';
                var under = document.elementFromPoint(ev.clientX, ev.clientY);
                if (ghost) ghost.style.visibility = 'visible';
                if (under) {
                    var dayCell = under.closest('.fc-daygrid-day[data-date]');
                    if (dayCell) dropDay = dayCell.getAttribute('data-date');
                }
                clearDateTargets();

                if (dropDay) {
                    var oldDate = card.getAttribute('data-date');
                    var eventId = card.getAttribute('data-event-id');
                    if (dropDay !== oldDate && window.rescheduleEventDate) {
                        window.rescheduleEventDate(eventId, dropDay, oldDate).then(function () {
                            var monthKey = dropDay.slice(0, 7);
                            var url = new URL(window.location.href);
                            if (url.searchParams.get('month') && url.searchParams.get('month') !== monthKey) {
                                url.searchParams.set('month', monthKey);
                                window.location.href = url.toString();
                            }
                        }).catch(function (err) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Could not reschedule',
                                text: err.message || 'Please try another date.',
                                confirmButtonColor: '#2563eb',
                                customClass: { popup: 'rounded-2xl shadow-xl' }
                            });
                        });
                    }
                } else {
                    var to = placeholder && placeholder.parentElement;
                    if (to && to.classList.contains('js-board-sortable')) {
                        to.insertBefore(card, placeholder);
                        card.setAttribute('data-status', to.getAttribute('data-status'));
                        var tasks = [persist(to)];
                        if (from && from !== to) tasks.push(persist(from));
                        refreshCounts();
                        Promise.all(tasks).catch(function () {
                            Swal.fire({
                                icon: 'error',
                                title: 'Could not save order',
                                confirmButtonColor: '#2563eb',
                                customClass: { popup: 'rounded-2xl shadow-xl' }
                            });
                        });
                    }
                }

                card.classList.remove('is-dragging');
                if (placeholder) placeholder.remove();
                if (ghost) ghost.remove();
                document.body.style.cursor = '';
                document.body.style.userSelect = '';
            }

            window.addEventListener('pointermove', onMove, { passive: false });
            window.addEventListener('pointerup', onUp);
            window.addEventListener('pointercancel', onUp);
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', boot);
        } else {
            boot();
        }
    })();
</script>
@endif
