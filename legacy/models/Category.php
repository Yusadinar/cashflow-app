<?php
// ─────────────────────────────────────────
// models/Category.php
// ─────────────────────────────────────────
require_once __DIR__ . '/../config/database.php';

class Category
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = getDBConnection();
    }

    public function getAll(int $userId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM categories WHERE user_id = ? ORDER BY type, name');
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getByType(int $userId, string $type): array
    {
        $stmt = $this->db->prepare('SELECT * FROM categories WHERE user_id = ? AND type = ? ORDER BY name');
        $stmt->bind_param('is', $userId, $type);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getById(int $userId, int $id): array|null
    {
        $stmt = $this->db->prepare('SELECT * FROM categories WHERE user_id = ? AND id = ? LIMIT 1');
        $stmt->bind_param('ii', $userId, $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0 ? $result->fetch_assoc() : null;
    }

    public function create(int $userId, string $name, string $type): bool
    {
        $stmt = $this->db->prepare('INSERT INTO categories (user_id, name, type) VALUES (?, ?, ?)');
        $stmt->bind_param('iss', $userId, $name, $type);
        return $stmt->execute();
    }

    public function update(int $userId, int $id, string $name, string $type): bool
    {
        $stmt = $this->db->prepare('UPDATE categories SET name = ?, type = ? WHERE id = ? AND user_id = ?');
        $stmt->bind_param('ssii', $name, $type, $id, $userId);
        return $stmt->execute();
    }

    /** Only delete if no transactions reference this category */
    public function delete(int $userId, int $id): array
    {
        $check = $this->db->prepare(
            'SELECT COUNT(*) AS cnt FROM transactions WHERE category_id = ? AND user_id = ?'
        );
        $check->bind_param('ii', $id, $userId);
        $check->execute();
        $row = $check->get_result()->fetch_assoc();

        if ((int)$row['cnt'] > 0) {
            return ['success' => false, 'message' => 'Category is in use by transactions.'];
        }

        $stmt = $this->db->prepare('DELETE FROM categories WHERE id = ? AND user_id = ?');
        $stmt->bind_param('ii', $id, $userId);
        $stmt->execute();
        return ['success' => true, 'message' => 'Category deleted.'];
    }
}
