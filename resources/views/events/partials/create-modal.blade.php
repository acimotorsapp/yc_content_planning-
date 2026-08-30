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

    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div x-show="showCreateModal"
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
                        <button @click="showCreateModal = false" type="button" class="text-gray-400 hover:text-gray-700 bg-white hover:bg-gray-100 p-2.5 rounded-full border border-gray-200 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                                <input type="text" name="boosting_budget" value="0" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs font-medium">
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
                            <button type="button" @click="showCreateModal = false" class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-300">
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
                                <input type="text" name="boosting_budget" value="0" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none transition-all shadow-xs font-medium">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Remarks</label>
                                <input type="text" name="remarks" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-lg px-4 py-2.5 focus:bg-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none transition-all shadow-xs font-medium">
                            </div>
                        </div>
                        
                        <div class="pt-6 flex items-center justify-end gap-3 mt-6 border-t border-gray-200">
                            <button type="button" @click="showCreateModal = false" class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-300">
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
                            <button type="button" @click="showCreateModal = false" class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-300">
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
