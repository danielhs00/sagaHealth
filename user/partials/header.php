<!-- Header Navigation -->
<header class="plan-header-nav">
    <div class="container-wrapper nav-content">
        <div class="nav-left">
            <img src="../assets/img/logo.png" alt="SagaHealth" class="logo" 
                 onerror="this.src='https://placehold.co/120x40/014C63/ffffff?text=SagaHealth'">
        </div>
        <div class="nav-right">
            <div class="user-info" id="user-display">
                <a href="../user/profile.php" class="btn-profile" title="Profil Saya">
                    <i class="fas fa-user-circle"></i>
                    <span id="user-name-display"></span>
                </a>
            </div>

            <a href="javascript:void(0)" onclick="logoutUser()">
                <button class="btn-logout" id="logout-btn" title="Keluar">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Keluar</span>
                </button>
            </a>
        </div>
    </div>
</header>

<script>
    // Fungsi update header (nama + foto profil)
    function updateHeaderProfile() {
        const nameEl = document.getElementById('user-name-display');
        const profileLink = document.querySelector('.btn-profile');

        if (!profileLink || !nameEl) return;

        // Ambil data terbaru dari storage
        const savedName = localStorage.getItem('userName') || sessionStorage.getItem('userName') || '';
        const savedPhoto = localStorage.getItem('userProfilePicture');

        // Update nama
        nameEl.textContent = savedName || 'Pengguna';

        // Hapus elemen lama (ikon atau img sebelumnya)
        const oldIcon = profileLink.querySelector('i');
        const oldImg = profileLink.querySelector('img.profile-avatar-header');
        if (oldIcon) oldIcon.remove();
        if (oldImg) oldImg.remove();

        // Jika ada foto → tampilkan img
        if (savedPhoto) {
            const img = document.createElement('img');
            img.src = savedPhoto;
            img.className = 'profile-avatar-header';
            img.alt = 'Foto Profil';
            profileLink.insertBefore(img, nameEl);
        } else {
            // Jika tidak ada foto → kembalikan ikon default
            const icon = document.createElement('i');
            icon.className = 'fas fa-user-circle';
            profileLink.insertBefore(icon, nameEl);
        }
    }

    // Jalankan saat load
    document.addEventListener('DOMContentLoaded', updateHeaderProfile);

    // Update saat storage berubah (tab lain)
    window.addEventListener('storage', updateHeaderProfile);

    // Update saat event custom dari profile.php (tab sama)
    window.addEventListener('profilePhotoChanged', updateHeaderProfile);
</script>   
