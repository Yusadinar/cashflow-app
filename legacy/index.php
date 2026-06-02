<?php
require_once __DIR__ . '/config/app.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if (!empty($_SESSION['user_id'])) {
    redirect('views/dashboard.php');
} else {
    redirect('auth/login.php');
}
