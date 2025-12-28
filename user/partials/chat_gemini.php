<?php
session_start();
header('Content-Type: application/json');

// 1. Cek Login
if (!isset($_SESSION['isLoggedIn'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

// 2. Ambil Input JSON
$input = json_decode(file_get_contents('php://input'), true);
$userMessage = $input['message'] ?? '';

if (empty($userMessage)) {
    echo json_encode(['status' => 'error', 'message' => 'Pesan kosong']);
    exit;
}

// ==================================================================
// KONFIGURASI GEMINI API
// ==================================================================
$apiKey = "AIzaSyCt1XgsENBClJWhkTAFH-8aeU3Z1XclkyQ"; 
$apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $apiKey;

// 3. Siapkan Konteks (System Instruction)
// Ini agar AI tahu perannya sebagai dokter/asisten kesehatan
$systemPrompt = "Kamu adalah SagaBot, asisten kesehatan AI yang ramah, empati, dan profesional dari aplikasi SagaHealth. 
Tugasmu adalah menjawab pertanyaan seputar kesehatan fisik dan mental, memberikan tips pola hidup sehat, dan menjelaskan istilah medis dengan bahasa yang mudah dimengerti.
JANGAN memberikan diagnosa medis pasti atau resep obat keras. Selalu sarankan pengguna ke dokter jika gejalanya serius.
Nama pengguna saat ini adalah: " . ($_SESSION['user_name'] ?? 'Pengguna') . ".
Jawablah dengan singkat, padat, dan gunakan format bullet point jika perlu.";

// 4. Struktur Data untuk dikirim ke Gemini
$payload = [
    "contents" => [
        [
            "parts" => [
                ["text" => $systemPrompt . "\n\nUser bertanya: " . $userMessage]
            ]
        ]
    ],
    "generationConfig" => [
        "temperature" => 0.7, // Kreativitas (0.0 - 1.0)
        "maxOutputTokens" => 500 // Batas panjang jawaban
    ]
];

// 5. Kirim Request menggunakan CURL
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

// FIX: Matikan verifikasi SSL untuk Localhost (agar tidak error curl)
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo json_encode(['status' => 'error', 'reply' => 'Koneksi ke AI gagal: ' . curl_error($ch)]);
    curl_close($ch);
    exit;
}

curl_close($ch);

// 6. Parsing Jawaban JSON dari Google
$data = json_decode($response, true);

// Ambil teks jawaban
if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
    $botReply = $data['candidates'][0]['content']['parts'][0]['text'];
    
    // Format sedikit teksnya (Markdown to HTML simple)
    // Ganti baris baru jadi <br> dan bold **teks** jadi <b>teks</b>
    $botReply = nl2br($botReply); 
    $botReply = preg_replace('/\*\*(.*?)\*\*/', '<b>$1</b>', $botReply);

    echo json_encode(['status' => 'success', 'reply' => $botReply]);
} else {
    // Jika AI menolak menjawab atau error kuota
    echo json_encode(['status' => 'error', 'reply' => 'Maaf, saya sedang sibuk. Coba lagi nanti.']);
}
?>