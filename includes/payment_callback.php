<?php
// includes/payment_callback.php
require_once __DIR__ . '/subscriptions.php';

// Contoh: GET /includes/payment_callback.php?status=success&plan=premium&billing=monthly&user_id=123&payref=INV-001

$status   = $_GET['status']   ?? 'failed';
$plan     = $_GET['plan']     ?? 'basic';    // 'basic' atau 'premium'
$billing  = $_GET['billing']  ?? 'monthly';  // 'monthly' / 'yearly'
$userId   = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
$payRef   = $_GET['payref']   ?? null;

if (!$userId) {
    die('User tidak valid');
}

if ($status === 'success' || $status === 'paid') {
    createOrUpdateSubscription($userId, $plan, $billing, $payRef, 'active');
    // Redirect balik ke dashboard user
    header('Location: ../user/dashboard.php?payment=success');
    exit;
} else {
    // Bisa diarahkan ke halaman gagal
    header('Location: ../user/plan.php?payment=failed');
    exit;
}
