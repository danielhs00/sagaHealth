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

    <?php include '../user/partials/header.php'; ?>

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

                <div class="plan-grid">

    <!-- PREMIUM (mirip Hostinger Premium) -->
    <div class="plan-card">
        <div class="plan-card-header">
            <span class="discount-badge">85% OFF</span>
            <h3>Basic</h3>
            <p class="plan-description">Untuk memulai perjalanan kesehatan anda</p>
            <div class="plan-price">
                <span class="currency">Rp</span>
                <span class="amount">50.000</span>
                <span class="period">/bulan</span>
            </div>
            <p class="price-note">Get 48 months for US$93.60</p>
        </div>

        <ul class="plan-features">
            <li><i class="fa fa-check"></i> Screning Fisik</li>
            <li><i class="fa fa-check"></i> Anjuran Kesahatan</li>
            <li><i class="fa fa-check"></i> Riwayat Kesehatan </li>
        </ul>

        <button class="btn-plan">Pilih Paket</button>
    </div>

    <!-- BUSINESS — Most Popular -->
    <div class="plan-card">
        <div class="plan-card-header">
            <span class="discount-badge">85% OFF</span>
            <h3>Premium</h3>
            <p class="plan-description">Untuk memulai perjalanan kesehatan anda</p>
            <div class="plan-price">
                <span class="currency">Rp</span>
                <span class="amount">100.000</span>
                <span class="period">/bulan</span>
            </div>
            <p class="price-note">Get 48 months for US$93.60</p>
        </div>

        <ul class="plan-features">
            <li><i class="fa fa-check"></i> Semua Fitur Basic</li>
            <li><i class="fa fa-check"></i> Screening Mood</li>
            <li><i class="fa fa-check"></i> Tantangan 30 Hari</li>
            <li><i class="fa fa-check"></i> Progress Tracking</li>
            <li><i class="fa fa-check"></i> Prioritas Support</li>
            <li><i class="fa fa-check"></i> Konten Eksklusif</li>

        </ul>

        <button class="btn-plan">Pilih Paket</button>
    </div>
</div>
<!-- Included in All Plans -->
<section class="included-section">
    <h2 class="included-title">Termasuk dalam setiap paket</h2>

    <div class="included-grid">

        <!-- Item 1 -->
        <div class="included-item">
            <div class="included-icon">
                <i class="fas fa-comment-dots"></i>
            </div>
            <h3>Pembuat Situs Web</h3>
            <p>Bangun situs web Anda dalam 3 langkah mudah dengan perangkat AI. Anda akan langsung aktif dalam hitungan menit.</p>
        </div>

        <!-- Item 2 -->
        <div class="included-item">
            <div class="included-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h3>Keamanan Total</h3>
            <p>Tenang, situs web dan pengunjung Anda dilindungi oleh perangkat lunak keamanan terkini.</p>
        </div>

        <!-- Item 3 -->
        <div class="included-item">
            <div class="included-icon">
                <i class="fas fa-tachometer-alt"></i>
            </div>
            <h3>Waktu Aktif 99,9%, Terjamin</h3>
            <p>Jaminan uptime sebesar 99,9% memastikan situs Anda selalu tersedia.</p>
        </div>

        <!-- Item 4 -->
        <div class="included-item">
            <div class="included-icon">
                <i class="fas fa-table"></i>
            </div>
            <h3>Dasbor Sederhana</h3>
            <p>Mudah digunakan untuk pemula maupun profesional. Pantau performa situs Anda secara instan.</p>
        </div>

        <!-- Item 5 -->
        <div class="included-item">
            <div class="included-icon">
                <i class="fas fa-headset"></i>
            </div>
            <h3>Dukungan Pelanggan 24/7</h3>
            <p>Dapatkan bantuan ahli kapan pun Anda membutuhkannya. Respons cepat dan layanan ramah.</p>
        </div>

    </div>

    <div class="included-btn-wrapper">
        <a href="#" class="included-btn">Memulai</a>
    </div>
</section>

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