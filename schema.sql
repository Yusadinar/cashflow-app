-- ============================================================
-- Cash Flow Management App — Database Schema
-- ============================================================

CREATE DATABASE IF NOT EXISTS cashflow_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE cashflow_db;

-- ─────────────────────────────────────────
-- Users
-- ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
    id         INT            AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100)   NOT NULL,
    email      VARCHAR(100)   NOT NULL UNIQUE,
    password   VARCHAR(255)   NOT NULL,
    created_at TIMESTAMP      DEFAULT CURRENT_TIMESTAMP
);

-- ─────────────────────────────────────────
-- Categories
-- ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS categories (
    id   INT          AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type ENUM('income','expense') NOT NULL
);

-- ─────────────────────────────────────────
-- Transactions
-- ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS transactions (
    id               INT            AUTO_INCREMENT PRIMARY KEY,
    user_id          INT            NOT NULL,
    category_id      INT            NOT NULL,
    type             ENUM('income','expense') NOT NULL,
    amount           DECIMAL(10,2)  NOT NULL,
    description      TEXT,
    transaction_date DATE           NOT NULL,
    created_at       TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)     REFERENCES users(id)      ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
);

-- ─────────────────────────────────────────
-- Seed data — demo user (password: admin123)
-- ─────────────────────────────────────────
INSERT INTO users (name, email, password) VALUES
('Admin User', 'admin@cashflow.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Seed categories
INSERT INTO categories (name, type) VALUES
('Salary',        'income'),
('Freelance',     'income'),
('Investment',    'income'),
('Other Income',  'income'),
('Rent',          'expense'),
('Food',          'expense'),
('Transport',     'expense'),
('Utilities',     'expense'),
('Entertainment', 'expense'),
('Healthcare',    'expense'),
('Shopping',      'expense'),
('Other Expense', 'expense');

-- Seed sample transactions
INSERT INTO transactions (user_id, category_id, type, amount, description, transaction_date) VALUES
(1, 1, 'income',  5000000, 'Monthly salary',           DATE_SUB(CURDATE(), INTERVAL 25 DAY)),
(1, 2, 'income',  1500000, 'Website project payment',  DATE_SUB(CURDATE(), INTERVAL 20 DAY)),
(1, 5, 'expense', 1200000, 'Monthly rent',              DATE_SUB(CURDATE(), INTERVAL 18 DAY)),
(1, 6, 'expense',  450000, 'Groceries',                 DATE_SUB(CURDATE(), INTERVAL 15 DAY)),
(1, 7, 'expense',  200000, 'Grab & transport',          DATE_SUB(CURDATE(), INTERVAL 12 DAY)),
(1, 8, 'expense',  350000, 'Electricity & water',       DATE_SUB(CURDATE(), INTERVAL 10 DAY)),
(1, 1, 'income',  5000000, 'Monthly salary',            DATE_SUB(CURDATE(), INTERVAL 5 DAY)),
(1, 9, 'expense',  300000, 'Cinema & dining',           DATE_SUB(CURDATE(), INTERVAL 3 DAY)),
(1, 3, 'income',   750000, 'Stock dividend',            DATE_SUB(CURDATE(), INTERVAL 2 DAY)),
(1,11, 'expense',  680000, 'Online shopping',           DATE_SUB(CURDATE(), INTERVAL 1 DAY));
