<?php
// payment/process_success.php
session_start();
header('Content-Type: application/json');

// Pastikan path koneksi benar (sesuaikan dengan struktur folder Anda)
if (file_exists('../includes/koneksi.php')) {
    include '../includes/koneksi.php';
} else {
    echo json_encode(['status' => 'error', 'message' => 'File koneksi.php tidak ditemukan']);
    exit;
}

// Pastikan user login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User tidak login']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak valid']);
    exit;
}

$user_id = $_SESSION['user_id'];
$plan_type = $input['plan_type'];
$order_id = $input['order_id'] ?? 'MANUAL-BYPASS';
$amount = $input['amount'];

// Set durasi 30 hari
$start_date = date("Y-m-d H:i:s");
$end_date = date("Y-m-d H:i:s", strtotime("+30 days"));

// 1. Matikan paket lama
$stmt_off = $conn->prepare("UPDATE subscriptions SET status = 'expired' WHERE user_id = ? AND status = 'active'");
$stmt_off->bind_param("i", $user_id);
$stmt_off->execute();

// 2. Insert paket baru
$stmt = $conn->prepare("
    INSERT INTO subscriptions 
    (user_id, plan_name, plan_type, price, billing_period, payment_method, start_date, end_date, status) 
    VALUES (?, ?, ?, ?, 'monthly', 'midtrans', ?, ?, 'active')
");

$stmt->bind_param("isssss", 
    $user_id, 
    $plan_type,    
    $plan_type,    
    $amount,       
    $start_date, 
    $end_date
);

if ($stmt->execute()) {
    // 3. Update Session
    $_SESSION['plan_type'] = $plan_type;
    
    // Redirect sesuai paket
    $redirectUrl = ($plan_type === 'premium') 
        ? '../user/dashboard_premium.php' 
        : '../user/dashboard_basic.php';

    echo json_encode([
        'status' => 'success', 
        'redirect_url' => $redirectUrl
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal insert DB: ' . $stmt->error]);
}