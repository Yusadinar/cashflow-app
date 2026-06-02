<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../controllers/authController.php';
require_once __DIR__ . '/../controllers/transactionController.php';
require_once __DIR__ . '/../controllers/categoryController.php';
require_once __DIR__ . '/../controllers/PaymentMethodController.php';

$auth    = new AuthController();
$auth->requireAuth();

$txCtrl  = new TransactionController();
$catCtrl = new CategoryController();
$pmCtrl  = new PaymentMethodController();
$userId  = $auth->currentUserId();
$id      = (int)($_GET['id'] ?? 0);

$tx = $txCtrl->show($id, $userId);
if (!$tx) {
    redirect('views/transactions.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $txCtrl->update($id, $userId, $_POST);
    if ($result['success']) {
        redirect('views/transactions.php?updated=1');
    }
    $errors = $result['errors'] ?? [$result['message']];
    // Merge POST over the original tx for display
    $tx = array_merge($tx, $_POST);
}

$categories = $catCtrl->index($userId);
$paymentMethods = $pmCtrl->index($userId);
$pageTitle  = 'Edit Transaction';
include __DIR__ . '/../includes/header.php';
?>

<div class="flex h-screen overflow-hidden">
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>

    <main class="flex-1 lg:ml-64 overflow-y-auto">
        <!-- Top bar -->
        <div class="sticky top-0 z-10 bg-white/80 backdrop-blur border-b border-slate-100 px-6 h-16 flex items-center gap-3">
            <button onclick="toggleSidebar()" class="lg:hidden text-slate-500 hover:text-slate-700 p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <a href="<?= url('views/transactions.php') ?>" class="text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h1 class="font-bold text-slate-800">Edit Transaction</h1>
        </div>

        <div class="p-6 max-w-xl">
            <?php if ($errors): ?>
            <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-xl">
                <ul class="text-sm text-red-600 space-y-0.5 list-disc list-inside">
                    <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                <form method="POST" class="space-y-5">

                    <!-- Type toggle -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Transaction Type</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="cursor-pointer">
                                <input type="radio" name="type" value="income" class="sr-only peer"
                                       <?= $tx['type'] === 'income' ? 'checked' : '' ?>>
                                <div class="flex items-center justify-center gap-2 px-4 py-3 rounded-xl border-2 border-slate-200
                                            peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-700
                                            transition-all text-sm font-medium text-slate-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                                    </svg>
                                    Income
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="type" value="expense" class="sr-only peer"
                                       <?= $tx['type'] === 'expense' ? 'checked' : '' ?>>
                                <div class="flex items-center justify-center gap-2 px-4 py-3 rounded-xl border-2 border-slate-200
                                            peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:text-red-600
                                            transition-all text-sm font-medium text-slate-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                                    </svg>
                                    Expense
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Payment Method</label>
                        <select name="payment_method_id" required
                                class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm
                                       focus:ring-2 focus:ring-brand-500 focus:outline-none focus:border-transparent bg-white">
                            <option value="">Select payment method</option>
                            <?php foreach ($paymentMethods as $pm): ?>
                            <option value="<?= $pm['id'] ?>" <?= ($tx['payment_method_id'] ?? '') == $pm['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($pm['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Amount -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Amount (Rp)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm font-medium">Rp</span>
                            <input type="number" name="amount" min="1" step="any" required
                                   value="<?= htmlspecialchars($tx['amount']) ?>"
                                   class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl text-sm
                                          focus:ring-2 focus:ring-brand-500 focus:outline-none font-mono">
                        </div>
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Category</label>
                        <select name="category_id" required
                                class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm
                                       focus:ring-2 focus:ring-brand-500 focus:outline-none bg-white">
                            <optgroup label="Income">
                                <?php foreach ($categories as $cat): if ($cat['type'] !== 'income') continue; ?>
                                <option value="<?= $cat['id'] ?>" <?= $tx['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </optgroup>
                            <optgroup label="Expense">
                                <?php foreach ($categories as $cat): if ($cat['type'] !== 'expense') continue; ?>
                                <option value="<?= $cat['id'] ?>" <?= $tx['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </optgroup>
                        </select>
                    </div>

                    <!-- Date -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Date</label>
                        <input type="date" name="transaction_date" required
                               value="<?= htmlspecialchars($tx['transaction_date']) ?>"
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm
                                      focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Description</label>
                        <textarea name="description" rows="3"
                                  class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm resize-none
                                         focus:ring-2 focus:ring-brand-500 focus:outline-none"
                        ><?= htmlspecialchars($tx['description'] ?? '') ?></textarea>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <a href="<?= url('views/transactions.php') ?>"
                           class="flex-1 text-center px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                            Cancel
                        </a>
                        <button type="submit"
                                class="flex-1 bg-brand-600 hover:bg-brand-700 text-white font-semibold py-2.5 px-4 rounded-xl transition-all shadow-sm text-sm">
                            Update Transaction
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
