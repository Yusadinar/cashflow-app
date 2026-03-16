# 💰 CashFlow Manager

A clean, modern **Cash Flow Management** web application for small business accounting.
Built with **Native PHP + MySQL** (MVC pattern) and styled with **Tailwind CSS**.

---

## ✨ Features

| Feature | Details |
|---|---|
| 🔐 Authentication | Session-based login / logout |
| 📊 Dashboard | Balance, income, expense summary + Chart.js bar chart |
| 💳 Transactions | Full CRUD with category, type, amount, date, description |
| 🏷️ Categories | Add / edit / delete income & expense categories |
| 📄 Reports | Monthly report with doughnut chart + PDF export |
| 📱 Responsive | Mobile-first sidebar, Tailwind utility classes |

---

## 🚀 Quick Start

### 1 — Requirements
- PHP 8.1+
- MySQL 5.7+ or MariaDB 10.4+
- A local server: **XAMPP**, **Laragon**, or **php -S**

### 2 — Database Setup
```sql
-- Import the schema (creates DB + seeds demo data)
mysql -u root -p < schema.sql
```

Or open **phpMyAdmin** → Import → select `schema.sql`.

### 3 — Configure DB connection
Edit `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');     // your MySQL user
define('DB_PASS', '');         // your MySQL password
define('DB_NAME', 'cashflow_db');
```

### 4 — Run the app
```bash
# Option A — PHP built-in server (from project root)
php -S localhost:8000

# Option B — XAMPP
# Place folder in /htdocs, visit http://localhost/cashflow-app
```

### 5 — Login
```
Email   : admin@cashflow.com
Password: password
```

---

## 📁 Folder Structure

```
cashflow-app/
├── config/
│   └── database.php          # DB connection helper
├── controllers/
│   ├── authController.php    # Login / logout / session
│   ├── transactionController.php
│   └── categoryController.php
├── models/
│   ├── User.php
│   ├── Transaction.php
│   └── Category.php
├── views/
│   ├── dashboard.php         # Summary cards + Chart.js
│   ├── transactions.php      # List + delete
│   ├── add_transaction.php   # Create form
│   ├── edit_transaction.php  # Edit form
│   ├── categories.php        # CRUD modals
│   └── report.php            # Monthly report + PDF export
├── assets/
│   ├── css/style.css
│   └── js/script.js
├── includes/
│   ├── header.php            # HTML head + Tailwind CDN
│   ├── sidebar.php           # Navigation sidebar
│   └── footer.php            # Closing tags + JS
├── auth/
│   ├── login.php             # Login page + handler
│   └── logout.php            # Session destroy
├── index.php                 # Entry point (redirect)
├── schema.sql                # DB schema + seed data
└── README.md
```

---

## 🏗️ Architecture

The project follows a lightweight **MVC separation**:

```
Request → view (PHP page)
            ↓
       controller  (business logic, validation)
            ↓
          model   (prepared-statement DB queries)
            ↓
       MySQL DB
```

Views call controllers directly — no router layer — keeping it simple enough
for a student project while demonstrating clean separation of concerns.

---

## 🛡️ Security

- Passwords hashed with `password_hash()` (bcrypt)
- All DB queries use **prepared statements** (no SQL injection)
- Session authentication on every protected page
- HTML output escaped with `htmlspecialchars()`
- CSRF protection via session token (add to production)

---

## 📦 Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.1 (native, no framework) |
| Database | MySQL / MariaDB |
| Frontend | HTML5 + Tailwind CSS (CDN) |
| Charts | Chart.js 4 |
| PDF Export | jsPDF + jsPDF-AutoTable |
| Fonts | Plus Jakarta Sans (Google Fonts) |

---

## 🖼️ Screenshots

| Page | Description |
|---|---|
| Login | Clean card with password toggle |
| Dashboard | Summary cards + monthly bar chart + recent transactions |
| Transactions | Filterable table with inline edit / delete |
| Categories | Two-column income/expense list with modal CRUD |
| Reports | Period selector + doughnut chart + PDF export |

---

## 📝 License

MIT — free to use for educational and commercial projects.
