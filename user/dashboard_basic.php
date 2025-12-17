<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Dashboard - SagaHealth</title>
    <link rel="icon" href="../assets/img/tittle.png" type="image/png" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <!-- CSS gabungan: utama dashboard + auth (jika butuh style bersama) -->
    <link rel="stylesheet" href="../assets/style/auth.css" />
    <link rel="stylesheet" href="../user/style/dashboard.css" />
    <link rel="stylesheet" href="../assets/style/styles.css" />

    <script>
        /* GUARD LOGIN (eksekusi secepat mungkin)
           - cek sessionStorage atau localStorage
           - jika tidak valid -> bersihkan storage dan redirect ke login
        */
        (function () {
            const isLoggedIn = sessionStorage.getItem('isLoggedIn') === 'true' ||
                               localStorage.getItem('isLoggedIn') === 'true';
            const userId = sessionStorage.getItem('userId') || localStorage.getItem('userId');

            if (!isLoggedIn || !userId) {
                // clear only auth-related keys to be safe (but keeping as original behavior: clear all)
                sessionStorage.clear();
                localStorage.clear();
                window.location.replace('../auth/login.php');
            }
        })();
    </script>
</head>
<body class="page-dashboard">

    <!-- HEADER -->
    <?php include '../user/partials/header.php'; ?>

    <!-- MAIN CONTENT -->
    <main class="main-dashboard">
        <div class="container-wrapper">

            <!-- Carousel / Banner (dari incoming) -->
            <section class="wave-section">

                    <div class="banner-layout-single" id="poster-container">
                        <div class="poster-carousel card" id="main-poster-carousel" aria-roledescription="carousel">
                            <div class="carousel-track" id="carousel-track" aria-live="polite">
                                <div class="carousel-item">
                                    <img src="../assets/img/poster1.png" alt="Poster 1 Kemenkes" class="banner-image">
                                </div>
                                <div class="carousel-item">
                                    <img src="../assets/img/poster2.png" alt="Poster 2 Layanan Baru" class="banner-image">
                                </div>
                                <div class="carousel-item">
                                    <img src="../assets/img/poster3.png" alt="Poster 3 Promosi" class="banner-image">
                                </div>
                                <div class="carousel-item">
                                    <img src="../assets/img/poster4.png" alt="Poster 4 Promosi" class="banner-image">
                                </div>
                            </div>

                            <button class="carousel-button prev-button" aria-label="Previous Slide">
                                <i class="fas fa-chevron-left" aria-hidden="true"></i>
                            </button>
                            <button class="carousel-button next-button" aria-label="Next Slide">
                                <i class="fas fa-chevron-right" aria-hidden="true"></i>
                            </button>

                            <div class="carousel-indicators" id="carousel-indicators" role="tablist"></div>
                        </div>

                        <div class="single-poster-mobile" id="single-poster-mobile" aria-hidden="true">
                            <img src="../assets/img/poster1.png" alt="Poster Mobile" class="banner-image">
                        </div>
                    </div>
                </div>
            </section>
            <br>
            <!-- Welcome -->
            <section class="welcome-header">
                <h1 id="welcome-title">Halo, Memuat...</h1>
                <p id="welcome-sub">Selamat datang di dashboard kesehatan Anda. Pilih layanan di bawah ini untuk memulai.</p>
            </section>
            <br>

            <!-- GRID FITUR (gabungan) -->
            <section class="feature-grid" id="feature-grid">

                <!-- Basic features (dari incoming) -->
                <a href="skrining_kesehatan.php" class="feature-card accent-teal feature-basic" aria-label="Skrining Kesehatan">
                    <div class="card-icon"><i class="fas fa-file-medical-alt" aria-hidden="true"></i></div>
                    <h3>Skrining Kesehatan</h3>
                    <p>Isi formulir skrining mandiri untuk mengetahui risiko kesehatan Anda.</p>
                </a>

                <a href="anjuran.php" class="feature-card accent-green feature-basic" aria-label="Anjuran Kesehatan">
                    <div class="card-icon"><i class="fas fa-heartbeat" aria-hidden="true"></i></div>
                    <h3>Anjuran Kesehatan</h3>
                    <p>Rekomendasi personal untuk kesehatan Anda.</p>
                </a>

                <a href="riwayat.php" class="feature-card accent-gray feature-basic" aria-label="Riwayat Kesehatan">
                    <div class="card-icon"><i class="fas fa-notes-medical" aria-hidden="true"></i></div>
                    <h3>Riwayat Kesehatan</h3>
                    <p>Pantau dan simpan riwayat kesehatan.</p>
                </a>

                <a href="profile.php" class="feature-card accent-gray feature-basic" aria-label="Profil Saya">
                    <div class="card-icon"><i class="fas fa-id-card" aria-hidden="true"></i></div>
                    <h3>Profil Saya</h3>
                    <p>Lihat dan kelola data pribadi serta riwayat login Anda.</p>
                </a>

                <!-- Premium features (gabungan) -->
                <a href="indexmood.php" class="feature-card accent-blue feature-premium" data-feature="mood" aria-label="Mood Tracker">
                    <div class="card-icon"><i class="fas fa-smile-beam" aria-hidden="true"></i></div>
                    <h3>Mood Tracker</h3>
                    <p>Catat dan pantau suasana hati Anda dengan program 30 hari.</p>
                </a>


                <a href="support.php" class="feature-card accent-red feature-premium" aria-label="Prioritas Support">
                    <div class="card-icon"><i class="fas fa-headset" aria-hidden="true"></i></div>
                    <h3>Prioritas Support</h3>
                    <p>Bantuan cepat untuk pengguna premium.</p>
                </a>

                <a href="konten.php" class="feature-card accent-black feature-premium" aria-label="Konten Eksklusif">
                    <div class="card-icon"><i class="fas fa-lock-open" aria-hidden="true"></i></div>
                    <h3>Konten Eksklusif</h3>
                    <p>Akses materi kesehatan yang premium.</p>
                </a>

                <a href="chatbot.php" class="feature-card accent-pink" aria-label="SagaBot AI">
                    <div class="card-icon"><i class="fas fa-robot" aria-hidden="true"></i></div>
                    <h3>SagaBot AI</h3>
                    <p>Tanyakan apapun seputar kesehatan kepada asisten AI pribadi Anda.</p>
                </a>

            </section>

        </div>
    </main>

    <!-- FOOTER -->
    <?php include '../partials/footer.php'; ?>

    <!-- SCRIPTS: carousel, welcome, plan guard -->
    <script>
        // -------------------------
        // Carousel (simple, accessible)
        // -------------------------
        (function initCarousel() {
            const track = document.getElementById('carousel-track');
            const items = track ? Array.from(track.children) : [];
            const prevBtn = document.querySelector('.prev-button');
            const nextBtn = document.querySelector('.next-button');
            const indicators = document.getElementById('carousel-indicators');
            let index = 0;
            let autoplayTimer = null;

            if (!track || items.length === 0) return;

            function update() {
                // translate track
                const offset = -index * 100;
                track.style.transform = `translateX(${offset}%)`;
                // update indicators
                if (indicators) {
                    indicators.querySelectorAll('button').forEach((b, i) => {
                        b.classList.toggle('active', i === index);
                        b.setAttribute('aria-selected', String(i === index));
                    });
                }
                // update mobile poster src if present
                const mobilePoster = document.getElementById('single-poster-mobile');
                if (mobilePoster) {
                    const img = items[index].querySelector('img');
                    if (img) {
                        mobilePoster.querySelector('img').src = img.src;
                        mobilePoster.querySelector('img').alt = img.alt;
                    }
                }
            }

            function goTo(i) {
                index = (i + items.length) % items.length;
                update();
            }

            // create indicators
            if (indicators) {
                indicators.innerHTML = '';
                items.forEach((_, i) => {
                    const btn = document.createElement('button');
                    btn.setAttribute('role', 'tab');
                    btn.setAttribute('aria-label', `Slide ${i + 1}`);
                    btn.addEventListener('click', () => { goTo(i); resetAutoplay(); });
                    indicators.appendChild(btn);
                });
            }

            if (prevBtn) prevBtn.addEventListener('click', () => { goTo(index - 1); resetAutoplay(); });
            if (nextBtn) nextBtn.addEventListener('click', () => { goTo(index + 1); resetAutoplay(); });

            function startAutoplay() {
                autoplayTimer = setInterval(() => { goTo(index + 1); }, 5000);
            }
            function resetAutoplay() {
                if (autoplayTimer) clearInterval(autoplayTimer);
                startAutoplay();
            }

            // init styles
            track.style.display = 'flex';
            track.style.transition = 'transform 0.45s ease';
            items.forEach(item => {
                item.style.minWidth = '100%';
            });

            update();
            startAutoplay();
        })();

        // -------------------------
        // Set welcome name & show/hide premium features
        // -------------------------
        document.addEventListener('DOMContentLoaded', () => {
            const userName = sessionStorage.getItem('userName') || localStorage.getItem('userName');
            const welcomeTitle = document.getElementById('welcome-title');
            if (userName && welcomeTitle) {
                welcomeTitle.textContent = `Halo, ${userName}!`;
            } else if (welcomeTitle) {
                welcomeTitle.textContent = 'Halo, Pengguna!';
            }

            // Plan guard will be applied after loadSubscriptionAndGuard
            loadSubscriptionAndGuard();
        });

        // -------------------------
        // Subscription & Plan guard (ambil dari current)
        // -------------------------
        async function loadSubscriptionAndGuard() {
            const userId = sessionStorage.getItem('userId') || localStorage.getItem('userId');
            if (!userId) return;

            try {
                const res = await fetch(`../includes/subscriptions.php?action=get_status&userId=${encodeURIComponent(userId)}`, {
                    credentials: 'include'
                });
                if (!res.ok) throw new Error('Gagal mengambil status subscription');
                const data = await res.json();
                applyPlanGuard(data.plan_type || null);
            } catch (e) {
                console.error('subscriptions error', e);
                // Jika gagal, bersikap konservatif: sembunyikan fitur premium
                applyPlanGuard(null);
            }
        }

        function applyPlanGuard(planType) {
            const premiumFeatures = document.querySelectorAll('.feature-premium');
            const moodCard = document.querySelector('[data-feature="mood"]');

            if (!planType || planType === 'basic') {
                // sembunyikan atau kunci akses ke premium
                premiumFeatures.forEach(card => {
                    card.classList.add('locked');
                    // intercept clicks to redirect to upgrade page
                    card.addEventListener('click', (ev) => {
                        // jika kartu memiliki link asli, cegah lalu alihkan
                        ev.preventDefault();
                        window.location.href = 'plan.php?upgrade=premium';
                    });
                });

                if (moodCard) {
                    // Tambahan: beri tooltip / label
                    moodCard.setAttribute('title', 'Fitur premium — tingkatkan paket untuk membuka');
                }
            } else {
                // tampilkan penuh
                premiumFeatures.forEach(card => {
                    card.classList.remove('locked');
                });
            }
        }

        // -------------------------
        // Optional: fungsi logout (dari current)
        // -------------------------
        function logoutUser() {
            if (confirm('Apakah Anda yakin ingin keluar?')) {
                sessionStorage.clear();
                localStorage.clear();
                window.location.replace('../auth/login.php');
            }
        }
    </script>

</body>
</html>
