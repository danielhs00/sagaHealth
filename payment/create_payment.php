<?php
<<<<<<< HEAD
// ======== CONFIG (ISI API KEY MAYAR KAMU) ===========
$API_KEY = "MASUKKAN_API_KEY_MAYAR_DI_SINI"; 

$input = json_decode(file_get_contents("php://input"), true);
$amount = $input["amount"];
$title = $input["title"];

// Redirect setelah pembayaran
$success = "http://localhost/SagaHealth/payment/success.php";
$failed  = "http://localhost/SagaHealth/payment/failed.php";

// ======== REQUEST KE API MAYAR ======================
$payload = [
    "amount" => $amount,
    "title" => $title,
    "redirect_url" => $success,
    "cancel_url" => $failed
];

$ch = curl_init("https://mayar.id/api/v3/payment-link");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $API_KEY",
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);

echo $response;
=======
// payment/create_payment.php - Inisiasi Pembayaran DANA
session_start();
require_once '../includes/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die(json_encode(["status" => "error", "message" => "Unauthorized access."]));
}

header('Content-Type: application/json');

// --- 1. Konfigurasi DANA API (GANTI DENGAN DATA ASLI ANDA) ---
// GANTI SEMUA NILAI PLACEHOLDER INI
define('DANA_API_ENDPOINT', 'https://api.dana.id/v1/payment/create'); 
define('DANA_MERCHANT_ID', 'YOUR_MERCHANT_ID');
define('DANA_API_KEY', 'YOUR_API_KEY'); // Jika DANA menggunakan header API Key
define('DANA_SECRET', 'YOUR_API_SECRET'); // Secret untuk Digital Signature (CRITICAL!)

// GANTI 'https://YOUR_DOMAIN.com' dengan domain Anda yang sebenarnya
$baseUrl = 'https://YOUR_DOMAIN.com'; 

// --- 2. Ambil Data dari Request ---
$plan_data = json_decode(file_get_contents('php://input'), true);

if (!$plan_data || empty($plan_data['plan_name']) || empty($plan_data['price'])) {
    http_response_code(400);
    die(json_encode(["status" => "error", "message" => "Data paket tidak lengkap."]));
}

$planName = $plan_data['plan_name'];
$price = (float)$plan_data['price'];
$amount = (int)($price * 100); // Konversi ke satuan terkecil (sen/cents)

$userId = $_SESSION['user_id'];
$orderId = 'SAG-' . time() . '-' . $userId; 
$callbackUrl = $baseUrl . '/includes/payment_callback.php'; 
$returnUrl = $baseUrl . '/user/plan.php?status=payment_pending&order=' . $orderId; 

// --- 3. Buat Payload DANA ---
$payload = [
    'merchant_id' => DANA_MERCHANT_ID,
    'order_id' => $orderId,
    'amount' => $amount,
    'currency' => 'IDR',
    'callback_url' => $callbackUrl,
    'return_url' => $returnUrl, // Setelah pembayaran selesai, user dikembalikan ke sini
    'item_details' => [
        [
            'name' => 'Subscription: ' . $planName,
            'price' => $amount,
            'quantity' => 1
        ]
    ],
    // Hashing dan Signature (Jika diperlukan oleh DANA)
    // 'signature' => generateDanaSignature($payload, DANA_SECRET),
];

// --- 4. Kirim Request ke DANA API menggunakan cURL ---
$ch = curl_init(DANA_API_ENDPOINT);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    // 'X-Api-Key: ' . DANA_API_KEY, // Tambahkan header yang diperlukan DANA
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$danaResponse = json_decode($response, true);

// --- 5. Handle Response dan Simpan Order (CRITICAL) ---
if ($httpCode === 200 && isset($danaResponse['redirect_url'])) {
    // Di sini Anda HARUS menyimpan Order ID, User ID, dan detail paket ke tabel 'orders' 
    // agar dapat dicocokkan di payment_callback.php
    
    echo json_encode([
        "status" => "success",
        "redirect_url" => $danaResponse['redirect_url'], 
        "order_id" => $orderId
    ]);

} else {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Gagal membuat transaksi DANA: " . ($danaResponse['message'] ?? 'Unknown API error'),
    ]);
}
?>
>>>>>>> 4b250f7 (benerin home,benerin dan integrasiin halaman login, nyiapin integrasi api payment jadi ke dana)
