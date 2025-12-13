<?php
header("Content-Type: application/json");

// === CONFIG MIDTRANS ===
$serverKey = "SB-Mid-server-ESGj19S0rXP3QyjZDmB108OE"; // GANTI DENGAN SERVER KEY SANDBOX
$midtransUrl = "https://app.sandbox.midtrans.com/snap/v1/transactions";

// Ambil input JSON
$input = json_decode(file_get_contents("php://input"), true);

$title = $input["title"] ?? null;
$amount = $input["amount"] ?? null;
$redirect = $input["redirect"] ?? null;

// Validasi
if (!$title || !$amount || !is_numeric($amount)) {
    echo json_encode([
        "status" => "error",
        "message" => "nominal paket tidak valid"
    ]);
    exit;
}

// Data transaksi
$payload = [
    "transaction_details" => [
        "order_id" => "ORDER-" . time(),
        "gross_amount" => (int)$amount
    ],
    "item_details" => [
        [
            "id" => $redirect,
            "price" => (int)$amount,
            "quantity" => 1,
            "name" => $title
        ]
    ],
    "customer_details" => [
        "first_name" => "User",
        "email" => "user@example.com"
    ]
];

// Curl ke Midtrans
$ch = curl_init($midtransUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Basic " . base64_encode($serverKey . ":")
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

// Jika gagal
if (!isset($result["token"])) {
    echo json_encode([
        "status" => "error",
        "message" => $response
    ]);
    exit;
}

// Jika sukses
echo json_encode([
    "status" => "success",
    "token" => $result["token"],
    "redirect" => $redirect
]);
exit;
?>
