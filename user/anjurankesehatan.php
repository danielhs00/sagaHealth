<?php 
// SagaHealth/dashboard/anjurankesehatan.php - Menampilkan Hasil Generatif AI

session_start();
$multi_risiko = $_SESSION['multi_risiko'] ?? [];
$ai_recommendation_text = $_SESSION['ai_recommendation'] ?? "Sistem gagal mendapatkan rekomendasi AI. Silakan coba lagi.";

// Peta nama penyakit untuk tampilan (hanya untuk ringkasan)
$disease_map = [
    'risiko_hipertensi' => 'Hipertensi',
    'risiko_dm' => 'Diabetes Melitus (DM)',
    'risiko_jantung' => 'Penyakit Jantung',
    'risiko_stroke' => 'Stroke',
    'risiko_asma' => 'Asma / PPOK',
    'risiko_kanker' => 'Kanker'
];

$risiko_count = ['TINGGI' => 0, 'SEDANG' => 0];
$anjuran_spesifik_list = [];

foreach ($multi_risiko as $key => $data) {
    if ($data['kategori'] === 'TINGGI') {
        $risiko_count['TINGGI']++;
    } elseif ($data['kategori'] === 'SEDANG') {
        $risiko_count['SEDANG']++;
    }
    
    if ($data['kategori'] !== 'RENDAH') {
        $anjuran_spesifik_list[] = [
            'nama' => $disease_map[$key] ?? $key,
            'kategori' => $data['kategori'],
            'probabilitas' => $data['probabilitas'] ?? 'N/A',
            'warna' => $data['warna'] ?? 'hijau',
        ];
    }
}

// Penentuan warna utama untuk tampilan
if ($risiko_count['TINGGI'] > 0) {
    $warna_utama = '#9B1C1C'; // Merah
} elseif ($risiko_count['SEDANG'] > 0) {
    $warna_utama = '#F59E0B'; // Kuning
} else {
    $warna_utama = '#03543F'; // Hijau
}

// Hapus data sesi setelah ditampilkan
unset($_SESSION['multi_risiko']);
unset($_SESSION['ai_recommendation']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>SagaHealth</title>
    <link rel="icon" href="../assets/img/tittle.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; margin: 0; padding: 0; background: #f6fbfa; }
        header { display: flex; align-items: center; background-color: #f8fdfc; padding: 10px 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); position: sticky; top: 0; z-index: 1000; }
        header img.logo { height: 40px; margin-right: 30px; }
        nav { flex-grow: 1; }
        nav ul { list-style: none; display: flex; margin: 0; padding: 0; gap: 30px; }
        nav ul li a { text-decoration: none; color: #333; font-weight: 600; font-size: 16px; padding: 5px 0; }
        .btn-daftar { background-color: #0b3d91; color: white; padding: 8px 18px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; text-decoration: none; }
        .container { max-width: 800px; margin: 40px auto; padding: 24px; background: #fff; border-radius: 12px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); }
        h2 { color: #0b3d91; }
        ul.anjuran { background: #eaf7fa; border-radius: 10px; padding: 24px 30px 24px 36px; }
        ul.anjuran li { margin-bottom: 14px; font-size: 16px; display: flex; align-items: flex-start; gap: 10px; }
        ul.anjuran li i { margin-top: 5px; flex-shrink: 0; }
        .detail-card { background: #fff; border: 1px solid #ddd; padding: 15px; border-radius: 8px; margin-top: 10px; }
        .ai-output-box { 
            white-space: pre-wrap; 
            font-size: 1.05rem; 
            line-height: 1.6; 
            padding: 20px; 
            background: #fff; 
            border-left: 5px solid; 
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>

<header>
  <img src="../assets/img/logo.png"     alt="SagaHealth Logo" class="logo" />
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="skrining_kesehatan.php">Kesehatan Fisik</a></li>
            <li><a href="mood.php">Kesehatan Mental</a></li>
        </ul>
    </nav>
    <a href="../auth/register.php" class="btn-daftar">Daftar</a>
</header>

<div class="container">
    <h2 style="color: #0b3d91;">HASIL SKRINING RISIKO KESEHATAN 🎯</h2>
    <p>Hasil ini didapatkan dari analisis 10 fitur input Anda menggunakan 6 Model AI SVM.</p>
    <br>
    
    <h2 style="color: <?php echo $warna_utama; ?>;">Rekomendasi AI SagaBot untuk Anda</h2>
    
    <div class="ai-output-box" style="border-left-color: <?php echo $warna_utama; ?>;">
        <?php echo nl2br(htmlspecialchars($ai_recommendation_text)); ?>
    </div>
    
    <h2 style="margin-top: 30px;">Ringkasan Risiko (Analisis Model SVM)</h2>
    
    <div class="detail-card" style="padding: 20px;">
    <?php if (!empty($anjuran_spesifik_list)): ?>
        <?php foreach ($anjuran_spesifik_list as $item): ?>
            <div style="margin-bottom: 15px; border-bottom: 1px dashed #eee; padding-bottom: 10px;">
                <p style="margin: 0; font-size: 1.1rem;">
                    <i class="fas fa-chart-line" style="color: <?php echo $item['warna']; ?>;"></i> Risiko **<?php echo $item['nama']; ?>**: 
                    <span style="color: <?php echo $item['warna']; ?>; font-weight: bold;"><?php echo $item['kategori']; ?></span>
                </p>
                <small style="margin-left: 20px; color: #666;">(Probabilitas: <?php echo $item['probabilitas']; ?>%)</small>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="color: #03543F; font-weight: bold; text-align: center;">Semua Risiko Berada dalam Kategori RENDAH.</p>
    <?php endif; ?>
    </div>
</div>
</body>
</html>