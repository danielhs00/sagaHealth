<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SagaHealth</title>
  <link rel="icon" href="../assets/img/tittle.png" type="image/png">
    <link rel="stylesheet" href="../assets/style/partials.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="font-sans bg-white">

<?php include '../partials/header.php'; ?>

    <main>

        <section class="about-hero">
            <div class="container-wrapper text-center">
                <h1>Tentang Kami</h1>
                <p class="about-subtitle">
                    Misi kami adalah mendemokratisasi akses kesehatan bagi seluruh masyarakat Indonesia melalui teknologi.
                </p>
                <img src="../assets/img/App.png" alt="Tim Kami" class="about-hero-image">
            </div>
        </section>

        <section class="vision-mission-section">
            <div class="container-wrapper">
                <div class="vision-mission-grid">
                    <div class="vm-card">
                        <h3><i class="fas fa-eye"></i> Visi Kami</h3>
                        <p>Menjadi platform kesehatan digital terdepan dan terpercaya yang memberikan dampak positif berkelanjutan bagi kualitas hidup masyarakat.</p>
                    </div>
                    <div class="vm-card">
                        <h3><i class="fas fa-rocket"></i> Misi Kami</h3>
                        <ul>
                            <li>Memberikan akses layanan konsultasi dokter yang cepat, mudah, dan terjangkau.</li>
                            <li>Menyediakan informasi kesehatan yang akurat dan terverifikasi.</li>
                            <li>Membangun ekosistem digital yang menghubungkan pasien dengan seluruh kebutuhan kesehatannya.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section class="founder-section">
            <div class="container-wrapper">
                <h2 class="text-center">Para Founder Kami</h2>
                <p class="text-center section-subtitle">
                    Kami adalah tim yang berdedikasi untuk mewujudkan visi besar ini.
                </p>
                
                <div class="founder-grid">
                    
                    <div class="founder-card">
                        <img src="../assets/img/Septi.png" alt="Foto Founder 1">
                        <div class="founder-card-info">
                            <h4>Septiawan Hadi Prasetyo</h4>
                            <span>Project Manager and AI Engine Specialist</span>
                        </div>
                    </div>

                    <div class="founder-card">
                        <img src="../assets/img/Restu.png" alt="Foto Founder 2">
                        <div class="founder-card-info">
                            <h4>Restu Utami</h4>
                            <span>Quality Assurance Specialist</span>
                        </div>
                    </div>
                    
                    <div class="founder-card">
                        <img src="../assets/img/Daniel.png" alt="Foto Founder 3">
                        <div class="founder-card-info">
                            <h4>Daniel Hulio Saptianus</h4>
                            <span>Backend Developer</span>
                        </div>
                    </div>

                    <div class="founder-card">
                        <img src="../assets/img/Mirza.png" alt="Foto Founder 4">
                        <div class="founder-card-info">
                            <h4>Mirza Arya Herdiansyah</h4>
                            <span>Front End Developer</span>
                        </div>
                    </div>

                    <div class="founder-card">
                        <img src="../assets/img/Zein.png" alt="Foto Founder 5">
                        <div class="founder-card-info">
                            <h4>Zein Naufal Jasyr</h4>
                            <span>Backend</span>
                        </div>
                    </div>

                    <div class="founder-card">
                        <img src="../assets/img/Jilan.png" alt="Foto Founder 6">
                        <div class="founder-card-info">
                            <h4>Muhammad Jilan Satria</h4>
                            <span>Quality Assurance Specialist</span>
                        </div>
                    </div>
                    
                </div>
            </div>
        </section>

    </main>
    <br>

<?php include '../partials/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const menuToggle = document.getElementById('mobile-menu-toggle');
            const navMenu = document.getElementById('main-nav-menu');

            if (menuToggle && navMenu) {
                menuToggle.addEventListener('click', () => {
                    navMenu.classList.toggle('active');
                });
            }
        });
    </script>
    
</body>
</html>