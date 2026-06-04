# CashFlow App

A modern, responsive personal finance tracking application built with Laravel 12 and Tailwind CSS. CashFlow App empowers users to take control of their finances by meticulously tracking incomes, expenses, and managing multiple wallets seamlessly.

Website Live: https://cashflow-app.xo.je/

## ✨ Key Features

- **Multi-Wallet Management:** Create and manage multiple payment methods (e.g., Bank Accounts, E-Wallets, Investment Portfolios) with accurate initial balance tracking.
- **Income & Expense Tracking:** Log daily financial activities categorized accurately by custom Income/Expense types.
- **Internal Transfers:** Move funds between your different payment methods seamlessly without inflating your monthly income/expense charts.
- **Debts & Receivables:** Track money you owe to others or money owed to you. Seamlessly pay off debts directly using your saved payment methods, automatically adjusting your wallet balances.
- **Wishlist Planner:** Plan future purchases, set target payment methods, and preview estimated remaining balances before spending.
- **Analytics Dashboard:** A comprehensive, real-time dashboard featuring monthly charts, account balance summaries, and a quick overview of recent transactions.
- **Modern & Responsive UI:** Built with Tailwind CSS and Alpine.js, offering a premium user experience with a collapsible sidebar and seamless responsive design that works flawlessly on desktop and mobile devices.
- **Secure Authentication:** Built on top of Laravel Breeze for secure, out-of-the-box user registration and authentication.

## 🛠️ Technology Stack

- **Backend:** Laravel 12 (PHP)
- **Frontend:** Tailwind CSS, Alpine.js, Blade Templates
- **Data Visualization:** Chart.js
- **Database:** MySQL / MariaDB (or SQLite for local development)

## 🚀 Installation & Setup

Follow these steps to run the project locally:

1. **Clone the repository**
   ```bash
   git clone https://github.com/Yusadinar/cashflow-app.git
   cd cashflow-app
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install NPM dependencies**
   ```bash
   npm install
   ```

4. **Environment Setup**
   Copy the `.env.example` file to `.env` and configure your database credentials.
   ```bash
   cp .env.example .env
   ```

5. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

6. **Run Database Migrations**
   Make sure your database server is running, then execute:
   ```bash
   php artisan migrate
   ```

7. **Compile Frontend Assets**
   ```bash
   npm run build
   ```

8. **Start the Development Server**
   ```bash
   php artisan serve
   ```
   Visit `http://localhost:8000` in your browser.

## 📦 Deployment (e.g., InfinityFree / Shared Hosting)
When deploying to a shared hosting environment without SSH access:
1. Ensure you have run `npm run build` locally and upload the `public/build` directory.
2. Upload the source code (excluding `node_modules`).
3. For database migrations, you can export your local database via phpMyAdmin and import it to the live server, OR temporarily create a route in `routes/web.php` that calls `Artisan::call('migrate', ['--force' => true])` and access it via the browser.

## 📄 License

This project is open-source and available under the [MIT License](LICENSE).
