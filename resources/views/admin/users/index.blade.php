<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-white leading-tight">
            {{ __('User Management') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto pb-12 pt-8 px-4 sm:px-6 lg:px-8">
        
        <!-- Alerts -->
        @if(session('success'))
            <div class="bg-white dark:bg-[#111] border border-green-500/30 p-4 mb-6 rounded-xl shadow-sm flex items-center" role="alert">
                <svg class="w-5 h-5 text-green-400 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <p class="text-green-400 text-sm font-medium">{{ session('success') }}</p>
            </div>
        @endif
        @if($errors->any())
            <div class="bg-red-500/10 border border-red-500/20 p-4 mb-6 rounded-xl flex items-center" role="alert">
                <svg class="w-5 h-5 text-red-400 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <p class="text-red-400 text-sm font-medium">{{ $errors->first() }}</p>
            </div>
        @endif

        <div class="relative flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-4 p-8 rounded-3xl bg-white dark:bg-[#111] bg-gradient-to-br from-gray-50 to-white dark:from-white/5 dark:to-transparent border border-gray-200 dark:border-white/10 overflow-hidden shadow-xl dark:shadow-2xl">
            <div class="absolute -top-32 -right-32 w-64 h-64 bg-indigo-500/10 dark:bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="relative z-10">
                <h1 class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-gray-900 via-gray-700 to-gray-500 dark:from-white dark:via-gray-200 dark:to-gray-400 tracking-tight">System Users</h1>
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-2 font-medium">Manage access levels and roles across the organization.</p>
            </div>
        </div>

        <div class="bg-white/80 dark:bg-[#111]/80 backdrop-blur-xl border border-gray-200 dark:border-white/10 rounded-2xl shadow-lg dark:shadow-2xl overflow-hidden ring-1 ring-gray-900/5 dark:ring-white/5">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-[#161616] border-b border-gray-200 dark:border-white/5">
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-white/5 bg-transparent">
                        @foreach($users as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors duration-200 group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 dark:from-indigo-900/50 dark:to-purple-900/50 flex items-center justify-center border border-indigo-200 dark:border-indigo-500/30">
                                            <span class="text-sm font-bold text-indigo-700 dark:text-indigo-300 uppercase">{{ substr($user->name, 0, 2) }}</span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $user->name }}</div>
                                        <div class="text-xs text-gray-500">Joined {{ $user->created_at->format('M d, Y') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap" x-data="{ editing: false }">
                                <div x-show="!editing" class="flex items-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold uppercase tracking-wider border
                                        {{ $user->role == 'super_admin' ? 'bg-purple-100 text-purple-700 border-purple-200 dark:bg-purple-500/10 dark:text-purple-400 dark:border-purple-500/20' : '' }}
                                        {{ $user->role == 'digital_team' ? 'bg-teal-100 text-teal-700 border-teal-200 dark:bg-teal-500/10 dark:text-teal-400 dark:border-teal-500/20' : '' }}
                                        {{ $user->role == 'product_team' ? 'bg-blue-100 text-blue-700 border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20' : '' }}">
                                        {{ str_replace('_', ' ', $user->role) }}
                                    </span>
                                    @if(auth()->id() !== $user->id)
                                    <button @click="editing = true" class="ml-2 text-gray-400 hover:text-indigo-500 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </button>
                                    @endif
                                </div>
                                <div x-show="editing" style="display: none;" class="flex items-center">
                                    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="flex items-center gap-2">
                                        @csrf
                                        @method('PUT')
                                        <select name="role" class="block w-36 py-1.5 pl-3 pr-8 text-sm border-gray-300 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-lg" onchange="this.form.submit()">
                                            <option value="digital_team" {{ $user->role == 'digital_team' ? 'selected' : '' }}>Digital Team</option>
                                            <option value="product_team" {{ $user->role == 'product_team' ? 'selected' : '' }}>Product Team</option>
                                            <option value="super_admin" {{ $user->role == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                                        </select>
                                        <button type="button" @click="editing = false" class="text-gray-400 hover:text-red-500">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                @if(auth()->id() !== $user->id)
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition-colors" title="Delete User">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
