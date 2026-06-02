<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../controllers/authController.php';
require_once __DIR__ . '/../controllers/transactionController.php';

$auth   = new AuthController();
$auth->requireAuth();

$txCtrl = new TransactionController();
$userId = $auth->currentUserId();

$year  = (int)($_GET['year']  ?? date('Y'));
$month = (int)($_GET['month'] ?? date('n'));

$report = $txCtrl->report($userId, $year, $month);

$monthNames = [
    1=>'January',2=>'February',3=>'March',4=>'April',
    5=>'May',6=>'June',7=>'July',8=>'August',
    9=>'September',10=>'October',11=>'November',12=>'December'
];

$pageTitle = 'Reports';
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
                <div>
                    <h1 class="font-bold text-slate-800">Monthly Report</h1>
                    <p class="text-xs text-slate-400"><?= $monthNames[$month] ?> <?= $year ?></p>
                </div>
            </div>
            <button onclick="exportPDF()"
                    class="flex items-center gap-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-all shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1
                             1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export PDF
            </button>
        </div>

        <div class="p-6 space-y-6" id="reportContent">

            <!-- Period selector -->
            <form method="GET" action="<?= url('views/report.php') ?>" class="bg-white rounded-xl border border-slate-100 p-4 flex flex-wrap gap-3 items-end shadow-sm">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Year</label>
                    <select name="year" class="px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none bg-white">
                        <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                        <option value="<?= $y ?>" <?= $y === $year ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Month</label>
                    <select name="month" class="px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none bg-white">
                        <?php foreach ($monthNames as $n => $name): ?>
                        <option value="<?= $n ?>" <?= $n === $month ? 'selected' : '' ?>><?= $name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit"
                        class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-lg transition-colors">
                    View Report
                </button>
            </form>

            <!-- Summary cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm">
                    <p class="text-xs font-medium text-slate-500 mb-1">Total Income</p>
                    <p class="text-2xl font-bold font-mono text-emerald-600">
                        Rp <?= number_format($report['total_income'], 0, ',', '.') ?>
                    </p>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm">
                    <p class="text-xs font-medium text-slate-500 mb-1">Total Expense</p>
                    <p class="text-2xl font-bold font-mono text-red-500">
                        Rp <?= number_format($report['total_expense'], 0, ',', '.') ?>
                    </p>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm">
                    <p class="text-xs font-medium text-slate-500 mb-1">Net Cash Flow</p>
                    <p class="text-2xl font-bold font-mono <?= $report['net'] >= 0 ? 'text-emerald-600' : 'text-red-500' ?>">
                        <?= $report['net'] >= 0 ? '+' : '' ?>Rp <?= number_format($report['net'], 0, ',', '.') ?>
                    </p>
                </div>
            </div>

            <!-- Chart -->
            <?php if (!empty($report['transactions'])): ?>
            <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
                <h2 class="font-bold text-slate-800 mb-5">Income vs Expense — <?= $monthNames[$month] ?> <?= $year ?></h2>
                <div class="h-48">
                    <canvas id="reportChart"></canvas>
                </div>
            </div>
            <?php endif; ?>

            <!-- Transactions table -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-50">
                    <h2 class="font-bold text-slate-800">
                        Transactions — <?= $monthNames[$month] ?> <?= $year ?>
                    </h2>
                </div>

                <?php if (empty($report['transactions'])): ?>
                <div class="py-16 text-center text-slate-400 text-sm">
                    No transactions for this period.
                </div>
                <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm" id="reportTable">
                        <thead>
                            <tr class="bg-slate-50 text-xs text-slate-500 uppercase tracking-wide">
                                <th class="text-left px-6 py-3 font-semibold">Date</th>
                                <th class="text-left px-6 py-3 font-semibold">Description</th>
                                <th class="text-left px-6 py-3 font-semibold">Category</th>
                                <th class="text-left px-6 py-3 font-semibold">Type</th>
                                <th class="text-right px-6 py-3 font-semibold">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php foreach ($report['transactions'] as $tx): ?>
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-3.5 text-slate-500 whitespace-nowrap">
                                    <?= date('d M Y', strtotime($tx['transaction_date'])) ?>
                                </td>
                                <td class="px-6 py-3.5 font-medium text-slate-700">
                                    <?= htmlspecialchars($tx['description'] ?: '—') ?>
                                </td>
                                <td class="px-6 py-3.5 text-slate-500"><?= htmlspecialchars($tx['category_name']) ?></td>
                                <td class="px-6 py-3.5">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        <?= $tx['type'] === 'income' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600' ?>">
                                        <?= ucfirst($tx['type']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 text-right font-mono font-semibold
                                    <?= $tx['type'] === 'income' ? 'text-emerald-600' : 'text-red-500' ?>">
                                    <?= $tx['type'] === 'income' ? '+' : '-' ?>Rp <?= number_format($tx['amount'], 0, ',', '.') ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="border-t-2 border-slate-200">
                            <tr class="bg-slate-50 font-semibold">
                                <td colspan="3" class="px-6 py-3 text-slate-700">Summary</td>
                                <td class="px-6 py-3 text-emerald-600">
                                    Income: Rp <?= number_format($report['total_income'], 0, ',', '.') ?>
                                </td>
                                <td class="px-6 py-3 text-right text-red-500 font-mono">
                                    −Rp <?= number_format($report['total_expense'], 0, ',', '.') ?>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <?php endif; ?>
            </div>

        </div><!-- /reportContent -->
    </main>
</div>

<!-- jsPDF CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>

<script>
<?php if (!empty($report['transactions'])): ?>
new Chart(document.getElementById('reportChart').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: ['Income', 'Expense'],
        datasets: [{
            data: [<?= $report['total_income'] ?>, <?= $report['total_expense'] ?>],
            backgroundColor: ['rgba(34,197,94,0.85)', 'rgba(248,113,113,0.85)'],
            borderWidth: 0,
            hoverOffset: 6,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'right' },
            tooltip: {
                callbacks: {
                    label: ctx => ' Rp ' + ctx.raw.toLocaleString('id-ID')
                }
            }
        },
        cutout: '65%',
    }
});
<?php endif; ?>

function exportPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    // Header
    doc.setFontSize(18);
    doc.setTextColor(22, 163, 74);
    doc.text('CashFlow Manager', 14, 20);

    doc.setFontSize(12);
    doc.setTextColor(71, 85, 105);
    doc.text('Monthly Report — <?= $monthNames[$month] . ' ' . $year ?>', 14, 30);

    // Summary box
    doc.setFontSize(10);
    doc.setTextColor(15, 23, 42);
    doc.text('Total Income : Rp <?= number_format($report['total_income'], 0, ',', '.') ?>', 14, 42);
    doc.text('Total Expense: Rp <?= number_format($report['total_expense'], 0, ',', '.') ?>', 14, 50);
    doc.text('Net Cash Flow: Rp <?= number_format($report['net'], 0, ',', '.') ?>', 14, 58);

    // Table
    const rows = [];
    <?php foreach ($report['transactions'] as $tx): ?>
    rows.push([
        '<?= date('d M Y', strtotime($tx['transaction_date'])) ?>',
        '<?= addslashes(htmlspecialchars_decode($tx['description'] ?: $tx['category_name'])) ?>',
        '<?= htmlspecialchars($tx['category_name']) ?>',
        '<?= ucfirst($tx['type']) ?>',
        '<?= ($tx['type']==='income'?'+':'-') ?>Rp <?= number_format($tx['amount'], 0, ',', '.') ?>'
    ]);
    <?php endforeach; ?>

    doc.autoTable({
        startY: 68,
        head: [['Date', 'Description', 'Category', 'Type', 'Amount']],
        body: rows,
        styles: { fontSize: 9, cellPadding: 3 },
        headStyles: { fillColor: [22, 163, 74], textColor: 255, fontStyle: 'bold' },
        alternateRowStyles: { fillColor: [240, 253, 244] },
        columnStyles: { 4: { halign: 'right' } }
    });

    doc.save('cashflow-report-<?= $year ?>-<?= str_pad($month, 2, '0', STR_PAD_LEFT) ?>.pdf');
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
