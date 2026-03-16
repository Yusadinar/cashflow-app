<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../controllers/authController.php';
require_once __DIR__ . '/../controllers/transactionController.php';
require_once __DIR__ . '/../controllers/categoryController.php';

$auth    = new AuthController();
$auth->requireAuth();

$txCtrl  = new TransactionController();
$catCtrl = new CategoryController();
$userId  = $auth->currentUserId();

$errors  = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $txCtrl->store($userId, $_POST);
    if ($result['success']) {
        redirect('views/transactions.php?added=1');
    }
    $errors = $result['errors'] ?? [$result['message']];
}

$categories  = $catCtrl->index();
$pageTitle   = 'Add Transaction';
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
            <h1 class="font-bold text-slate-800">Add Transaction</h1>
        </div>

        <div class="p-6 max-w-xl">
            <?php if ($errors): ?>
            <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-xl">
                <p class="text-sm font-medium text-red-700 mb-1">Please fix the following:</p>
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
                                       <?= ($_POST['type'] ?? 'income') === 'income' ? 'checked' : '' ?>>
                                <div class="flex items-center justify-center gap-2 px-4 py-3 rounded-xl border-2 border-slate-200
                                            peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-700
                                            hover:border-slate-300 transition-all text-sm font-medium text-slate-500" id="incomeLabel">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                                    </svg>
                                    Income
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="type" value="expense" class="sr-only peer"
                                       <?= ($_POST['type'] ?? '') === 'expense' ? 'checked' : '' ?>>
                                <div class="flex items-center justify-center gap-2 px-4 py-3 rounded-xl border-2 border-slate-200
                                            peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:text-red-600
                                            hover:border-slate-300 transition-all text-sm font-medium text-slate-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                                    </svg>
                                    Expense
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Amount -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Amount (Rp)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm font-medium">Rp</span>
                            <input type="number" name="amount" min="1" step="any" required
                                   value="<?= htmlspecialchars($_POST['amount'] ?? '') ?>"
                                   placeholder="0"
                                   class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl text-sm
                                          focus:ring-2 focus:ring-brand-500 focus:outline-none focus:border-transparent font-mono">
                        </div>
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Category</label>
                        <select name="category_id" required
                                class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm
                                       focus:ring-2 focus:ring-brand-500 focus:outline-none focus:border-transparent bg-white">
                            <option value="">Select category</option>
                            <optgroup label="Income">
                                <?php foreach ($categories as $cat): ?>
                                <?php if ($cat['type'] === 'income'): ?>
                                <option value="<?= $cat['id'] ?>" <?= ($_POST['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                                <?php endif; ?>
                                <?php endforeach; ?>
                            </optgroup>
                            <optgroup label="Expense">
                                <?php foreach ($categories as $cat): ?>
                                <?php if ($cat['type'] === 'expense'): ?>
                                <option value="<?= $cat['id'] ?>" <?= ($_POST['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                                <?php endif; ?>
                                <?php endforeach; ?>
                            </optgroup>
                        </select>
                    </div>

                    <!-- Date -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Date</label>
                        <input type="date" name="transaction_date" required
                               value="<?= htmlspecialchars($_POST['transaction_date'] ?? date('Y-m-d')) ?>"
                               max="<?= date('Y-m-d') ?>"
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm
                                      focus:ring-2 focus:ring-brand-500 focus:outline-none focus:border-transparent">
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Description <span class="text-slate-400 font-normal">(optional)</span></label>
                        <textarea name="description" rows="3" placeholder="Add a note..."
                                  class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm resize-none
                                         focus:ring-2 focus:ring-brand-500 focus:outline-none focus:border-transparent"
                        ><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <a href="<?= url('views/transactions.php') ?>"
                           class="flex-1 text-center px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                            Cancel
                        </a>
                        <button type="submit"
                                class="flex-1 bg-brand-600 hover:bg-brand-700 text-white font-semibold py-2.5 px-4 rounded-xl transition-all shadow-sm text-sm">
                            Save Transaction
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
