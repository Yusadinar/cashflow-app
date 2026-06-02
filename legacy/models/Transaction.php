<?php
// ─────────────────────────────────────────
// models/Transaction.php
// ─────────────────────────────────────────
require_once __DIR__ . '/../config/database.php';

class Transaction
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = getDBConnection();
    }

    /** All transactions for a user (with category name), newest first */
    public function getAllByUser(int $userId, int $limit = 0): array
    {
        $sql = 'SELECT t.*, c.name AS category_name, pm.name AS payment_method_name
                FROM transactions t
                JOIN categories c ON c.id = t.category_id
                JOIN payment_methods pm ON pm.id = t.payment_method_id
                WHERE t.user_id = ?
                ORDER BY t.transaction_date DESC, t.created_at DESC';

        if ($limit > 0) {
            $sql .= ' LIMIT ' . (int)$limit;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /** Single transaction by ID (verifies ownership) */
    public function getById(int $id, int $userId): array|null
    {
        $stmt = $this->db->prepare(
            'SELECT t.*, c.name AS category_name, pm.name AS payment_method_name
             FROM transactions t
             JOIN categories c ON c.id = t.category_id
             JOIN payment_methods pm ON pm.id = t.payment_method_id
             WHERE t.id = ? AND t.user_id = ?
             LIMIT 1'
        );
        $stmt->bind_param('ii', $id, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0 ? $result->fetch_assoc() : null;
    }

    /** Filter by date range */
    public function getByDateRange(int $userId, string $startDate, string $endDate): array
    {
        $stmt = $this->db->prepare(
            'SELECT t.*, c.name AS category_name, pm.name AS payment_method_name
             FROM transactions t
             JOIN categories c ON c.id = t.category_id
             JOIN payment_methods pm ON pm.id = t.payment_method_id
             WHERE t.user_id = ?
               AND t.transaction_date BETWEEN ? AND ?
             ORDER BY t.transaction_date DESC'
        );
        $stmt->bind_param('iss', $userId, $startDate, $endDate);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /** Monthly totals grouped by month for chart */
    public function getMonthlyTotals(int $userId, int $year): array
    {
        $stmt = $this->db->prepare(
            'SELECT MONTH(transaction_date) AS month,
                    type,
                    SUM(amount) AS total
             FROM transactions
             WHERE user_id = ? AND YEAR(transaction_date) = ?
             GROUP BY month, type
             ORDER BY month'
        );
        $stmt->bind_param('ii', $userId, $year);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /** Summary totals: balance, income, expense */
    public function getSummary(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT
                SUM(CASE WHEN type = "income"  THEN amount ELSE 0 END) AS total_income,
                SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) AS total_expense
             FROM transactions
             WHERE user_id = ?'
        );
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        $income  = (float)($row['total_income']  ?? 0);
        $expense = (float)($row['total_expense'] ?? 0);

        return [
            'total_income'  => $income,
            'total_expense' => $expense,
            'balance'       => $income - $expense,
        ];
    }

    /** Balances per payment method */
    public function getBalancesByPaymentMethod(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT pm.name AS method_name,
                    SUM(CASE WHEN t.type = "income" THEN t.amount ELSE 0 END) AS total_income,
                    SUM(CASE WHEN t.type = "expense" THEN t.amount ELSE 0 END) AS total_expense
             FROM payment_methods pm
             LEFT JOIN transactions t ON t.payment_method_id = pm.id AND t.user_id = ?
             WHERE pm.user_id = ?
             GROUP BY pm.id, pm.name
             ORDER BY pm.name'
        );
        $stmt->bind_param('ii', $userId, $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /** Insert a new transaction */
    public function create(
        int    $userId,
        int    $categoryId,
        int    $paymentMethodId,
        string $type,
        float  $amount,
        string $description,
        string $date
    ): bool {
        $stmt = $this->db->prepare(
            'INSERT INTO transactions
                (user_id, category_id, payment_method_id, type, amount, description, transaction_date)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->bind_param('iiisdss', $userId, $categoryId, $paymentMethodId, $type, $amount, $description, $date);
        return $stmt->execute();
    }

    /** Update an existing transaction (ownership enforced) */
    public function update(
        int    $id,
        int    $userId,
        int    $categoryId,
        int    $paymentMethodId,
        string $type,
        float  $amount,
        string $description,
        string $date
    ): bool {
        $stmt = $this->db->prepare(
            'UPDATE transactions
             SET category_id = ?, payment_method_id = ?, type = ?, amount = ?, description = ?, transaction_date = ?
             WHERE id = ? AND user_id = ?'
        );
        $stmt->bind_param('iisdssii', $categoryId, $paymentMethodId, $type, $amount, $description, $date, $id, $userId);
        return $stmt->execute();
    }

    /** Delete a transaction (ownership enforced) */
    public function delete(int $id, int $userId): bool
    {
        $stmt = $this->db->prepare(
            'DELETE FROM transactions WHERE id = ? AND user_id = ?'
        );
        $stmt->bind_param('ii', $id, $userId);
        return $stmt->execute();
    }

    /** Monthly report: income + expense per month */
    public function getMonthlyReport(int $userId, int $year, int $month): array
    {
        $stmt = $this->db->prepare(
            'SELECT t.*, c.name AS category_name, pm.name AS payment_method_name
             FROM transactions t
             JOIN categories c ON c.id = t.category_id
             JOIN payment_methods pm ON pm.id = t.payment_method_id
             WHERE t.user_id = ?
               AND YEAR(t.transaction_date)  = ?
               AND MONTH(t.transaction_date) = ?
             ORDER BY t.transaction_date DESC'
        );
        $stmt->bind_param('iii', $userId, $year, $month);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
