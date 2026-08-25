<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                {{ __('Edit Event') }}
            </h2>
            <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700 transition-colors flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto pb-12 pt-4 px-4 sm:px-6 lg:px-8">
        


        <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden">
            <!-- Header Section -->
            <div class="px-8 py-6 border-b border-gray-100 bg-slate-50/70 flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center font-bold shadow-xs
                        {{ $event->team_type === 'product_team' ? 'bg-blue-100 text-blue-600 border border-blue-200' : ($event->team_type === 'digital_team' ? 'bg-teal-100 text-teal-600 border border-teal-200' : 'bg-amber-100 text-amber-600 border border-amber-200') }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-[10px] font-bold uppercase tracking-wider border
                                {{ $event->team_type == 'product_team' ? 'bg-blue-50 text-blue-700 border-blue-200' : ($event->team_type == 'digital_team' ? 'bg-teal-50 text-teal-700 border-teal-200' : 'bg-amber-50 text-amber-700 border-amber-200') }}">
                                {{ str_replace('_', ' ', $event->team_type) }}
                            </span>
                        </div>
                        <h1 class="text-2xl font-black text-gray-900 tracking-tight mt-0.5">
                            Edit Event Details
                        </h1>
                    </div>
                </div>
            </div>

            <!-- Form Body -->
            <div class="p-8">
                <form action="{{ route('events.update', $event) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    @if($event->team_type === 'product_team')
                        <!-- Product Team Form Fields -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Publish Date*</label>
                                <input type="date" name="event_date" value="{{ old('event_date', $event->event_date ? $event->event_date->format('Y-m-d') : '') }}" required class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs font-medium">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Shoot Date</label>
                                <input type="date" name="shoot_date" value="{{ old('shoot_date', $event->shoot_date ? $event->shoot_date->format('Y-m-d') : '') }}" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs font-medium">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Content Title*</label>
                            <input type="text" name="content_title" value="{{ old('content_title', $event->content_title) }}" required class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs font-medium" placeholder="e.g. Life Style Review">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Product</label>
                                <select name="product" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs font-medium">
                                    <option value="">Select Product</option>
                                    @if(isset($masterData['product']))
                                        @foreach($masterData['product'] as $item)
                                            <option value="{{ $item->value }}" {{ old('product', $event->product) == $item->value ? 'selected' : '' }}>{{ $item->value }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Platform</label>
                                <select name="platform" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs font-medium">
                                    <option value="">Select Platform</option>
                                    @if(isset($masterData['platform']))
                                        @foreach($masterData['platform'] as $item)
                                            <option value="{{ $item->value }}" {{ old('platform', $event->platform) == $item->value ? 'selected' : '' }}>{{ $item->value }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Content Objective</label>
                            <input type="text" name="content_objective" value="{{ old('content_objective', $event->content_objective) }}" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs font-medium" placeholder="Briefly describe the goal">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">A.I.P.E Pillar</label>
                                <select name="aipe_pillar" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs font-medium">
                                    <option value="">Select Pillar</option>
                                    @if(isset($masterData['aipe_pillar']))
                                        @foreach($masterData['aipe_pillar'] as $item)
                                            <option value="{{ $item->value }}" {{ old('aipe_pillar', $event->aipe_pillar) == $item->value ? 'selected' : '' }}>{{ $item->value }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Color Concern</label>
                                <input type="text" name="color_concern" value="{{ old('color_concern', $event->color_concern) }}" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs font-medium">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Format</label>
                                <select name="format" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs font-medium">
                                    <option value="">Select Format</option>
                                    @if(isset($masterData['format']))
                                        @foreach($masterData['format'] as $item)
                                            <option value="{{ $item->value }}" {{ old('format', $event->format) == $item->value ? 'selected' : '' }}>{{ $item->value }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Budget</label>
                                <input type="text" name="boosting_budget" value="{{ old('boosting_budget', $event->boosting_budget) }}" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs font-medium">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Drive Link</label>
                                <input type="text" name="drive_link" value="{{ old('drive_link', $event->drive_link) }}" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs font-medium" placeholder="https://drive.google.com/...">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Remarks</label>
                            <input type="text" name="remarks" value="{{ old('remarks', $event->remarks) }}" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs font-medium">
                        </div>

                    @elseif($event->team_type === 'digital_team')
                        <!-- Digital Team Form Fields -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Event Date*</label>
                                <input type="date" name="event_date" value="{{ old('event_date', $event->event_date ? $event->event_date->format('Y-m-d') : '') }}" required class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:bg-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none transition-all shadow-xs font-medium">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Post No.</label>
                                <input type="text" name="post_no" value="{{ old('post_no', $event->post_no) }}" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:bg-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none transition-all shadow-xs font-medium" placeholder="e.g. 1">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Product Focus</label>
                                <select name="product_focus" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:bg-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none transition-all shadow-xs font-medium">
                                    <option value="">Select Product</option>
                                    @if(isset($masterData['product']))
                                        @foreach($masterData['product'] as $item)
                                            <option value="{{ $item->value }}" {{ old('product_focus', $event->product_focus) == $item->value ? 'selected' : '' }}>{{ $item->value }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">A.I.P.E Pillar</label>
                                <select name="aipe_pillar" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:bg-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none transition-all shadow-xs font-medium">
                                    <option value="">Select Pillar</option>
                                    @if(isset($masterData['aipe_pillar']))
                                        @foreach($masterData['aipe_pillar'] as $item)
                                            <option value="{{ $item->value }}" {{ old('aipe_pillar', $event->aipe_pillar) == $item->value ? 'selected' : '' }}>{{ $item->value }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Content Objective</label>
                            <input type="text" name="content_objective" value="{{ old('content_objective', $event->content_objective) }}" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:bg-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none transition-all shadow-xs font-medium" placeholder="Briefly describe the goal">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Asset/Drive Link</label>
                                <input type="text" name="drive_link" value="{{ old('drive_link', $event->drive_link) }}" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:bg-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none transition-all shadow-xs font-medium" placeholder="https://drive.google.com/...">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Format</label>
                                <select name="format" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:bg-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none transition-all shadow-xs font-medium">
                                    <option value="">Select Format</option>
                                    @if(isset($masterData['format']))
                                        @foreach($masterData['format'] as $item)
                                            <option value="{{ $item->value }}" {{ old('format', $event->format) == $item->value ? 'selected' : '' }}>{{ $item->value }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Budget</label>
                                <input type="text" name="boosting_budget" value="{{ old('boosting_budget', $event->boosting_budget) }}" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:bg-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none transition-all shadow-xs font-medium">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Remarks</label>
                                <input type="text" name="remarks" value="{{ old('remarks', $event->remarks) }}" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:bg-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none transition-all shadow-xs font-medium">
                            </div>
                        </div>

                    @else
                        <!-- Global Team Form Fields -->
                        <div class="space-y-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Event Date*</label>
                                <input type="date" name="event_date" value="{{ old('event_date', $event->event_date ? $event->event_date->format('Y-m-d') : '') }}" required class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none transition-all shadow-xs font-medium">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Event Title*</label>
                                <input type="text" name="content_title" value="{{ old('content_title', $event->content_title) }}" required class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none transition-all shadow-xs font-medium" placeholder="e.g. World Tourism Day">
                            </div>
                        </div>
                    @endif

                    <div class="pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                        <a href="{{ route('dashboard') }}" class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2.5 text-sm font-bold text-white bg-blue-600 border border-transparent rounded-xl hover:bg-blue-700 shadow-md shadow-blue-500/20 transition-all focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
