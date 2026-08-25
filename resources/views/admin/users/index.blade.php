<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-900 leading-tight">
            {{ __('User Management') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto pb-12 pt-4 px-4 sm:px-6 lg:px-8">
        


        <div class="relative flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4 p-8 rounded-3xl bg-white border border-gray-200/80 shadow-sm overflow-hidden">
            <div class="absolute -top-32 -right-32 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="relative z-10">
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">System Users</h1>
                <p class="text-gray-500 text-sm mt-1 font-medium">Manage access levels and roles across the organization.</p>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-3xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-gray-100">
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach($users as $user)
                        <tr class="hover:bg-slate-50 transition-colors duration-150 group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-indigo-50 flex items-center justify-center border border-indigo-200">
                                            <span class="text-sm font-bold text-indigo-700 uppercase">{{ substr($user->name, 0, 2) }}</span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900">{{ $user->name }}</div>
                                        <div class="text-xs text-gray-500">Joined {{ $user->created_at->format('M d, Y') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-600 font-medium">{{ $user->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap" x-data="{ editing: false }">
                                <div x-show="!editing" class="flex items-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-xl text-xs font-bold uppercase tracking-wider border
                                        {{ $user->role == 'super_admin' ? 'bg-purple-50 text-purple-700 border-purple-200' : '' }}
                                        {{ $user->role == 'digital_team' ? 'bg-teal-50 text-teal-700 border-teal-200' : '' }}
                                        {{ $user->role == 'product_team' ? 'bg-blue-50 text-blue-700 border-blue-200' : '' }}">
                                        {{ str_replace('_', ' ', $user->role) }}
                                    </span>
                                    @if(auth()->id() !== $user->id)
                                    <button @click="editing = true" class="ml-2 text-gray-400 hover:text-indigo-600 opacity-0 group-hover:opacity-100 transition-opacity p-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </button>
                                    @endif
                                </div>
                                <div x-show="editing" style="display: none;" class="flex items-center">
                                    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="flex items-center gap-2">
                                        @csrf
                                        @method('PUT')
                                        <select name="role" class="block w-36 py-1.5 pl-3 pr-8 text-sm border-gray-300 bg-white text-gray-800 focus:outline-none focus:ring-blue-500 focus:border-blue-500 rounded-xl" onchange="this.form.submit()">
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
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline delete-form" data-confirm="Are you sure you want to delete this user?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors" title="Delete User">
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
