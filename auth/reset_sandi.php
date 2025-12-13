<?php
// Reset Kata Sandi - SagaHealth
session_start();
require_once '../includes/koneksi.php';
require_once '../includes/auth_functions.php';

$message = '';
$message_type = '';
$token = $_GET['token'] ?? null;
$token_valid = false;

// Cek jika ada pesan dari redirect
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $message_type = $_SESSION['message_type'];
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}

// 1. Cek token dari URL (GET request)
if ($token) {
    // PENTING: Logika ini harus dicek di database Anda
    // Cek apakah $token ada di tabel 'password_resets', belum expired, dan belum digunakan.
    
    // SIMULASI: Anggap token valid
    if (true) { // Ganti dengan logika DB: (is_token_valid($token))
        $token_valid = true;
    } else {
        $message = "Token reset kata sandi tidak valid atau sudah kedaluwarsa.";
        $message_type = "error";
    }

} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 2. Proses form reset sandi (POST request)
    $token = $_POST['token'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Lakukan validasi input
    if (empty($token) || empty($new_password) || empty($confirm_password)) {
        $message = "Semua field harus diisi.";
        $message_type = "error";
    } else if ($new_password !== $confirm_password) {
        $message = "Konfirmasi kata sandi tidak cocok.";
        $message_type = "error";
    } else if (strlen($new_password) < 6) { // Contoh validasi
        $message = "Kata sandi minimal 6 karakter.";
        $message_type = "error";
    } else {
        // --- LOGIKA UPDATE SANDI ---
        // PENTING: Lakukan validasi token di DB sekali lagi
        // Hash kata sandi baru ($new_password) dan update di tabel 'users'.
        // Tandai token di tabel 'password_resets' sebagai terpakai.
        
        // SIMULASI:
        $success = true; 
        
        if ($success) {
            $_SESSION['message'] = "Kata sandi Anda berhasil direset! Silakan masuk dengan kata sandi baru.";
            $_SESSION['message_type'] = "success";
            header("Location: login.php");
            exit();
        } else {
            $message = "Gagal mereset kata sandi. Silakan coba lagi.";
            $message_type = "error";
            $token_valid = true; // Agar form tetap terlihat setelah error POST
        }
    }
    // Jika ada error pada POST, pastikan form reset tetap ditampilkan
    if ($message_type === 'error') {
        $token_valid = true;
    }

} else {
    // Akses langsung ke halaman tanpa token dan tanpa POST
    $message = "Akses tidak sah. Silakan gunakan link dari email Anda.";
    $message_type = "error";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Kata Sandi | SagaHealth</title>
    <link rel="stylesheet" href="../assets/style/styles.css">
    <link rel="stylesheet" href="../assets/style/auth.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <h2 class="auth-title">Reset Kata Sandi</h2>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <?php if ($token_valid): ?>
            <form action="reset_sandi.php" method="POST">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                
                <div class="input-container">
                    <label for="new_password">Kata Sandi Baru</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="new_password" name="new_password" class="password-field" placeholder="Masukkan Kata Sandi Baru" required>
                        <button type="button" class="password-toggle-btn">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="input-container">
                    <label for="confirm_password">Konfirmasi Kata Sandi Baru</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="confirm_password" name="confirm_password" class="password-field" placeholder="Konfirmasi Kata Sandi Baru" required>
                        <button type="button" class="password-toggle-btn">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <button type="submit" class="btn-submit">Simpan Kata Sandi Baru</button>
            </form>
            <?php endif; ?>
        </div>
    </div>
    <script src="../assets/js/app.js"></script>
</body>
</html>