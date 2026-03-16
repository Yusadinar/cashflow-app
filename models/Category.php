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

    public function getAll(): array
    {
        $result = $this->db->query('SELECT * FROM categories ORDER BY type, name');
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getByType(string $type): array
    {
        $stmt = $this->db->prepare('SELECT * FROM categories WHERE type = ? ORDER BY name');
        $stmt->bind_param('s', $type);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getById(int $id): array|null
    {
        $stmt = $this->db->prepare('SELECT * FROM categories WHERE id = ? LIMIT 1');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0 ? $result->fetch_assoc() : null;
    }

    public function create(string $name, string $type): bool
    {
        $stmt = $this->db->prepare('INSERT INTO categories (name, type) VALUES (?, ?)');
        $stmt->bind_param('ss', $name, $type);
        return $stmt->execute();
    }

    public function update(int $id, string $name, string $type): bool
    {
        $stmt = $this->db->prepare('UPDATE categories SET name = ?, type = ? WHERE id = ?');
        $stmt->bind_param('ssi', $name, $type, $id);
        return $stmt->execute();
    }

    /** Only delete if no transactions reference this category */
    public function delete(int $id): array
    {
        $check = $this->db->prepare(
            'SELECT COUNT(*) AS cnt FROM transactions WHERE category_id = ?'
        );
        $check->bind_param('i', $id);
        $check->execute();
        $row = $check->get_result()->fetch_assoc();

        if ((int)$row['cnt'] > 0) {
            return ['success' => false, 'message' => 'Category is in use by transactions.'];
        }

        $stmt = $this->db->prepare('DELETE FROM categories WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return ['success' => true, 'message' => 'Category deleted.'];
    }
}
