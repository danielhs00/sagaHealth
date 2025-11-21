<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SagaHealth</title>
    <link rel="icon" href="../assets/img/tittle.png" type="image/png">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- CSS utama (auth.css) -->
    <link rel="stylesheet" href="../assets/style/auth.css">
    <link rel="stylesheet" href="../assets/style/styles.css">

    <script>
        // GUARD LOGIN
        const isLoggedIn = sessionStorage.getItem('isLoggedIn') === 'true' || 
                           localStorage.getItem('isLoggedIn') === 'true';

        const userId = sessionStorage.getItem('userId') || localStorage.getItem('userId');

        if (!isLoggedIn || !userId) {
            sessionStorage.clear();
            localStorage.clear();
            window.location.replace('../auth/login.php');
        }
    </script>
</head>

<body class="page-dashboard">

    <?php include '../user/partials/header.php'; ?>

    <main class="main-dashboard">
        <div class="container-wrapper">
                <section class="wave-section">
    <div class="container-wrapper">
        
        <div class="banner-layout-single" id="poster-container">
            
            <div class="poster-carousel card" id="main-poster-carousel">
                
                <div class="carousel-track">
                    
                    <div class="carousel-item">
                        <img src="../assets/img/poster1.png" 
                             alt="Poster 1 Kemenkes" 
                             class="banner-image">
                    </div>
                    
                    <div class="carousel-item">
                        <img src="../assets/img/poster2.png" 
                             alt="Poster 2 Layanan Baru" 
                             class="banner-image">
                    </div>
                    
                    <div class="carousel-item">
                        <img src="../assets/img/poster3.png" 
                             alt="Poster 3 Promosi" 
                             class="banner-image">
                    </div>
                     <div class="carousel-item">
                        <img src="../assets/img/poster4.png" 
                             alt="Poster 3 Promosi" 
                             class="banner-image">
                    </div>
                </div>
                
                <button class="carousel-button prev-button" aria-label="Previous Slide">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="carousel-button next-button" aria-label="Next Slide">
                    <i class="fas fa-chevron-right"></i>
                </button>

                <div class="carousel-indicators"></div>
            </div>
            
            <div class="single-poster-mobile" id="single-poster-mobile">
                <img src="../assets/img/poster1.png" alt="Poster Mobile" class="banner-image">
            </div>
        </div>
    </div>
</section>

            <!-- Welcome -->
            <section class="welcome-header">
                <h1 id="welcome-title">Halo, Memuat...</h1>
                <p>Selamat datang di dashboard kesehatan Anda.</p>
            </section>

            <!-- GRID FITUR -->
            <section class="feature-grid">

                <!-- ================= BASIC FEATURES ================= -->

                <!-- SCREENING FISIK -->
                <a href="skrining_kesehatan.php" class="feature-card accent-teal feature-basic">
                    <div class="card-icon"><i class="fas fa-file-medical-alt"></i></div>
                    <h3>Screening Fisik</h3>
                    <p>Evaluasi menyeluruh kondisi fisik Anda.</p>
                </a>

                <!-- ANJURAN KESEHATAN -->
                <a href="anjuran.php" class="feature-card accent-green feature-basic">
                    <div class="card-icon"><i class="fas fa-heartbeat"></i></div>
                    <h3>Anjuran Kesehatan</h3>
                    <p>Rekomendasi personal untuk kesehatan Anda.</p>
                </a>

                <!-- RIWAYAT KESEHATAN -->
                <a href="riwayat.php" class="feature-card accent-gray feature-basic">
                    <div class="card-icon"><i class="fas fa-notes-medical"></i></div>
                    <h3>Riwayat Kesehatan</h3>
                    <p>Pantau dan simpan riwayat kesehatan.</p>
                </a>

                <!-- ================= PREMIUM FEATURES ================= -->

                <!-- SCREENING MOOD -->
                <a href="indexmood.php" class="feature-card accent-blue feature-premium">
                    <div class="card-icon"><i class="fas fa-smile-beam"></i></div>
                    <h3>Screening Mood</h3>
                    <p>Analisis suasana hati Anda setiap hari.</p>
                </a>

                <!-- TANTANGAN 30 HARI -->
                <a href="tantangan.php" class="feature-card accent-orange feature-premium">
                    <div class="card-icon"><i class="fas fa-trophy"></i></div>
                    <h3>Tantangan 30 Hari</h3>
                    <p>Bangun kebiasaan sehat dengan target harian.</p>
                </a>

                <!-- PROGRESS TRACKING -->
                <a href="progress.php" class="feature-card accent-purple feature-premium">
                    <div class="card-icon"><i class="fas fa-chart-line"></i></div>
                    <h3>Progress Tracking</h3>
                    <p>Lihat perkembangan kesehatan Anda secara visual.</p>
                </a>

                <!-- PRIORITAS SUPPORT -->
                <a href="support.php" class="feature-card accent-red feature-premium">
                    <div class="card-icon"><i class="fas fa-headset"></i></div>
                    <h3>Prioritas Support</h3>
                    <p>Bantuan cepat untuk pengguna premium.</p>
                </a>

                <!-- KONTEN EKSKLUSIF -->
                <a href="konten.php" class="feature-card accent-black feature-premium">
                    <div class="card-icon"><i class="fas fa-lock-open"></i></div>
                    <h3>Konten Eksklusif</h3>
                    <p>Akses materi kesehatan yang premium.</p>
                </a>

            </section>

        </div>
    </main>

    <?php include '../partials/footer.php'; ?>

    <script>
        // SET NAMA USER
        document.addEventListener('DOMContentLoaded', () => {
            const userName = localStorage.getItem('userName') || sessionStorage.getItem('userName');
            document.getElementById('welcome-title').textContent = "Halo, " + userName + "!";
        });

        // SHOW / HIDE FITUR BERDASARKAN PLAN
        document.addEventListener('DOMContentLoaded', () => {
            const userPlan = localStorage.getItem('userPlan') || "premium";

            const basicFeatures = document.querySelectorAll('.feature-basic');
            const premiumFeatures = document.querySelectorAll('.feature-premium');

            if (userPlan === "basic") {
                premiumFeatures.forEach(e => e.style.display = "none");
            } else {
                premiumFeatures.forEach(e => e.style.display = "flex");
            }
        });
    </script>

</body>
</html>
