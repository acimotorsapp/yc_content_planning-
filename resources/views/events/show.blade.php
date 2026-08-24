<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-2xl text-white leading-tight">
                {{ __('Event Details') }}
            </h2>
            <a href="{{ route('dashboard') }}" class="text-sm text-gray-400 hover:text-white transition-colors flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto pb-12 px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-[#111] rounded-2xl border border-gray-200 dark:border-white/5 shadow-xl overflow-hidden mt-8">
            <!-- Header Section -->
            <div class="p-8 border-b border-gray-200 dark:border-white/5 bg-gray-50 dark:bg-[#161616] flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold uppercase tracking-wider border
                            {{ $event->team_type == 'product_team' ? 'bg-blue-500/10 text-blue-400 border-blue-500/20' : ($event->team_type == 'digital_team' ? 'bg-teal-500/10 text-teal-400 border-teal-500/20' : 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20') }}">
                            {{ str_replace('_', ' ', $event->team_type) }}
                        </span>
                        @if($event->aipe_pillar)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold uppercase tracking-wider bg-yellow-500/20 text-yellow-500 border border-yellow-500/30">
                                {{ $event->aipe_pillar }}
                            </span>
                        @endif
                    </div>
                    <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                        {{ $event->team_type == 'product_team' ? $event->content_title : ($event->team_type == 'global_team' ? $event->content_title : 'Post #'.$event->post_no) }}
                    </h1>
                </div>
                <div class="flex items-center text-sm font-medium text-gray-500 bg-white dark:bg-[#1a1a1a] px-4 py-2 rounded-lg border border-gray-200 dark:border-white/10 shadow-sm">
                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
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
                            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Content Objective</h3>
                            <p class="text-base text-gray-900 dark:text-gray-200 font-medium">{{ $event->content_objective }}</p>
                        </div>
                        @endif

                        <div>
                            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Scheduled By</h3>
                            <div class="flex items-center mt-1">
                                <div class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-800 flex items-center justify-center text-gray-600 dark:text-gray-300 font-bold mr-3 border border-gray-300 dark:border-white/10">
                                    {{ substr($event->user->name ?? 'U', 0, 1) }}
                                </div>
                                <span class="text-base font-medium text-gray-900 dark:text-gray-200">{{ $event->user->name ?? 'Unknown User' }}</span>
                            </div>
                        </div>
                        
                        @if($event->platform)
                        <div>
                            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Platform</h3>
                            <p class="text-base text-gray-900 dark:text-gray-200 font-medium">{{ $event->platform }}</p>
                        </div>
                        @endif
                        
                        @if($event->format)
                        <div>
                            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Format</h3>
                            <p class="text-base text-gray-900 dark:text-gray-200 font-medium">{{ $event->format }}</p>
                        </div>
                        @endif
                    </div>

                    <!-- Column 2 -->
                    <div class="space-y-6">
                        @if($event->product || $event->product_focus)
                        <div>
                            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Product Focus</h3>
                            <p class="text-base text-gray-900 dark:text-gray-200 font-medium">{{ $event->product ?? $event->product_focus }}</p>
                        </div>
                        @endif

                        @if($event->shoot_date)
                        <div>
                            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Shoot Date</h3>
                            <p class="text-base text-gray-900 dark:text-gray-200 font-medium">{{ $event->shoot_date->format('F j, Y') }}</p>
                        </div>
                        @endif

                        @if($event->color_concern)
                        <div>
                            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Color Concern</h3>
                            <p class="text-base text-gray-900 dark:text-gray-200 font-medium">{{ $event->color_concern }}</p>
                        </div>
                        @endif

                        @if($event->boosting_budget)
                        <div>
                            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Budget</h3>
                            <p class="text-base text-gray-900 dark:text-gray-200 font-medium">{{ $event->boosting_budget }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                @if($event->remarks || $event->drive_link)
                <div class="mt-8 pt-8 border-t border-gray-200 dark:border-white/5 space-y-6">
                    @if($event->remarks)
                    <div>
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Remarks</h3>
                        <div class="bg-gray-50 dark:bg-[#1a1a1a] p-4 rounded-xl border border-gray-200 dark:border-white/5 text-gray-800 dark:text-gray-300">
                            {{ $event->remarks }}
                        </div>
                    </div>
                    @endif

                    @if($event->drive_link)
                    <div>
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Assets / Drive Link</h3>
                        <a href="{{ $event->drive_link }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-500/10 text-blue-500 border border-blue-500/20 rounded-lg hover:bg-blue-500 hover:text-white transition-all font-medium text-sm">
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
