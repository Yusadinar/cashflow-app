<x-app-layout>
    <x-slot name="header">
        <h1 class="font-bold text-slate-800 text-xl">Edit Transaction</h1>
        <p class="text-sm text-slate-500 mt-1">Update income or expense details</p>
    </x-slot>



    <div class="bg-white rounded-2xl border border-slate-100 shadow-xl shadow-slate-200/40 overflow-hidden w-full relative">
        <!-- Decorative header line -->
        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-brand-500 to-emerald-400"></div>

        <div class="p-5 sm:p-10">
            <form method="POST" action="{{ route('transactions.update', $transaction) }}" class="space-y-8"
                  x-data="{ 
                      selectedType: '{{ old('type', $transaction->type) }}', 
                      allCategories: [
                          @foreach($categories as $category)
                              { id: {{ $category->id }}, name: '{{ addslashes($category->name) }}', type: '{{ $category->type }}' },
                          @endforeach
                      ],
                      get filteredCategories() {
                          if (!this.selectedType) return this.allCategories;
                          return this.allCategories.filter(c => c.type === this.selectedType);
                      }
                  }">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-12 gap-y-8">
                    <!-- Left Column -->
                    <div class="space-y-8">
                        <div>
                            <label for="transaction_date" class="block text-sm font-semibold text-slate-700 mb-2">Date</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <input type="date" id="transaction_date" name="transaction_date" value="{{ old('transaction_date', $transaction->transaction_date->format('Y-m-d')) }}" required
                                       class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 transition-all shadow-sm">
                            </div>
                            <x-input-error :messages="$errors->get('transaction_date')" class="mt-2" />
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-semibold text-slate-700 mb-2">Transaction Type</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                    </svg>
                                </div>
                                <select id="type" name="type" required x-model="selectedType" @change="document.getElementById('category_id').value = ''"
                                        class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 transition-all shadow-sm">
                                    <option value="expense" {{ old('type', $transaction->type) == 'expense' ? 'selected' : '' }}>Expense (Outcome)</option>
                                    <option value="income" {{ old('type', $transaction->type) == 'income' ? 'selected' : '' }}>Income (Income)</option>
                                </select>
                            </div>
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        <div>
                            <label for="category_id" class="block text-sm font-semibold text-slate-700 mb-2">Category</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                </div>
                                <select id="category_id" name="category_id" required
                                        class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 transition-all shadow-sm">
                                    <option value="" disabled {{ old('category_id', $transaction->category_id) ? '' : 'selected' }}>Select category...</option>
                                    <template x-for="category in filteredCategories" :key="category.id">
                                        <option :value="category.id" 
                                                x-text="category.name + ' (' + category.type.charAt(0).toUpperCase() + category.type.slice(1) + ')'" 
                                                :selected="category.id == '{{ old('category_id', $transaction->category_id) }}'">
                                        </option>
                                    </template>
                                </select>
                            </div>
                            <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-8">
                        <div>
                            <label for="payment_method_id" class="block text-sm font-semibold text-slate-700 mb-2">Payment Method</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                    </svg>
                                </div>
                                <select id="payment_method_id" name="payment_method_id" required
                                        class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 transition-all shadow-sm">
                                    @foreach($paymentMethods as $pm)
                                        <option value="{{ $pm->id }}" {{ old('payment_method_id', $transaction->payment_method_id) == $pm->id ? 'selected' : '' }}>
                                            {{ $pm->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <x-input-error :messages="$errors->get('payment_method_id')" class="mt-2" />
                        </div>

                        @php
                            $oldAmount = old('amount', $transaction->amount);
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
                            <label for="amount_display" class="block text-sm font-semibold text-slate-700 mb-2">Amount (IDR)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="text-slate-400 font-medium">Rp</span>
                                </div>
                                <input type="hidden" name="amount" x-model="numericValue">
                                <input type="text" id="amount_display" x-model="displayValue" @input="formatInput" required
                                       placeholder="0"
                                       class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 transition-all shadow-sm">
                            </div>
                            <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-semibold text-slate-700 mb-2">Description</label>
                            <div class="relative">
                                <div class="absolute top-3.5 left-4 pointer-events-none">
                                    <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </div>
                                <textarea id="description" name="description" rows="3"
                                          placeholder="Enter transaction details..."
                                          class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 transition-all shadow-sm">{{ old('description', $transaction->description) }}</textarea>
                            </div>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="pt-4 flex justify-end gap-3 sm:gap-4">
                    <a href="{{ route('transactions.index') }}"
                       class="px-4 py-2 sm:px-6 sm:py-3 border border-slate-200 text-slate-600 font-semibold rounded-lg sm:rounded-xl hover:bg-slate-50 transition-all text-xs sm:text-sm shadow-sm flex items-center justify-center">
                        Cancel
                    </a>
                    <button type="submit"
                            class="bg-brand-600 hover:bg-brand-700 text-white font-semibold py-2 px-4 sm:py-3 sm:px-8 rounded-lg sm:rounded-xl transition-all shadow-md hover:shadow-lg active:scale-[0.98] text-xs sm:text-sm flex items-center gap-1.5 sm:gap-2">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Update Transaction
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
