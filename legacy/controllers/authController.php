<?php
// controllers/authController.php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../models/User.php';

class AuthController
{
    private User $user;

    public function __construct()
    {
        $this->user = new User();
    }

    /** Start session; redirect to dashboard if already logged in */
    public function init(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /** Require authentication — redirect to login if not logged in */
    public function requireAuth(): void
    {
        $this->init();
        if (empty($_SESSION['user_id'])) {
            redirect('auth/login.php');
        }
    }

    /** Process login form submission */
    public function login(string $email, string $password): array
    {
        $this->init();

        if (empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'Email and password are required.'];
        }

        $user = $this->user->findByEmail($email);

        if (!$user || !$this->user->verifyPassword($password, $user['password'])) {
            return ['success' => false, 'message' => 'Invalid email or password.'];
        }

        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];

        return ['success' => true, 'message' => 'Login successful.'];
    }

    /** Destroy session and redirect */
    public function logout(): void
    {
        $this->init();
        session_destroy();
        redirect('auth/login.php');
    }

    /** Process registration form submission */
    public function register(string $name, string $email, string $password): array
    {
        $this->init();

        if (empty($name) || empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'All fields are required.'];
        }

        if ($this->user->findByEmail($email)) {
            return ['success' => false, 'message' => 'Email is already registered.'];
        }

        if (!$this->user->create($name, $email, $password)) {
            return ['success' => false, 'message' => 'Registration failed.'];
        }

        $user = $this->user->findByEmail($email);
        
        // Seed default categories for this user
        require_once __DIR__ . '/../models/Category.php';
        $categoryModel = new Category();
        $defaults = [
            ['name' => 'Salary',        'type' => 'income'],
            ['name' => 'Freelance',     'type' => 'income'],
            ['name' => 'Investment',    'type' => 'income'],
            ['name' => 'Other Income',  'type' => 'income'],
            ['name' => 'Rent',          'type' => 'expense'],
            ['name' => 'Food',          'type' => 'expense'],
            ['name' => 'Transport',     'type' => 'expense'],
            ['name' => 'Utilities',     'type' => 'expense'],
            ['name' => 'Entertainment', 'type' => 'expense'],
            ['name' => 'Healthcare',    'type' => 'expense'],
            ['name' => 'Shopping',      'type' => 'expense'],
            ['name' => 'Other Expense', 'type' => 'expense']
        ];
        
        foreach ($defaults as $cat) {
            $categoryModel->create($user['id'], $cat['name'], $cat['type']);
        }

        // Seed default payment methods
        require_once __DIR__ . '/../models/PaymentMethod.php';
        $pmModel = new PaymentMethod();
        $defaultPMs = ['BCA', 'Jago', 'Seabank', 'Gopay', 'Bibit', 'Dana', 'BNI', 'Saqu', 'Shopeepay'];
        foreach ($defaultPMs as $pm) {
            $pmModel->create($user['id'], $pm);
        }

        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];

        return ['success' => true, 'message' => 'Registration successful.'];
    }

    /** Return current logged-in user ID */
    public function currentUserId(): int
    {
        $this->init();
        return (int)($_SESSION['user_id'] ?? 0);
    }

    /** Return current user name */
    public function currentUserName(): string
    {
        $this->init();
        return $_SESSION['user_name'] ?? 'User';
    }
}
