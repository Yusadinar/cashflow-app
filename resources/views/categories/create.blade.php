<x-app-layout>
    <x-slot name="header">
        <h1 class="font-bold text-slate-800 text-xl">Add Category</h1>
        <p class="text-sm text-slate-500 mt-1">Create a new transaction category</p>
    </x-slot>



    <div class="bg-white rounded-2xl border border-slate-100 shadow-xl shadow-slate-200/40 overflow-hidden w-full relative">
        <!-- Decorative header line -->
        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-brand-500 to-emerald-400"></div>

        <div class="p-5 sm:p-10">
            <form method="POST" action="{{ route('categories.store') }}" class="space-y-8">
                @csrf
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-12 gap-y-8">
                    <div>
                        <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">Category Name</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                            </div>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                                   placeholder="e.g. Food & Beverages"
                                   class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 transition-all shadow-sm">
                        </div>
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-semibold text-slate-700 mb-2">Transaction Type</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                </svg>
                            </div>
                            <select id="type" name="type" required
                                    class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 transition-all shadow-sm">
                                <option value="" disabled {{ old('type') ? '' : 'selected' }}>Select transaction type...</option>
                                <option value="expense" {{ old('type') == 'expense' ? 'selected' : '' }}>Expense (Outcome)</option>
                                <option value="income" {{ old('type') == 'income' ? 'selected' : '' }}>Income (Income)</option>
                            </select>
                        </div>
                        <x-input-error :messages="$errors->get('type')" class="mt-2" />
                    </div>
                </div>

                <div class="pt-4 flex justify-end gap-3 sm:gap-4">
                    <a href="{{ route('categories.index') }}"
                       class="px-4 py-2 sm:px-6 sm:py-3 border border-slate-200 text-slate-600 font-semibold rounded-lg sm:rounded-xl hover:bg-slate-50 transition-all text-xs sm:text-sm shadow-sm flex items-center justify-center">
                        Cancel
                    </a>
                    <button type="submit"
                            class="bg-brand-600 hover:bg-brand-700 text-white font-semibold py-2 px-4 sm:py-3 sm:px-8 rounded-lg sm:rounded-xl transition-all shadow-md hover:shadow-lg active:scale-[0.98] text-xs sm:text-sm flex items-center gap-1.5 sm:gap-2">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
