<link rel="stylesheet" href="../assets/style/styles.css">
<header class="header">
    <div class="container-wrapper nav-content">
        
        <div class="flex items-center space-x-8">
          <a href="../dashboard/index.php"> <img src="../assets/img/logo.png" alt="Logo Halodoc" class="logo"></a>
            <nav class="main-nav" id="main-nav-menu">
                 <a href="../dashboard/index.php">Home</a>
                <a href="../dashboard/skrining_kesehatan.php">Kesehatan Fisik</a>
                <a href="../dashboard/mood.php">Kesehatan Mental</a>
            </nav>
        </div>
        
        <div class="flex items-center gap-4">
            <!-- DIUBAH: Mengganti <button> dengan <a> yang mengarah ke halaman login -->
            <a href="../auth/login.php" class="btn-primary" id="btn-daftar-desktop">
                Daftar
            </a>
            <!-- Tombol Menu untuk Mobile -->
            <button id="mobile-menu-toggle" class="mobile-menu-button">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>
</header>