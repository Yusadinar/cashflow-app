<x-app-layout>
    <x-slot name="header">
        <h1 class="font-bold text-slate-800 text-base truncate">Debts & Receivables</h1>
        <p class="text-[10px] sm:text-xs text-slate-400 truncate">Manage your loans and owed money</p>
    </x-slot>

    <x-slot name="headerActions">
        <div x-data="{ showAddModal: false }">
            <button @click="showAddModal = true"
               class="flex items-center gap-1.5 bg-brand-600 hover:bg-brand-700 text-white text-xs sm:text-sm font-semibold
                      px-3 py-2 sm:px-4 sm:py-2 rounded-lg sm:rounded-xl transition-all shadow-sm hover:shadow-md cursor-pointer">
                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="hidden sm:inline">Add Record</span>
                <span class="sm:hidden">Add</span>
            </button>

            <!-- Add Modal -->
            <template x-teleport="body">
                <div x-show="showAddModal" style="display: none;" class="fixed inset-0 z-[9999] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:flex sm:items-center sm:p-0">
                        <div x-show="showAddModal" 
                             x-transition:enter="ease-out duration-300"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="ease-in duration-200"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="fixed inset-0 transition-opacity bg-slate-900/50 backdrop-blur-sm" aria-hidden="true" @click="showAddModal = false"></div>
                        
                        <div x-show="showAddModal"
                             x-transition:enter="ease-out duration-300"
                             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                             x-transition:leave="ease-in duration-200"
                             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                             class="relative inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl whitespace-normal z-[10000]">
                            
                            <h3 class="text-lg font-bold text-slate-800 mb-4" id="modal-title">Add New Record</h3>
                            <form action="{{ route('debts.store') }}" method="POST">
                                @csrf
                                <div class="space-y-4">
                                    <div>
                                        <label for="type" class="block text-sm font-medium text-slate-700 mb-1">Record Type</label>
                                        <select id="type" name="type" required class="w-full border-slate-200 rounded-xl focus:ring-brand-500 focus:border-brand-500">
                                            <option value="debt">I Owe (Debt)</option>
                                            <option value="receivable">I am Owed (Receivable)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Person Name</label>
                                        <input type="text" id="name" name="name" required placeholder="e.g. John Doe" class="w-full border-slate-200 rounded-xl focus:ring-brand-500 focus:border-brand-500">
                                    </div>
                                    <div x-data="{ 
                                            amountText: '',
                                            format(val) {
                                                return val.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                                            }
                                        }">
                                        <label for="amountText" class="block text-sm font-medium text-slate-700 mb-1">Amount (Rp)</label>
                                        <input type="text" id="amountText" x-model="amountText" @input="amountText = format($event.target.value)" required placeholder="0" class="w-full border-slate-200 rounded-xl focus:ring-brand-500 focus:border-brand-500">
                                        <input type="hidden" name="amount" :value="amountText.replace(/\./g, '')">
                                    </div>
                                    <div>
                                        <label for="payment_method_id" class="block text-sm font-medium text-slate-700 mb-1">Payment Method (Optional)</label>
                                        <select id="payment_method_id" name="payment_method_id" class="w-full border-slate-200 rounded-xl focus:ring-brand-500 focus:border-brand-500 text-sm">
                                            <option value="">-- No Wallet Selected (Only track record) --</option>
                                            @foreach($paymentMethods as $pm)
                                                <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                                            @endforeach
                                        </select>
                                        <p class="text-[10px] text-slate-500 mt-1">If selected, this will automatically adjust your wallet balance right now.</p>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label for="due_date" class="block text-sm font-medium text-slate-700 mb-1">Due Date</label>
                                            <input type="date" id="due_date" name="due_date" class="w-full border-slate-200 rounded-xl focus:ring-brand-500 focus:border-brand-500 text-sm">
                                        </div>
                                        <div>
                                            <label for="description" class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                                            <input type="text" id="description" name="description" class="w-full border-slate-200 rounded-xl focus:ring-brand-500 focus:border-brand-500 text-sm" placeholder="Optional">
                                        </div>
                                    </div>
                                </div>
                                <div class="flex justify-end gap-3 mt-6">
                                    <button type="button" @click="showAddModal = false" class="px-4 py-2 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 cursor-pointer">Cancel</button>
                                    <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-brand-600 rounded-xl hover:bg-brand-700 cursor-pointer">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-50 border border-emerald-100 rounded-xl text-emerald-600 font-medium text-sm flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-4 p-4 bg-red-50 border border-red-100 rounded-xl text-red-600 font-medium text-sm">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Saya Berhutang (Debts) -->
        <div>
            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                </div>
                <h2 class="text-xl font-bold text-slate-800">I Owe (Debts)</h2>
            </div>
            
            <div class="space-y-4">
                @forelse($debts as $debt)
                    @include('debts.partials.card', ['item' => $debt])
                @empty
                    <div class="py-8 text-center bg-white rounded-2xl border border-slate-100 shadow-sm">
                        <p class="text-slate-500 font-medium text-sm">No debt records found.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Saya Menghutangi (Receivables) -->
        <div>
            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                </div>
                <h2 class="text-xl font-bold text-slate-800">I am Owed (Receivables)</h2>
            </div>

            <div class="space-y-4">
                @forelse($receivables as $item)
                    @include('debts.partials.card', ['item' => $item])
                @empty
                    <div class="py-8 text-center bg-white rounded-2xl border border-slate-100 shadow-sm">
                        <p class="text-slate-500 font-medium text-sm">No receivable records found.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
