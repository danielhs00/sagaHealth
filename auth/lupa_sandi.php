<?php
// Lupa Kata Sandi - SagaHealth
// PENTING: SELALU MULAI SESI DI AWAL FILE PHP
require_once '../includes/koneksi.php';
require_once '../includes/auth_functions.php'; 

// Cek dan ambil pesan dari Session, lalu bersihkan
$message = $_SESSION['message'] ?? '';
$message_type = $_SESSION['message_type'] ?? '';

unset($_SESSION['message']);
unset($_SESSION['message_type']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    if (empty($email)) {
        $_SESSION['message'] = "Email tidak boleh kosong.";
        $_SESSION['message_type'] = "error";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = "Format email tidak valid.";
        $_SESSION['message_type'] = "error";
    } else {
        // --- LOGIKA UTAMA LUPA SANDI ---
        // PENTING: Di sini Anda harus memanggil fungsi untuk
        // 1. Cek email di database
        // 2. Generate token (misalnya menggunakan bin2hex(random_bytes(32)))
        // 3. Simpan token ke tabel 'password_resets' dengan waktu kedaluwarsa
        // 4. Kirim email berisi link ke 'auth/reset_sandi.php?token=...'
        
        // Simulasikan pesan informasi yang aman:
        $_SESSION['message'] = "Jika alamat email terdaftar, link reset kata sandi akan dikirim ke email tersebut. Silakan cek kotak masuk (atau folder Spam) Anda.";
        $_SESSION['message_type'] = "info";
    }
    
    // Pola PRG (Post-Redirect-Get) untuk mencegah re-submission dan error method
    header("Location: lupa_sandi.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Sandi | SagaHealth</title>
    <link rel="stylesheet" href="../assets/style/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <h2 class="auth-title">Lupa Kata Sandi</h2>
            <p class="auth-subtitle">Masukkan alamat email Anda yang terdaftar untuk menerima link reset kata sandi.</p>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form action="lupa_sandi.php" method="POST">
                
                <div class="input-container">
                    <label for="email">Alamat Email</label>
                    <input type="email" id="email" name="email" placeholder="contoh@email.com" required>
                </div>
                
                <button type="submit" class="btn-submit">Kirim Link Reset</button>
            </form>

            <div class="auth-link-footer">
                <p>Ingat kata sandi Anda? <a href="login.php">Masuk Sekarang</a></p>
            </div>
        </div>
    </div>
</body>
</html>