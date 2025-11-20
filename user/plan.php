<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>SagaHealth</title>
    <link rel="icon" href="../assets/img/tittle.png" type="image/png">
    <link rel="stylesheet" href="../assets/style/auth.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <!-- Loading Screen -->
    <div id="page-loader" class="page-loader">
        <div class="loader-content">
            <div class="spinner-large"></div>
            <p>Memuat halaman...</p>
        </div>
    </div>

    <!-- Header Navigation -->
    <header class="plan-header-nav">
        <div class="container-wrapper nav-content">
            <div class="nav-left">
                <img src="../assets/img/logo.png" alt="SagaHealth" class="logo" onerror="this.src='https://placehold.co/120x40/014C63/ffffff?text=SagaHealth'">
            </div>
            <div class="nav-right">
                <div class="user-info" id="user-display">
                <a href="../user/profile.php" class="btn-profile" title="Profil Saya">
                  <i class="fas fa-user-circle"></i>
                    <span id="../user/profile.php"></span>
                </div>
                <button class="btn-logout" id="logout-btn" title="Keluar">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Keluar</span>
                </button>
            </div>
        </div>
    </header>

    <main>
        <!-- Hero Section -->
        <section class="plan-hero">
            <div class="container-wrapper">
                <div class="hero-content">
                    <div class="welcome-badge">
                        <i class="fas fa-crown"></i>
                        <span>Langkah Terakhir</span>
                    </div>
                    <h1 id="welcome-message">Selamat Datang!</h1>
                    <p class="hero-subtitle">
                        Pilih paket langganan yang sesuai dengan kebutuhan kesehatan Anda.
                        <br>Semua paket dilengkapi dengan jaminan kepuasan 100%.
                    </p>
                    <div class="hero-stats">
                        <div class="stat-item">
                            <i class="fas fa-users"></i>
                            <div>
                                <strong>10,000+</strong>
                                <span>Pengguna Aktif</span>
                            </div>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-star"></i>
                            <div>
                                <strong>4.9/5</strong>
                                <span>Rating Pengguna</span>
                            </div>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-heartbeat"></i>
                            <div>
                                <strong>50,000+</strong>
                                <span>Screening Selesai</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Plans Section -->
        <section class="plan-section">
            <div class="container-wrapper">
                
                <!-- Toggle Annual/Monthly -->
                <div class="billing-toggle">
                    <span class="toggle-label">Bulanan</span>
                    <label class="switch">
                        <input type="checkbox" id="billing-period">
                        <span class="slider"></span>
                    </label>
                    <span class="toggle-label">Tahunan <span class="save-badge">Hemat 20%</span></span>
                </div>

                <div class="plan-grid-two">
                    
                    <!-- Plan Basic -->
                    <div class="plan-card" data-plan="basic">
                        <div class="plan-card-header">
                            <div class="plan-icon">
                                <i class="fas fa-heartbeat"></i>
                            </div>
                            <h3>Basic</h3>
                            <p class="plan-description">Untuk memulai perjalanan kesehatan Anda</p>
                            <div class="plan-price">
                                <span class="currency">Rp</span>
                                <span class="amount" data-monthly="50.000" data-yearly="480.000">50.000</span>
                                <span class="period">/bulan</span>
                            </div>
                            <p class="price-note">Atau <span class="yearly-price">Rp 480.000/tahun</span></p>
                        </div>
                        <ul class="plan-features">
                            <li>
                                <i class="fas fa-check-circle"></i> 
                                <div>
                                    <strong>Screening Fisik</strong>
                                    <span class="feature-desc">Evaluasi kondisi kesehatan fisik Anda secara menyeluruh</span>
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i> 
                                <div>
                                    <strong>Anjuran Kesehatan</strong>
                                    <span class="feature-desc">Rekomendasi personal untuk meningkatkan kesehatan Anda</span>
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i> 
                                <div>
                                    <strong>Chatbot 24/7</strong>
                                    <span class="feature-desc">Asisten kesehatan virtual siap membantu kapan saja</span>
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i> 
                                <div>
                                    <strong>Riwayat Kesehatan</strong>
                                    <span class="feature-desc">Simpan dan akses data kesehatan Anda</span>
                                </div>
                            </li>
                            <li class="disabled">
                                <i class="fas fa-times-circle"></i> 
                                <div>
                                    <strong>Screening Mood</strong>
                                    <span class="feature-desc">Tersedia di paket Premium</span>
                                </div>
                            </li>
                            <li class="disabled">
                                <i class="fas fa-times-circle"></i> 
                                <div>
                                    <strong>Tantangan 30 Hari</strong>
                                    <span class="feature-desc">Tersedia di paket Premium</span>
                                </div>
                            </li>
                        </ul>
                        <button class="btn-plan" onclick="selectPlan('Basic', '50.000', 'basic')">
                            <span>Pilih Basic</span>
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>

                    <!-- Plan Premium (Popular) -->
                    <div class="plan-card premium" data-plan="premium">
                        <div class="popular-badge">
                            <i class="fas fa-fire"></i>
                            <span>Paling Diminati</span>
                        </div>
                        <div class="plan-card-header">
                            <div class="plan-icon premium-icon">
                                <i class="fas fa-crown"></i>
                            </div>
                            <h3>Premium</h3>
                            <p class="plan-description">Solusi lengkap untuk kesehatan fisik & mental</p>
                            <div class="plan-price">
                                <span class="currency">Rp</span>
                                <span class="amount" data-monthly="100.000" data-yearly="960.000">100.000</span>
                                <span class="period">/bulan</span>
                            </div>
                            <p class="price-note">Atau <span class="yearly-price">Rp 960.000/tahun</span></p>
                        </div>
                        <ul class="plan-features">
                            <li>
                                <i class="fas fa-check-circle"></i> 
                                <div>
                                    <strong>Semua Fitur Basic</strong>
                                    <span class="feature-desc">Screening Fisik, Anjuran, Chatbot & Riwayat</span>
                                </div>
                            </li>
                            <li class="featured">
                                <i class="fas fa-check-circle"></i> 
                                <div>
                                    <strong>Screening Mood</strong>
                                    <span class="feature-desc">Analisis kesehatan mental & emotional tracking</span>
                                </div>
                            </li>
                            <li class="featured">
                                <i class="fas fa-check-circle"></i> 
                                <div>
                                    <strong>Tantangan 30 Hari</strong>
                                    <span class="feature-desc">=gram terstruktur untuk membangun kebiasaan sehat</span>
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i> 
                                <div>
                                    <strong>Progress Tracking</strong>
                                    <span class="feature-desc">Pantau perkembangan kesehatan Anda secara detail</span>
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i> 
                                <div>
                                    <strong>Prioritas Support</strong>
                                    <span class="feature-desc">Respon lebih cepat dari tim support kami</span>
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i> 
                                <div>
                                    <strong>Konten Eksklusif</strong>
                                    <span class="feature-desc">Akses artikel & video kesehatan premium</span>
                                </div>
                            </li>
                        </ul>
                        <button class="btn-plan premium" onclick="selectPlan('Premium', '100.000', 'premium')">
                            <span>Pilih Premium</span>
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>

                </div>

                <!-- Feature Highlights -->
                <div class="feature-highlights">
                    <h2>Apa yang Anda Dapatkan</h2>
                    <div class="highlights-grid">
                        <div class="highlight-card">
                            <div class="highlight-icon">
                                <i class="fas fa-clipboard-check"></i>
                            </div>
                            <h3>Screening Fisik</h3>
                            <p>Evaluasi komprehensif meliputi BMI, tekanan darah, riwayat penyakit, dan indikator kesehatan lainnya untuk memberikan gambaran lengkap kondisi fisik Anda.</p>
                        </div>
                        <div class="highlight-card">
                            <div class="highlight-icon">
                                <i class="fas fa-brain"></i>
                            </div>
                            <h3>Screening Mood</h3>
                            <p>Analisis kesehatan mental melalui kuesioner tervalidasi, tracking mood harian, dan insight tentang pola emosional Anda. <span class="badge-premium">Premium</span></p>
                        </div>
                        <div class="highlight-card">
                            <div class="highlight-icon">
                                <i class="fas fa-lightbulb"></i>
                            </div>
                            <h3>Anjuran Kesehatan</h3>
                            <p>Rekomendasi personal berdasarkan hasil screening, termasuk tips nutrisi, olahraga, dan gaya hidup sehat yang disesuaikan dengan kondisi Anda.</p>
                        </div>
                        <div class="highlight-card">
                            <div class="highlight-icon">
                                <i class="fas fa-robot"></i>
                            </div>
                            <h3>Chatbot AI</h3>
                            <p>Asisten kesehatan virtual yang siap menjawab pertanyaan, memberikan informasi medis dasar, dan mengingatkan Anda tentang kebiasaan sehat 24/7.</p>
                        </div>
                        <div class="highlight-card">
                            <div class="highlight-icon">
                                <i class="fas fa-trophy"></i>
                            </div>
                            <h3>Tantangan 30 Hari</h3>
                            <p>Program terstruktur dengan target harian untuk membangun kebiasaan sehat, dilengkapi progress tracking dan motivasi untuk mencapai tujuan kesehatan Anda. <span class="badge-premium">Premium</span></p>
                        </div>
                        <div class="highlight-card">
                            <div class="highlight-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h3>Progress Tracking</h3>
                            <p>Dashboard interaktif untuk memantau perkembangan kesehatan, visualisasi data, dan laporan berkala tentang pencapaian target kesehatan Anda.</p>
                        </div>
                    </div>
                </div>

                <!-- Comparison Table -->
                <div class="comparison-section">
                    <h2>Bandingkan Paket</h2>
                    <div class="comparison-toggle">
                        <button class="comparison-btn" id="show-comparison">
                            <span>Lihat Perbandingan Detail</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>
                    <div class="comparison-table-wrapper" id="comparison-table" style="display: none;">
                        <table class="comparison-table">
                            <thead>
                                <tr>
                                    <th>Fitur</th>
                                    <th>Basic</th>
                                    <th class="premium-col">Premium</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Screening Fisik</strong></td>
                                    <td><i class="fas fa-check"></i> Tersedia</td>
                                    <td><i class="fas fa-check"></i> Tersedia</td>
                                </tr>
                                <tr>
                                    <td><strong>Anjuran Kesehatan</strong></td>
                                    <td><i class="fas fa-check"></i> Personal</td>
                                    <td><i class="fas fa-check"></i> Personal + Lanjutan</td>
                                </tr>
                                <tr>
                                    <td><strong>Chatbot 24/7</strong></td>
                                    <td><i class="fas fa-check"></i> Dasar</td>
                                    <td><i class="fas fa-check"></i> Advanced AI</td>
                                </tr>
                                <tr>
                                    <td><strong>Riwayat Kesehatan</strong></td>
                                    <td><i class="fas fa-check"></i> 3 Bulan</td>
                                    <td><i class="fas fa-check"></i> Unlimited</td>
                                </tr>
                                <tr class="highlight-row">
                                    <td><strong>Screening Mood</strong></td>
                                    <td><i class="fas fa-times"></i></td>
                                    <td><i class="fas fa-check"></i> <strong>Tersedia</strong></td>
                                </tr>
                                <tr class="highlight-row">
                                    <td><strong>Tantangan 30 Hari</strong></td>
                                    <td><i class="fas fa-times"></i></td>
                                    <td><i class="fas fa-check"></i> <strong>Tersedia</strong></td>
                                </tr>
                                <tr>
                                    <td><strong>Progress Tracking</strong></td>
                                    <td>Basic</td>
                                    <td>Advanced Dashboard</td>
                                </tr>
                                <tr>
                                    <td><strong>Konten Eksklusif</strong></td>
                                    <td><i class="fas fa-times"></i></td>
                                    <td><i class="fas fa-check"></i></td>
                                </tr>
                                <tr>
                                    <td><strong>Prioritas Support</strong></td>
                                    <td><i class="fas fa-times"></i></td>
                                    <td><i class="fas fa-check"></i></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="plan-footer">
        <div class="container-wrapper">
            <p>&copy; 2024 SagaHealth. Semua hak dilindungi.</p>
            <div class="footer-links">
                <a href="#">Syarat & Ketentuan</a>
                <a href="#">Kebijakan Privasi</a>
                <a href="#">Bantuan</a>
            </div>
        </div>
    </footer>

    <script src="../assets/js/plan.js"></script>
</body>
</html>