<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                {{ __('Event Details') }}
            </h2>
            <div class="flex items-center gap-3">
                @if(auth()->id() === $event->user_id || auth()->user()->role === 'super_admin')
                <a href="{{ route('events.edit', $event) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl shadow-xs transition-all">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Edit Event
                </a>
                @endif
                <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700 transition-colors flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to Dashboard
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto pb-12 px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden mt-6">
            <!-- Header Section -->
            <div class="p-8 border-b border-gray-100 bg-slate-50/70 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-xl text-xs font-bold uppercase tracking-wider border
                            {{ $event->team_type == 'product_team' ? 'bg-blue-50 text-blue-700 border-blue-200' : ($event->team_type == 'digital_team' ? 'bg-teal-50 text-teal-700 border-teal-200' : 'bg-amber-50 text-amber-700 border-amber-200') }}">
                            {{ str_replace('_', ' ', $event->team_type) }}
                        </span>
                        @if($event->aipe_pillar)
                            <span class="inline-flex items-center px-3 py-1 rounded-xl text-xs font-bold uppercase tracking-wider bg-amber-100 text-amber-800 border border-amber-200">
                                {{ $event->aipe_pillar }}
                            </span>
                        @endif
                    </div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">
                        {{ $event->team_type == 'product_team' ? $event->content_title : ($event->team_type == 'global_team' ? $event->content_title : 'Post #'.$event->post_no) }}
                    </h1>
                </div>
                <div class="flex items-center text-sm font-bold text-gray-700 bg-white px-4 py-2.5 rounded-xl border border-gray-200 shadow-xs">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    {{ $event->event_date->format('l, F j, Y') }}
                </div>
            </div>

            <!-- Details Section -->
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">
                    <!-- Column 1 -->
                    <div class="space-y-6">
                        @if($event->content_objective)
                        <div>
                            <h3 class="text-xs font-extrabold text-gray-400 uppercase tracking-widest mb-1.5">Content Objective</h3>
                            <p class="text-base text-gray-900 font-semibold">{{ $event->content_objective }}</p>
                        </div>
                        @endif

                        <div>
                            <h3 class="text-xs font-extrabold text-gray-400 uppercase tracking-widest mb-1.5">Scheduled By</h3>
                            <div class="flex items-center mt-1">
                                <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold mr-3 border border-blue-200">
                                    {{ substr($event->user->name ?? 'U', 0, 1) }}
                                </div>
                                <span class="text-base font-bold text-gray-900">{{ $event->user->name ?? 'Unknown User' }}</span>
                            </div>
                        </div>
                        
                        @if($event->platform)
                        <div>
                            <h3 class="text-xs font-extrabold text-gray-400 uppercase tracking-widest mb-1.5">Platform</h3>
                            <p class="text-base text-gray-900 font-semibold">{{ $event->platform }}</p>
                        </div>
                        @endif
                        
                        @if($event->format)
                        <div>
                            <h3 class="text-xs font-extrabold text-gray-400 uppercase tracking-widest mb-1.5">Format</h3>
                            <p class="text-base text-gray-900 font-semibold">{{ $event->format }}</p>
                        </div>
                        @endif
                    </div>

                    <!-- Column 2 -->
                    <div class="space-y-6">
                        @if($event->product || $event->product_focus)
                        <div>
                            <h3 class="text-xs font-extrabold text-gray-400 uppercase tracking-widest mb-1.5">Product Focus</h3>
                            <p class="text-base text-gray-900 font-semibold">{{ $event->product ?? $event->product_focus }}</p>
                        </div>
                        @endif

                        @if($event->shoot_date)
                        <div>
                            <h3 class="text-xs font-extrabold text-gray-400 uppercase tracking-widest mb-1.5">Shoot Date</h3>
                            <p class="text-base text-gray-900 font-semibold">{{ $event->shoot_date->format('F j, Y') }}</p>
                        </div>
                        @endif

                        @if($event->color_concern)
                        <div>
                            <h3 class="text-xs font-extrabold text-gray-400 uppercase tracking-widest mb-1.5">Color Concern</h3>
                            <p class="text-base text-gray-900 font-semibold">{{ $event->color_concern }}</p>
                        </div>
                        @endif

                        @if($event->boosting_budget)
                        <div>
                            <h3 class="text-xs font-extrabold text-gray-400 uppercase tracking-widest mb-1.5">Budget</h3>
                            <p class="text-base text-gray-900 font-semibold">{{ $event->boosting_budget }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                @if($event->remarks || $event->drive_link)
                <div class="mt-8 pt-8 border-t border-gray-100 space-y-6">
                    @if($event->remarks)
                    <div>
                        <h3 class="text-xs font-extrabold text-gray-400 uppercase tracking-widest mb-2">Remarks</h3>
                        <div class="bg-slate-50 p-4 rounded-2xl border border-gray-200 text-gray-800 font-medium">
                            {{ $event->remarks }}
                        </div>
                    </div>
                    @endif

                    @if($event->drive_link)
                    <div>
                        <h3 class="text-xs font-extrabold text-gray-400 uppercase tracking-widest mb-2">Assets / Drive Link</h3>
                        <a href="{{ $event->drive_link }}" target="_blank" class="inline-flex items-center px-5 py-2.5 bg-blue-50 text-blue-700 border border-blue-200 rounded-xl hover:bg-blue-600 hover:text-white transition-all font-bold text-sm shadow-xs">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                            Open External Link
                        </a>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
