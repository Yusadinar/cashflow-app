<x-app-layout>
    <x-slot name="header">
        <h1 class="font-bold text-slate-800 text-base truncate">Wishlist</h1>
        <p class="text-[10px] sm:text-xs text-slate-400 truncate">Plan your future purchases</p>
    </x-slot>

    <x-slot name="headerActions">
        <a href="{{ route('wishlists.create') }}"
           class="flex items-center gap-1.5 bg-brand-600 hover:bg-brand-700 text-white text-xs sm:text-sm font-semibold
                  px-3 py-2 sm:px-4 sm:py-2 rounded-lg sm:rounded-xl transition-all shadow-sm hover:shadow-md">
            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            <span class="hidden sm:inline">Add Wishlist</span>
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

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($wishlists as $wishlist)
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden flex flex-col transition-all hover:shadow-md">
                <div class="p-5 flex-1 flex flex-col">
                    <div class="flex justify-between items-start mb-1">
                        <h3 class="font-bold text-slate-800 text-lg">{{ $wishlist->name }}</h3>
                        <!-- Actions -->
                        <div class="flex items-center gap-1 -mr-2">
                            <a href="{{ route('wishlists.edit', $wishlist) }}" class="text-slate-300 hover:text-blue-500 transition-colors p-1.5" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <div x-data="{ showModal: false }" class="inline-block m-0 p-0">
                                <button type="button" @click="showModal = true" class="text-slate-300 hover:text-red-500 transition-colors p-1.5 focus:outline-none" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                                
                                <template x-teleport="body">
                                    <div x-show="showModal" style="display: none;" class="fixed inset-0 z-[9999] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:flex sm:items-center sm:p-0">
                                            
                                            <!-- Backdrop -->
                                            <div x-show="showModal" 
                                                 x-transition:enter="ease-out duration-300"
                                                 x-transition:enter-start="opacity-0"
                                                 x-transition:enter-end="opacity-100"
                                                 x-transition:leave="ease-in duration-200"
                                                 x-transition:leave-start="opacity-100"
                                                 x-transition:leave-end="opacity-0"
                                                 class="fixed inset-0 transition-opacity bg-slate-900/50 backdrop-blur-sm" aria-hidden="true" @click="showModal = false"></div>
                                            
                                            <!-- Modal Panel -->
                                            <div x-show="showModal"
                                                 x-transition:enter="ease-out duration-300"
                                                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                                 x-transition:leave="ease-in duration-200"
                                                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                 class="relative inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl whitespace-normal z-[10000]">
                                                
                                                <div class="flex items-start gap-4 mb-5">
                                                    <div class="flex items-center justify-center w-12 h-12 bg-red-100 rounded-full shrink-0">
                                                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <h3 class="text-lg font-bold text-slate-800" id="modal-title">Delete Wishlist Item</h3>
                                                        <p class="mt-2 text-sm text-slate-500 leading-relaxed">Are you sure you want to delete <span class="font-bold text-slate-700">"{{ $wishlist->name }}"</span>? This action cannot be undone.</p>
                                                    </div>
                                                </div>

                                                <div class="flex justify-end gap-3 mt-6">
                                                    <button type="button" @click="showModal = false" class="cursor-pointer px-4 py-2 text-sm font-semibold text-slate-600 transition-colors bg-white border border-slate-200 rounded-xl hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-200 focus:ring-offset-2">
                                                        Cancel
                                                    </button>
                                                    <form action="{{ route('wishlists.destroy', $wishlist) }}" method="POST" class="m-0 p-0">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="cursor-pointer px-4 py-2 text-sm font-semibold text-white transition-colors bg-red-600 border border-transparent rounded-xl hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 shadow-sm">
                                                            Yes, Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    <p class="text-brand-600 font-semibold text-xl mb-4">Rp {{ number_format($wishlist->amount, 0, ',', '.') }}</p>
                    
                    @if($wishlist->paymentMethod)
                        @php
                            $currentBalance = $balances[$wishlist->payment_method_id] ?? 0;
                            $estimatedRemaining = $currentBalance - $wishlist->amount;
                            $isAffordable = $estimatedRemaining >= 0;
                        @endphp
                        
                        <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                                <span class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Target Account: {{ $wishlist->paymentMethod->name }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center text-sm mb-1">
                                <span class="text-slate-500">Current Balance:</span>
                                <span class="font-medium text-slate-700">Rp {{ number_format($currentBalance, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-slate-500">Est. Remaining:</span>
                                <span class="font-bold {{ $isAffordable ? 'text-emerald-600' : 'text-red-500' }}">
                                    Rp {{ number_format($estimatedRemaining, 0, ',', '.') }}
                                </span>
                            </div>
                            
                            @if(!$isAffordable)
                                <div class="mt-2 text-[10px] text-red-500 bg-red-50/50 p-1.5 rounded-md text-center border border-red-100/50">
                                    You need Rp {{ number_format(abs($estimatedRemaining), 0, ',', '.') }} more!
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="bg-slate-50 rounded-xl p-3 border border-slate-100 text-center text-slate-400 text-xs py-5">
                            No target account specified
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center bg-white rounded-2xl border border-slate-100 shadow-sm">
                <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                <p class="text-slate-500 font-medium">Your wishlist is empty.</p>
                <p class="text-slate-400 text-sm mt-1">Start adding items you want to buy!</p>
            </div>
        @endforelse
    </div>
</x-app-layout>
