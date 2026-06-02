<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../controllers/authController.php';
require_once __DIR__ . '/../controllers/transactionController.php';

$auth   = new AuthController();
$auth->requireAuth();

$txCtrl  = new TransactionController();
$userId  = $auth->currentUserId();
$summary = $txCtrl->summary($userId);
$recent  = $txCtrl->recent($userId, 8);
$chart   = $txCtrl->monthlyChart($userId, (int)date('Y'));
$pmBalances = $txCtrl->balancesByPaymentMethod($userId);

$pageTitle = 'Dashboard';
include __DIR__ . '/../includes/header.php';
?>

<div class="flex h-screen overflow-hidden">
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>

    <!-- Main content -->
    <main class="flex-1 lg:ml-64 overflow-y-auto">

        <!-- Top bar -->
        <div class="sticky top-0 z-10 bg-white/80 backdrop-blur border-b border-slate-100 px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="lg:hidden text-slate-500 hover:text-slate-700 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <div>
                    <h1 class="font-bold text-slate-800 text-base">Dashboard</h1>
                    <p class="text-xs text-slate-400"><?= date('l, d F Y') ?></p>
                </div>
            </div>
            <a href="<?= url('views/add_transaction.php') ?>"
               class="flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold
                      px-4 py-2 rounded-xl transition-all shadow-sm hover:shadow-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Add Transaction
            </a>
        </div>

        <div class="p-6 space-y-6">

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
                    <div class="text-2xl font-bold font-mono"><?= formatRupiah($summary['balance']) ?></div>
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
                    <div class="text-2xl font-bold font-mono text-slate-800"><?= formatRupiah($summary['total_income']) ?></div>
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
                    <div class="text-2xl font-bold font-mono text-slate-800"><?= formatRupiah($summary['total_expense']) ?></div>
                    <div class="text-xs text-slate-400 mt-1">All-time expenses</div>
                </div>
            </div>

            <!-- Chart -->
            <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h2 class="font-bold text-slate-800">Income vs Expense</h2>
                        <p class="text-xs text-slate-400 mt-0.5"><?= date('Y') ?> Overview</p>
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
                <div class="h-64">
                    <canvas id="cashflowChart"></canvas>
                </div>
            </div>

            <!-- Recent transactions -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-50">
                    <h2 class="font-bold text-slate-800">Recent Transactions</h2>
                    <a href="<?= url('views/transactions.php') ?>" class="text-xs text-brand-600 hover:text-brand-700 font-medium">View all →</a>
                </div>

                <?php if (empty($recent)): ?>
                <div class="py-12 text-center text-slate-400 text-sm">No transactions yet.</div>
                <?php else: ?>
                <div class="divide-y divide-slate-50">
                    <?php foreach ($recent as $tx): ?>
                    <div class="flex items-center px-6 py-3.5 hover:bg-slate-50/50 transition-colors">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 mr-4
                                    <?= $tx['type'] === 'income' ? 'bg-emerald-50' : 'bg-red-50' ?>">
                            <?php if ($tx['type'] === 'income'): ?>
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                            </svg>
                            <?php else: ?>
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                            </svg>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-700 truncate"><?= htmlspecialchars($tx['description'] ?: $tx['category_name']) ?></p>
                            <p class="text-xs text-slate-400"><?= htmlspecialchars($tx['category_name']) ?> · <?= date('d M Y', strtotime($tx['transaction_date'])) ?></p>
                        </div>
                        <div class="text-right ml-4">
                            <span class="text-sm font-semibold font-mono <?= $tx['type'] === 'income' ? 'text-emerald-600' : 'text-red-500' ?>">
                                <?= $tx['type'] === 'income' ? '+' : '-' ?><?= formatRupiah($tx['amount']) ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Balances by Payment Method -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden mt-6">
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-50">
                    <h2 class="font-bold text-slate-800">Income & Balances by Payment Method</h2>
                </div>
                
                <?php if (empty($pmBalances)): ?>
                <div class="py-12 text-center text-slate-400 text-sm">No payment methods found.</div>
                <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 p-6 gap-4">
                    <?php foreach ($pmBalances as $pm): 
                        $pmNet = $pm['total_income'] - $pm['total_expense'];
                    ?>
                    <div class="border border-slate-100 rounded-xl p-4 hover:shadow-md transition-shadow">
                        <div class="text-sm font-bold text-slate-700 mb-2"><?= htmlspecialchars($pm['method_name']) ?></div>
                        <div class="flex justify-between items-center text-xs mb-1">
                            <span class="text-slate-500">Income:</span>
                            <span class="font-medium text-emerald-600"><?= formatRupiah($pm['total_income']) ?></span>
                        </div>
                        <div class="flex justify-between items-center text-xs mb-3">
                            <span class="text-slate-500">Expense:</span>
                            <span class="font-medium text-red-500"><?= formatRupiah($pm['total_expense']) ?></span>
                        </div>
                        <div class="flex justify-between items-center text-sm pt-2 border-t border-slate-50">
                            <span class="text-slate-600 font-medium">Net Balance:</span>
                            <span class="font-bold <?= $pmNet >= 0 ? 'text-brand-600' : 'text-red-600' ?>">
                                <?= formatRupiah($pmNet) ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

        </div>
    </main>
</div>

<script>
const ctx = document.getElementById('cashflowChart').getContext('2d');
const chartData = <?= json_encode($chart) ?>;

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: chartData.labels,
        datasets: [
            {
                label: 'Income',
                data: chartData.income,
                backgroundColor: 'rgba(34,197,94,0.85)',
                borderRadius: 6,
                borderSkipped: false,
            },
            {
                label: 'Expense',
                data: chartData.expense,
                backgroundColor: 'rgba(248,113,113,0.85)',
                borderRadius: 6,
                borderSkipped: false,
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => 'Rp ' + ctx.raw.toLocaleString('id-ID')
                }
            }
        },
        scales: {
            x: { grid: { display: false }, border: { display: false } },
            y: {
                grid: { color: 'rgba(0,0,0,0.05)' },
                border: { display: false },
                ticks: {
                    callback: v => 'Rp ' + (v / 1000000).toFixed(1) + 'M'
                }
            }
        }
    }
});
</script>

<?php
function formatRupiah(float $n): string {
    return 'Rp ' . number_format($n, 0, ',', '.');
}
include __DIR__ . '/../includes/footer.php';
?>
