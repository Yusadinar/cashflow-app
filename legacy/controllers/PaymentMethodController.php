<?php
// ─────────────────────────────────────────
// controllers/PaymentMethodController.php
// ─────────────────────────────────────────
require_once __DIR__ . '/../models/PaymentMethod.php';

class PaymentMethodController
{
    private PaymentMethod $paymentMethod;

    public function __construct()
    {
        $this->paymentMethod = new PaymentMethod();
    }

    public function index(int $userId): array
    {
        return $this->paymentMethod->getAll($userId);
    }
}
