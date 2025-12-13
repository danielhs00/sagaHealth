<?php
// File ini tidak lagi menggunakan Sesi PHP, 
// semua autentikasi ditangani oleh JavaScript di sisi klien.
?><!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - SagaHealth</title>
    <!-- Menggunakan CSS yang sama dengan login -->
    <link rel="stylesheet" href="../assets/style/auth.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="../assets/img/tittle.png" type="image/png">
</head>
<body style="background-color: var(--bg-page);">

    <!-- GUARD SCRIPT: Wajib ada di paling atas -->
    <script>
        const userId = sessionStorage.getItem('userId') || localStorage.getItem('userId');
        const isLoggedIn = sessionStorage.getItem('isLoggedIn') === 'true' || localStorage.getItem('isLoggedIn') === 'true';

        if (!isLoggedIn || !userId) {
            // Bersihkan storage jika datanya tidak sinkron
            sessionStorage.clear();
            localStorage.clear();
            window.location.replace('login.php');
        }
    </script>

    <?php include '../user/partials/header.php'; ?>

    <!-- Konten Utama Halaman Profil -->
    <main class="profile-container">
        <div class="container-wrapper" style="max-width: 900px;">
            <h1 class="profile-title">Profil Saya</h1>

            <!-- Kartu Info Profil (Read-only) -->
            <div class="profile-card info-card">
                <div class="profile-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="profile-info-details">
                    <!-- Data diisi oleh JavaScript -->
                    <h2 id="profile-name">Memuat nama...</h2>
                    <p id="profile-email">email@memuat...</p>
                    
                    <div class="info-list">
                        <p>
                            <i class="fas fa-phone"></i> 
                            Nomor Telepon: <strong id="profile-phone">...</strong>
                        </p>
                        <p>
                            <i class="fas fa-calendar-alt"></i> 
                            Bergabung: <strong id="profile-joined">...</strong>
                        </p>
                        <p>
                            <i class="fas fa-clock"></i> 
                            Login Terakhir: <strong id="profile-last-login">...</strong>
                        </p>
                        <p>
                            <i class="fas fa-shield-alt"></i> 
                            Status: <strong id="profile-status" class="status-badge">...</strong>
                        </p>
                    </div>
                </div>
            </div>
            <a href="javascript:history.back()" class="btn-back">
    <i class="fas fa-arrow-left"></i> Kembali
</a>


            <!-- Pesan Error (jika ada) -->
            <div id="profile-message" class="auth-message" style="display: none;"></div>

        </div>
    </main>

    <script>
        // Ambil userId dari storage (sudah dideklarasikan di guard script)
        const currentUserId = sessionStorage.getItem('userId') || localStorage.getItem('userId');

        // Fungsi Logout
        function logoutUser() {
            sessionStorage.clear();
            localStorage.clear();
            window.location.href = 'login.php';
        }

        // Fungsi tampilkan pesan
        function showMessage(elementId, message, type = 'error') {
            const el = document.getElementById(elementId);
            el.textContent = message;
            el.className = 'auth-message ' + type;
            el.style.display = 'flex';
        }

        // Fungsi format tanggal
        function formatTanggal(isoString) {
            if (!isoString) return 'N/A';
            // Format yang lebih baik untuk 'id-ID'
            const options = {
                day: 'numeric', month: 'long', year: 'numeric',
                hour: '2-digit', minute: '2-digit', hour12: false
            };
            return new Date(isoString).toLocaleString('id-ID', options).replace(/\./g, ':');
        }

        // 1. Ambil data profil saat halaman dimuat
        document.addEventListener('DOMContentLoaded', async () => {
            try {
                // Panggil backend untuk mengambil data terbaru
                const res = await fetch(`../includes/auth_functions.php?action=get_profile&userId=${currentUserId}`);
                
                if (!res.ok) {
                    throw new Error(`HTTP error! status: ${res.status}`);
                }

                const data = await res.json();

                if (data.status === 'success') {
                    const user = data.user;
                    
                    // Tampilkan di header
                    document.getElementById('user-name-display').textContent = user.name;
                    
                    // Tampilkan di Info Card
                    document.getElementById('profile-name').textContent = user.name;
                    document.getElementById('profile-email').textContent = user.email;
                    document.getElementById('profile-phone').textContent = user.phone || 'Belum diisi';
                    document.getElementById('profile-joined').textContent = formatTanggal(user.created_at);
                    document.getElementById('profile-last-login').textContent = formatTanggal(user.last_login);
                    document.getElementById('profile-status').textContent = user.status;

                } else {
                    showMessage('profile-message', 'Gagal memuat data profil: ' + data.message, 'error');
                }
            } catch (err) {
                console.error("Fetch error:", err);
                showMessage('profile-message', 'Terjadi kesalahan koneksi.', 'error');
            }
        });
    </script>
</body>
</html>