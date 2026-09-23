<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h2 class="font-extrabold text-xl sm:text-2xl text-gray-900 tracking-tight flex items-center gap-2.5">
                    <span class="p-2 rounded-xl bg-blue-50 text-blue-600 inline-flex">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                    </span>
                    {{ __('Bulk Data Upload & Import Hub') }}
                </h2>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">
                    Download official spreadsheet templates, fill in your content data, and import directly into the system.
                </p>
            </div>
            
            <!-- Summary Stats Badges -->
            <div class="flex flex-wrap items-center gap-2 text-xs font-semibold">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                    <span>Total Events:</span>
                    <strong class="font-black text-blue-900">{{ $totalEvents }}</strong>
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-50 border border-indigo-200 text-indigo-800 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                    <span>Product Team:</span>
                    <strong class="font-black text-indigo-900">{{ $productEventsCount }}</strong>
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Digital Team:</span>
                    <strong class="font-black text-emerald-900">{{ $digitalEventsCount }}</strong>
                </span>
            </div>
        </div>
    </x-slot>

    <div x-data="{ 
            activeTab: 'events',
            teamType: 'auto',
            fileName: '',
            fileSize: '',
            isDragging: false,
            masterFileName: '',
            masterFileSize: '',
            isMasterDragging: false
         }" class="max-w-7xl mx-auto pb-16 pt-2 sm:pt-4 space-y-8">

        <!-- Notification Alerts -->
        @if (session('success'))
            <div class="p-4 sm:p-5 rounded-2xl bg-emerald-50 border-2 border-emerald-200 text-emerald-900 flex items-start gap-3.5 shadow-sm animate-fade-in-up">
                <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center shrink-0 shadow-xs">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <div class="flex-1 text-sm">
                    <div class="font-black text-emerald-950 text-base">Upload &amp; Import Successful!</div>
                    <div class="mt-0.5 font-medium text-emerald-800 leading-relaxed">{{ session('success') }}</div>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 sm:p-5 rounded-2xl bg-rose-50 border-2 border-rose-200 text-rose-900 flex items-start gap-3.5 shadow-sm animate-fade-in-up">
                <div class="w-10 h-10 rounded-xl bg-rose-500 text-white flex items-center justify-center shrink-0 shadow-xs">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </div>
                <div class="flex-1 text-sm">
                    <div class="font-black text-rose-950 text-base">Upload Failed</div>
                    <div class="mt-0.5 font-medium text-rose-800 leading-relaxed">{{ session('error') }}</div>
                </div>
            </div>
        @endif

        @if (isset($errors) && $errors->any())
            <div class="p-4 sm:p-5 rounded-2xl bg-amber-50 border-2 border-amber-200 text-amber-950 flex items-start gap-3.5 shadow-sm animate-fade-in-up">
                <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-xs">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div class="flex-1 text-sm">
                    <div class="font-black text-amber-950 text-base">Validation Notice</div>
                    <ul class="list-disc list-inside mt-1 space-y-0.5 text-xs text-amber-900 font-semibold">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @if (session('upload_errors') && count(session('upload_errors')) > 0)
            <div class="p-4 sm:p-5 rounded-2xl bg-amber-50/90 border border-amber-200 text-amber-900 text-sm">
                <div class="font-bold text-amber-950 flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Row Warnings during import:
                </div>
                <ul class="list-disc list-inside mt-1.5 space-y-0.5 text-xs text-amber-800 font-medium">
                    @foreach(session('upload_errors') as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- 3-Step Visual Process Guide -->
        <div class="bg-white rounded-3xl border border-gray-200/90 p-5 sm:p-7 shadow-xs">
            <div class="flex items-center justify-between gap-2 mb-5">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-200 text-blue-700 text-xs font-black uppercase tracking-wider">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    Simple 3-Step Process
                </div>
                <span class="text-xs text-gray-500 font-medium hidden sm:inline">Follow these steps to import your planning spreadsheets</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6 relative">
                <!-- Step 1 Box -->
                <div class="flex items-start gap-4 p-4 sm:p-5 rounded-2xl bg-gradient-to-br from-blue-50/80 to-blue-50/30 border-2 border-blue-100">
                    <div class="w-10 h-10 rounded-2xl bg-blue-600 text-white font-black text-base flex items-center justify-center shrink-0 shadow-sm shadow-blue-500/30">
                        1
                    </div>
                    <div>
                        <div class="text-[11px] font-black uppercase tracking-wider text-blue-600">Step 1</div>
                        <h4 class="text-sm font-black text-gray-900 mt-0.5">Download Starter Template</h4>
                        <p class="text-xs text-gray-600 mt-1 leading-relaxed">
                            Get the pre-formatted <strong>Full 5-Sheet Workbook</strong> or single department template (.xlsx / .csv).
                        </p>
                    </div>
                </div>

                <!-- Step 2 Box -->
                <div class="flex items-start gap-4 p-4 sm:p-5 rounded-2xl bg-gradient-to-br from-indigo-50/80 to-indigo-50/30 border-2 border-indigo-100">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-600 text-white font-black text-base flex items-center justify-center shrink-0 shadow-sm shadow-indigo-500/30">
                        2
                    </div>
                    <div>
                        <div class="text-[11px] font-black uppercase tracking-wider text-indigo-600">Step 2</div>
                        <h4 class="text-sm font-black text-gray-900 mt-0.5">Fill In Your Data</h4>
                        <p class="text-xs text-gray-600 mt-1 leading-relaxed">
                            Add event dates, pillars, content links, <strong>Financial Budget</strong>, and <strong>Boosting Budget</strong>.
                        </p>
                    </div>
                </div>

                <!-- Step 3 Box -->
                <div class="flex items-start gap-4 p-4 sm:p-5 rounded-2xl bg-gradient-to-br from-emerald-50/80 to-emerald-50/30 border-2 border-emerald-100">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-600 text-white font-black text-base flex items-center justify-center shrink-0 shadow-sm shadow-emerald-500/30">
                        3
                    </div>
                    <div>
                        <div class="text-[11px] font-black uppercase tracking-wider text-emerald-600">Step 3</div>
                        <h4 class="text-sm font-black text-gray-900 mt-0.5">Upload &amp; Auto-Import</h4>
                        <p class="text-xs text-gray-600 mt-1 leading-relaxed">
                            Drop your completed file in the upload zone. The system will automatically parse and sync all events.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- STEP 1: STARTER TEMPLATES HUB -->
        <div class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 px-1">
                <div>
                    <h3 class="text-lg sm:text-xl font-black text-gray-900 flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-blue-600 text-white text-xs font-black inline-flex items-center justify-center">1</span>
                        <span>Step 1: Download Official Templates</span>
                    </h3>
                    <p class="text-xs sm:text-sm text-gray-500 mt-0.5">
                        Choose either the recommended All-in-One Workbook or individual sheets for specific teams.
                    </p>
                </div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold uppercase tracking-wider self-start sm:self-auto">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    <span>Duplicate Safe &middot; Existing Records Protected</span>
                </div>
            </div>

            <!-- Hero Card: RECOMMENDED ALL-IN-ONE WORKBOOK -->
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-indigo-950 to-blue-950 p-6 sm:p-8 text-white shadow-xl border border-indigo-900/50">
                <div class="absolute -right-16 -top-16 w-64 h-64 bg-cyan-500/20 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -left-16 -bottom-16 w-64 h-64 bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                    <div class="space-y-3.5 max-w-2xl">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-400/20 border border-cyan-400/40 text-cyan-300 text-xs font-black uppercase tracking-wider">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                            ⭐ Recommended Option (All-in-One)
                        </div>
                        <h3 class="text-xl sm:text-2xl font-black tracking-tight text-white">
                            Official YAMAHA Content Plan Workbook (Full 5 Sheets)
                        </h3>
                        <p class="text-slate-300 text-xs sm:text-sm leading-relaxed">
                            This single workbook contains all 5 standard sheets: <strong class="text-white">Final Content Calendar</strong>, <strong class="text-white">Product Team</strong>, <strong class="text-white">Digital Team</strong>, <strong class="text-white">Month Logic</strong>, and <strong class="text-white">Staff IDs</strong>.
                            Fill it out and upload back using <strong class="text-cyan-300">Auto-Detect</strong> to import everything in one pass.
                        </p>
                        
                        <div class="flex flex-wrap gap-2 pt-1 text-xs">
                            <span class="px-2.5 py-1 rounded-lg bg-white/10 border border-white/10 text-slate-200 font-semibold">✓ 5 Sheets Pre-formatted</span>
                            <span class="px-2.5 py-1 rounded-lg bg-white/10 border border-white/10 text-slate-200 font-semibold">✓ Financial &amp; Boosting Budget columns included</span>
                            <span class="px-2.5 py-1 rounded-lg bg-white/10 border border-white/10 text-slate-200 font-semibold">✓ Ready for 1-Click Auto-Detect</span>
                        </div>
                    </div>

                    <!-- Hero Download Button -->
                    <div class="shrink-0 flex flex-col items-start lg:items-end gap-2">
                        <a href="{{ route('admin.bulk_upload.sample', ['type' => 'full-content-plan']) }}" 
                           class="w-full sm:w-auto inline-flex items-center justify-center gap-3 px-6 py-4 rounded-2xl bg-gradient-to-r from-cyan-400 to-blue-500 hover:from-cyan-300 hover:to-blue-400 text-slate-950 font-black text-sm sm:text-base shadow-lg shadow-cyan-500/25 transition-all transform hover:-translate-y-0.5 group cursor-pointer">
                            <svg class="w-5 h-5 text-slate-950 group-hover:translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            <span>Download Full Workbook (.xlsx)</span>
                        </a>
                        <span class="text-[11px] text-slate-400 font-medium">Microsoft Excel Format &middot; Includes sample rows</span>
                    </div>
                </div>
            </div>

            <!-- Individual Single-Sheet Templates Section -->
            <div class="pt-2">
                <div class="mb-3 px-1">
                    <h4 class="text-xs font-black text-gray-700 uppercase tracking-wider">
                        Or Download Individual Single-Sheet Templates
                    </h4>
                    <p class="text-xs text-gray-500">
                        If you want to prepare or upload data for only one specific department or category, choose below:
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @php
                        $templates = [
                            [
                                'type' => 'content-calendar',
                                'title' => 'Final Content Calender',
                                'subtitle' => 'Monthly calendar &rarr; Digital Team',
                                'badge' => 'Digital Schedule',
                                'icon_bg' => 'bg-sky-100 text-sky-700',
                                'border' => 'hover:border-sky-300',
                                'desc' => 'Campaign objectives, creative direction, content links, and boosting budgets.',
                            ],
                            [
                                'type' => 'product-events',
                                'title' => 'Product Team',
                                'subtitle' => 'Product events & campaigns',
                                'badge' => 'Product Team',
                                'icon_bg' => 'bg-indigo-100 text-indigo-700',
                                'border' => 'hover:border-indigo-300',
                                'desc' => 'Product launches, shoot & publish dates, color concerns, and budgets.',
                            ],
                            [
                                'type' => 'digital-events',
                                'title' => 'Digital team',
                                'subtitle' => 'Social media posts & reels',
                                'badge' => 'Digital Posts',
                                'icon_bg' => 'bg-teal-100 text-teal-700',
                                'border' => 'hover:border-teal-300',
                                'desc' => 'Post sequence, AIPE pillars, product focus, asset links, and boosting.',
                            ],
                            [
                                'type' => 'plan-logic',
                                'title' => 'Month Logic',
                                'subtitle' => 'Pillars, share & methodology',
                                'badge' => 'Strategy & Logic',
                                'icon_bg' => 'bg-amber-100 text-amber-700',
                                'border' => 'hover:border-amber-300',
                                'desc' => 'Volume share shifts, retail forecasts, and strategic allocation notes.',
                            ],
                            [
                                'type' => 'staff',
                                'title' => 'Staff ID & Designation',
                                'subtitle' => 'Team member accounts',
                                'badge' => 'App Users',
                                'icon_bg' => 'bg-purple-100 text-purple-700',
                                'border' => 'hover:border-purple-300',
                                'desc' => 'Staff IDs, employee names, designations, and corporate emails for logins.',
                            ],
                            [
                                'type' => 'master-data',
                                'title' => 'Master Data',
                                'subtitle' => 'System dropdown categories',
                                'badge' => 'Master Categories',
                                'icon_bg' => 'bg-emerald-100 text-emerald-700',
                                'border' => 'hover:border-emerald-300',
                                'desc' => 'Values for platforms, formats, AIPE pillars, and Yamaha bike models.',
                            ],
                        ];
                    @endphp

                    @foreach($templates as $tpl)
                        <div class="bg-white rounded-2xl border border-gray-200/90 p-4 sm:p-5 flex flex-col justify-between transition-all duration-200 hover:shadow-md {{ $tpl['border'] }} group">
                            <div class="space-y-2.5">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="w-9 h-9 rounded-xl {{ $tpl['icon_bg'] }} flex items-center justify-center font-black text-sm shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <span class="text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded-full bg-gray-100 text-gray-700">
                                        {{ $tpl['badge'] }}
                                    </span>
                                </div>

                                <div>
                                    <h5 class="text-sm font-extrabold text-gray-900 group-hover:text-blue-600 transition-colors">
                                        {!! $tpl['title'] !!}
                                    </h5>
                                    <div class="text-[11px] text-gray-500 font-medium">
                                        {!! $tpl['subtitle'] !!}
                                    </div>
                                    <p class="text-xs text-gray-600 mt-1 line-clamp-2 leading-relaxed">
                                        {{ $tpl['desc'] }}
                                    </p>
                                </div>
                            </div>

                            <!-- Download Buttons (Excel & CSV) -->
                            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center gap-2">
                                <a href="{{ route('admin.bulk_upload.sample', ['type' => $tpl['type'], 'format' => 'xlsx']) }}" 
                                   class="flex-1 inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-extrabold transition-colors"
                                   title="Download as Microsoft Excel (.xlsx)">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    .XLSX (Excel)
                                </a>

                                <a href="{{ route('admin.bulk_upload.sample', ['type' => $tpl['type'], 'format' => 'csv']) }}" 
                                   class="inline-flex items-center justify-center py-2 px-3 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold transition-colors"
                                   title="Download as Plain Text CSV (.csv)">
                                    .CSV
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- STEP 2 & 3: UPLOAD STATION -->
        <div class="space-y-4 pt-4">
            <div class="px-1">
                <h3 class="text-lg sm:text-xl font-black text-gray-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-indigo-600 text-white text-xs font-black inline-flex items-center justify-center">2 &amp; 3</span>
                    <span>Step 2 &amp; 3: Configure Options &amp; Upload Completed File</span>
                </h3>
                <p class="text-xs sm:text-sm text-gray-500 mt-0.5">
                    Select your import tab, configure options, and drop your completed spreadsheet file below.
                </p>
            </div>

            <!-- Upload Type Navigation Tabs -->
            <div class="flex gap-2 sm:gap-3 border-b border-gray-200 pb-3 overflow-x-auto nice-scroll">
                <button @click="activeTab = 'events'"
                    :class="activeTab === 'events' ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20 font-black' : 'bg-white text-gray-700 hover:bg-gray-100 font-bold border border-gray-200'"
                    class="inline-flex shrink-0 items-center gap-2 px-5 py-3 rounded-2xl text-xs sm:text-sm whitespace-nowrap transition-all duration-150 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span>Upload Content Events / Full Workbook</span>
                </button>

                <button @click="activeTab = 'master'"
                    :class="activeTab === 'master' ? 'bg-teal-600 text-white shadow-md shadow-teal-500/20 font-black' : 'bg-white text-gray-700 hover:bg-gray-100 font-bold border border-gray-200'"
                    class="inline-flex shrink-0 items-center gap-2 px-5 py-3 rounded-2xl text-xs sm:text-sm whitespace-nowrap transition-all duration-150 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    <span>Upload Master Categories</span>
                </button>
            </div>

            <!-- TAB 1: Bulk Upload Events Form -->
            <div x-show="activeTab === 'events'" class="space-y-6">
                <div class="bg-white rounded-3xl border border-gray-200 shadow-sm p-5 sm:p-8 space-y-6">
                    <form action="{{ route('admin.bulk_upload.events') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <!-- Target Configuration Options -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-6 p-5 rounded-2xl bg-slate-50/90 border border-gray-200">
                            <div>
                                <label class="block text-xs font-black text-gray-800 uppercase tracking-wider mb-2">
                                    Team / Source Type
                                </label>
                                <select name="team_type" x-model="teamType" class="w-full bg-white border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 font-bold text-sm">
                                    <option value="auto">✨ Auto-Detect (YAMAHA Content Plan Workbook - Reads All 5 Sheets)</option>
                                    <option value="product_team">Product Team Events (Single Sheet)</option>
                                    <option value="digital_team">Digital Team Events (Single Sheet)</option>
                                    <option value="global_team">Global Events</option>
                                </select>
                                <p class="text-[11px] text-gray-500 mt-1.5 leading-relaxed">
                                    Keep <strong class="text-blue-600">"Auto-Detect"</strong> for the full YAMAHA Content Plan workbook — it automatically reads the calendar, Product Team, Digital team, Month Logic, and Staff sheets in one pass.
                                </p>
                            </div>

                            <div>
                                <label class="block text-xs font-black text-gray-800 uppercase tracking-wider mb-2">
                                    Target Calendar Year
                                </label>
                                <input type="number" name="target_year" value="2026" min="2020" max="2035" class="w-full bg-white border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 font-bold text-sm">
                                <p class="text-[11px] text-gray-500 mt-1.5 leading-relaxed">
                                    Used when spreadsheet rows contain dates without an explicit year (e.g. "1 Sep", "15 August").
                                </p>
                            </div>
                        </div>

                        <!-- Drag & Drop File Upload Box -->
                        <div>
                            <label class="block text-xs font-black text-gray-800 uppercase tracking-wider mb-2">
                                Select or Drop Spreadsheet File (.xlsx, .xls, .csv)
                            </label>
                            <div 
                                @dragover.prevent="isDragging = true"
                                @dragleave.prevent="isDragging = false"
                                @drop.prevent="
                                    isDragging = false;
                                    if ($event.dataTransfer.files.length > 0) {
                                        $refs.eventFileInput.files = $event.dataTransfer.files;
                                        fileName = $event.dataTransfer.files[0].name;
                                        fileSize = ($event.dataTransfer.files[0].size / 1024).toFixed(1) + ' KB';
                                    }
                                "
                                :class="isDragging ? 'border-blue-500 bg-blue-50/60 ring-4 ring-blue-500/20' : 'border-gray-300 bg-slate-50/80 hover:bg-slate-100/80 hover:border-blue-400'"
                                class="relative flex flex-col items-center justify-center border-2 border-dashed rounded-3xl p-8 sm:p-12 text-center transition-all cursor-pointer group">
                                
                                <input 
                                    x-ref="eventFileInput"
                                    type="file" 
                                    name="file" 
                                    required
                                    accept=".xlsx,.xls,.csv" 
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                    @change="
                                        if ($refs.eventFileInput.files.length > 0) {
                                            fileName = $refs.eventFileInput.files[0].name;
                                            fileSize = ($refs.eventFileInput.files[0].size / 1024).toFixed(1) + ' KB';
                                        }
                                    "
                                >

                                <!-- No file chosen state -->
                                <div x-show="!fileName" class="space-y-3 pointer-events-none">
                                    <div class="w-16 h-16 mx-auto rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center group-hover:scale-110 transition-transform shadow-xs">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                    </div>
                                    <div>
                                        <span class="text-sm font-black text-blue-600 group-hover:text-blue-700">Click to choose a file</span>
                                        <span class="text-sm text-gray-600 font-medium"> or drag and drop spreadsheet here</span>
                                    </div>
                                    <p class="text-xs text-gray-400 font-medium">Supports Microsoft Excel (.xlsx, .xls) and CSV (up to 20MB)</p>
                                </div>

                                <!-- File selected state -->
                                <div x-show="fileName" style="display: none;" class="flex items-center gap-4 p-4 sm:p-5 rounded-2xl bg-white border-2 border-emerald-400 shadow-sm z-10 w-full max-w-lg mx-auto">
                                    <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <div class="text-left flex-1 min-w-0">
                                        <div class="text-sm font-black text-gray-900 truncate" x-text="fileName"></div>
                                        <div class="text-xs text-emerald-600 font-bold" x-text="'Ready for upload • ' + fileSize"></div>
                                    </div>
                                    <button 
                                        type="button" 
                                        @click.stop="$refs.eventFileInput.value = ''; fileName = ''; fileSize = '';"
                                        class="p-2 rounded-xl text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors shrink-0 cursor-pointer"
                                        title="Remove file">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 border-t border-gray-100">
                            <div class="text-xs text-gray-500 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span>Existing events in database will be protected from duplicates automatically.</span>
                            </div>
                            <button type="submit" class="inline-flex items-center justify-center w-full sm:w-auto px-8 py-3.5 text-sm font-black text-white rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all shadow-md shadow-blue-500/25 transform hover:-translate-y-0.5 cursor-pointer">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                Start Bulk Upload &amp; Sync
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Sheets Reference Guide -->
                <div class="bg-white rounded-3xl border border-gray-200 shadow-sm p-6 sm:p-8 space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-base font-black text-gray-900">Sheets &amp; Column Structure Reference Guide</h4>
                            <p class="text-xs text-gray-500">Sheets are detected by name; column headers are case-insensitive and support both Financial &amp; Boosting Budgets.</p>
                        </div>
                    </div>

                    @php
                        $sheetGuide = [
                            [
                                'name' => 'Final Content Calender (…)',
                                'match' => 'name contains "calend"',
                                'target' => 'Events — Digital Team',
                                'accent' => 'sky',
                                'note' => 'Header sits on row 3, under the month banner.',
                                'cols' => ['Date', 'Day', 'Product / Focus', 'AIPE Pillar', 'Format', 'Content Type', 'RTM / Campaign Objective', 'Content Gist & Creative Direction', 'Content Link', 'Budget', 'Platform', 'Boosting Budget'],
                            ],
                            [
                                'name' => 'Product Team',
                                'match' => 'name contains "product"',
                                'target' => 'Events — Product Team',
                                'accent' => 'indigo',
                                'note' => 'Publish Date is used as the event date when present.',
                                'cols' => ['Date', 'Day', 'Content', 'A.I.P.E Pillar', 'Content Objective', 'Shoot Date', 'Publish Date', 'Color Concern', 'Format', 'Budget', 'Platform', 'Product', 'Drive Link', 'Remarks', 'Boosting Budget'],
                            ],
                            [
                                'name' => 'Digital team',
                                'match' => 'name contains "digital"',
                                'target' => 'Events — Digital Team',
                                'accent' => 'teal',
                                'note' => 'Trailing "Planning Dependencies" / "Total Posts" blocks are ignored.',
                                'cols' => ['Date', 'Day', 'Post No.', 'A.I.P.E Pillar', 'Product Focus', 'Content Objective', 'Format', 'Asset/Drive Link', 'Remarks', 'Boosting budget'],
                            ],
                            [
                                'name' => '&lt;Month&gt; Logic',
                                'match' => 'name contains "logic"',
                                'target' => 'Content Plan Logic',
                                'accent' => 'amber',
                                'note' => 'Free-text notes under the table are stored too.',
                                'cols' => ['Product', 'Units', 'Share', '12-Mo Share Shift', 'Retail', 'Forecast', 'Posts This Month', 'Pillar Split', 'Why This Allocation'],
                            ],
                            [
                                'name' => 'Staff ID &amp; Designation',
                                'match' => 'name contains "staff"',
                                'target' => 'Users',
                                'accent' => 'rose',
                                'note' => 'Creates sign-in accounts with the shared default password.',
                                'cols' => ['Staff ID', 'Name', 'Designation', 'Email Address'],
                            ],
                        ];
                    @endphp

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 text-sm">
                        @foreach($sheetGuide as $s)
                            <div class="p-4 sm:p-5 rounded-2xl bg-slate-50 border border-gray-200/80 space-y-3">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <span class="font-black text-gray-900">{!! $s['name'] !!}</span>
                                    <span class="text-[10px] font-extrabold uppercase px-2.5 py-0.5 bg-blue-100 text-blue-700 rounded-md">{{ count($s['cols']) }} Columns</span>
                                </div>
                                <div class="flex flex-wrap items-center gap-1.5 text-[11px] text-gray-500 font-medium">
                                    <span class="px-2 py-0.5 bg-white border border-gray-200 rounded-md font-mono">{{ $s['match'] }}</span>
                                    <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                    <span class="font-bold text-gray-700">{{ $s['target'] }}</span>
                                </div>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($s['cols'] as $col)
                                        <span class="px-2.5 py-1 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-700 shadow-2xs">{{ $col }}</span>
                                    @endforeach
                                </div>
                                <p class="text-[11px] text-gray-500">{{ $s['note'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- TAB 2: Bulk Upload Master Data Form -->
            <div x-show="activeTab === 'master'" style="display: none;" class="space-y-6">
                <div class="bg-white rounded-3xl border border-gray-200 shadow-sm p-5 sm:p-8">
                    <form action="{{ route('admin.bulk_upload.master_data') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <div>
                            <div class="mb-4">
                                <h3 class="text-lg font-black text-gray-900">Upload Master Data Categories</h3>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    Import dropdown values for <strong class="text-gray-700">Platforms</strong>, <strong class="text-gray-700">Formats</strong>, <strong class="text-gray-700">AIPE Pillars</strong>, and <strong class="text-gray-700">Products</strong>. Existing values won't be duplicated.
                                </p>
                            </div>

                            <!-- Drag & Drop File Upload Box for Master Data -->
                            <div 
                                @dragover.prevent="isMasterDragging = true"
                                @dragleave.prevent="isMasterDragging = false"
                                @drop.prevent="
                                    isMasterDragging = false;
                                    if ($event.dataTransfer.files.length > 0) {
                                        $refs.masterFileInput.files = $event.dataTransfer.files;
                                        masterFileName = $event.dataTransfer.files[0].name;
                                        masterFileSize = ($event.dataTransfer.files[0].size / 1024).toFixed(1) + ' KB';
                                    }
                                "
                                :class="isMasterDragging ? 'border-teal-500 bg-teal-50/60 ring-4 ring-teal-500/20' : 'border-gray-300 bg-slate-50/80 hover:bg-slate-100/80 hover:border-teal-400'"
                                class="relative flex flex-col items-center justify-center border-2 border-dashed rounded-3xl p-8 sm:p-12 text-center transition-all cursor-pointer group">
                                
                                <input 
                                    x-ref="masterFileInput"
                                    type="file" 
                                    name="file" 
                                    required
                                    accept=".xlsx,.xls,.csv" 
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                    @change="
                                        if ($refs.masterFileInput.files.length > 0) {
                                            masterFileName = $refs.masterFileInput.files[0].name;
                                            masterFileSize = ($refs.masterFileInput.files[0].size / 1024).toFixed(1) + ' KB';
                                        }
                                    "
                                >

                                <!-- No file chosen state -->
                                <div x-show="!masterFileName" class="space-y-3 pointer-events-none">
                                    <div class="w-16 h-16 mx-auto rounded-2xl bg-teal-100 text-teal-600 flex items-center justify-center group-hover:scale-110 transition-transform shadow-xs">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                    </div>
                                    <div>
                                        <span class="text-sm font-black text-teal-600 hover:text-teal-700">Click to choose Master Data file</span>
                                        <span class="text-sm text-gray-600 font-medium"> or drag and drop spreadsheet here</span>
                                    </div>
                                    <p class="text-xs text-gray-400 font-medium">Supports Microsoft Excel (.xlsx, .xls) and CSV</p>
                                </div>

                                <!-- File selected state -->
                                <div x-show="masterFileName" style="display: none;" class="flex items-center gap-4 p-4 sm:p-5 rounded-2xl bg-white border-2 border-teal-300 shadow-sm z-10 w-full max-w-lg mx-auto">
                                    <div class="w-12 h-12 rounded-xl bg-teal-100 text-teal-600 flex items-center justify-center shrink-0">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <div class="text-left flex-1 min-w-0">
                                        <div class="text-sm font-black text-gray-900 truncate" x-text="masterFileName"></div>
                                        <div class="text-xs text-teal-600 font-bold" x-text="'Ready for upload • ' + masterFileSize"></div>
                                    </div>
                                    <button 
                                        type="button" 
                                        @click.stop="$refs.masterFileInput.value = ''; masterFileName = ''; masterFileSize = '';"
                                        class="p-2 rounded-xl text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors shrink-0 cursor-pointer"
                                        title="Remove file">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Master Data Column Format Hint -->
                        <div class="p-5 rounded-2xl bg-teal-50/70 border border-teal-200/80 text-sm">
                            <div class="font-black text-teal-950 mb-2">Master Data File Structure:</div>
                            <div class="text-xs text-teal-900 space-y-1 font-medium">
                                <p><strong>Column A:</strong> Category (<code class="bg-white/80 px-1.5 py-0.5 rounded text-teal-800 font-mono font-bold">platform</code>, <code class="bg-white/80 px-1.5 py-0.5 rounded text-teal-800 font-mono font-bold">format</code>, <code class="bg-white/80 px-1.5 py-0.5 rounded text-teal-800 font-mono font-bold">aipe_pillar</code>, <code class="bg-white/80 px-1.5 py-0.5 rounded text-teal-800 font-mono font-bold">product</code>)</p>
                                <p><strong>Column B:</strong> Value (e.g. <code class="bg-white/80 px-1.5 py-0.5 rounded text-teal-800 font-bold">Facebook</code>, <code class="bg-white/80 px-1.5 py-0.5 rounded text-teal-800 font-bold">FZS V4</code>, <code class="bg-white/80 px-1.5 py-0.5 rounded text-teal-800 font-bold">Awareness</code>)</p>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end pt-4 border-t border-gray-100">
                            <button type="submit" class="inline-flex items-center justify-center w-full sm:w-auto px-8 py-3.5 text-sm font-black text-white rounded-xl bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 transition-all shadow-md shadow-teal-500/25 transform hover:-translate-y-0.5 cursor-pointer">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                Import Master Categories
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
