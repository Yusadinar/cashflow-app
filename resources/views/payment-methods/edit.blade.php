<x-app-layout>
    <x-slot name="header">
        <h1 class="font-bold text-slate-800 text-xl">Edit Payment Method</h1>
        <p class="text-sm text-slate-500 mt-1">Update payment source or account</p>
    </x-slot>



    <div class="bg-white rounded-2xl border border-slate-100 shadow-xl shadow-slate-200/40 overflow-hidden w-full relative">
        <!-- Decorative header line -->
        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-brand-500 to-emerald-400"></div>

        <div class="p-5 sm:p-10">
            <form method="POST" action="{{ route('payment-methods.update', $paymentMethod) }}" class="space-y-8">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-12 gap-y-8">
                    <div>
                        <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">Payment Method Name</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                            </div>
                            <input type="text" id="name" name="name" value="{{ old('name', $paymentMethod->name) }}" required autofocus
                                   class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 transition-all shadow-sm">
                        </div>
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <label for="balance" class="block text-sm font-semibold text-slate-700 mb-2">Initial Balance</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <input type="number" step="0.01" id="balance" name="balance" value="{{ old('balance', $paymentMethod->balance) }}"
                                   placeholder="0.00"
                                   class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 transition-all shadow-sm">
                        </div>
                        <x-input-error :messages="$errors->get('balance')" class="mt-2" />
                    </div>
                </div>

                <div class="pt-4 flex justify-end gap-3 sm:gap-4">
                    <a href="{{ route('payment-methods.index') }}"
                       class="px-4 py-2 sm:px-6 sm:py-3 border border-slate-200 text-slate-600 font-semibold rounded-lg sm:rounded-xl hover:bg-slate-50 transition-all text-xs sm:text-sm shadow-sm flex items-center justify-center">
                        Cancel
                    </a>
                    <button type="submit"
                            class="bg-brand-600 hover:bg-brand-700 text-white font-semibold py-2 px-4 sm:py-3 sm:px-8 rounded-lg sm:rounded-xl transition-all shadow-md hover:shadow-lg active:scale-[0.98] text-xs sm:text-sm flex items-center gap-1.5 sm:gap-2">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Update Method
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
