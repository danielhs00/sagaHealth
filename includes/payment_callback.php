<?php
// includes/payment_callback.php - DANA Webhook Listener
require_once 'koneksi.php';

// Atur header response agar DANA mengetahui server menerima callback
header('Content-Type: application/json');

// PENTING: Anda mungkin perlu mendefinisikan DANA_SECRET di sini lagi
// define('DANA_SECRET', 'YOUR_API_SECRET'); 

// --- 1. Ambil Payload ---
$payload = file_get_contents('php://input');
$danaData = json_decode($payload, true);

// --- 2. Verifikasi Tanda Tangan Digital (CRITICAL) ---
// Logika verifikasi signature DANA harus ditambahkan di sini untuk keamanan.

// --- 3. Proses Status Transaksi ---
if (isset($danaData['order_id']) && isset($danaData['status'])) {
    $orderId = $danaData['order_id'];
    $status = $danaData['status']; 

    // Asumsi: Anda sudah memiliki tabel 'orders' untuk mencari detail transaksi
    // $orderDetail = fetchOrderDetailFromDB($orderId); // Panggil fungsi Anda di sini
    
    if ($status === 'SUCCESS' || $status === 'PAID') { // Ganti dengan status sukses yang ditentukan DANA
        
        // Asumsi data order:
        $userId = 1; // Ganti dengan hasil query DB berdasarkan $orderId
        $plan_name = "Premium";
        $plan_type = "yearly";
        $price_str = "99000"; 
        $billing_period = "yearly";
        
        $endDate = date('Y-m-d H:i:s', strtotime('+1 year')); // Contoh 1 tahun
        $currentDate = date('Y-m-d H:i:s');
        
        // Update/Insert ke tabel subscriptions
        $stmt = $conn->prepare("INSERT INTO subscriptions (user_id, plan_name, plan_type, price, billing_period, payment_method, start_date, end_date, status) 
                                VALUES (?, ?, ?, ?, ?, 'DANA', ?, ?, 'active') 
                                ON DUPLICATE KEY UPDATE end_date=?, status='active'");

        // Perhatian: Pastikan jumlah dan tipe parameter sesuai dengan placeholder di query
        // Saya asumsikan plan_type dan plan_name sama untuk kemudahan contoh
        $stmt->bind_param("isssssss", $userId, $plan_name, $plan_type, $price_str, $billing_period, $currentDate, $endDate, $endDate);
        
        if ($stmt->execute()) {
             // Beri respons sukses (HTTP 200) ke DANA API
             http_response_code(200);
             die(json_encode(["status" => "success", "message" => "Subscription updated"]));
        } else {
             // Log error database
             http_response_code(500);
             die(json_encode(["status" => "error", "message" => "DB Error"]));
        }

    } else if ($status === 'FAILED' || $status === 'EXPIRED') {
        // Logika untuk menandai order gagal
        http_response_code(200);
        die(json_encode(["status" => "success", "message" => "Order logged as failed"]));
    }

} else {
    http_response_code(400);
    die(json_encode(["status" => "error", "message" => "Invalid payload received"]));
}
?>