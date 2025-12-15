<?php
// File ini tidak lagi menggunakan Sesi PHP, 
// semua autentikasi ditangani oleh JavaScript di sisi klien.
?><!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - SagaHealth</title>

    <!-- CSS Utama -->
    <link rel="stylesheet" href="../assets/style/auth.css">

    <!-- CSS Khusus Profil (avatar editable + header styling) -->
    <link rel="stylesheet" href="../assets/style/profile.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="icon" href="../assets/img/tittle.png" type="image/png">
</head>
<body style="background-color: var(--bg-page);">

    <!-- GUARD SCRIPT: Harus di paling atas -->
    <script>
        const userId = sessionStorage.getItem('userId') || localStorage.getItem('userId');
        const isLoggedIn = sessionStorage.getItem('isLoggedIn') === 'true' || localStorage.getItem('isLoggedIn') === 'true';

        if (!isLoggedIn || !userId) {
            sessionStorage.clear();
            localStorage.clear();
            window.location.replace('login.php');
        }
    </script>

    <?php include '../user/partials/header.php'; ?>

    <main class="profile-container">
        <div class="container-wrapper" style="max-width: 900px;">
            <h1 class="profile-title">Profil Saya</h1>

            <!-- Kartu Profil -->
            <div class="profile-card info-card">
                <div class="profile-avatar editable" id="profile-avatar-wrapper">
                    <i class="fas fa-user-circle default-icon"></i>
                    <div class="overlay-plus">
                        <i class="fas fa-plus"></i>
                    </div>
                    <input type="file" id="photo-input" accept="image/*" style="display:none;">
                </div>

                <div class="profile-info-details">
                    <h2 id="profile-name">Memuat nama...</h2>
                    <p id="profile-email">email@memuat...</p>
                    
                    <div class="info-list">
                        <p><i class="fas fa-phone"></i> Nomor Telepon: <strong id="profile-phone">...</strong></p>
                        <p><i class="fas fa-calendar-alt"></i> Bergabung: <strong id="profile-joined">...</strong></p>
                        <p><i class="fas fa-clock"></i> Login Terakhir: <strong id="profile-last-login">...</strong></p>
                        <p><i class="fas fa-shield-alt"></i> Status: <strong id="profile-status" class="status-badge">...</strong></p>
                    </div>
                </div>
            </div>

            <a href="javascript:history.back()" class="btn-back">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>

            <div id="profile-message" class="auth-message" style="display: none;"></div>
        </div>
    </main>

    <script>
        const currentUserId = sessionStorage.getItem('userId') || localStorage.getItem('userId');

        // Fungsi Logout (digunakan juga di header)
        function logoutUser() {
            sessionStorage.clear();
            localStorage.clear();
            window.location.href = 'login.php';
        }

        function showMessage(elementId, message, type = 'error') {
            const el = document.getElementById(elementId);
            el.textContent = message;
            el.className = 'auth-message ' + type;
            el.style.display = 'flex';
        }

        function formatTanggal(isoString) {
            if (!isoString) return 'N/A';
            const options = { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: false };
            return new Date(isoString).toLocaleString('id-ID', options).replace(/\./g, ':');
        }

        // Load dan tampilkan foto profil dari localStorage
        function loadProfilePhoto() {
            const photoBase64 = localStorage.getItem('userProfilePicture');
            const wrapper = document.getElementById('profile-avatar-wrapper');
            const defaultIcon = wrapper.querySelector('.default-icon');
            let img = wrapper.querySelector('img');

            if (photoBase64) {
                if (!img) {
                    img = document.createElement('img');
                    wrapper.insertBefore(img, defaultIcon);
                }
                img.src = photoBase64;
                defaultIcon.style.display = 'none';
            } else {
                if (img) img.remove();
                defaultIcon.style.display = 'flex';
            }
        }

        // Klik avatar → buka file picker
        document.getElementById('profile-avatar-wrapper').addEventListener('click', () => {
            document.getElementById('photo-input').click();
        });

        // Proses upload foto
        document.getElementById('photo-input').addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (!file) return;

            if (!file.type.startsWith('image/')) {
                showMessage('profile-message', 'File harus berupa gambar!', 'error');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(event) {
                const base64 = event.target.result;

                // Simpan ke localStorage
                localStorage.setItem('userProfilePicture', base64);

                // Update tampilan di profil
                loadProfilePhoto();

                // Beritahu header (tab sama & tab lain)
                window.dispatchEvent(new Event('profilePhotoChanged'));
                window.dispatchEvent(new Event('storage'));
            };
            reader.readAsDataURL(file);
        });

        // Load data saat halaman dimuat
        document.addEventListener('DOMContentLoaded', async () => {
            loadProfilePhoto(); // load foto dulu

            try {
                const res = await fetch(`../includes/auth_functions.php?action=get_profile&userId=${currentUserId}`);
                if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);

                const data = await res.json();

                if (data.status === 'success') {
                    const user = data.user;

                    // Simpan nama untuk header
                    localStorage.setItem('userName', user.name);
                    sessionStorage.setItem('userName', user.name);

                    // Update tampilan
                    document.getElementById('user-name-display') && (document.getElementById('user-name-display').textContent = user.name);
                    document.getElementById('profile-name').textContent = user.name;
                    document.getElementById('profile-email').textContent = user.email;
                    document.getElementById('profile-phone').textContent = user.phone || 'Belum diisi';
                    document.getElementById('profile-joined').textContent = formatTanggal(user.created_at);
                    document.getElementById('profile-last-login').textContent = formatTanggal(user.last_login);
                    document.getElementById('profile-status').textContent = user.status;

                    // Trigger update header
                    window.dispatchEvent(new Event('profilePhotoChanged'));
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
