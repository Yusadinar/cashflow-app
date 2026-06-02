<?php
// ─────────────────────────────────────────
// models/PaymentMethod.php
// ─────────────────────────────────────────
require_once __DIR__ . '/../config/database.php';

class PaymentMethod
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = getDBConnection();
    }

    public function getAll(int $userId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM payment_methods WHERE user_id = ? ORDER BY name');
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function create(int $userId, string $name): bool
    {
        $stmt = $this->db->prepare('INSERT INTO payment_methods (user_id, name) VALUES (?, ?)');
        $stmt->bind_param('is', $userId, $name);
        return $stmt->execute();
    }
}
