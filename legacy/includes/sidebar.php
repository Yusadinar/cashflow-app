<?php
// Determine active page
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
function navItem(string $href, string $icon, string $label, string $current): string {
    $page   = basename($href, '.php');
    $active = ($page === $current || ($current === 'index' && $page === 'dashboard'));
    $base   = 'flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-150';
    $cls    = $active
        ? "$base bg-brand-600 text-white shadow-sm"
        : "$base text-slate-500 hover:bg-slate-100 hover:text-slate-800";
    $fullUrl = base_url($href);
    return "<a href=\"$fullUrl\" class=\"$cls\">$icon <span>$label</span></a>";
}
?>

<!-- Mobile overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black/40 z-20 hidden lg:hidden" onclick="toggleSidebar()"></div>

<!-- Sidebar -->
<aside id="sidebar"
       class="fixed top-0 left-0 h-full w-64 bg-white border-r border-slate-200 z-30
              flex flex-col shadow-sm -translate-x-full lg:translate-x-0 transition-transform duration-300">

    <!-- Logo -->
    <div class="h-16 flex items-center gap-3 px-6 border-b border-slate-100">
        <div class="w-8 h-8 bg-brand-600 rounded-lg flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                      d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11
                         0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11
                         0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <span class="font-bold text-slate-800 text-lg tracking-tight">CashFlow</span>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
        <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest px-4 mb-2">Main</p>

        <?= navItem('views/dashboard.php',
            '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>',
            'Dashboard', $currentPage) ?>

        <?= navItem('views/transactions.php',
            '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>',
            'Transactions', $currentPage) ?>

        <?= navItem('views/categories.php',
            '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>',
            'Categories', $currentPage) ?>

        <?= navItem('views/report.php',
            '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>',
            'Reports', $currentPage) ?>
    </nav>

    <!-- User -->
    <div class="p-4 border-t border-slate-100">
        <div class="flex items-center gap-3 px-2 mb-3">
            <div class="w-8 h-8 rounded-full bg-brand-100 flex items-center justify-center text-brand-700 font-bold text-sm flex-shrink-0">
                <?= strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)) ?>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-slate-700 truncate"><?= htmlspecialchars($_SESSION['user_name'] ?? '') ?></p>
                <p class="text-xs text-slate-400">Small Business</p>
            </div>
        </div>
        <a href="<?= url('auth/logout.php') ?>"
           class="flex items-center gap-2 px-4 py-2 text-sm text-red-500 hover:bg-red-50 rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            Logout
        </a>
    </div>
</aside>
