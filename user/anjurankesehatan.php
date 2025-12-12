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
    
    <style>
        :root { --primary: #0b3d91; --bg-light: #f6fbfa; }
        body { font-family: 'Poppins', sans-serif; background: var(--bg-light); margin: 0; color: #333; -webkit-print-color-adjust: exact; }
        
        /* Layout Web */
        .container { max-width: 900px; margin: 40px auto; padding: 0 20px; }
        header { background: #fff; padding: 15px 5%; box-shadow: 0 2px 5px rgba(0,0,0,0.05); display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 99; }
        header img { height: 40px; }
        .btn-home { text-decoration: none; color: var(--primary); font-weight: 600; padding: 8px 15px; border-radius: 6px; transition: 0.3s; }
        .btn-home:hover { background: #eef2ff; }

        /* AI Section */
        .ai-section {
            background: #fff; border-radius: 16px; padding: 30px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05); margin-bottom: 30px;
            border-top: 6px solid <?php echo $theme['bar']; ?>;
            page-break-inside: avoid; /* Mencegah terpotong saat print */
        }
        .ai-header { display: flex; align-items: center; gap: 10px; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
        .ai-badge { background: #eef2ff; color: #4338ca; padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; }
        
        #ai-content { display: none; font-size: 1.05rem; line-height: 1.7; color: #374151; white-space: pre-wrap; text-align: justify; }
        #ai-loading { color: #666; font-style: italic; display: flex; align-items: center; gap: 10px; }
        #ai-error { display: none; color: #991b1b; background: #fee2e2; padding: 15px; border-radius: 8px; }

        /* Stats Grid */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 15px; }
        .stat-card { 
            background: #fff; padding: 20px; border-radius: 12px; border: 1px solid #eee; 
            display: flex; flex-direction: column; justify-content: space-between;
            page-break-inside: avoid;
        }
        .risk-tag { padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; width: fit-content; margin-top: 5px; }
        .prog-bg { height: 6px; background: #eee; border-radius: 3px; margin-top: 15px; overflow: hidden; }
        .prog-fill { height: 100%; border-radius: 3px; }

        .btn-print { background: var(--primary); color: white; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-size: 1rem; margin-top: 30px; display: inline-flex; align-items: center; gap: 8px; transition: 0.3s; }
        .btn-print:hover { opacity: 0.9; transform: translateY(-1px); }

        /* =========================================
           CSS KHUSUS MODE CETAK (PRINT / PDF) 
           ========================================= */
        @media print {
            body { background: #fff; margin: 0; padding: 0; font-size: 12pt; }
            .container { max-width: 100%; margin: 0; padding: 20px; box-shadow: none; }
            
            /* Sembunyikan Elemen yang Tidak Perlu */
            header, .btn-print, .btn-home, #ai-loading { display: none !important; }

            /* Header Khusus Laporan Cetak */
            .print-header {
                display: block !important;
                text-align: center;
                border-bottom: 2px solid #000;
                padding-bottom: 10px;
                margin-bottom: 30px;
            }
            .print-header img { height: 50px; }
            .print-header h1 { font-size: 18pt; margin: 5px 0 0 0; color: #000; }
            .print-header p { margin: 0; font-size: 10pt; color: #666; }

            /* Atur Ulang Tampilan Card */
            .ai-section, .stat-card {
                box-shadow: none;
                border: 1px solid #ccc;
                /* Memaksa browser mencetak warna background */
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact; 
                background-color: #fff !important; /* Reset bg */
            }

            /* Paksa warna background tag risiko muncul */
            .risk-tag { 
                -webkit-print-color-adjust: exact; 
                print-color-adjust: exact;
                border: 1px solid #000; /* Fallback jika warna hilang */
            }

            /* Atur layout grid agar rapi di kertas A4 */
            .stats-grid {
                grid-template-columns: 1fr 1fr; /* Paksa 2 kolom */
                gap: 10px;
            }

            /* Footer Laporan */
            .print-footer {
                display: block !important;
                margin-top: 50px;
                text-align: right;
                font-size: 10pt;
                color: #555;
            }
        }

        /* Helper Classes untuk Print */
        .print-header, .print-footer { display: none; }
    </style>
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