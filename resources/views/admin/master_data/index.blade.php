<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-white leading-tight">
            {{ __('Manage Categories') }}
        </h2>
    </x-slot>

    <div x-data="{ 
            showModal: {{ $errors->any() ? 'true' : 'false' }},
            activeTab: 'platform'
         }" class="max-w-7xl mx-auto pb-12 pt-8 px-4 sm:px-6 lg:px-8">
        
        <!-- Alerts -->
        @if(session('success'))
            <div class="bg-white dark:bg-[#111] border border-green-500/30 p-4 mb-6 rounded-xl shadow-sm flex items-center" role="alert">
                <svg class="w-5 h-5 text-green-400 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <p class="text-green-400 text-sm font-medium">{{ session('success') }}</p>
            </div>
        @endif

        <div class="relative flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-4 p-8 rounded-3xl bg-white dark:bg-[#111] bg-gradient-to-br from-gray-50 to-white dark:from-white/5 dark:to-transparent border border-gray-200 dark:border-white/10 overflow-hidden shadow-xl dark:shadow-2xl">
            <div class="absolute -top-32 -right-32 w-64 h-64 bg-teal-500/10 dark:bg-teal-500/20 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="relative z-10">
                <h1 class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-gray-900 via-gray-700 to-gray-500 dark:from-white dark:via-gray-200 dark:to-gray-400 tracking-tight">Manage Categories</h1>
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-2 font-medium">Manage dropdown lists for platforms, formats, pillars, and products.</p>
            </div>
            
            <button @click="showModal = true" class="relative z-10 inline-flex items-center justify-center px-6 py-3 text-sm font-bold text-white rounded-xl bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-500 hover:to-emerald-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-[#0a0a0a] focus:ring-teal-500 transition-all duration-300 shadow-[0_0_15px_rgba(20,184,166,0.3)] dark:shadow-[0_0_20px_rgba(20,184,166,0.4)] transform hover:-translate-y-1">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add New Entry
            </button>
        </div>

        <!-- Tabs -->
        <div class="flex space-x-2 mb-6 overflow-x-auto pb-2">
            @php
                $categories = ['platform' => 'Platforms', 'format' => 'Formats', 'aipe_pillar' => 'AIPE Pillars', 'product' => 'Products'];
            @endphp
            @foreach($categories as $key => $label)
                <button @click="activeTab = '{{ $key }}'" 
                        :class="activeTab === '{{ $key }}' ? 'bg-white dark:bg-[#111] border-teal-500 text-teal-600 dark:text-teal-400 shadow-sm' : 'bg-transparent border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'"
                        class="px-5 py-2.5 rounded-xl border-b-2 font-bold text-sm transition-all">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <div class="bg-white/80 dark:bg-[#111]/80 backdrop-blur-xl border border-gray-200 dark:border-white/10 rounded-2xl shadow-lg dark:shadow-2xl overflow-hidden ring-1 ring-gray-900/5 dark:ring-white/5">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-[#161616] border-b border-gray-200 dark:border-white/5">
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Value</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-white/5 bg-transparent">
                        @foreach($categories as $catKey => $catLabel)
                            @if(isset($masterData[$catKey]))
                                @foreach($masterData[$catKey] as $item)
                                    <tr x-show="activeTab === '{{ $catKey }}'" class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors duration-200 group">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $item->value }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <form action="{{ route('admin.master_data.destroy', $item) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this entry?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition-colors" title="Delete">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr x-show="activeTab === '{{ $catKey }}'">
                                    <td colspan="2" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400 text-sm">
                                        No {{ strtolower($catLabel) }} found. Click 'Add New Entry' to create one.
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Modal -->
        <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="fixed inset-0 bg-gray-50 dark:bg-[#0a0a0a]/80 backdrop-blur-sm transition-opacity" @click="showModal = false"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-white dark:bg-[#111] text-left shadow-2xl transition-all border border-gray-300 dark:border-white/10 ring-1 ring-white/5 p-6">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Add New Entry</h3>
                        <button @click="showModal = false" class="text-gray-400 hover:text-gray-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <form action="{{ route('admin.master_data.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Category</label>
                                <select name="category" class="w-full bg-gray-50 dark:bg-[#1a1a1a] border border-gray-200 dark:border-white/10 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-teal-500">
                                    <option value="platform">Platform</option>
                                    <option value="format">Format</option>
                                    <option value="aipe_pillar">AIPE Pillar</option>
                                    <option value="product">Product</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Value Name</label>
                                <input type="text" name="value" required placeholder="e.g., Facebook, Reel, Awareness" class="w-full bg-gray-50 dark:bg-[#1a1a1a] border border-gray-200 dark:border-white/10 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-teal-500">
                            </div>
                        </div>
                        <div class="mt-6">
                            <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                                Save Entry
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
