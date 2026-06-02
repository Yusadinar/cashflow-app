<x-app-layout>
    <x-slot name="header">
        <h1 class="font-bold text-slate-800 text-base truncate">Transfers</h1>
        <p class="text-[10px] sm:text-xs text-slate-400 truncate">Move money between accounts</p>
    </x-slot>

    <x-slot name="headerActions">
        <a href="{{ route('transfers.create') }}"
           class="flex items-center gap-1.5 bg-brand-600 hover:bg-brand-700 text-white text-xs sm:text-sm font-semibold
                  px-3 py-2 sm:px-4 sm:py-2 rounded-lg sm:rounded-xl transition-all shadow-sm hover:shadow-md">
            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            <span class="hidden sm:inline">Add Transfer</span>
            <span class="sm:hidden">Add</span>
        </a>
    </x-slot>

    @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-50 border border-emerald-100 rounded-xl text-emerald-600 font-medium text-sm flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/50">
                        <th class="py-3.5 px-6 font-semibold text-xs text-slate-500 uppercase tracking-wider">Date</th>
                        <th class="py-3.5 px-6 font-semibold text-xs text-slate-500 uppercase tracking-wider">From</th>
                        <th class="py-3.5 px-6 font-semibold text-xs text-slate-500 uppercase tracking-wider">To</th>
                        <th class="py-3.5 px-6 font-semibold text-xs text-slate-500 uppercase tracking-wider">Amount</th>
                        <th class="py-3.5 px-6 font-semibold text-xs text-slate-500 uppercase tracking-wider">Description</th>
                        <th class="py-3.5 px-6 font-semibold text-xs text-slate-500 uppercase tracking-wider text-right w-24">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($transfers as $transfer)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-3 px-6 text-sm text-slate-600 font-medium">{{ $transfer->transfer_date->format('d M Y') }}</td>
                            <td class="py-3 px-6 text-sm text-slate-600">{{ $transfer->fromPaymentMethod->name }}</td>
                            <td class="py-3 px-6 text-sm text-slate-600">{{ $transfer->toPaymentMethod->name }}</td>
                            <td class="py-3 px-6 text-sm font-semibold text-brand-600">Rp {{ number_format($transfer->amount, 2, ',', '.') }}</td>
                            <td class="py-3 px-6 text-sm text-slate-500 truncate max-w-[200px]">{{ $transfer->description ?? '-' }}</td>
                            <td class="py-3 px-6">
                                <div class="flex justify-end">
                                    <form action="{{ route('transfers.destroy', $transfer) }}" method="POST" class="inline-block"
                                          onsubmit="return confirm('Are you sure you want to delete this transfer?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 text-sm">No transfers found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $transfers->links() }}
        </div>
    </div>
</x-app-layout>
