<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-lg sm:text-2xl text-gray-900 leading-tight">
            {{ __('Notification Logs') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto pb-12 pt-0 sm:pt-4">
        <div class="relative flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4 p-5 sm:p-8 rounded-2xl sm:rounded-3xl bg-white border border-gray-200/80 shadow-sm overflow-hidden">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Email Logs</h1>
                <p class="text-gray-500 text-sm mt-1 font-medium">Monitor automatic scheduled event reminders.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white border border-gray-200 rounded-2xl sm:rounded-3xl shadow-sm overflow-hidden mb-6">
            <form method="GET" action="{{ route('admin.notification-logs.index') }}" class="p-4 sm:p-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="status" class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Status</label>
                    <select id="status" name="status" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 font-medium">
                        <option value="">All statuses</option>
                        @foreach(['pending' => 'Pending', 'processing' => 'Processing', 'sent' => 'Sent', 'failed' => 'Failed'] as $value => $label)
                            <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="target_date" class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Target Date</label>
                    <input id="target_date" type="date" name="target_date" value="{{ request('target_date') }}" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 font-medium">
                </div>
                <div>
                    <label for="search" class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">User or Email</label>
                    <input id="search" type="search" name="search" value="{{ request('search') }}" class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 font-medium" placeholder="Search">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-blue-600 rounded-xl hover:bg-blue-700">Filter</button>
                    <a href="{{ route('admin.notification-logs.index') }}" class="px-5 py-2.5 text-sm font-bold text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200">Clear</a>
                </div>
            </form>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl sm:rounded-3xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto nice-scroll">
                <table class="w-full text-left border-collapse min-w-[1100px]">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-gray-100">
                            <th class="px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Serial</th>
                            <th class="px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">To Email</th>
                            <th class="px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Target Date</th>
                            <th class="px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Attempts</th>
                            <th class="px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Sent At</th>
                            <th class="px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Last Error</th>
                            <th class="px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Created At</th>
                            <th class="px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($deliveries as $delivery)
                            @php
                                $badgeClass = match($delivery->status) {
                                    'sent' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'failed' => 'bg-rose-50 text-rose-700 border-rose-200',
                                    'processing' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    default => 'bg-slate-50 text-slate-700 border-slate-200',
                                };
                                $deliveryUsers = collect($delivery->user_ids ?? [])
                                    ->map(fn ($id) => $usersById->get((int) $id))
                                    ->filter();
                            @endphp
                            <tr class="hover:bg-slate-50 transition-colors duration-150">
                                <td class="px-5 py-4 text-sm font-semibold text-gray-700">{{ $deliveries->firstItem() + $loop->index }}</td>
                                <td class="px-5 py-4 text-sm font-bold text-gray-900">{{ $deliveryUsers->pluck('name')->join(', ') ?: 'Deleted user' }}</td>
                                <td class="px-5 py-4 text-sm text-gray-600 font-medium">{{ $deliveryUsers->pluck('email')->join(', ') ?: 'N/A' }}</td>
                                <td class="px-5 py-4 text-sm text-gray-700">{{ $delivery->target_date->format('Y-m-d') }}</td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-xl text-xs font-bold uppercase tracking-wider border {{ $badgeClass }}">
                                        {{ ucfirst($delivery->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-700">{{ $delivery->attempts }}</td>
                                <td class="px-5 py-4 text-sm text-gray-600">{{ $delivery->sent_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                <td class="px-5 py-4 text-sm text-rose-700 max-w-xs">
                                    <div class="whitespace-normal break-words">{{ $delivery->last_error ?? '-' }}</div>
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-600">{{ $delivery->created_at?->format('Y-m-d H:i') }}</td>
                                <td class="px-5 py-4 text-right">
                                    @if($delivery->status === 'failed')
                                        <form method="POST" action="{{ route('admin.notification-logs.retry', $delivery) }}" class="inline" onsubmit="return confirm('Queue this failed notification for retry?');">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 text-xs font-bold text-blue-700 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100">
                                                Retry
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-5 py-10 text-center text-sm font-semibold text-gray-500">
                                    No notification logs found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($deliveries->hasPages())
                <div class="px-4 sm:px-6 py-4 border-t border-gray-100 bg-slate-50/50">
                    {{ $deliveries->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
