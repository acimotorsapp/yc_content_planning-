<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-900 leading-tight">
            {{ __('Manage Categories') }}
        </h2>
    </x-slot>

    <div x-data="{ 
            showModal: {{ $errors->any() ? 'true' : 'false' }},
            activeTab: 'platform'
         }" class="max-w-7xl mx-auto pb-12 pt-4 px-4 sm:px-6 lg:px-8">
        


        <div class="relative flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4 p-8 rounded-3xl bg-white border border-gray-200/80 shadow-sm overflow-hidden">
            <div class="absolute -top-32 -right-32 w-64 h-64 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="relative z-10">
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Manage Categories</h1>
                <p class="text-gray-500 text-sm mt-1 font-medium">Manage dropdown lists for platforms, formats, pillars, and products.</p>
            </div>
            
            <button @click="showModal = true" class="relative z-10 inline-flex items-center justify-center px-6 py-3 text-sm font-bold text-white rounded-xl bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 transition-all shadow-md shadow-teal-500/20 transform hover:-translate-y-0.5">
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
                        :class="activeTab === '{{ $key }}' ? 'bg-teal-50 text-teal-700 border-teal-600 shadow-xs font-bold' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50 font-medium'"
                        class="px-5 py-2.5 rounded-xl border text-sm transition-all">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <div class="bg-white border border-gray-200 rounded-3xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-gray-100">
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Value</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach($categories as $catKey => $catLabel)
                            @if(isset($masterData[$catKey]))
                                @foreach($masterData[$catKey] as $item)
                                    <tr x-show="activeTab === '{{ $catKey }}'" class="hover:bg-slate-50 transition-colors duration-150 group">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-gray-900">{{ $item->value }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <form action="{{ route('admin.master_data.destroy', $item) }}" method="POST" class="inline delete-form" data-confirm="Are you sure you want to delete this entry?">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors" title="Delete">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr x-show="activeTab === '{{ $catKey }}'">
                                    <td colspan="2" class="px-6 py-10 text-center text-gray-500 text-sm">
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
            <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-xs transition-opacity" @click="showModal = false"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-md transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all border border-gray-200 ring-1 ring-black/5 p-6 sm:p-8">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-xl font-bold text-gray-900">Add New Entry</h3>
                        <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <form action="{{ route('admin.master_data.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Category</label>
                                <select name="category" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-3.5 py-2.5 focus:bg-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 font-medium">
                                    <option value="platform">Platform</option>
                                    <option value="format">Format</option>
                                    <option value="aipe_pillar">AIPE Pillar</option>
                                    <option value="product">Product</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Value Name</label>
                                <input type="text" name="value" required placeholder="e.g., Facebook, Reel, Awareness" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-3.5 py-2.5 focus:bg-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 placeholder-gray-400 font-medium">
                            </div>
                        </div>
                        <div class="mt-6 flex items-center justify-end gap-3">
                            <button type="button" @click="showModal = false" class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">
                                Cancel
                            </button>
                            <button type="submit" class="px-6 py-2.5 border border-transparent rounded-xl shadow-md shadow-teal-500/20 text-sm font-bold text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2">
                                Save Entry
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
