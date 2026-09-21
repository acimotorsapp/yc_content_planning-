@php
    $boardUser = auth()->user();
    $canDragBoard = $boardUser->canUseContentBoard();
    $isSuperAdmin = $boardUser->isSuperAdmin();
    $boardEvents = $monthEvents ?? $events;
    if (! $isSuperAdmin) {
        $boardEvents = $boardEvents->filter(fn ($e) => $boardUser->canManageEvent($e))->values();
    }
    $monthLabel = !empty($month)
        ? \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y')
        : now()->format('F Y');
    $plannedCards = $boardEvents
        ->filter(fn ($e) => ($e->status ?? 'not_done') !== 'done' && ($e->status ?? 'not_done') !== 'in_progress')
        ->sortBy([['sort_order', 'asc'], ['event_date', 'asc']])
        ->values();
    $progressCards = $boardEvents->where('status', 'in_progress')->sortBy([['sort_order', 'asc'], ['event_date', 'asc']])->values();
    $doneCards = $boardEvents->where('status', 'done')->sortBy([['sort_order', 'asc'], ['event_date', 'asc']])->values();
    $notDoneCards = $boardEvents
        ->filter(fn ($e) => ($e->status ?? 'not_done') !== 'done')
        ->sortBy([['sort_order', 'asc'], ['event_date', 'asc']])
        ->values();
    $boardColumns = $isSuperAdmin
        ? [
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
        ]
        : [
            [
                'status' => 'not_done',
                'label' => 'Not Done',
                'hint' => 'Open tasks — drag here or tap Not Done',
                'countId' => 'board-count-not_done',
                'header' => 'bg-rose-50 border-rose-200 text-rose-800',
                'dot' => 'bg-rose-500',
                'list' => 'bg-rose-50/40',
                'cards' => $notDoneCards,
            ],
            [
                'status' => 'done',
                'label' => 'Done',
                'hint' => 'Completed tasks — drag here or tap Done',
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

<script>
    function contentBoardApp() {
        return {
            open: false,
            saving: false,
            feedbackOpen: false,
            feedbackSaving: false,
            form: { id: null, title: '', date: '', status: 'not_done', url: '' },
            feedbackForm: { id: null, text: '', url: '' },
            originalDate: '',
            dateCounts: @json($dateCounts ?? []),
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
            openFeedback(el) {
                this.feedbackForm.id = el.dataset.eventId;
                this.feedbackForm.text = el.dataset.feedback || '';
                this.feedbackForm.url = el.dataset.feedbackUrl;
                this.feedbackOpen = true;
            },
            saveFeedback() {
                if (this.feedbackSaving || !this.feedbackForm.url) return;
                this.feedbackSaving = true;
                var self = this;
                fetch(this.feedbackForm.url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ feedback: this.feedbackForm.text })
                })
                .then(function(r) { return r.json().then(function(data) { return { ok: r.ok, data: data }; }); })
                .then(function(result) {
                    if (!result.ok || !result.data.success) {
                        throw new Error((result.data && (result.data.message || result.data.errors)) || 'Could not save feedback');
                    }
                    var card = document.querySelector('.js-board-card[data-event-id="' + self.feedbackForm.id + '"]');
                    if (card) {
                        var text = result.data.feedback || '';
                        card.setAttribute('data-feedback', text);
                        var snippet = card.querySelector('.js-card-feedback-text');
                        if (snippet) {
                            snippet.textContent = text;
                            snippet.classList.toggle('hidden', !text);
                        }
                    }
                    self.feedbackOpen = false;
                    self.feedbackSaving = false;
                })
                .catch(function(err) {
                    self.feedbackSaving = false;
                    Swal.fire({
                        icon: 'error',
                        title: 'Could not save feedback',
                        text: err.message || 'Please try again.',
                        confirmButtonColor: '#2563eb',
                        customClass: { popup: 'rounded-2xl shadow-xl' }
                    });
                });
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
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
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
        };
    }
</script>

<div id="content-board"
     class="mb-6 sm:mb-12"
     x-data="contentBoardApp()">
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-2 mb-4">
        <div class="min-w-0">
            <h2 class="text-lg sm:text-xl font-black text-gray-900 tracking-tight">Final Content Calendar</h2>
            <p class="text-xs sm:text-sm text-gray-500 font-medium mt-0.5">
                {{ $monthLabel }} · managed in-platform
                @if($canDragBoard)
                    · drag cards between columns, or mark Done / Not Done
                @endif
            </p>
        </div>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-100 text-[10px] sm:text-xs font-bold uppercase tracking-wider shrink-0">
            {{ $boardEvents->count() }} cards
        </span>
    </div>

    <div class="flex lg:grid {{ $isSuperAdmin ? 'lg:grid-cols-3' : 'lg:grid-cols-2' }} gap-3 sm:gap-4 overflow-x-auto lg:overflow-visible pb-2 items-start nice-scroll">
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
                            $canManageCard = $boardUser->canManageEvent($event);
                            $canEditCard = $canManageCard;
                            $cardStatus = $event->status ?? 'not_done';
                            $isDone = $cardStatus === 'done';
                        @endphp
                        <article class="js-board-card group bg-white border border-gray-200 rounded-xl shadow-xs hover:border-blue-200 hover:shadow-md transition-shadow p-3 {{ ($canDragBoard && $canManageCard) ? 'js-board-draggable cursor-grab active:cursor-grabbing' : '' }}"
                                 data-event-id="{{ $event->id }}"
                                 data-title="{{ $event->displayTitle() }}"
                                 data-date="{{ $event->event_date?->format('Y-m-d') }}"
                                 data-status="{{ $cardStatus }}"
                                 data-feedback="{{ e($event->feedback ?? '') }}"
                                 data-reschedule-url="{{ route('events.reschedule', $event) }}"
                                 data-status-url="{{ route('events.update_status', $event) }}"
                                 data-feedback-url="{{ route('events.update_feedback', $event) }}">
                            @if($canDragBoard && $canManageCard)
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
                                    <p class="js-card-feedback-text text-[11px] text-indigo-600 mt-1.5 font-medium line-clamp-2 {{ $event->feedback ? '' : 'hidden' }}">{{ $event->feedback }}</p>
                                </div>
                            </div>
                            @if($canManageCard)
                            <div class="js-no-drag mt-2.5 flex items-center gap-1">
                                <button type="button"
                                        data-status="done"
                                        class="js-card-status flex-1 inline-flex items-center justify-center px-2 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider border transition-colors {{ $isDone ? 'bg-emerald-600 text-white border-emerald-600' : 'text-emerald-700 bg-emerald-50 border-emerald-100 hover:bg-emerald-100' }}">
                                    Done
                                </button>
                                <button type="button"
                                        data-status="not_done"
                                        class="js-card-status flex-1 inline-flex items-center justify-center px-2 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider border transition-colors {{ ! $isDone ? 'bg-rose-600 text-white border-rose-600' : 'text-rose-700 bg-rose-50 border-rose-100 hover:bg-rose-100' }}">
                                    Not Done
                                </button>
                            </div>
                            @endif
                            <div class="mt-2.5 flex items-center justify-between gap-2">
                                <span class="text-[10px] font-semibold text-gray-400 truncate">{{ $event->user?->name ?? 'Unassigned' }}</span>
                                <div class="flex items-center gap-1 shrink-0 js-no-drag">
                                    @if($canManageCard)
                                        <button type="button"
                                                class="js-open-feedback inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider text-indigo-600 bg-indigo-50 hover:bg-indigo-100 border border-indigo-100 transition-colors"
                                                @click.stop.prevent="openFeedback($event.currentTarget.closest('.js-board-card'))">
                                            Feedback
                                        </button>
                                    @endif
                                    @if($canEditCard)
                                        <button type="button"
                                                class="js-open-edit inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-100 transition-colors"
                                                @click.stop.prevent="openEdit($event.currentTarget.closest('.js-board-card'))">
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
    <div x-show="open" x-cloak
         class="fixed inset-0 z-[200] items-center justify-center p-4"
         :class="open ? 'flex' : 'hidden'"
         role="dialog" aria-modal="true"
         @keydown.escape.window="if (open) open = false">
        <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-xs" @click="open = false"></div>
        <div class="relative w-full max-w-md rounded-2xl bg-white shadow-2xl border border-gray-200 overflow-hidden" @click.stop>
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
                            @if($isSuperAdmin)
                            <option value="not_done">Planned</option>
                            <option value="in_progress">In Progress</option>
                            <option value="done">Done</option>
                            @else
                            <option value="not_done">Not Done</option>
                            <option value="done">Done</option>
                            @endif
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
    </template>

    <template x-teleport="body">
    <div x-show="feedbackOpen" x-cloak
         class="fixed inset-0 z-[200] items-center justify-center p-4"
         :class="feedbackOpen ? 'flex' : 'hidden'"
         role="dialog" aria-modal="true"
         @keydown.escape.window="if (feedbackOpen) feedbackOpen = false">
        <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-xs" @click="feedbackOpen = false"></div>
        <div class="relative w-full max-w-md rounded-2xl bg-white shadow-2xl border border-gray-200 overflow-hidden" @click.stop>
                <div class="px-5 py-4 border-b border-gray-100 bg-slate-50/80 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-black text-gray-900">Task feedback</h3>
                        <p class="text-xs text-gray-500 font-medium mt-0.5">Leave a note on this task for the team.</p>
                    </div>
                    <button type="button" @click="feedbackOpen = false" class="p-2 rounded-full text-gray-400 hover:bg-gray-100 hover:text-gray-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form class="p-5 space-y-4" @submit.prevent="saveFeedback()">
                    <div>
                        <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-1.5">Feedback</label>
                        <textarea x-model="feedbackForm.text" rows="5" maxlength="2000" class="w-full rounded-xl border-gray-300 bg-slate-50 text-sm font-medium text-gray-900 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500" placeholder="What should the team know about this task?"></textarea>
                    </div>
                    <div class="pt-2 flex flex-col-reverse sm:flex-row sm:justify-end gap-2">
                        <button type="button" @click="feedbackOpen = false" class="px-4 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50">Cancel</button>
                        <button type="submit" :disabled="feedbackSaving" :class="feedbackSaving ? 'opacity-40 cursor-not-allowed' : ''" class="px-5 py-2.5 text-sm font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700">
                            Save feedback
                        </button>
                    </div>
                </form>
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

        function paintStatusButtons(card) {
            var status = card.getAttribute('data-status') === 'done' ? 'done' : 'not_done';
            card.querySelectorAll('.js-card-status').forEach(function (btn) {
                var isActive = btn.getAttribute('data-status') === status;
                if (btn.getAttribute('data-status') === 'done') {
                    btn.className = 'js-card-status flex-1 inline-flex items-center justify-center px-2 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider border transition-colors ' +
                        (isActive ? 'bg-emerald-600 text-white border-emerald-600' : 'text-emerald-700 bg-emerald-50 border-emerald-100 hover:bg-emerald-100');
                } else {
                    btn.className = 'js-card-status flex-1 inline-flex items-center justify-center px-2 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider border transition-colors ' +
                        (isActive ? 'bg-rose-600 text-white border-rose-600' : 'text-rose-700 bg-rose-50 border-rose-100 hover:bg-rose-100');
                }
            });
        }

        function boot() {
            var columns = Array.from(document.querySelectorAll('.js-board-sortable'));
            if (!columns.length) return;

            columns.forEach(function (col) {
                col.addEventListener('pointerdown', onPointerDown);
            });

            document.getElementById('content-board').addEventListener('click', function (e) {
                var target = e.target.nodeType === 3 ? e.target.parentElement : e.target;
                if (!target || !target.closest) return;

                var feedbackBtn = target.closest('.js-open-feedback');
                var editBtn = target.closest('.js-open-edit');
                if (feedbackBtn || editBtn) {
                    e.preventDefault();
                    e.stopPropagation();
                    var card = (feedbackBtn || editBtn).closest('.js-board-card');
                    var root = document.getElementById('content-board');
                    var data = (window.Alpine && root) ? window.Alpine.$data(root) : null;
                    if (!card || !data) return;
                    if (feedbackBtn) data.openFeedback(card);
                    if (editBtn) data.openEdit(card);
                    return;
                }

                var btn = target.closest('.js-card-status');
                if (!btn) return;
                e.preventDefault();
                e.stopPropagation();
                var card = btn.closest('.js-board-card');
                var status = btn.getAttribute('data-status');
                if (!card || !status || card.getAttribute('data-status') === status) return;
                var from = card.closest('.js-board-sortable');
                var to = document.querySelector('.js-board-sortable[data-status="' + status + '"]');
                card.setAttribute('data-status', status);
                paintStatusButtons(card);
                if (to) to.appendChild(card);
                var tasks = [];
                if (to) tasks.push(persist(to));
                if (from && from !== to) tasks.push(persist(from));
                refreshCounts();
                Promise.all(tasks).catch(function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Could not update status',
                        confirmButtonColor: '#2563eb',
                        customClass: { popup: 'rounded-2xl shadow-xl' }
                    });
                });
            });
        }

        function onPointerDown(e) {
            if (e.button !== 0) return;
            var target = e.target.nodeType === 3 ? e.target.parentElement : e.target;
            if (!target || !target.closest || target.closest(ignore)) return;
            var card = e.target.closest('.js-board-card');
            if (!card || !card.classList.contains('js-board-draggable')) return;

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
                        paintStatusButtons(card);
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
