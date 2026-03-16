<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../controllers/authController.php';
require_once __DIR__ . '/../controllers/categoryController.php';

$auth    = new AuthController();
$auth->requireAuth();

$catCtrl = new CategoryController();
$msg     = '';
$error   = '';

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $result = $catCtrl->store($_POST['name'] ?? '', $_POST['type'] ?? '');
        $msg    = $result['success'] ? $result['message'] : '';
        $error  = $result['success'] ? '' : implode(', ', $result['errors'] ?? [$result['message']]);

    } elseif ($action === 'update') {
        $result = $catCtrl->update((int)$_POST['id'], $_POST['name'] ?? '', $_POST['type'] ?? '');
        $msg    = $result['success'] ? $result['message'] : '';
        $error  = $result['success'] ? '' : implode(', ', $result['errors'] ?? [$result['message']]);

    } elseif ($action === 'delete') {
        $result = $catCtrl->destroy((int)$_POST['id']);
        $msg    = $result['success'] ? $result['message'] : '';
        $error  = $result['success'] ? '' : $result['message'];
    }
}

$categories = $catCtrl->index();
$pageTitle  = 'Categories';
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
                <h1 class="font-bold text-slate-800">Categories</h1>
            </div>
            <button onclick="openModal('addModal')"
                    class="flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-all shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Add Category
            </button>
        </div>

        <div class="p-6 space-y-5">

            <?php if ($msg): ?>
            <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-sm text-emerald-700"><?= htmlspecialchars($msg) ?></div>
            <?php elseif ($error): ?>
            <div class="p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="grid md:grid-cols-2 gap-5">

                <!-- Income categories -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-50 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <h2 class="font-semibold text-slate-700 text-sm">Income Categories</h2>
                    </div>
                    <div class="divide-y divide-slate-50">
                        <?php $income = array_filter($categories, fn($c) => $c['type'] === 'income'); ?>
                        <?php foreach ($income as $cat): ?>
                        <div class="flex items-center justify-between px-5 py-3 hover:bg-slate-50/50 transition-colors">
                            <span class="text-sm text-slate-700"><?= htmlspecialchars($cat['name']) ?></span>
                            <div class="flex gap-1.5">
                                <button onclick='openEdit(<?= json_encode($cat) ?>)'
                                        class="p-1.5 text-slate-400 hover:text-brand-600 hover:bg-brand-50 rounded-lg transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <form method="POST" onsubmit="return confirm('Delete this category?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php if (!$income): ?>
                        <div class="py-8 text-center text-slate-400 text-sm">No income categories yet.</div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Expense categories -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-50 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-red-400"></span>
                        <h2 class="font-semibold text-slate-700 text-sm">Expense Categories</h2>
                    </div>
                    <div class="divide-y divide-slate-50">
                        <?php $expense = array_filter($categories, fn($c) => $c['type'] === 'expense'); ?>
                        <?php foreach ($expense as $cat): ?>
                        <div class="flex items-center justify-between px-5 py-3 hover:bg-slate-50/50 transition-colors">
                            <span class="text-sm text-slate-700"><?= htmlspecialchars($cat['name']) ?></span>
                            <div class="flex gap-1.5">
                                <button onclick='openEdit(<?= json_encode($cat) ?>)'
                                        class="p-1.5 text-slate-400 hover:text-brand-600 hover:bg-brand-50 rounded-lg transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <form method="POST" onsubmit="return confirm('Delete this category?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php if (!$expense): ?>
                        <div class="py-8 text-center text-slate-400 text-sm">No expense categories yet.</div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </main>
</div>

<!-- Add Modal -->
<div id="addModal" class="modal-overlay hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl p-6 w-full max-w-sm shadow-2xl">
        <h3 class="font-bold text-slate-800 mb-5">Add Category</h3>
        <form method="POST" class="space-y-4">
            <input type="hidden" name="action" value="create">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Name</label>
                <input type="text" name="name" required placeholder="Category name"
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Type</label>
                <select name="type" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none bg-white">
                    <option value="income">Income</option>
                    <option value="expense">Expense</option>
                </select>
            </div>
            <div class="flex gap-3 pt-1">
                <button type="button" onclick="closeModal('addModal')"
                        class="flex-1 px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                    Cancel
                </button>
                <button type="submit"
                        class="flex-1 bg-brand-600 hover:bg-brand-700 text-white font-semibold py-2.5 px-4 rounded-xl transition-all text-sm">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal-overlay hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl p-6 w-full max-w-sm shadow-2xl">
        <h3 class="font-bold text-slate-800 mb-5">Edit Category</h3>
        <form method="POST" class="space-y-4">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" id="editId">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Name</label>
                <input type="text" name="name" id="editName" required
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Type</label>
                <select name="type" id="editType" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none bg-white">
                    <option value="income">Income</option>
                    <option value="expense">Expense</option>
                </select>
            </div>
            <div class="flex gap-3 pt-1">
                <button type="button" onclick="closeModal('editModal')"
                        class="flex-1 px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                    Cancel
                </button>
                <button type="submit"
                        class="flex-1 bg-brand-600 hover:bg-brand-700 text-white font-semibold py-2.5 px-4 rounded-xl transition-all text-sm">
                    Update
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openEdit(cat) {
    document.getElementById('editId').value   = cat.id;
    document.getElementById('editName').value = cat.name;
    document.getElementById('editType').value = cat.type;
    openModal('editModal');
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
