<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-900 leading-tight">
            {{ __('Bulk Data Upload') }}
        </h2>
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
         }" class="max-w-7xl mx-auto pb-16 pt-4 px-4 sm:px-6 lg:px-8 space-y-8">

        <!-- Top Hero Card -->
        <div class="relative flex flex-col md:flex-row justify-between items-start md:items-center gap-6 p-8 rounded-3xl bg-white border border-gray-200/80 shadow-sm overflow-hidden">
            <div class="absolute -top-32 -right-32 w-72 h-72 bg-gradient-to-br from-blue-500/15 via-indigo-500/10 to-teal-500/15 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="relative z-10 space-y-1">
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-200 text-blue-700 text-xs font-bold uppercase tracking-wider">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        Data Management Hub
                    </div>
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold uppercase tracking-wider">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        Duplicate Protection Active
                    </div>
                </div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Bulk Data Upload</h1>
                <p class="text-gray-500 text-sm font-medium max-w-2xl">
                    Import multiple calendar events, content schedules, and master category items simultaneously from Excel (<span class="font-semibold text-gray-700">.xlsx, .xls</span>) or <span class="font-semibold text-gray-700">.csv</span> files. Existing database records are automatically protected from duplication.
                </p>
            </div>

            <!-- Summary Badges -->
            <div class="relative z-10 flex flex-wrap md:flex-col gap-2.5 shrink-0 bg-slate-50 p-4 rounded-2xl border border-gray-200">
                <div class="text-xs font-semibold text-gray-500 flex items-center justify-between gap-4">
                    <span>Total Database Events:</span>
                    <span class="font-extrabold text-blue-600 bg-white px-2.5 py-0.5 rounded-lg border border-gray-200 shadow-2xs">{{ $totalEvents }}</span>
                </div>
                <div class="text-xs font-semibold text-gray-500 flex items-center justify-between gap-4">
                    <span>Product Team Events:</span>
                    <span class="font-extrabold text-indigo-600 bg-white px-2.5 py-0.5 rounded-lg border border-gray-200 shadow-2xs">{{ $productEventsCount }}</span>
                </div>
                <div class="text-xs font-semibold text-gray-500 flex items-center justify-between gap-4">
                    <span>Digital Team Events:</span>
                    <span class="font-extrabold text-teal-600 bg-white px-2.5 py-0.5 rounded-lg border border-gray-200 shadow-2xs">{{ $digitalEventsCount }}</span>
                </div>
            </div>
        </div>

        <!-- Notification Alerts -->
        @if (session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start gap-3 shadow-xs">
                <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center shrink-0 shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <div class="flex-1 text-sm">
                    <div class="font-bold text-emerald-900 text-base">Upload Completed!</div>
                    <div class="mt-0.5">{{ session('success') }}</div>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 flex items-start gap-3 shadow-xs">
                <div class="w-8 h-8 rounded-xl bg-rose-500 text-white flex items-center justify-center shrink-0 shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </div>
                <div class="flex-1 text-sm">
                    <div class="font-bold text-rose-900 text-base">Upload Failed</div>
                    <div class="mt-0.5">{{ session('error') }}</div>
                </div>
            </div>
        @endif

        @if (isset($errors) && $errors->any())
            <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 flex items-start gap-3 shadow-xs">
                <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div class="flex-1 text-sm">
                    <div class="font-bold text-amber-950 text-base">Validation Errors</div>
                    <ul class="list-disc list-inside mt-1 space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @if (session('upload_errors') && count(session('upload_errors')) > 0)
            <div class="p-4 rounded-2xl bg-amber-50/80 border border-amber-200 text-amber-900 text-sm">
                <div class="font-bold text-amber-950 flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Row Warnings during import:
                </div>
                <ul class="list-disc list-inside mt-1.5 space-y-0.5 text-xs text-amber-800 font-medium">
                    @foreach(session('upload_errors') as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Quick Template Download Section -->
        <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-cyan-300 text-xs font-bold uppercase tracking-wider mb-2">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Download Starter Templates
                    </div>
                    <h3 class="text-xl font-bold">Need sample files with formatted headers?</h3>
                    <p class="text-slate-300 text-sm mt-1 max-w-xl">
                        Download pre-formatted Excel or CSV templates matching the database structure. Fill them out and upload directly.
                    </p>
                </div>

                <!-- Template Download Buttons -->
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3 shrink-0">
                    <!-- Full Yamaha Plan -->
                    <a href="{{ route('admin.bulk_upload.sample', ['type' => 'full-content-plan']) }}" class="flex items-center justify-between px-4 py-3 bg-white/10 hover:bg-white/20 border border-white/15 rounded-2xl transition-all duration-200 group">
                        <div class="text-left">
                            <div class="text-xs font-bold text-white group-hover:text-cyan-300 transition-colors">Full Plan Workbook</div>
                            <div class="text-[11px] text-slate-300">Multi-sheet XLSX</div>
                        </div>
                        <svg class="w-5 h-5 text-cyan-400 group-hover:translate-y-0.5 transition-transform ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    </a>

                    <!-- Product Team Template -->
                    <div class="flex items-center bg-white/10 border border-white/15 rounded-2xl overflow-hidden">
                        <div class="px-3.5 py-3 flex-1 text-left">
                            <div class="text-xs font-bold text-white">Product Team</div>
                            <div class="text-[11px] text-slate-300">Content schedule</div>
                        </div>
                        <div class="flex flex-col border-l border-white/10">
                            <a href="{{ route('admin.bulk_upload.sample', ['type' => 'product-events', 'format' => 'xlsx']) }}" class="px-3 py-1.5 hover:bg-white/20 text-[10px] font-bold text-cyan-300 transition-colors" title="Download Excel">.XLSX</a>
                            <a href="{{ route('admin.bulk_upload.sample', ['type' => 'product-events', 'format' => 'csv']) }}" class="px-3 py-1.5 hover:bg-white/20 text-[10px] font-bold text-slate-300 border-t border-white/10 transition-colors" title="Download CSV">.CSV</a>
                        </div>
                    </div>

                    <!-- Digital Team Template -->
                    <div class="flex items-center bg-white/10 border border-white/15 rounded-2xl overflow-hidden">
                        <div class="px-3.5 py-3 flex-1 text-left">
                            <div class="text-xs font-bold text-white">Digital Team</div>
                            <div class="text-[11px] text-slate-300">Posts & Reels</div>
                        </div>
                        <div class="flex flex-col border-l border-white/10">
                            <a href="{{ route('admin.bulk_upload.sample', ['type' => 'digital-events', 'format' => 'xlsx']) }}" class="px-3 py-1.5 hover:bg-white/20 text-[10px] font-bold text-cyan-300 transition-colors" title="Download Excel">.XLSX</a>
                            <a href="{{ route('admin.bulk_upload.sample', ['type' => 'digital-events', 'format' => 'csv']) }}" class="px-3 py-1.5 hover:bg-white/20 text-[10px] font-bold text-slate-300 border-t border-white/10 transition-colors" title="Download CSV">.CSV</a>
                        </div>
                    </div>

                    <!-- Master Data Template -->
                    <div class="flex items-center bg-white/10 border border-white/15 rounded-2xl overflow-hidden">
                        <div class="px-3.5 py-3 flex-1 text-left">
                            <div class="text-xs font-bold text-white">Master Data</div>
                            <div class="text-[11px] text-slate-300">Categories</div>
                        </div>
                        <div class="flex flex-col border-l border-white/10">
                            <a href="{{ route('admin.bulk_upload.sample', ['type' => 'master-data', 'format' => 'xlsx']) }}" class="px-3 py-1.5 hover:bg-white/20 text-[10px] font-bold text-cyan-300 transition-colors" title="Download Excel">.XLSX</a>
                            <a href="{{ route('admin.bulk_upload.sample', ['type' => 'master-data', 'format' => 'csv']) }}" class="px-3 py-1.5 hover:bg-white/20 text-[10px] font-bold text-slate-300 border-t border-white/10 transition-colors" title="Download CSV">.CSV</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Navigation Tabs -->
        <div class="flex space-x-3 border-b border-gray-200 pb-4">
            <button @click="activeTab = 'events'"
                :class="activeTab === 'events' ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20 font-bold' : 'bg-white text-gray-600 hover:bg-gray-100 font-medium border border-gray-200'"
                class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl text-sm transition-all duration-150">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Upload Content Events
            </button>

            <button @click="activeTab = 'master'"
                :class="activeTab === 'master' ? 'bg-teal-600 text-white shadow-md shadow-teal-500/20 font-bold' : 'bg-white text-gray-600 hover:bg-gray-100 font-medium border border-gray-200'"
                class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl text-sm transition-all duration-150">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                Upload Master Categories
            </button>
        </div>

        <!-- TAB 1: Bulk Upload Events Form -->
        <div x-show="activeTab === 'events'" class="space-y-8">
            <div class="bg-white rounded-3xl border border-gray-200 shadow-sm p-6 sm:p-8">
                <form action="{{ route('admin.bulk_upload.events') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- Target Configuration Options -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-5 rounded-2xl bg-slate-50/80 border border-gray-200">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                                Team / Source Type
                            </label>
                            <select name="team_type" x-model="teamType" class="w-full bg-white border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 font-medium text-sm">
                                <option value="auto">Auto-Detect / Multi-Sheet (e.g. Yamaha Full Plan)</option>
                                <option value="product_team">Product Team Events</option>
                                <option value="digital_team">Digital Team Events</option>
                                <option value="global_team">Global Events</option>
                            </select>
                            <p class="text-[11px] text-gray-500 mt-1.5">
                                Select "Auto-Detect" if uploading the entire Excel workbook containing multiple sheets.
                            </p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                                Target Calendar Year
                            </label>
                            <input type="number" name="target_year" value="2026" min="2020" max="2035" class="w-full bg-white border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 font-medium text-sm">
                            <p class="text-[11px] text-gray-500 mt-1.5">
                                Used when rows contain dates without year (e.g., "1 Sep", "15 August").
                            </p>
                        </div>
                    </div>

                    <!-- Drag & Drop File Upload Box -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                            Select or Drop File (.xlsx, .xls, .csv)
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
                            :class="isDragging ? 'border-blue-500 bg-blue-50/50 ring-2 ring-blue-500/20' : 'border-gray-300 bg-slate-50 hover:bg-slate-100/70'"
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
                                    <span class="text-sm font-bold text-blue-600 hover:text-blue-700">Click to choose a file</span>
                                    <span class="text-sm text-gray-500"> or drag and drop here</span>
                                </div>
                                <p class="text-xs text-gray-400 font-medium">Supports Microsoft Excel (.xlsx, .xls) and CSV (up to 20MB)</p>
                            </div>

                            <!-- File selected state -->
                            <div x-show="fileName" style="display: none;" class="flex items-center gap-4 p-4 rounded-2xl bg-white border border-blue-200 shadow-sm z-10">
                                <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <div class="text-left">
                                    <div class="text-sm font-bold text-gray-900 truncate max-w-xs sm:max-w-md" x-text="fileName"></div>
                                    <div class="text-xs text-gray-400 font-medium" x-text="fileSize"></div>
                                </div>
                                <button 
                                    type="button" 
                                    @click.stop="$refs.eventFileInput.value = ''; fileName = ''; fileSize = '';"
                                    class="p-2 rounded-xl text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors ml-2"
                                    title="Remove file">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-end pt-4 border-t border-gray-100">
                        <button type="submit" class="inline-flex items-center justify-center px-8 py-3.5 text-sm font-bold text-white rounded-xl bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all shadow-md shadow-blue-500/25 transform hover:-translate-y-0.5 cursor-pointer">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            Start Bulk Upload
                        </button>
                    </div>
                </form>
            </div>

            <!-- Supported Columns Guide -->
            <div class="bg-white rounded-3xl border border-gray-200 shadow-sm p-6 sm:p-8 space-y-6">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-base font-bold text-gray-900">Supported Spreadsheet Column Formats</h4>
                        <p class="text-xs text-gray-500">Column names are case-insensitive and allow common aliases automatically.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <!-- Product Team Format -->
                    <div class="p-5 rounded-2xl bg-slate-50 border border-gray-200/80 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-indigo-900">Product Team Sheet Headers</span>
                            <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded-md">12 Columns</span>
                        </div>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach(['Date / Publish Date', 'Content', 'A.I.P.E Pillar', 'Content Objective', 'Shoot Date', 'Color Concern', 'Format', 'Budget', 'Platform', 'Product', 'Asset/Drive Link', 'Remarks'] as $col)
                                <span class="px-2.5 py-1 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-700 shadow-2xs">{{ $col }}</span>
                            @endforeach
                        </div>
                    </div>

                    <!-- Digital Team Format -->
                    <div class="p-5 rounded-2xl bg-slate-50 border border-gray-200/80 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-teal-900">Digital Team Sheet Headers</span>
                            <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 bg-teal-100 text-teal-700 rounded-md">10 Columns</span>
                        </div>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach(['Date', 'Day', 'Post No.', 'A.I.P.E Pillar', 'Product Focus', 'Content Objective', 'Format', 'Asset/Drive Link', 'Remarks', 'Boosting budget'] as $col)
                                <span class="px-2.5 py-1 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-700 shadow-2xs">{{ $col }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 2: Bulk Upload Master Data Form -->
        <div x-show="activeTab === 'master'" style="display: none;" class="space-y-8">
            <div class="bg-white rounded-3xl border border-gray-200 shadow-sm p-6 sm:p-8">
                <form action="{{ route('admin.bulk_upload.master_data') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div>
                        <div class="mb-4">
                            <h3 class="text-lg font-bold text-gray-900">Upload Master Data Categories</h3>
                            <p class="text-xs text-gray-500 mt-0.5">
                                Import dropdown values for <span class="font-semibold text-gray-700">Platforms</span>, <span class="font-semibold text-gray-700">Formats</span>, <span class="font-semibold text-gray-700">AIPE Pillars</span>, and <span class="font-semibold text-gray-700">Products</span>. Existing values won't be duplicated.
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
                            :class="isMasterDragging ? 'border-teal-500 bg-teal-50/50 ring-2 ring-teal-500/20' : 'border-gray-300 bg-slate-50 hover:bg-slate-100/70'"
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
                                    <span class="text-sm font-bold text-teal-600 hover:text-teal-700">Click to choose Master Data file</span>
                                    <span class="text-sm text-gray-500"> or drag and drop here</span>
                                </div>
                                <p class="text-xs text-gray-400 font-medium">Supports Microsoft Excel (.xlsx, .xls) and CSV</p>
                            </div>

                            <!-- File selected state -->
                            <div x-show="masterFileName" style="display: none;" class="flex items-center gap-4 p-4 rounded-2xl bg-white border border-teal-200 shadow-sm z-10">
                                <div class="w-12 h-12 rounded-xl bg-teal-100 text-teal-600 flex items-center justify-center shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <div class="text-left">
                                    <div class="text-sm font-bold text-gray-900 truncate max-w-xs sm:max-w-md" x-text="masterFileName"></div>
                                    <div class="text-xs text-gray-400 font-medium" x-text="masterFileSize"></div>
                                </div>
                                <button 
                                    type="button" 
                                    @click.stop="$refs.masterFileInput.value = ''; masterFileName = ''; masterFileSize = '';"
                                    class="p-2 rounded-xl text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors ml-2"
                                    title="Remove file">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Master Data Column Format Hint -->
                    <div class="p-5 rounded-2xl bg-teal-50/60 border border-teal-200/80 text-sm">
                        <div class="font-bold text-teal-950 mb-2">Master Data File Structure:</div>
                        <div class="text-xs text-teal-900 space-y-1">
                            <p><strong>Column A:</strong> Category (<code class="bg-white/80 px-1 py-0.5 rounded text-teal-800 font-mono">platform</code>, <code class="bg-white/80 px-1 py-0.5 rounded text-teal-800 font-mono">format</code>, <code class="bg-white/80 px-1 py-0.5 rounded text-teal-800 font-mono">aipe_pillar</code>, <code class="bg-white/80 px-1 py-0.5 rounded text-teal-800 font-mono">product</code>)</p>
                            <p><strong>Column B:</strong> Value (e.g. <code class="bg-white/80 px-1 py-0.5 rounded text-teal-800">Facebook</code>, <code class="bg-white/80 px-1 py-0.5 rounded text-teal-800">FZS V4</code>, <code class="bg-white/80 px-1 py-0.5 rounded text-teal-800">Awareness</code>)</p>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-end pt-4 border-t border-gray-100">
                        <button type="submit" class="inline-flex items-center justify-center px-8 py-3.5 text-sm font-bold text-white rounded-xl bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 transition-all shadow-md shadow-teal-500/25 transform hover:-translate-y-0.5 cursor-pointer">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            Import Master Categories
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
