<?php
session_start();

// Mengecek apakah user sudah login sebelum bisa menggunakan fitur mood tracker / chatbot
function checkUserAuthentication() {
    if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
        header('Location: login.php'); // Arahkan ke halaman login jika belum login
        exit();
    }
}

// Fungsi menampilkan pesan chat ke UI (bisa dihubungkan dengan frontend)
function createMessage($text, $sender = 'bot') {
    $class = ($sender == 'bot') ? 'bot-message' : 'user-message';
    echo "<div class='message {$class}'>" . htmlspecialchars($text) . "</div>";
}

// Simpan pesan ke riwayat sesi (user atau bot)
function saveMessageToHistory($text, $role) {
    if (!isset($_SESSION['chat_history'])) {
        $_SESSION['chat_history'] = [];
    }
    $_SESSION['chat_history'][] = [
        'role' => $role,
        'text' => $text,
        'timestamp' => time()
    ];
}

// Memanggil API Gemini untuk mendapatkan respon chatbot
function chatWithGemini($message) {
    $apiKey = 'YOUR_API_KEY';  // Ganti dengan API key yang valid
    $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $apiKey;

    $postData = json_encode([
        'prompt' => [
            'text' => $message
        ]
    ]);

    $options = [
        'http' => [
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'POST',
            'content' => $postData
        ]
    ];

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    if ($result === FALSE) {
        return "Maaf, tidak bisa menghubungi server chatbot sekarang.";
    }

    $response = json_decode($result, true);

    if (isset($response['candidates'][0]['content']['text'])) {
        return $response['candidates'][0]['content']['text'];
    }

    return "Maaf, terjadi kesalahan dalam mendapatkan respons chatbot.";
}

// Fungsi menangani pesan yang masuk dari user
function handleUserMessage($message) {
    if (trim($message) === '') {
        return;
    }

    // Tampilkan pesan user
    createMessage($message, 'user');
    saveMessageToHistory($message, 'user');

    // Dapatkan respon chatbot dari API Gemini
    $botReply = chatWithGemini($message);

    // Tampilkan pesan bot
    createMessage($botReply, 'bot');
    saveMessageToHistory($botReply, 'bot');
}

// Fungsi menampilkan seluruh riwayat chat dari session saat load page
function displayChatHistory() {
    if (!empty($_SESSION['chat_history'])) {
        foreach ($_SESSION['chat_history'] as $msg) {
            createMessage($msg['text'], $msg['role']);
        }
    }
}

// Proses request POST dari user untuk mengirim pesan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['userMessage'])) {
    handleUserMessage($_POST['userMessage']);
    exit; // Hentikan eksekusi setelah respon dikirim
}
?>
