<?php
// SagaHealth/ai-engine/process_skrining.php
// Controller: Validasi Data, Hitung Skor, & Kirim ke AI

session_start();

// Validasi Akses
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../user/skrining_kesehatan.php");
    exit;
}

// ============================================================
// 1. HITUNG & VALIDASI DATA FISIK (PENTING!)
// ============================================================
$bb = abs(floatval($_POST['bb'] ?? 0));
$tb = abs(floatval($_POST['tb'] ?? 0));

// [AUTO-FIX] Konversi Meter ke CM
// Jika user mengisi 1.75 (meter), sistem otomatis ubah jadi 175 (cm)
// agar perhitungan IMT tidak error/ribuan.
if ($tb > 0 && $tb < 3.0) {
    $tb = $tb * 100;
}

// Hitung IMT (Berat / Tinggi(m)^2)
// Kita bagi TB dengan 100 karena input sudah dipastikan dalam CM
$imt = ($bb > 0 && $tb > 0) ? $bb / (($tb / 100) ** 2) : 0;

// [SAFETY] Batasi IMT Maksimal 100 agar AI tidak bingung data aneh
if ($imt > 100) $imt = 100;

// ============================================================
// 2. HITUNG TOTAL SKOR (RIWAYAT & GEJALA)
// ============================================================

// A. Total Riwayat Penyakit Pribadi
$total_pribadi = intval($_POST['pribadi_dm'] ?? 0) + 
                 intval($_POST['pribadi_hip'] ?? 0) + 
                 intval($_POST['pribadi_jantung'] ?? 0) + 
                 intval($_POST['pribadi_stroke'] ?? 0) + 
                 intval($_POST['pribadi_asma'] ?? 0) + 
                 intval($_POST['pribadi_kanker'] ?? 0);

// B. Total Riwayat Penyakit Keluarga
$total_keluarga = intval($_POST['keluarga_dm'] ?? 0) + 
                  intval($_POST['keluarga_hip'] ?? 0) + 
                  intval($_POST['keluarga_jantung'] ?? 0);

// C. Total Gejala Fisik Saat Ini
$total_gejala = intval($_POST['gejala_batuk'] ?? 0) + 
                intval($_POST['gejala_bbturun'] ?? 0) + 
                intval($_POST['gejala_demam'] ?? 0) + 
                intval($_POST['gejala_lemas'] ?? 0) + 
                intval($_POST['gejala_sesak'] ?? 0);

// D. Jumlah Keluhan Umum (Checkbox)
$jumlah_keluhan = count($_POST['keluhan_list'] ?? []);

// ============================================================
// 3. SUSUN PAYLOAD API
// ============================================================
$features_for_api = [
    "imt"                       => $imt,
    "riwayat_hipertensi"        => intval($_POST['riwayat_hip'] ?? 0),
    "konsumsi_obat"             => intval($_POST['konsumsi_obat'] ?? 0),
    "sistolik"                  => abs(floatval($_POST['sistolik'] ?? 120)),
    "diastolik"                 => abs(floatval($_POST['diastolik'] ?? 80)),
    "kebiasaan_begadang"        => intval($_POST['begadang'] ?? 0),
    "jumlah_keluhan"            => $jumlah_keluhan,
    "riwayat_penyakit_pribadi"  => $total_pribadi,
    "riwayat_penyakit_keluarga" => $total_keluarga,
    "jumlah_gejala"             => $total_gejala
];

// ============================================================
// 4. KIRIM KE PYTHON API (MODE CEPAT/SKOR SAJA)
// ============================================================
$api_url = 'http://localhost:5001/predict_score_only';

$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($features_for_api));
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Timeout 10 detik

$api_response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// ============================================================
// 5. PROSES HASIL & REDIRECT
// ============================================================
if ($http_code == 200) {
    $result = json_decode($api_response, true);
    
    if (isset($result['status']) && $result['status'] === 'success') {
        
        // Simpan Hasil SVM (Untuk Grafik Bar di Halaman Hasil)
        $_SESSION['multi_risiko'] = $result['data'];
        
        // Simpan Data Fisik User (PENTING: Untuk dikirim ke AI SagaBot nanti)
        $_SESSION['fitur_user'] = $features_for_api; 
        
        // Redirect ke Halaman Hasil
        header("Location: ../user/anjurankesehatan.php");
        exit;
        
    } else {
        $msg = $result['msg'] ?? 'Unknown Error';
        die("<h3>Error API Python:</h3><p>$msg</p><a href='../user/skrining_kesehatan.php'>Kembali</a>");
    }
} else {
    die("<h3>Gagal Terhubung ke AI Engine</h3>
         <p>Pastikan <code>api_multidisease.py</code> berjalan di Port 5001.</p>
         <a href='../user/skrining_kesehatan.php'>Coba Lagi</a>");
}
?>