@php
    $dateCounts = $dateCounts ?? (\App\Models\CalendarEvent::selectRaw('event_date, count(*) as count')
        ->groupBy('event_date')
        ->pluck('count', 'event_date')
        ->mapWithKeys(fn($count, $date) => [\Carbon\Carbon::parse($date)->format('Y-m-d') => (int)$count])
        ->all());
@endphp

<!-- STYLISH & MODERN Add Event Modal Overlay (Light Theme) -->
<div x-show="showCreateModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div x-show="showCreateModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-900/40 backdrop-blur-xs transition-opacity" 
         @click="showCreateModal = false"></div>

    <div class="flex min-h-full items-center justify-center p-3 text-center sm:p-4">
        <div x-show="showCreateModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
             class="relative transform w-full overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all my-4 sm:my-8 sm:max-w-3xl border border-gray-200 ring-1 ring-black/5 max-h-[92vh] flex flex-col">
            
            @if(isset($errors) && $errors->any())
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

            <div x-data="{ 
                selectedTeam: 'product_team',
                productDate: '',
                digitalDate: '',
                brandDate: '',
                serviceDate: '',
                globalDate: '',
                dateCounts: {{ json_encode($dateCounts) }},
                getCount(date) {
                    return (date && this.dateCounts[date]) ? Number(this.dateCounts[date]) : 0;
                },
                isFullyBooked(date) {
                    return this.getCount(date) >= 6;
                }
            }" class="w-full flex-1 min-h-0 flex flex-col">
                <div class="bg-slate-50 px-4 sm:px-8 py-4 sm:py-6 border-b border-gray-200 shrink-0">
                    <div class="flex items-center justify-between gap-3 mb-4 sm:mb-5">
                        <div class="flex items-center space-x-3 sm:space-x-4 min-w-0">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 shrink-0 rounded-xl flex items-center justify-center transition-colors"
                                 :class="{
                                    'bg-amber-100 text-amber-600 shadow-sm shadow-amber-500/10': selectedTeam === 'product_team',
                                    'bg-purple-100 text-purple-600 shadow-sm shadow-purple-500/10': selectedTeam === 'digital_team',
                                    'bg-sky-100 text-sky-600 shadow-sm shadow-sky-500/10': selectedTeam === 'brand_team',
                                    'bg-emerald-100 text-emerald-600 shadow-sm shadow-emerald-500/10': selectedTeam === 'service_team',
                                    'bg-rose-100 text-rose-600 shadow-sm shadow-rose-500/10': selectedTeam === 'global_team'
                                 }">
                                <svg x-show="selectedTeam === 'product_team'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                                <svg x-show="selectedTeam === 'digital_team'" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                <svg x-show="selectedTeam === 'brand_team'" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                <svg x-show="selectedTeam === 'service_team'" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"></path></svg>
                                <svg x-show="selectedTeam === 'global_team'" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-base sm:text-xl font-bold text-gray-900 tracking-tight">Create New Event</h3>
                                <p class="text-[11px] sm:text-sm text-gray-500 font-medium mt-0.5">Schedule up to 6 events per date</p>
                            </div>
                        </div>
                        <button @click="showCreateModal = false" type="button" class="text-gray-400 hover:text-gray-700 bg-white hover:bg-gray-100 p-2 sm:p-2.5 shrink-0 rounded-full border border-gray-200 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    
                    <!-- Segmented Tabs for Type Selection -->
                    <div class="flex flex-wrap items-center gap-1 p-1 bg-gray-200/80 rounded-xl w-full">
                        <button type="button" @click="selectedTeam = 'product_team'" 
                                :class="selectedTeam === 'product_team' ? 'bg-amber-400 text-amber-950 shadow-sm border border-amber-500 font-bold' : 'text-gray-600 hover:text-gray-900 border border-transparent font-medium'"
                                class="flex-1 min-w-[calc(50%-0.125rem)] sm:min-w-0 py-2 px-1 sm:px-3 text-[11px] sm:text-sm rounded-lg transition-all text-center whitespace-nowrap">
                            Product Event
                        </button>
                        <button type="button" @click="selectedTeam = 'digital_team'" 
                                :class="selectedTeam === 'digital_team' ? 'bg-purple-500 text-white shadow-sm border border-purple-600 font-bold' : 'text-gray-600 hover:text-gray-900 border border-transparent font-medium'"
                                class="flex-1 min-w-[calc(50%-0.125rem)] sm:min-w-0 py-2 px-1 sm:px-3 text-[11px] sm:text-sm rounded-lg transition-all text-center whitespace-nowrap">
                            Digital Event
                        </button>
                        <button type="button" @click="selectedTeam = 'brand_team'"
                                :class="selectedTeam === 'brand_team' ? 'bg-sky-500 text-white shadow-sm border border-sky-600 font-bold' : 'text-gray-600 hover:text-gray-900 border border-transparent font-medium'"
                                class="flex-1 min-w-[calc(50%-0.125rem)] sm:min-w-0 py-2 px-1 sm:px-3 text-[11px] sm:text-sm rounded-lg transition-all text-center whitespace-nowrap">
                            Brand Event
                        </button>
                        <button type="button" @click="selectedTeam = 'service_team'"
                                :class="selectedTeam === 'service_team' ? 'bg-emerald-500 text-white shadow-sm border border-emerald-600 font-bold' : 'text-gray-600 hover:text-gray-900 border border-transparent font-medium'"
                                class="flex-1 min-w-[calc(50%-0.125rem)] sm:min-w-0 py-2 px-1 sm:px-3 text-[11px] sm:text-sm rounded-lg transition-all text-center whitespace-nowrap">
                            Service Event
                        </button>
                        @if(auth()->check() && auth()->user()->role === 'super_admin')
                        <button type="button" @click="selectedTeam = 'global_team'" 
                                :class="selectedTeam === 'global_team' ? 'bg-rose-500 text-white shadow-sm border border-rose-600 font-bold' : 'text-gray-600 hover:text-gray-900 border border-transparent font-medium'"
                                class="flex-1 min-w-[calc(50%-0.125rem)] sm:min-w-0 py-2 px-1 sm:px-3 text-[11px] sm:text-sm rounded-lg transition-all text-center whitespace-nowrap">
                            Global Event
                        </button>
                        @endif
                    </div>
                </div>
                
                <div class="px-4 sm:px-8 py-5 sm:py-6 bg-white flex-1 overflow-y-auto nice-scroll">
                    <!-- Product Form -->
                    <form x-show="selectedTeam === 'product_team'" action="{{ route('events.product.store') }}" method="POST" class="space-y-5">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest">Publish Date*</label>
                                    <span x-show="productDate && !isFullyBooked(productDate)" x-cloak class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                                        <span x-text="getCount(productDate)"></span>/6 slots used
                                    </span>
                                </div>
                                <input type="date" name="event_date" x-model="productDate" required class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs font-medium" :class="isFullyBooked(productDate) ? '!border-rose-500 !bg-rose-50/40' : (getCount(productDate) > 0 ? '!border-blue-400 !bg-blue-50/20' : '')">
                                
                                <div x-show="isFullyBooked(productDate)" x-cloak class="mt-1.5 p-2.5 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    <span>⚠️ Maximum 6 events already scheduled on this date. Please select another date.</span>
                                </div>

                                <div x-show="productDate && !isFullyBooked(productDate) && getCount(productDate) > 0" x-cloak class="mt-1.5 p-2 rounded-lg bg-blue-50 border border-blue-200 text-blue-700 text-xs font-semibold flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>ℹ️ <span class="font-bold" x-text="getCount(productDate)"></span> event(s) on this date. You can add <span class="font-bold" x-text="6 - getCount(productDate)"></span> more.</span>
                                </div>
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
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
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
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-5">
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
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Financial Budget</label>
                                <input type="text" name="financial_budget" value="0" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs font-medium">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Boosting Budget</label>
                                <input type="text" name="boosting_budget" value="0" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs font-medium">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Drive Link</label>
                                <input type="text" name="drive_link" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs placeholder-gray-400 font-medium" placeholder="https://drive.google.com/...">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Remarks</label>
                                <input type="text" name="remarks" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs font-medium">
                            </div>
                        </div>
                        
                        <div class="pt-5 sm:pt-6 flex flex-col-reverse sm:flex-row items-stretch sm:items-center sm:justify-end gap-2.5 sm:gap-3 mt-5 sm:mt-6 border-t border-gray-200">
                            <button type="button" @click="showCreateModal = false" class="w-full sm:w-auto px-5 py-3 sm:py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-300 cursor-pointer">
                                Cancel
                            </button>
                            <button type="submit" :disabled="isFullyBooked(productDate)" :class="isFullyBooked(productDate) ? 'opacity-40 cursor-not-allowed bg-gray-400' : 'bg-amber-400 hover:bg-amber-500 text-amber-950 shadow-md shadow-amber-500/20 cursor-pointer'" class="w-full sm:w-auto px-6 py-3 sm:py-2.5 text-sm font-bold text-white border border-transparent rounded-xl transition-all focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2">
                                Create Event
                            </button>
                        </div>
                    </form>

                    <!-- Digital Form -->
                    <form x-show="selectedTeam === 'digital_team'" x-cloak action="{{ route('events.digital.store') }}" method="POST" class="space-y-5">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest">Event Date*</label>
                                    <span x-show="digitalDate && !isFullyBooked(digitalDate)" x-cloak class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-teal-50 text-teal-700 border border-teal-200">
                                        <span x-text="getCount(digitalDate)"></span>/6 slots used
                                    </span>
                                </div>
                                <input type="date" name="event_date" x-model="digitalDate" required class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none transition-all shadow-xs font-medium" :class="isFullyBooked(digitalDate) ? '!border-rose-500 !bg-rose-50/40' : (getCount(digitalDate) > 0 ? '!border-teal-400 !bg-teal-50/20' : '')">
                                
                                <div x-show="isFullyBooked(digitalDate)" x-cloak class="mt-1.5 p-2.5 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    <span>⚠️ Maximum 6 events already scheduled on this date. Please select another date.</span>
                                </div>

                                <div x-show="digitalDate && !isFullyBooked(digitalDate) && getCount(digitalDate) > 0" x-cloak class="mt-1.5 p-2 rounded-lg bg-teal-50 border border-teal-200 text-teal-700 text-xs font-semibold flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-teal-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>ℹ️ <span class="font-bold" x-text="getCount(digitalDate)"></span> event(s) on this date. You can add <span class="font-bold" x-text="6 - getCount(digitalDate)"></span> more.</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Post No.</label>
                                <input type="text" name="post_no" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none transition-all shadow-xs placeholder-gray-400 font-medium" placeholder="e.g. 1">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Content Title</label>
                            <input type="text" name="content_title" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none transition-all shadow-xs placeholder-gray-400 font-medium" placeholder="e.g. Month-opening offer announcement">
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
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
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
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
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Financial Budget</label>
                                <input type="text" name="financial_budget" value="0" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none transition-all shadow-xs font-medium">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Boosting Budget</label>
                                <input type="text" name="boosting_budget" value="0" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none transition-all shadow-xs font-medium">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Remarks</label>
                            <input type="text" name="remarks" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none transition-all shadow-xs font-medium">
                        </div>
                        
                        <div class="pt-5 sm:pt-6 flex flex-col-reverse sm:flex-row items-stretch sm:items-center sm:justify-end gap-2.5 sm:gap-3 mt-5 sm:mt-6 border-t border-gray-200">
                            <button type="button" @click="showCreateModal = false" class="w-full sm:w-auto px-5 py-3 sm:py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-300 cursor-pointer">
                                Cancel
                            </button>
                            <button type="submit" :disabled="isFullyBooked(digitalDate)" :class="isFullyBooked(digitalDate) ? 'opacity-40 cursor-not-allowed bg-gray-400' : 'bg-purple-600 hover:bg-purple-700 shadow-md shadow-purple-500/20 cursor-pointer'" class="w-full sm:w-auto px-6 py-3 sm:py-2.5 text-sm font-bold text-white border border-transparent rounded-xl transition-all focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                                Create Event
                            </button>
                        </div>
                    </form>

                    <!-- Brand Form -->
                    <form x-show="selectedTeam === 'brand_team'" x-cloak action="{{ route('events.brand.store') }}" method="POST" class="space-y-5">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest">Event Date*</label>
                                    <span x-show="brandDate && !isFullyBooked(brandDate)" x-cloak class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-sky-50 text-sky-700 border border-sky-200">
                                        <span x-text="getCount(brandDate)"></span>/6 slots used
                                    </span>
                                </div>
                                <input type="date" name="event_date" x-model="brandDate" required class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none transition-all shadow-xs font-medium" :class="isFullyBooked(brandDate) ? '!border-rose-500 !bg-rose-50/40' : (getCount(brandDate) > 0 ? '!border-sky-400 !bg-sky-50/20' : '')">
                                
                                <div x-show="isFullyBooked(brandDate)" x-cloak class="mt-1.5 p-2.5 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    <span>⚠️ Maximum 6 events already scheduled on this date. Please select another date.</span>
                                </div>

                                <div x-show="brandDate && !isFullyBooked(brandDate) && getCount(brandDate) > 0" x-cloak class="mt-1.5 p-2 rounded-lg bg-sky-50 border border-sky-200 text-sky-700 text-xs font-semibold flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-sky-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>ℹ️ <span class="font-bold" x-text="getCount(brandDate)"></span> event(s) on this date. You can add <span class="font-bold" x-text="6 - getCount(brandDate)"></span> more.</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Platform</label>
                                <select name="platform" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none transition-all shadow-xs font-medium">
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
                            <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Content Title*</label>
                            <input type="text" name="content_title" required class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none transition-all shadow-xs placeholder-gray-400 font-medium" placeholder="e.g. Brand campaign launch">
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Product</label>
                                <select name="product" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none transition-all shadow-xs font-medium">
                                    <option value="">Select Product</option>
                                    @if(isset($masterData['product']))
                                        @foreach($masterData['product'] as $item)
                                            <option value="{{ $item->value }}">{{ $item->value }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Format</label>
                                <select name="format" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none transition-all shadow-xs font-medium">
                                    <option value="">Select Format</option>
                                    @if(isset($masterData['format']))
                                        @foreach($masterData['format'] as $item)
                                            <option value="{{ $item->value }}">{{ $item->value }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Content Objective</label>
                            <input type="text" name="content_objective" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none transition-all shadow-xs placeholder-gray-400 font-medium" placeholder="Briefly describe the brand goal">
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">A.I.P.E Pillar</label>
                                <select name="aipe_pillar" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none transition-all shadow-xs font-medium">
                                    <option value="">Select Pillar</option>
                                    @if(isset($masterData['aipe_pillar']))
                                        @foreach($masterData['aipe_pillar'] as $item)
                                            <option value="{{ $item->value }}">{{ $item->value }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Drive Link</label>
                                <input type="text" name="drive_link" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none transition-all shadow-xs placeholder-gray-400 font-medium" placeholder="https://drive.google.com/...">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Financial Budget</label>
                                <input type="text" name="financial_budget" value="0" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none transition-all shadow-xs font-medium">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Boosting Budget</label>
                                <input type="text" name="boosting_budget" value="0" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none transition-all shadow-xs font-medium">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Remarks</label>
                            <input type="text" name="remarks" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none transition-all shadow-xs font-medium">
                        </div>
                        
                        <div class="pt-5 sm:pt-6 flex flex-col-reverse sm:flex-row items-stretch sm:items-center sm:justify-end gap-2.5 sm:gap-3 mt-5 sm:mt-6 border-t border-gray-200">
                            <button type="button" @click="showCreateModal = false" class="w-full sm:w-auto px-5 py-3 sm:py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-300 cursor-pointer">
                                Cancel
                            </button>
                            <button type="submit" :disabled="isFullyBooked(brandDate)" :class="isFullyBooked(brandDate) ? 'opacity-40 cursor-not-allowed bg-gray-400' : 'bg-sky-600 hover:bg-sky-700 shadow-md shadow-sky-500/20 cursor-pointer'" class="w-full sm:w-auto px-6 py-3 sm:py-2.5 text-sm font-bold text-white border border-transparent rounded-xl transition-all focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2">
                                Create Brand Event
                            </button>
                        </div>
                    </form>

                    <!-- Service Form -->
                    <form x-show="selectedTeam === 'service_team'" x-cloak action="{{ route('events.service.store') }}" method="POST" class="space-y-5">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest">Event Date*</label>
                                    <span x-show="serviceDate && !isFullyBooked(serviceDate)" x-cloak class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span x-text="getCount(serviceDate)"></span>/6 slots used
                                    </span>
                                </div>
                                <input type="date" name="event_date" x-model="serviceDate" required class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all shadow-xs font-medium" :class="isFullyBooked(serviceDate) ? '!border-rose-500 !bg-rose-50/40' : (getCount(serviceDate) > 0 ? '!border-emerald-400 !bg-emerald-50/20' : '')">
                                
                                <div x-show="isFullyBooked(serviceDate)" x-cloak class="mt-1.5 p-2.5 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    <span>⚠️ Maximum 6 events already scheduled on this date. Please select another date.</span>
                                </div>

                                <div x-show="serviceDate && !isFullyBooked(serviceDate) && getCount(serviceDate) > 0" x-cloak class="mt-1.5 p-2 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>ℹ️ <span class="font-bold" x-text="getCount(serviceDate)"></span> event(s) on this date. You can add <span class="font-bold" x-text="6 - getCount(serviceDate)"></span> more.</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Platform</label>
                                <select name="platform" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all shadow-xs font-medium">
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
                            <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Content Title*</label>
                            <input type="text" name="content_title" required class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all shadow-xs placeholder-gray-400 font-medium" placeholder="e.g. Free service camp">
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Product</label>
                                <select name="product" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all shadow-xs font-medium">
                                    <option value="">Select Product</option>
                                    @if(isset($masterData['product']))
                                        @foreach($masterData['product'] as $item)
                                            <option value="{{ $item->value }}">{{ $item->value }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Format</label>
                                <select name="format" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all shadow-xs font-medium">
                                    <option value="">Select Format</option>
                                    @if(isset($masterData['format']))
                                        @foreach($masterData['format'] as $item)
                                            <option value="{{ $item->value }}">{{ $item->value }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Content Objective</label>
                            <input type="text" name="content_objective" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all shadow-xs placeholder-gray-400 font-medium" placeholder="Briefly describe the service goal">
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">A.I.P.E Pillar</label>
                                <select name="aipe_pillar" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all shadow-xs font-medium">
                                    <option value="">Select Pillar</option>
                                    @if(isset($masterData['aipe_pillar']))
                                        @foreach($masterData['aipe_pillar'] as $item)
                                            <option value="{{ $item->value }}">{{ $item->value }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Drive Link</label>
                                <input type="text" name="drive_link" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all shadow-xs placeholder-gray-400 font-medium" placeholder="https://drive.google.com/...">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Financial Budget</label>
                                <input type="text" name="financial_budget" value="0" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all shadow-xs font-medium">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Boosting Budget</label>
                                <input type="text" name="boosting_budget" value="0" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all shadow-xs font-medium">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Remarks</label>
                            <input type="text" name="remarks" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all shadow-xs font-medium">
                        </div>
                        
                        <div class="pt-5 sm:pt-6 flex flex-col-reverse sm:flex-row items-stretch sm:items-center sm:justify-end gap-2.5 sm:gap-3 mt-5 sm:mt-6 border-t border-gray-200">
                            <button type="button" @click="showCreateModal = false" class="w-full sm:w-auto px-5 py-3 sm:py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-300 cursor-pointer">
                                Cancel
                            </button>
                            <button type="submit" :disabled="isFullyBooked(serviceDate)" :class="isFullyBooked(serviceDate) ? 'opacity-40 cursor-not-allowed bg-gray-400' : 'bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-500/20 cursor-pointer'" class="w-full sm:w-auto px-6 py-3 sm:py-2.5 text-sm font-bold text-white border border-transparent rounded-xl transition-all focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                                Create Service Event
                            </button>
                        </div>
                    </form>

                    <!-- Global Form -->
                    @if(auth()->check() && auth()->user()->role === 'super_admin')
                    <form x-show="selectedTeam === 'global_team'" x-cloak action="{{ route('events.global.store') }}" method="POST" class="space-y-5">
                        @csrf
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest">Event Date*</label>
                                <span x-show="globalDate && !isFullyBooked(globalDate)" x-cloak class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                                    <span x-text="getCount(globalDate)"></span>/6 slots used
                                </span>
                            </div>
                            <input type="date" name="event_date" x-model="globalDate" required class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none transition-all shadow-xs font-medium" :class="isFullyBooked(globalDate) ? '!border-rose-500 !bg-rose-50/40' : (getCount(globalDate) > 0 ? '!border-amber-400 !bg-amber-50/20' : '')">
                            
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
                            <input type="text" name="content_title" required class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none transition-all shadow-xs placeholder-gray-400 font-medium" placeholder="e.g. World Tourism Day">
                        </div>
                        
                        <div class="pt-5 sm:pt-6 flex flex-col-reverse sm:flex-row items-stretch sm:items-center sm:justify-end gap-2.5 sm:gap-3 mt-5 sm:mt-6 border-t border-gray-200">
                            <button type="button" @click="showCreateModal = false" class="w-full sm:w-auto px-5 py-3 sm:py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-300 cursor-pointer">
                                Cancel
                            </button>
                            <button type="submit" :disabled="isFullyBooked(globalDate)" :class="isFullyBooked(globalDate) ? 'opacity-40 cursor-not-allowed bg-gray-400' : 'bg-rose-600 hover:bg-rose-700 shadow-md shadow-rose-500/20 cursor-pointer'" class="w-full sm:w-auto px-6 py-3 sm:py-2.5 text-sm font-bold text-white border border-transparent rounded-xl transition-all focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2">
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
