<x-app-layout>
    <x-slot name="header">
        <h1 class="font-bold text-slate-800 text-base truncate">Dashboard</h1>
        <p class="text-[10px] sm:text-xs text-slate-400 truncate">{{ date('l, d F Y') }}</p>
    </x-slot>

    <x-slot name="headerActions">
        <a href="{{ route('transactions.create') ?? '#' }}"
           class="flex items-center gap-1.5 bg-brand-600 hover:bg-brand-700 text-white text-xs sm:text-sm font-semibold
                  px-3 py-2 sm:px-4 sm:py-2 rounded-lg sm:rounded-xl transition-all shadow-sm hover:shadow-md">
            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            <span class="hidden sm:inline">Add Transaction</span>
            <span class="sm:hidden">Add</span>
        </a>
    </x-slot>

    <!-- Summary cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Balance -->
        <div class="bg-gradient-to-br from-brand-600 to-brand-700 rounded-2xl p-5 text-white shadow-lg">
            <div class="flex items-start justify-between mb-3">
                <div class="text-sm font-medium text-brand-100">Total Balance</div>
                <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-bold font-mono">Rp {{ number_format($income - $expense, 0, ',', '.') }}</div>
            <div class="text-xs text-brand-200 mt-1">Current net balance</div>
        </div>

        <!-- Income -->
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm">
            <div class="flex items-start justify-between mb-3">
                <div class="text-sm font-medium text-slate-500">Total Income</div>
                <div class="w-8 h-8 bg-emerald-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-bold font-mono text-slate-800">Rp {{ number_format($income, 0, ',', '.') }}</div>
            <div class="text-xs text-slate-400 mt-1">All-time income</div>
        </div>

        <!-- Expense -->
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm">
            <div class="flex items-start justify-between mb-3">
                <div class="text-sm font-medium text-slate-500">Total Expenses</div>
                <div class="w-8 h-8 bg-red-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-bold font-mono text-slate-800">Rp {{ number_format($expense, 0, ',', '.') }}</div>
            <div class="text-xs text-slate-400 mt-1">All-time expenses</div>
        </div>
    </div>

    <!-- Chart (If chart data is available) -->
    <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="font-bold text-slate-800">Income vs Expense</h2>
                <p class="text-xs text-slate-400 mt-0.5">{{ date('Y') }} Overview</p>
            </div>
            <div class="flex items-center gap-4 text-xs text-slate-500">
                <span class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-brand-500 inline-block"></span> Income
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-red-400 inline-block"></span> Expense
                </span>
            </div>
        </div>
        <div class="relative h-64 w-full">
            <canvas id="cashflowChart"></canvas>
        </div>
    </div>

    <!-- Recent transactions -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-50">
            <h2 class="font-bold text-slate-800">Recent Transactions</h2>
            <a href="{{ route('transactions.index') }}" class="text-xs text-brand-600 hover:text-brand-700 font-medium">View all &rarr;</a>
        </div>

        @if($transactions->isEmpty())
        <div class="py-12 text-center text-slate-400 text-sm">No transactions yet.</div>
        @else
        <div class="divide-y divide-slate-50">
            @foreach($transactions->take(8) as $tx)
            <div class="flex items-center px-6 py-3.5 hover:bg-slate-50/50 transition-colors">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 mr-4
                            {{ $tx->type === 'income' ? 'bg-emerald-50' : 'bg-red-50' }}">
                    @if($tx->type === 'income')
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                    </svg>
                    @else
                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                    </svg>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-700 truncate">{{ $tx->description ?: $tx->category->name }}</p>
                    <p class="text-xs text-slate-400">{{ $tx->category->name }} &middot; {{ $tx->transaction_date->format('d M Y') }}</p>
                </div>
                <div class="text-right ml-4">
                    <span class="text-sm font-semibold font-mono {{ $tx->type === 'income' ? 'text-emerald-600' : 'text-red-500' }}">
                        {{ $tx->type === 'income' ? '+' : '-' }}Rp {{ number_format($tx->amount, 0, ',', '.') }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <!-- Balances by Payment Method -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden mt-6">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-50">
            <h2 class="font-bold text-slate-800">Balances by Payment Method</h2>
        </div>
        
        @if(count($balances) === 0)
        <div class="py-12 text-center text-slate-400 text-sm">No payment methods found.</div>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 p-6 gap-4">
            @foreach($balances as $b)
            <div class="border border-slate-100 rounded-xl p-4 hover:shadow-md transition-shadow">
                <div class="text-sm font-bold text-slate-700 mb-2">{{ $b['name'] }}</div>
                <div class="flex justify-between items-center text-sm pt-2">
                    <span class="text-slate-600 font-medium">Net Balance:</span>
                    <span class="font-bold font-mono {{ $b['balance'] >= 0 ? 'text-brand-600' : 'text-red-600' }}">
                        Rp {{ number_format($b['balance'], 0, ',', '.') }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('cashflowChart');
            if (ctx) {
                const chartData = @json($chartData);
                
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: chartData.labels,
                        datasets: [
                            {
                                label: 'Income',
                                data: chartData.income,
                                backgroundColor: '#10b981', // emerald-500
                                borderRadius: 4,
                                barPercentage: 0.6,
                                categoryPercentage: 0.8
                            },
                            {
                                label: 'Expense',
                                data: chartData.expense,
                                backgroundColor: '#f87171', // red-400
                                borderRadius: 4,
                                barPercentage: 0.6,
                                categoryPercentage: 0.8
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(context.parsed.y);
                                        }
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        family: "'Plus Jakarta Sans', sans-serif",
                                        size: 11
                                    },
                                    color: '#94a3b8'
                                }
                            },
                            y: {
                                border: {
                                    display: false
                                },
                                grid: {
                                    color: '#f1f5f9',
                                    drawTicks: false,
                                },
                                ticks: {
                                    font: {
                                        family: "'Plus Jakarta Sans', sans-serif",
                                        size: 11
                                    },
                                    color: '#94a3b8',
                                    callback: function(value, index, values) {
                                        if (value >= 1000000) {
                                            return 'Rp ' + (value / 1000000) + 'M';
                                        } else if (value >= 1000) {
                                            return 'Rp ' + (value / 1000) + 'K';
                                        }
                                        return 'Rp ' + value;
                                    }
                                }
                            }
                        },
                        interaction: {
                            mode: 'nearest',
                            axis: 'x',
                            intersect: false
                        }
                    }
                });
            }
        });
    </script>
</x-app-layout>
