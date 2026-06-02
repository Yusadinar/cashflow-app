<x-app-layout>
    <x-slot name="header">
        <h1 class="font-bold text-slate-800 text-xl">Edit Wishlist Item</h1>
        <p class="text-sm text-slate-500 mt-1">Update your future purchase plans</p>
    </x-slot>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-xl shadow-slate-200/40 overflow-hidden w-full relative">
        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-brand-500 to-emerald-400"></div>

        <div class="p-5 sm:p-10">
            <form method="POST" action="{{ route('wishlists.update', $wishlist) }}" class="space-y-8">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-12 gap-y-8">
                    <!-- Left Column -->
                    <div class="space-y-8">
                        <div>
                            <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">Item Name</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $wishlist->name) }}" required placeholder="e.g. New Laptop, Vacation..."
                                   class="w-full px-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 transition-all shadow-sm">
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <label for="payment_method_id" class="block text-sm font-semibold text-slate-700 mb-2">Target Account (Optional)</label>
                            <select id="payment_method_id" name="payment_method_id"
                                    class="w-full px-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 transition-all shadow-sm">
                                <option value="" {{ old('payment_method_id', $wishlist->payment_method_id) ? '' : 'selected' }}>Not decided yet...</option>
                                @foreach($paymentMethods as $pm)
                                    <option value="{{ $pm->id }}" {{ old('payment_method_id', $wishlist->payment_method_id) == $pm->id ? 'selected' : '' }}>
                                        {{ $pm->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('payment_method_id')" class="mt-2" />
                            <p class="text-xs text-slate-400 mt-2">Selecting an account lets you preview your estimated balance.</p>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-8">
                        @php
                            $oldAmount = old('amount', $wishlist->amount);
                            $oldAmountFormatted = $oldAmount ? number_format(floor($oldAmount), 0, ',', '.') : '';
                            $oldAmountNumeric = $oldAmount ? floor($oldAmount) : '';
                        @endphp
                        <div x-data="{
                                numericValue: '{{ $oldAmountNumeric }}',
                                displayValue: '{{ $oldAmountFormatted }}',
                                formatInput(e) {
                                    let val = e.target.value.replace(/\D/g, '');
                                    this.numericValue = val;
                                    this.displayValue = val ? new Intl.NumberFormat('id-ID').format(val) : '';
                                }
                            }">
                            <label for="amount_display" class="block text-sm font-semibold text-slate-700 mb-2">Estimated Amount (IDR)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="text-slate-400 font-medium">Rp</span>
                                </div>
                                <input type="hidden" name="amount" x-model="numericValue">
                                <input type="text" id="amount_display" x-model="displayValue" @input="formatInput" required placeholder="0"
                                       class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 transition-all shadow-sm">
                            </div>
                            <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="pt-4 flex justify-end gap-3 sm:gap-4">
                    <a href="{{ route('wishlists.index') }}"
                       class="px-4 py-2 sm:px-6 sm:py-3 border border-slate-200 text-slate-600 font-semibold rounded-lg sm:rounded-xl hover:bg-slate-50 transition-all text-xs sm:text-sm shadow-sm flex items-center justify-center">
                        Cancel
                    </a>
                    <button type="submit"
                            class="bg-brand-600 hover:bg-brand-700 text-white font-semibold py-2 px-4 sm:py-3 sm:px-8 rounded-lg sm:rounded-xl transition-all shadow-md hover:shadow-lg active:scale-[0.98] text-xs sm:text-sm flex items-center gap-1.5 sm:gap-2">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Update Wishlist
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
