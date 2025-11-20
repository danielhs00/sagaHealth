<link rel="stylesheet" href="../user/style/partials_user.css">
 <!-- Header Navigasi Pengguna -->
    <header class="user-header">
        <div class="container-wrapper nav-content">
            <a href="index.php">
                <img src="../assets/img/logo.png" alt="SagaHealth" class="logo" onerror="this.src='https://placehold.co/120x40/ffffff/014C63?text=SagaHealth'">
            </a>
            <div class="user-info">
                <a href="../user/profile.php" class="user-info-link" title="Profil Saya">
                    <i class="fas fa-user-circle"></i>
                    <span id="user-name-display" get$nama>Memuat...</span>
                </a>
                <button class="btn-logout" onclick="logoutUser()" title="Keluar">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </div>
        </div>
    </header>