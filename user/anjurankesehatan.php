<?php 
// SagaHealth/dashboard/anjurankesehatan.php
// Halaman Hasil: Tampilan Web Interaktif + Mode Cetak Profesional

session_start();

$multi_risiko = $_SESSION['multi_risiko'] ?? [];

if (empty($multi_risiko)) {
    header("Location: skrining_kesehatan.php");
    exit;
}

$disease_map = [
    'risiko_hipertensi' => 'Hipertensi',
    'risiko_dm'         => 'Diabetes Melitus',
    'risiko_jantung'    => 'Penyakit Jantung',
    'risiko_stroke'     => 'Stroke',
    'risiko_asma'       => 'Asma / PPOK',
    'risiko_kanker'     => 'Kanker'
];

function get_color_schema($label) {
    switch (strtolower($label)) {
        case 'tinggi': return ['bg' => '#fee2e2', 'text' => '#991b1b', 'bar' => '#ef4444'];
        case 'sedang': return ['bg' => '#fef3c7', 'text' => '#92400e', 'bar' => '#f59e0b'];
        default:       return ['bg' => '#dcfce7', 'text' => '#166534', 'bar' => '#22c55e'];
    }
}

$highest_risk_level = 'RENDAH';
foreach ($multi_risiko as $data) {
    $cat = $data['cat'] ?? $data['kategori'] ?? 'RENDAH';
    if ($cat === 'TINGGI') {
        $highest_risk_level = 'TINGGI';
        break; 
    } elseif ($cat === 'SEDANG' && $highest_risk_level !== 'TINGGI') {
        $highest_risk_level = 'SEDANG';
    }
}
$theme = get_color_schema($highest_risk_level);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Hasil Analisis Medis - SagaHealth</title>
    <link rel="icon" href="../assets/img/tittle.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    
</head>
<body>

<header>
    <img src="../assets/img/logo.png" alt="SagaHealth" />
    <a href="skrining_kesehatan.php" class="btn-home"><i class="fas fa-redo"></i> Ulangi Tes</a>
</header>

<div class="print-header">
    <img src="../assets/img/logo.png" alt="Logo">
    <h1>LAPORAN ANALISIS KESEHATAN</h1>
    <p>Dicetak pada: <?php echo date("d-m-Y H:i"); ?> | ID: <?php echo uniqid(); ?></p>
</div>

<div class="container">

    <div class="ai-section">
        <div class="ai-header">
            <span class="ai-badge"><i class="fas fa-user-md"></i> SAGA BOT AI</span>
            <h2 style="margin:0; font-size: 1.4rem;">Rekomendasi Dokter</h2>
        </div>

        <div id="ai-loading">
            <i class="fas fa-spinner fa-spin fa-lg" style="color:var(--primary)"></i> 
            <span>Sedang menganalisis hasil medis Anda...</span>
        </div>

        <div id="ai-content"></div>

        <div id="ai-error">
            <i class="fas fa-exclamation-circle"></i> 
            Maaf, SagaBot tidak dapat terhubung. Namun hasil statistik di bawah tetap akurat.
        </div>
    </div>

    <h3 style="color: #555; margin-bottom: 20px; border-bottom: 2px solid #eee; padding-bottom:10px;">
        <i class="fas fa-chart-bar"></i> Rincian Risiko Penyakit
    </h3>
    
    <div class="stats-grid">
        <?php foreach ($multi_risiko as $key => $data): ?>
            <?php 
                $risk_level = $data['cat'] ?? $data['kategori'] ?? 'RENDAH';
                $prob = $data['prob'] ?? $data['probabilitas'] ?? 0;
                $colors = get_color_schema($risk_level);
                $label_name = $disease_map[$key] ?? $key;
            ?>
            <div class="stat-card">
                <div>
                    <strong style="font-size: 1.1rem; color: #333;"><?php echo $label_name; ?></strong>
                    <div class="risk-tag" style="background: <?php echo $colors['bg']; ?> !important; color: <?php echo $colors['text']; ?> !important;">
                        <?php echo $risk_level; ?>
                    </div>
                </div>
                <div>
                    <div class="prog-bg">
                        <div class="prog-fill" style="width: <?php echo $prob; ?>%; background: <?php echo $colors['bar']; ?> !important;"></div>
                    </div>
                    <div style="font-size: 0.85rem; color: #666; margin-top: 5px; display: flex; justify-content: space-between;">
                        <span>Probabilitas</span>
                        <strong><?php echo $prob; ?>%</strong>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="print-footer">
        <p>Dokumen ini hasil analisis komputer oleh SagaHealth AI.<br>Konsultasikan hasil ini dengan dokter spesialis untuk diagnosis final.</p>
        <p>__________________________<br>Tanda Tangan Dokter / Pemeriksa</p>
    </div>

    <center>
        <button onclick="window.print()" class="btn-print">
            <i class="fas fa-print"></i> Simpan sebagai PDF
        </button>
    </center>
</div>

<script>
    const userRisks = <?php echo json_encode($multi_risiko); ?>;
    const userFeatures = <?php echo json_encode($_SESSION['fitur_user'] ?? []); ?>;

    document.addEventListener("DOMContentLoaded", function() {
        const loadingDiv = document.getElementById('ai-loading');
        const contentDiv = document.getElementById('ai-content');
        const errorDiv = document.getElementById('ai-error');

        fetch('http://localhost:5001/ask_ai_advice', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ risks: userRisks, features: userFeatures })
        })
        .then(response => response.json())
        .then(data => {
            loadingDiv.style.display = 'none';
            if(data.status === 'success') {
                contentDiv.style.display = 'block';
                // Ganti newline jadi <br> agar paragraf rapi di web & print
                contentDiv.innerHTML = data.advice.replace(/\n/g, "<br>");
            } else {
                errorDiv.style.display = 'block';
                errorDiv.innerHTML = "Gagal memuat saran: " + (data.advice || "Error API");
            }
        })
        .catch(err => {
            loadingDiv.style.display = 'none';
            errorDiv.style.display = 'block';
            errorDiv.innerText = "Koneksi ke SagaBot terputus.";
        });
    });
</script>

</body>
</html>