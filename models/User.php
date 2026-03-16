<?php
// ─────────────────────────────────────────
// models/User.php
// ─────────────────────────────────────────
require_once __DIR__ . '/../config/database.php';

class User
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = getDBConnection();
    }

    /** Find user by email */
    public function findByEmail(string $email): array|null
    {
        $stmt = $this->db->prepare(
            'SELECT id, name, email, password FROM users WHERE email = ? LIMIT 1'
        );
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0 ? $result->fetch_assoc() : null;
    }

    /** Find user by ID */
    public function findById(int $id): array|null
    {
        $stmt = $this->db->prepare(
            'SELECT id, name, email, created_at FROM users WHERE id = ? LIMIT 1'
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0 ? $result->fetch_assoc() : null;
    }

    /** Create new user */
    public function create(string $name, string $email, string $password): bool
    {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare(
            'INSERT INTO users (name, email, password) VALUES (?, ?, ?)'
        );
        $stmt->bind_param('sss', $name, $email, $hash);
        return $stmt->execute();
    }

    /** Verify plain password against stored hash */
    public function verifyPassword(string $plain, string $hash): bool
    {
        return password_verify($plain, $hash);
    }
}
