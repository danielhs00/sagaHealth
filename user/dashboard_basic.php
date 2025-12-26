<?php
session_start();

// --- 1. SECURITY CHECK (SERVER SIDE) ---
// Cek Login
if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] !== true) {
    header("Location: ../auth/login.php");
    exit();
}

// Cek Paket
// Jika user sebenarnya Premium, arahkan ke dashboard premium
if (isset($_SESSION['plan_type']) && $_SESSION['plan_type'] === 'premium') {
    header("Location: dashboard_premium.php");
    exit();
}

// Jika belum punya paket sama sekali, arahkan ke pemilihan paket
if (!isset($_SESSION['plan_type']) || $_SESSION['plan_type'] !== 'basic') {
    header("Location: plan.php");
    exit();
}

$userName = $_SESSION['user_name'] ?? 'Sahabat Sehat';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Basic - SagaHealth</title>
    
    <link rel="stylesheet" href="../assets/style/styles.css"> <link rel="stylesheet" href="style/dashboard.css"> <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <?php include 'partials/header.php'; ?>

    <div class="dashboard-container">
        
        <div class="welcome-section">
            <div class="welcome-text">
                <h1>Halo, <?php echo htmlspecialchars($userName); ?>! 👋</h1>
                <p>Mulai perjalanan sehatmu hari ini.</p>
            </div>
            <div class="plan-badge">
                <i class="fas fa-leaf"></i> Basic Plan
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
                    <p>Tanya jawab keluhan ringan dan tips kesehatan dengan asisten cerdas kami.</p>
                    <div class="action-link">Chat Sekarang <i class="fas fa-arrow-right"></i></div>
                </div>
            </a>

            <div class="feature-card locked" onclick="window.location.href='plan.php'">
                <div class="icon-wrapper bg-pink">
                    <i class="fas fa-smile-beam"></i>
                </div>
                <div class="card-content">
                    <h3>Mood Tracker</h3>
                    <p>Pantau suasana hati harian dan dapatkan analisis emosional mendalam.</p>
                    <div class="lock-overlay">
                        <i class="fas fa-lock lock-icon"></i>
                        <span class="upgrade-text">Upgrade ke Premium</span>
                    </div>
                </div>
            </div>

            <div class="feature-card locked" onclick="window.location.href='plan.php'">
                <div class="icon-wrapper bg-orange">
                    <i class="fas fa-file-medical-alt"></i>
                </div>
                <div class="card-content">
                    <h3>Laporan Lengkap</h3>
                    <p>Unduh laporan kesehatan bulanan dan grafik perkembangan kondisi Anda.</p>
                    <div class="lock-overlay">
                        <i class="fas fa-lock lock-icon"></i>
                        <span class="upgrade-text">Upgrade ke Premium</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        (function () {
            const isLoggedIn = sessionStorage.getItem('isLoggedIn') === 'true' || localStorage.getItem('isLoggedIn') === 'true';
            if (!isLoggedIn) {
                // window.location.href = '../auth/login.php'; // Aktifkan jika sudah live
            }
        })();
    </script>
</body>
</html>