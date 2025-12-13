<?php
// includes/subscriptions.php
require_once __DIR__ . '/db.php'; // sesuaikan dengan file koneksi kamu
header('Content-Type: application/json');

function getPdo() {
    // kalau di db.php sudah ada variabel $pdo, pakai global
    global $pdo;
    return $pdo;
}

// Buat / update subscription saat pembayaran sukses
function createOrUpdateSubscription($userId, $planType, $billingPeriod, $paymentRef, $status = 'active') {
    $pdo = getPdo();

    // Cek apakah sudah ada subscription aktif
    $stmt = $pdo->prepare("SELECT id FROM subscriptions 
                           WHERE user_id = ? AND status = 'active' 
                           ORDER BY id DESC LIMIT 1");
    $stmt->execute([$userId]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    $start = date('Y-m-d H:i:s');

    // Hitung end_date (contoh: 30 hari untuk bulanan, 365 hari untuk tahunan)
    $end = null;
    if ($status === 'active') {
        if ($billingPeriod === 'monthly') {
            $end = date('Y-m-d H:i:s', strtotime('+30 days'));
        } else {
            $end = date('Y-m-d H:i:s', strtotime('+1 year'));
        }
    }

    if ($existing) {
        // Update subscription lama (optional: di-expire dulu)
        $upd = $pdo->prepare("UPDATE subscriptions 
                              SET status = 'expired' 
                              WHERE id = ?");
        $upd->execute([$existing['id']]);
    }

    $ins = $pdo->prepare("INSERT INTO subscriptions
        (user_id, plan_type, billing_period, status, start_date, end_date, payment_reference)
        VALUES (?, ?, ?, ?, ?, ?, ?)");
    $ins->execute([$userId, $planType, $billingPeriod, $status, $start, $end, $paymentRef]);
}

// Ambil status langganan user (dipakai di dashboard & guard fitur)
function getActiveSubscription($userId) {
    $pdo = getPdo();
    $stmt = $pdo->prepare("SELECT * FROM subscriptions 
                           WHERE user_id = ? 
                           AND status = 'active'
                           AND (end_date IS NULL OR end_date >= NOW())
                           ORDER BY id DESC LIMIT 1");
    $stmt->execute([$userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Endpoint simple: ?action=get_status&userId=123
if (isset($_GET['action']) && $_GET['action'] === 'get_status') {
    $userId = isset($_GET['userId']) ? (int)$_GET['userId'] : 0;
    if (!$userId) {
        echo json_encode(['status' => 'error', 'message' => 'userId tidak valid']);
        exit;
    }

    $sub = getActiveSubscription($userId);
    if ($sub) {
        echo json_encode([
            'status' => 'success',
            'plan_type' => $sub['plan_type'],
            'billing_period' => $sub['billing_period'],
            'end_date' => $sub['end_date'],
        ]);
    } else {
        echo json_encode(['status' => 'success', 'plan_type' => null]);
    }
    exit;
}
