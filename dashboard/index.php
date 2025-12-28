<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SagaHealth - Sehat Fisik & Mental</title>
    <link rel="icon" href="assets/img/tittle.png" type="image/png">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style/index.css">
    
</head>
<body>

<?php include '../partials/header.php'; ?>

    <section class="hero">
        <div class="hero-text" data-aos="fade-right">
            <h1>Kesehatan Fisik & Mental dalam Satu Genggaman</h1>
            <p>SagaHealth membantu Anda memantau kondisi kesehatan, mengelola stres, dan berkonsultasi dengan AI cerdas kapan saja dan di mana saja.</p>
            <a href="../auth/register.php" class="cta-btn" style="background: var(--white); color: var(--primary);">Mulai Sekarang - Disini</a>
        </div>
        <div class="hero-img" data-aos="fade-left">
            <img src="../assets/img/MASKOT.png" alt="Ilustrasi Kesehatan Digital">
            <i class="fas fa-heart" style="font-size: 5rem; color: #F59E0B; position: absolute; top: 20%; right: 20%; animation: float 4s infinite;"></i>
            <i class="fas fa-brain" style="font-size: 6rem; color: #028C8B; position: absolute; bottom: 20%; left: 10%; animation: float 5s infinite reverse;"></i>
        </div>
    </section>

    <section id="fitur" class="features">
        <div class="section-title" data-aos="fade-up">
            <h2>Layanan Unggulan</h2>
            <p>Teknologi canggih untuk mendukung kesejahteraan hidup Anda.</p>
        </div>

        <div class="feature-grid">
            <div class="feature-card" data-aos="fade-up" data-aos-delay="100">
                <div class="icon-box"><i class="fas fa-stethoscope"></i></div>
                <h3>Skrining Kesehatan</h3>
                <p>Analisis risiko penyakit fisik seperti Diabetes dan Hipertensi dengan algoritma cerdas berdasarkan input medis Anda.</p>
            </div>

            <div class="feature-card" data-aos="fade-up" data-aos-delay="200">
                <div class="icon-box"><i class="fas fa-robot"></i></div>
                <h3>SagaBot AI</h3>
                <p>Asisten kesehatan virtual 24/7 yang siap menjawab pertanyaan seputar gejala, tips diet, dan pola hidup sehat.</p>
            </div>

            <div class="feature-card" data-aos="fade-up" data-aos-delay="300">
                <div class="icon-box"><i class="fas fa-smile-beam"></i></div>
                <h3>Mood Tracker</h3>
                <p>Pantau kesehatan mental Anda setiap hari. Catat emosi, dapatkan grafik analisis, dan tips regulasi emosi.</p>
            </div>
        </div>
    </section>

    <section class="features" style="background: #F9FAFB;">
        <div class="section-title">
            <h2>Mengapa SagaHealth?</h2>
        </div>
        <div class="feature-grid">
            <div style="display: flex; gap: 20px; align-items: start;" data-aos="fade-right">
                <i class="fas fa-check-circle" style="font-size: 2rem; color: var(--secondary);"></i>
                <div>
                    <h4>Terintegrasi</h4>
                    <p style="color: #666;">Satu aplikasi untuk kesehatan fisik dan mental tanpa perlu pindah platform.</p>
                </div>
            </div>
            <div style="display: flex; gap: 20px; align-items: start;" data-aos="fade-right" data-aos-delay="100">
                <i class="fas fa-check-circle" style="font-size: 2rem; color: var(--secondary);"></i>
                <div>
                    <h4>Berbasis Data</h4>
                    <p style="color: #666;">Keputusan kesehatan yang lebih baik dengan analisis data riwayat Anda.</p>
                </div>
            </div>
            <div style="display: flex; gap: 20px; align-items: start;" data-aos="fade-right" data-aos-delay="200">
                <i class="fas fa-check-circle" style="font-size: 2rem; color: var(--secondary);"></i>
                <div>
                    <h4>Privasi Aman</h4>
                    <p style="color: #666;">Data kesehatan Anda dienkripsi dan dijaga kerahasiaannya.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="cta-section" data-aos="zoom-in">
        <h2>Siap Untuk Hidup Lebih Sehat?</h2>
        <p>Bergabunglah dengan ribuan pengguna lainnya hari ini.</p>
        <a href="../auth/register.php" class="cta-btn">Daftar Sekarang</a>
    </section>

<?php include '../partials/footer.php'; ?>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 1000,
            once: true
        });
    </script>
</body>
</html>