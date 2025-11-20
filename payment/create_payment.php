<?php
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
