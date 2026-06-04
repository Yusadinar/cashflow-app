@php
    $isPaid = $item->status === 'paid';
    $isOverdue = !$isPaid && $item->due_date && \Carbon\Carbon::parse($item->due_date)->isPast();
@endphp

<div x-data="{ showPayModal: false, showEditModal: false, showDeleteModal: false }" class="bg-white rounded-2xl border {{ $isPaid ? 'border-emerald-100 opacity-75' : ($isOverdue ? 'border-red-200 shadow-red-50' : 'border-slate-100') }} shadow-sm overflow-hidden transition-all hover:shadow-md p-4 sm:p-5 relative">
    
    @if($isPaid)
        <div class="absolute top-0 right-0 bg-emerald-100 text-emerald-700 text-[10px] font-bold px-3 py-1 rounded-bl-xl uppercase tracking-wider">Paid</div>
    @elseif($isOverdue)
        <div class="absolute top-0 right-0 bg-red-100 text-red-700 text-[10px] font-bold px-3 py-1 rounded-bl-xl uppercase tracking-wider">Overdue</div>
    @endif

    <div class="flex justify-between items-start mb-2">
        <h3 class="font-bold text-slate-800 text-lg {{ $isPaid ? 'line-through text-slate-500' : '' }}">{{ $item->name }}</h3>
        
        <!-- Actions -->
        <div class="flex items-center gap-1 mt-1">
            @if(!$isPaid)
                <button type="button" @click="showPayModal = true" class="text-slate-400 hover:text-emerald-500 transition-colors p-1" title="Mark as Paid">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </button>
            @endif

            <button type="button" @click="showEditModal = true" class="text-slate-400 hover:text-blue-500 transition-colors p-1" title="Edit">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
            </button>
            <button type="button" @click="showDeleteModal = true" class="text-slate-400 hover:text-red-500 transition-colors p-1" title="Delete">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
        </div>
    </div>
    
    <p class="{{ $item->type == 'debt' ? 'text-red-600' : 'text-emerald-600' }} font-semibold text-xl mb-3 {{ $isPaid ? 'opacity-60' : '' }}">
        Rp {{ number_format($item->amount, 0, ',', '.') }}
    </p>

    <div class="text-xs text-slate-500 space-y-1">
        @if($item->due_date)
            <div class="flex items-center gap-1.5 {{ $isOverdue ? 'text-red-500 font-medium' : '' }}">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Due Date: {{ \Carbon\Carbon::parse($item->due_date)->format('d M Y') }}
            </div>
        @endif
        @if($item->description)
            <div class="flex items-start gap-1.5">
                <svg class="w-3.5 h-3.5 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                <span class="truncate">{{ $item->description }}</span>
            </div>
        @endif
    </div>

    <!-- Pay Modal -->
    <template x-teleport="body">
        <div x-show="showPayModal" style="display: none;" class="fixed inset-0 z-[9999] overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:flex sm:items-center sm:p-0">
                <div x-show="showPayModal" @click="showPayModal = false" class="fixed inset-0 transition-opacity bg-slate-900/50 backdrop-blur-sm"></div>
                <div x-show="showPayModal" class="relative inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle bg-white shadow-xl rounded-2xl z-[10000]">
                    <div class="flex items-start gap-4 mb-5">
                        <div class="flex items-center justify-center w-12 h-12 bg-emerald-100 rounded-full shrink-0">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-800">Mark as Paid</h3>
                            <p class="mt-2 text-sm text-slate-500">Record repayment for <span class="font-bold">"{{ $item->name }}"</span> of Rp {{ number_format($item->amount, 0, ',', '.') }}.</p>
                        </div>
                    </div>
                    <form action="{{ route('debts.update', $item) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="name" value="{{ $item->name }}">
                        <input type="hidden" name="type" value="{{ $item->type }}">
                        <input type="hidden" name="amount" value="{{ $item->amount }}">
                        <input type="hidden" name="due_date" value="{{ $item->due_date ? \Carbon\Carbon::parse($item->due_date)->format('Y-m-d') : '' }}">
                        <input type="hidden" name="description" value="{{ $item->description }}">
                        <input type="hidden" name="status" value="paid">
                        
                        <div class="mt-4 mb-6">
                            <label for="payment_method_id" class="block text-sm font-medium text-slate-700 mb-1">Target Wallet (Optional)</label>
                            <select id="payment_method_id" name="payment_method_id" class="w-full border-slate-200 rounded-xl focus:ring-brand-500 focus:border-brand-500 text-sm">
                                <option value="">-- No Wallet Selected --</option>
                                @foreach($paymentMethods as $pm)
                                    <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                                @endforeach
                            </select>
                            <p class="text-[10px] text-slate-500 mt-1">
                                {{ $item->type == 'debt' ? 'Money will be deducted from this wallet.' : 'Money will be added to this wallet.' }}
                            </p>
                        </div>

                        <div class="flex justify-end gap-3">
                            <button type="button" @click="showPayModal = false" class="px-4 py-2 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 cursor-pointer">Cancel</button>
                            <button type="submit" class="flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white bg-emerald-600 rounded-xl hover:bg-emerald-700 cursor-pointer shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                Confirm Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>

    <!-- Edit Modal -->
    <template x-teleport="body">
        <div x-show="showEditModal" style="display: none;" class="fixed inset-0 z-[9999] overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:flex sm:items-center sm:p-0">
                <div x-show="showEditModal" @click="showEditModal = false" class="fixed inset-0 transition-opacity bg-slate-900/50 backdrop-blur-sm"></div>
                <div x-show="showEditModal" class="relative inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle bg-white shadow-xl rounded-2xl z-[10000]">
                    <h3 class="text-lg font-bold text-slate-800 mb-4">Edit Record</h3>
                    <form action="{{ route('debts.update', $item) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                                <select name="status" required class="w-full border-slate-200 rounded-xl focus:ring-brand-500 focus:border-brand-500">
                                    <option value="unpaid" {{ $item->status == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                    <option value="paid" {{ $item->status == 'paid' ? 'selected' : '' }}>Paid</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Record Type</label>
                                <select name="type" required class="w-full border-slate-200 rounded-xl focus:ring-brand-500 focus:border-brand-500">
                                    <option value="debt" {{ $item->type == 'debt' ? 'selected' : '' }}>I Owe (Debt)</option>
                                    <option value="receivable" {{ $item->type == 'receivable' ? 'selected' : '' }}>I am Owed (Receivable)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Person Name</label>
                                <input type="text" name="name" value="{{ $item->name }}" required class="w-full border-slate-200 rounded-xl focus:ring-brand-500 focus:border-brand-500">
                            </div>
                            <div x-data="{ 
                                    amountText: '{{ (int)$item->amount }}',
                                    format(val) {
                                        return val.toString().replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                                    }
                                }"
                                x-init="amountText = format(amountText)">
                                <label class="block text-sm font-medium text-slate-700 mb-1">Amount (Rp)</label>
                                <input type="text" x-model="amountText" @input="amountText = format($event.target.value)" required class="w-full border-slate-200 rounded-xl focus:ring-brand-500 focus:border-brand-500">
                                <input type="hidden" name="amount" :value="amountText.replace(/\./g, '')">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Due Date</label>
                                <input type="date" name="due_date" value="{{ $item->due_date ? \Carbon\Carbon::parse($item->due_date)->format('Y-m-d') : '' }}" class="w-full border-slate-200 rounded-xl focus:ring-brand-500 focus:border-brand-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                                <textarea name="description" rows="2" class="w-full border-slate-200 rounded-xl focus:ring-brand-500 focus:border-brand-500">{{ $item->description }}</textarea>
                            </div>
                        </div>
                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" @click="showEditModal = false" class="px-4 py-2 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50">Cancel</button>
                            <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-brand-600 rounded-xl hover:bg-brand-700">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>

    <!-- Delete Modal -->
    <template x-teleport="body">
        <div x-show="showDeleteModal" style="display: none;" class="fixed inset-0 z-[9999] overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:flex sm:items-center sm:p-0">
                <div x-show="showDeleteModal" @click="showDeleteModal = false" class="fixed inset-0 transition-opacity bg-slate-900/50 backdrop-blur-sm"></div>
                <div x-show="showDeleteModal" class="relative inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle bg-white shadow-xl rounded-2xl z-[10000]">
                    <div class="flex items-start gap-4 mb-5">
                        <div class="flex items-center justify-center w-12 h-12 bg-red-100 rounded-full shrink-0">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-800">Delete Record</h3>
                            <p class="mt-2 text-sm text-slate-500">Are you sure you want to delete the record for <span class="font-bold">"{{ $item->name }}"</span>? This action cannot be undone.</p>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" @click="showDeleteModal = false" class="px-4 py-2 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50">Cancel</button>
                        <form action="{{ route('debts.destroy', $item) }}" method="POST" class="m-0 p-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-xl hover:bg-red-700">Yes, Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
