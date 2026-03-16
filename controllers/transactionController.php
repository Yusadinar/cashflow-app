<?php
// ─────────────────────────────────────────
// controllers/transactionController.php
// ─────────────────────────────────────────
require_once __DIR__ . '/../models/Transaction.php';

class TransactionController
{
    private Transaction $transaction;

    public function __construct()
    {
        $this->transaction = new Transaction();
    }

    public function index(int $userId): array
    {
        return $this->transaction->getAllByUser($userId);
    }

    public function recent(int $userId, int $limit = 5): array
    {
        return $this->transaction->getAllByUser($userId, $limit);
    }

    public function show(int $id, int $userId): array|null
    {
        return $this->transaction->getById($id, $userId);
    }

    public function summary(int $userId): array
    {
        return $this->transaction->getSummary($userId);
    }

    public function monthlyChart(int $userId, int $year): array
    {
        $rows = $this->transaction->getMonthlyTotals($userId, $year);
        $months = array_fill(1, 12, ['income' => 0, 'expense' => 0]);

        foreach ($rows as $row) {
            $m = (int)$row['month'];
            $months[$m][$row['type']] = (float)$row['total'];
        }

        $labels  = [];
        $income  = [];
        $expense = [];

        $monthNames = ['Jan','Feb','Mar','Apr','May','Jun',
                       'Jul','Aug','Sep','Oct','Nov','Dec'];

        for ($i = 1; $i <= 12; $i++) {
            $labels[]  = $monthNames[$i - 1];
            $income[]  = $months[$i]['income'];
            $expense[] = $months[$i]['expense'];
        }

        return compact('labels', 'income', 'expense');
    }

    public function store(int $userId, array $data): array
    {
        $errors = $this->validate($data);
        if ($errors) return ['success' => false, 'errors' => $errors];

        $ok = $this->transaction->create(
            $userId,
            (int)$data['category_id'],
            $data['type'],
            (float)$data['amount'],
            trim($data['description'] ?? ''),
            $data['transaction_date']
        );

        return $ok
            ? ['success' => true,  'message' => 'Transaction added.']
            : ['success' => false, 'message' => 'Failed to add transaction.'];
    }

    public function update(int $id, int $userId, array $data): array
    {
        $errors = $this->validate($data);
        if ($errors) return ['success' => false, 'errors' => $errors];

        $ok = $this->transaction->update(
            $id,
            $userId,
            (int)$data['category_id'],
            $data['type'],
            (float)$data['amount'],
            trim($data['description'] ?? ''),
            $data['transaction_date']
        );

        return $ok
            ? ['success' => true,  'message' => 'Transaction updated.']
            : ['success' => false, 'message' => 'Failed to update transaction.'];
    }

    public function destroy(int $id, int $userId): array
    {
        $ok = $this->transaction->delete($id, $userId);
        return $ok
            ? ['success' => true,  'message' => 'Transaction deleted.']
            : ['success' => false, 'message' => 'Failed to delete transaction.'];
    }

    public function report(int $userId, int $year, int $month): array
    {
        $rows    = $this->transaction->getMonthlyReport($userId, $year, $month);
        $income  = 0;
        $expense = 0;

        foreach ($rows as $r) {
            if ($r['type'] === 'income')  $income  += $r['amount'];
            else                          $expense += $r['amount'];
        }

        return [
            'transactions'  => $rows,
            'total_income'  => $income,
            'total_expense' => $expense,
            'net'           => $income - $expense,
        ];
    }

    public function filterByDate(int $userId, string $start, string $end): array
    {
        return $this->transaction->getByDateRange($userId, $start, $end);
    }

    // ── private ─────────────────────────────
    private function validate(array $data): array
    {
        $errors = [];

        if (empty($data['category_id'])) $errors[] = 'Category is required.';
        if (empty($data['type']) || !in_array($data['type'], ['income','expense']))
            $errors[] = 'Type must be income or expense.';
        if (!isset($data['amount']) || (float)$data['amount'] <= 0)
            $errors[] = 'Amount must be greater than 0.';
        if (empty($data['transaction_date']))
            $errors[] = 'Date is required.';

        return $errors;
    }
}
