<?php
header("Content-Type: application/json");

ini_set('display_errors', 0);
error_reporting(0);

$midtrans_path = __DIR__ . '/../vendor/midtrans/midtrans-php/Midtrans.php';

if (!file_exists($midtrans_path)) {
    echo json_encode([
        "status"  => "error",
        "message" => "Midtrans SDK tidak ditemukan"
    ]);
    exit;
}

require_once $midtrans_path;

\Midtrans\Config::$serverKey     = 'SB-Mid-server-ESGj19S0rXP3QyjZDmB108OE';
\Midtrans\Config::$isProduction  = false;
\Midtrans\Config::$isSanitized   = true;
\Midtrans\Config::$is3ds         = true;

$raw   = file_get_contents("php://input");
$input = json_decode($raw, true);

if (!$input) {
    echo json_encode([
        "status"  => "error",
        "message" => "Invalid JSON input"
    ]);
    exit;
}

$title  = $input['title']   ?? "";
$amount = (int)($input['amount'] ?? 0);
$planId = $input['planId']  ?? "";

$validPackages = [
    "basic"   => ["name" => "Basic",   "amount" => 50000],
    "premium" => ["name" => "Premium", "amount" => 100000]
];

if (!isset($validPackages[$planId])) {
    echo json_encode([
        "status"  => "error",
        "message" => "Plan tidak dikenali"
    ]);
    exit;
}

if ($amount !== $validPackages[$planId]['amount']) {
    echo json_encode([
        "status"  => "error",
        "message" => "Nominal plan tidak sesuai"
    ]);
    exit;
}

if (empty($title)) {
    $title = $validPackages[$planId]['name'];
}

$orderId = "ORDER-" . time() . "-" . rand(1000, 9999);

$params = [
    "transaction_details" => [
        "order_id"     => $orderId,
        "gross_amount" => $amount
    ],
    "item_details" => [
        [
            "id"       => $planId,
            "price"    => $amount,
            "quantity" => 1,
            "name"     => $title
        ]
    ]
];

try {
    $snapToken = \Midtrans\Snap::getSnapToken($params);

    $redirect = ($planId === "premium")
        ? "../user/dashboard_premium.php"
        : "../user/dashboard_basic.php";

    echo json_encode([
        "status"   => "success",
        "token"    => $snapToken,
        "redirect" => $redirect,
        "order_id" => $orderId
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status"  => "error",
        "message" => $e->getMessage()
    ]);
}
