<?php
session_start();
require_once '../includes/koneksi.php'; 

// --- 1. SECURITY CHECK ---
if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] !== true) {
    header("Location: ../auth/login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$planType = $_SESSION['plan_type'] ?? 'none';

// --- 2. AMBIL DATA USER ---
$stmt = $conn->prepare("SELECT name, email, phone, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "User tidak ditemukan.";
    exit();
}

// Format Tanggal
$joinDate = date("d F Y", strtotime($user['created_at']));
// Inisial Avatar
$initial = strtoupper(substr($user['name'], 0, 1));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SagaHealth - Sehat Fisik & Mental</title>
    <link rel="icon" href="../assets/img/tittle.png" type="image/png">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/style/styles.css">
    <link rel="stylesheet" href="../user/style/partials_user.css">

</head>
<body>

    <?php include 'partials/header.php'; ?>

    <main class="profile-container">
        
        <div class="profile-header">
            <div class="profile-avatar">
                <?php echo $initial; ?>
            </div>
            <div class="profile-info">
                <h1><?php echo htmlspecialchars($user['name']); ?></h1>
                <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($user['email']); ?></p>
                
                <?php if ($planType === 'premium'): ?>
                    <div class="plan-badge" style="background: rgba(255, 215, 0, 0.2); color: #FFF; border-color: gold;">
                        <i class="fas fa-crown"></i> PREMIUM MEMBER
                    </div>
                <?php elseif ($planType === 'basic'): ?>
                    <div class="plan-badge">
                        <i class="fas fa-leaf"></i> BASIC MEMBER
                    </div>
                <?php else: ?>
                    <div class="plan-badge" style="background: rgba(255,0,0,0.2);">
                        <i class="fas fa-exclamation-circle"></i> BELUM BERLANGGANAN
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="profile-content">
            
            <h3 class="section-title">Informasi Pribadi</h3>

            <form style="display: contents;">
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" value="<?php echo htmlspecialchars($user['name']); ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Nomor Telepon</label>
                    <input type="text" value="<?php echo htmlspecialchars($user['phone'] ?? '-'); ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Bergabung Sejak</label>
                    <input type="text" value="<?php echo $joinDate; ?>" readonly>
                </div>
            </form>

            <h3 class="section-title">Pengaturan Akun</h3>
            
            <div style="grid-column: 1 / -1; display: flex; gap: 15px; flex-wrap: wrap;">
                
                <?php if ($planType === 'premium'): ?>
                    <button class="btn-action btn-disabled" disabled>
                        <i class="fas fa-check-circle"></i> Paket Premium Aktif
                    </button>
                <?php else: ?>
                    <a href="plan.php" class="btn-action" style="background: #F59E0B;">
                        <i class="fas fa-arrow-up"></i> Upgrade Paket
                    </a>
                <?php endif; ?>

                <button onclick="logoutUser()" class="btn-action" style="background: #EF4444;">
                    <i class="fas fa-sign-out-alt"></i> Keluar
                </button>
            </div>

        </div>
    </main>

    <?php include '../partials/footer.php'; ?>

</body>
</html>