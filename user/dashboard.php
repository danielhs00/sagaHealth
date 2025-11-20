<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SagaHealth</title>
    <link rel="icon" href="../assets/img/tittle.png" type="image/png">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- CSS Dashboard Baru -->
    <link rel="stylesheet" href="../user/style/dashboard.css">

    <a href="indexmood.php" class="feature-card accent-blue" data-feature="mood">
    <div class="card-icon"><i class="fas fa-smile-beam"></i></div>
    <h3>Mood Tracker</h3>
    <p>Catat dan pantau suasana hati Anda dengan program 30 hari.</p>
    </a>
    
    <!-- GUARD SCRIPT (PENJAGA HALAMAN) -->
    <script>
        // Cek status login dari sessionStorage ATAU localStorage
        const isLoggedIn = sessionStorage.getItem('isLoggedIn') === 'true' || 
                           localStorage.getItem('isLoggedIn') === 'true';
        
        // Ambil userId
        const userId = sessionStorage.getItem('userId') || localStorage.getItem('userId');

        if (!isLoggedIn || !userId) {
            // Jika tidak login, bersihkan semua data & tendang ke halaman login
            sessionStorage.clear();
            localStorage.clear();
            // Arahkan ke folder auth (sesuai struktur Anda)
            window.location.replace('../auth/login.php');
        }
    </script>
</head>
<body class="page-dashboard">

    <?php include '../user/partials/header.php'; ?>
    <!-- Konten Utama Dashboard -->
    <main class="main-dashboard">
        <div class="container-wrapper">
            
            <!-- Pesan Selamat Datang -->
            <section class="welcome-header">
                <h1 id="welcome-title">Halo, Memuat...</h1>
                <p>Selamat datang di dashboard kesehatan Anda. Pilih layanan di bawah ini untuk memulai.</p>
            </section>

            <!-- Grid Fitur Utama -->
            <section class="feature-grid">
                
                <!-- Kartu Chatbot -->
                <a href="chatbot.php" class="feature-card accent-pink">
                    <div class="card-icon"><i class="fas fa-robot"></i></div>
                    <h3>SagaBot AI</h3>
                    <p>Tanyakan apapun seputar kesehatan kepada asisten AI pribadi Anda.</p>
                </a>
                
                <!-- Kartu Mood Tracker -->
                <a href="indexmood.php" class="feature-card accent-blue">
                    <div class="card-icon"><i class="fas fa-smile-beam"></i></div>
                    <h3>Mood Tracker</h3>
                    <p>Catat dan pantau suasana hati Anda dengan program 30 hari.</p>
                </a>

                <!-- Kartu Skrining (dari PDF) -->
                <a href="skrining_kesehatan.php" class="feature-card accent-teal">
                    <div class="card-icon"><i class="fas fa-file-medical-alt"></i></div>
                    <h3>Skrining Kesehatan</h3>
                    <p>Isi formulir skrining mandiri untuk mengetahui risiko kesehatan Anda.</p>
                </a>
                
                <!-- Kartu Profil -->
                <a href="profile.php" class="feature-card accent-gray">
                    <div class="card-icon"><i class="fas fa-id-card"></i></div>
                    <h3>Profil Saya</h3>
                    <p>Lihat dan kelola data pribadi serta riwayat login Anda.</p>
                </a>

            </section>
        </div>
    </main>

    <script>
        // Fungsi Logout
        function logoutUser() {
            if (confirm('Apakah Anda yakin ingin keluar?')) {
                sessionStorage.clear();
                localStorage.clear();
                window.location.replace('../auth/login.php');
            }
        }

        // Mengisi Nama Pengguna
        document.addEventListener('DOMContentLoaded', () => {
            // Ambil nama dari storage
            const userName = sessionStorage.getItem('userName') || localStorage.getItem('userName');
            
            if (userName) {
                // Set nama di Header
                document.getElementById('user-name-display').textContent = userName;
                // Set nama di Judul Selamat Datang
                document.getElementById('welcome-title').textContent = `Halo, ${userName}!`;
            } else {
                // Fallback jika nama tidak ada (seharusnya tidak terjadi karena guard script)
                document.getElementById('user-name-display').textContent = 'Pengguna';
                document.getElementById('welcome-title').textContent = 'Selamat Datang!';
            }

            loadSubscriptionAndGuard();
        });

        
async function loadSubscriptionAndGuard() {
    const userId = sessionStorage.getItem('userId') || localStorage.getItem('userId');
    if (!userId) return;

    try {
        const res = await fetch(`../includes/subscriptions.php?action=get_status&userId=${encodeURIComponent(userId)}`);
        const data = await res.json();
        applyPlanGuard(data.plan_type || null);
    } catch (e) {
        console.error(e);
        applyPlanGuard(null);
    }
}

function applyPlanGuard(planType) {
    // Mood Tracker & Tantangan 30 hari = Premium
    const moodCard = document.querySelector('[data-feature="mood"]');
    // kalau nanti ada card khusus tantangan 30 hari, bisa atur juga

    if (!planType || planType === 'basic') {
        if (moodCard) {
            moodCard.classList.add('locked');
            moodCard.addEventListener('click', (e) => {
                e.preventDefault();
                window.location.href = 'plan.php?upgrade=premium';
            });
        }
    }
}
    </script>
</body>
</html>