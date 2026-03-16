<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../controllers/authController.php';
require_once __DIR__ . '/../controllers/transactionController.php';

$auth   = new AuthController();
$auth->requireAuth();

$txCtrl  = new TransactionController();
$userId  = $auth->currentUserId();

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_method'] ?? '') === 'DELETE') {
    $id = (int)($_POST['id'] ?? 0);
    $txCtrl->destroy($id, $userId);
    redirect('views/transactions.php?deleted=1');
}

// Filters
$filterStart = $_GET['start'] ?? '';
$filterEnd   = $_GET['end']   ?? '';

if ($filterStart && $filterEnd) {
    $transactions = $txCtrl->filterByDate($userId, $filterStart, $filterEnd);
} else {
    $transactions = $txCtrl->index($userId);
}

$pageTitle = 'Transactions';
include __DIR__ . '/../includes/header.php';
?>

<div class="flex h-screen overflow-hidden">
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>

    <main class="flex-1 lg:ml-64 overflow-y-auto">

        <!-- Top bar -->
        <div class="sticky top-0 z-10 bg-white/80 backdrop-blur border-b border-slate-100 px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="lg:hidden text-slate-500 hover:text-slate-700 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <h1 class="font-bold text-slate-800">Transactions</h1>
            </div>
            <a href="<?= url('views/add_transaction.php') ?>"
               class="flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-all shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Add Transaction
            </a>
        </div>

        <div class="p-6 space-y-5">

            <?php if (isset($_GET['deleted'])): ?>
            <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-sm text-emerald-700 flex items-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                Transaction deleted successfully.
            </div>
            <?php endif; ?>

            <!-- Filter bar -->
            <form method="GET" action="<?= url('views/transactions.php') ?>" class="bg-white rounded-xl border border-slate-100 p-4 flex flex-wrap gap-3 items-end shadow-sm">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">From</label>
                    <input type="date" name="start" value="<?= htmlspecialchars($filterStart) ?>"
                           class="px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">To</label>
                    <input type="date" name="end" value="<?= htmlspecialchars($filterEnd) ?>"
                           class="px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                </div>
                <button type="submit" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-lg transition-colors">
                    Filter
                </button>
                <?php if ($filterStart || $filterEnd): ?>
                <a href="<?= url('views/transactions.php') ?>" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-medium rounded-lg transition-colors">
                    Clear
                </a>
                <?php endif; ?>
            </form>

            <!-- Table -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-50 flex items-center justify-between">
                    <p class="text-sm text-slate-500"><?= count($transactions) ?> transaction(s)</p>
                </div>

                <?php if (empty($transactions)): ?>
                <div class="py-16 text-center text-slate-400 text-sm">
                    <svg class="w-10 h-10 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    No transactions found.
                </div>
                <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 text-xs text-slate-500 uppercase tracking-wide">
                                <th class="text-left px-6 py-3 font-semibold">Date</th>
                                <th class="text-left px-6 py-3 font-semibold">Description</th>
                                <th class="text-left px-6 py-3 font-semibold">Category</th>
                                <th class="text-left px-6 py-3 font-semibold">Type</th>
                                <th class="text-right px-6 py-3 font-semibold">Amount</th>
                                <th class="text-center px-6 py-3 font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php foreach ($transactions as $tx): ?>
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-3.5 text-slate-500 whitespace-nowrap">
                                    <?= date('d M Y', strtotime($tx['transaction_date'])) ?>
                                </td>
                                <td class="px-6 py-3.5">
                                    <span class="font-medium text-slate-700"><?= htmlspecialchars($tx['description'] ?: '—') ?></span>
                                </td>
                                <td class="px-6 py-3.5 text-slate-500"><?= htmlspecialchars($tx['category_name']) ?></td>
                                <td class="px-6 py-3.5">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        <?= $tx['type'] === 'income'
                                            ? 'bg-emerald-50 text-emerald-700'
                                            : 'bg-red-50 text-red-600' ?>">
                                        <?= ucfirst($tx['type']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 text-right font-mono font-semibold
                                    <?= $tx['type'] === 'income' ? 'text-emerald-600' : 'text-red-500' ?>">
                                    <?= $tx['type'] === 'income' ? '+' : '-' ?>Rp <?= number_format($tx['amount'], 0, ',', '.') ?>
                                </td>
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="<?= url('views/edit_transaction.php?id=' . $tx['id']) ?>"
                                           class="text-slate-400 hover:text-brand-600 transition-colors p-1 rounded-lg hover:bg-brand-50">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <form method="POST" onsubmit="return confirm('Delete this transaction?')">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="hidden" name="id" value="<?= $tx['id'] ?>">
                                            <button type="submit" class="text-slate-400 hover:text-red-500 transition-colors p-1 rounded-lg hover:bg-red-50">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
