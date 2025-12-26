<?php
// payment/create_payment.php
header("Content-Type: application/json");

// Matikan display error
ini_set('display_errors', 0);
error_reporting(0);

try {
    // 1. Cek & Load Midtrans
    $midtrans_path = __DIR__ . '/../vendor/midtrans/midtrans-php/Midtrans.php';
    if (!file_exists($midtrans_path)) {
        throw new Exception("Library Midtrans tidak ditemukan.");
    }
    require_once $midtrans_path;

    // 2. Konfigurasi Midtrans
    \Midtrans\Config::$serverKey    = 'SB-Mid-server-ESGj19S0rXP3QyjZDmB108OE'; 
    \Midtrans\Config::$isProduction = false;
    \Midtrans\Config::$isSanitized  = true;
    \Midtrans\Config::$is3ds        = true;

    // --- [SOLUSI LOADING LAMA] ---
    // Tambahkan baris ini agar CURL tidak lambat di Localhost
    \Midtrans\Config::$curlOptions = [
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0
    ];
    // -----------------------------

    // 3. Ambil Input JSON
    $input = json_decode(file_get_contents("php://input"), true);
    if (!$input) throw new Exception("Input JSON tidak valid");

    $planId = $input['planId'] ?? ''; 
    $amount = (int)($input['amount'] ?? 0);
    $title  = $input['title']  ?? 'Paket SagaHealth';

    // 4. DAFTAR HARGA VALID
    $validPackages = [
        "basic"   => ["name" => "Basic Plan",   "amount" => 50000],
        "premium" => ["name" => "Premium Plan", "amount" => 100000]
    ];

    if (!array_key_exists($planId, $validPackages)) throw new Exception("Paket tidak terdaftar.");
    if ($amount !== $validPackages[$planId]['amount']) throw new Exception("Nominal tidak valid.");

    // 5. Buat Parameter Transaksi
    $orderId = "ORDER-" . time() . "-" . rand(1000, 9999);
    
    $params = [
        "transaction_details" => [
            "order_id"     => $orderId,
            "gross_amount" => $amount,
        ],
        "item_details" => [
            [
                "id"       => $planId,
                "price"    => $amount,
                "quantity" => 1,
                "name"     => substr($title, 0, 50)
            ]
        ],
        "customer_details" => [
            "first_name" => "User",
            "email"      => "user@example.com"
        ]
    ];

    // 6. Request Snap Token
    $snapToken = \Midtrans\Snap::getSnapToken($params);

    echo json_encode([
        "status"   => "success",
        "token"    => $snapToken,
        "order_id" => $orderId
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "status"  => "error",
        "message" => $e->getMessage()
    ]);
}
?>