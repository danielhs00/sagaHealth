<?php
session_start();

// --- 1. SECURITY CHECK (SERVER SIDE) ---
// Cek apakah user sudah login
if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] !== true) {
    header("Location: ../auth/login.php");
    exit();
}

// Cek apakah user punya paket PREMIUM
// Jika statusnya 'basic', lempar ke dashboard basic
if (isset($_SESSION['plan_type']) && $_SESSION['plan_type'] === 'basic') {
    header("Location: dashboard_basic.php");
    exit();
}

// Jika statusnya 'none' (belum bayar) atau expired, lempar ke plan
if (!isset($_SESSION['plan_type']) || $_SESSION['plan_type'] !== 'premium') {
    header("Location: plan.php");
    exit();
}

$userName = $_SESSION['user_name'] ?? 'Premium Member';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SagaHealth - Sehat Fisik & Mental</title>
    <link rel="icon" href="../assets/img/tittle.png" type="image/png">
    
    <link rel="stylesheet" href="../assets/style/styles.css">
    <link rel="stylesheet" href="style/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>
<body>

    <?php include 'partials/header.php'; ?>

    <div class="dashboard-container">
        
        <div class="welcome-section">
            <div class="welcome-text">
                <h1>Selamat Datang, <?php echo htmlspecialchars($userName); ?>! 👑</h1>
                <p>Akses kesehatan tanpa batas di genggaman Anda.</p>
            </div>
            <div class="plan-badge">
                <i class="fas fa-crown"></i> Premium Member
            </div>
        </div>

        <div class="dashboard-grid">

            <a href="skrining_kesehatan.php" class="feature-card">
                <div class="icon-wrapper bg-blue">
                    <i class="fas fa-heartbeat"></i>
                </div>
                <div class="card-content">
                    <h3>Skrining Kesehatan</h3>
                    <p>Cek risiko kesehatan fisik & mental Anda dengan kuesioner medis terstandar.</p>
                    <div class="action-link">Mulai Skrining <i class="fas fa-arrow-right"></i></div>
                </div>
            </a>

            <a href="chatbot.php" class="feature-card">
                <div class="icon-wrapper bg-teal">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="card-content">
                    <h3>AI Health Assistant</h3>
                    <p>Konsultasi 24/7 tanpa batas antrean dengan respon prioritas.</p>
                    <div class="action-link">Chat Sekarang <i class="fas fa-arrow-right"></i></div>
                </div>
            </a>

            <a href="mood_tracker.php" class="feature-card">
                <div class="icon-wrapper bg-pink">
                    <i class="fas fa-smile-beam"></i>
                </div>
                <div class="card-content">
                    <h3>Mood Tracker</h3>
                    <p>Analisis emosi harian, grafik mood mingguan, dan rekomendasi aktivitas.</p>
                    <div class="action-link">Catat Mood <i class="fas fa-arrow-right"></i></div>
                </div>
            </a>

            <a style="margin-top: 4rem; max-height: 300px;" href="#" class="feature-card">
                <div class="icon-wrapper bg-orange">
                    <i class="fas fa-file-medical-alt"></i>
                </div>
                <div class="card-content">
                    <h3>Laporan Lengkap</h3>
                    <p>Unduh riwayat medis dan hasil analisis kesehatan Anda dalam format PDF.</p>
                    <div class="action-link">Lihat Laporan <i class="fas fa-arrow-right"></i></div>
                </div>
            </a>

        </div>
    </div>

    <script>
        (function () {
            const isLoggedIn = sessionStorage.getItem('isLoggedIn') === 'true' || localStorage.getItem('isLoggedIn') === 'true';
            if (!isLoggedIn) {
                // window.location.href = '../auth/login.php'; 
            }
        })();
    </script>
    <br>
</body>
</html>