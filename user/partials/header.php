<header class="modern-header">
    <div class="nav-content">
        
        <a href="dashboard_basic.php" class="logo-container">
            <img src="../assets/img/logo.png" 
                 alt="SagaHealth" 
                 onerror="this.src='https://placehold.co/140x45/014C63/ffffff?text=SagaHealth'">
        </a>

        <div class="nav-right">
            
            <a href="../user/profile.php" class="profile-pill" id="profile-link-container">
                <div id="avatar-placeholder" class="header-avatar-icon">
                    <i class="fas fa-user"></i>
                </div>
                <span class="user-name" id="user-name-display">Memuat...</span>
            </a>

            <button onclick="logoutUser()" class="btn-logout-modern" title="Keluar">
                <i class="fas fa-power-off"></i>
            </button>
            
        </div>
    </div>
    <link rel="stylesheet" href="../user/style/partials_user.css">
</header>

<script>
    // === LOGIC UPDATE PROFIL DI HEADER ===
    function updateHeaderProfile() {
        const nameEl = document.getElementById('user-name-display');
        const container = document.getElementById('profile-link-container');
        const placeholder = document.getElementById('avatar-placeholder');

        if (!nameEl || !container) return;

        // 1. Ambil data dari Storage
        const savedName = localStorage.getItem('userName') || sessionStorage.getItem('userName') || 'Pengguna';
        const savedPhoto = localStorage.getItem('userProfilePicture'); // Format Base64

        // 2. Update Nama
        // Ambil nama depan saja agar tidak kepanjangan di header
        const firstName = savedName.split(' ')[0]; 
        nameEl.textContent = firstName;

        // 3. Update Foto
        // Hapus foto lama jika ada (agar tidak duplikat saat update)
        const oldImg = container.querySelector('img.header-avatar');
        if (oldImg) oldImg.remove();

        if (savedPhoto && savedPhoto.startsWith('data:image/')) {
            // Jika ada foto custom
            if(placeholder) placeholder.style.display = 'none'; // Sembunyikan ikon default

            const img = document.createElement('img');
            img.src = savedPhoto;
            img.className = 'header-avatar';
            img.alt = 'Profil';
            
            // Masukkan gambar sebelum elemen nama
            container.insertBefore(img, nameEl);
        } else {
            // Jika tidak ada foto, tampilkan ikon default
            if(placeholder) placeholder.style.display = 'flex';
        }
    }

    // === LOGIC LOGOUT ===
  function logoutUser() {
        if (confirm('Apakah Anda yakin ingin keluar?')) {
            // 1. Hapus data di browser (agar nama hilang)
            sessionStorage.clear();
            localStorage.clear();
            
            // 2. Arahkan ke script PHP logout untuk menghancurkan sesi server
            window.location.href = '../auth/logout.php';
        }
    }

    // Event Listeners
    document.addEventListener('DOMContentLoaded', updateHeaderProfile);
    window.addEventListener('storage', updateHeaderProfile); // Sinkron antar tab
    window.addEventListener('profileUpdated', updateHeaderProfile); // Trigger custom event
</script>