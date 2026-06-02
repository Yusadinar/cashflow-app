<?php
require_once __DIR__ . '/../controllers/authController.php';
$auth = new AuthController();
$auth->logout();
