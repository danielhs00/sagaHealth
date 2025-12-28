<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] !== true) {
    // Jika tidak login, arahkan ke halaman login
    header("Location: ../auth/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SagaHealth - Sehat Fisik & Mental</title>
    <link rel="icon" href="../assets/img/tittle.png" type="image/png">
    <link rel="stylesheet" href="../assets/style/auth.css">
    <link rel="stylesheet" href="../assets/style/plan.css">
    <link rel="stylesheet" 
 href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

</head>
<body>

  <div id="page-loader" class="loader-page">
    <h3>Memuat halaman...</h3>
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

    <!-- BASIC (mirip Hostinger Basic) -->
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
<button class="btn-plan" onclick="selectPlan('Basic Plan', 50000, 'basic')">
    Pilih Basic
</button>

    </div>

    <!-- PREMIUM — Most Popular -->
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

<button class="btn-plan premium" onclick="selectPlan('Premium Plan', 100000, 'premium')">
    Pilih Premium
</button>

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
                <i class="fas fa-clock"></i>
            </div>
            <h3>Waktu Aktif 99,9%, Terjamin</h3>
            <p>Jaminan uptime sebesar 99,9% memastikan situs Anda selalu tersedia.</p>
        </div>

        <!-- Item 4 -->
        <div class="included-item">
            <div class="included-icon">
                <i class="fas fa-chart-line"></i>
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

</section>
<div class="included-btn-wrapper">
        <a href="#" class="included-btn">Memulai</a>
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
                    <div class="comparison-table-wrapper comparison" id="comparison-table">
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

    
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-nW3D8-QuLJC-1SVc"></script>
<script src="../assets/js/plan.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const loader = document.getElementById("page-loader");
    if (loader) {
        loader.style.display = "none";
    }
});

document.addEventListener("DOMContentLoaded", function () {
    const btn = document.getElementById("show-comparison");
    const table = document.getElementById("comparison-table");

    btn.addEventListener("click", function () {
        if (table.style.display === "none" || table.style.display === "") {
            table.style.display = "block";
            btn.querySelector("span").textContent = "Sembunyikan Perbandingan";
            btn.querySelector("i").classList.remove("fa-chevron-down");
            btn.querySelector("i").classList.add("fa-chevron-up");
        } else {
            table.style.display = "none";
            btn.querySelector("span").textContent = "Lihat Perbandingan Detail";
            btn.querySelector("i").classList.remove("fa-chevron-up");
            btn.querySelector("i").classList.add("fa-chevron-down");
        }
    });
});

</script>

</body>
</html>