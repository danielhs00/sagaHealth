<?php
// SagaHealth/dashboard/process_skrining.php - Multi Disease & AI Recommendation

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // --- 1. PENGUMPULAN & PENGHITUNGAN 10 FITUR (SAMA SEPERTI SEBELUMNYA) ---
    // Pastikan logika ini benar dan menghasilkan 10 fitur numerik.
    
    $bb = floatval($_POST['bb'] ?? 0);
    $tb = floatval($_POST['tb'] ?? 0);
    $imt = ($bb > 0 && $tb > 0) ? $bb / (($tb / 100) ** 2) : 0;
    
    $features_for_api = [
        "imt" => $imt,
        "riwayat_hipertensi" => intval($_POST['riwayat_hip'] ?? 0),
        "konsumsi_obat" => intval($_POST['konsumsi_obat'] ?? 0),
        "tekanan_sistolik" => floatval($_POST['sistolik'] ?? 120),
        "tekanan_diastolik" => floatval($_POST['diastolik'] ?? 80),
        "kebiasaan_begadang" => intval($_POST['begadang'] ?? 0),
        "jumlah_keluhan" => count($_POST['keluhan_list'] ?? []),
        "riwayat_penyakit_pribadi" => intval($_POST['pribadi_dm'] ?? 0) + intval($_POST['pribadi_hip'] ?? 0) + intval($_POST['pribadi_jantung'] ?? 0) + intval($_POST['pribadi_stroke'] ?? 0) + intval($_POST['pribadi_asma'] ?? 0) + intval($_POST['pribadi_kanker'] ?? 0),
        "riwayat_penyakit_keluarga" => intval($_POST['keluarga_dm'] ?? 0) + intval($_POST['keluarga_hip'] ?? 0) + intval($_POST['keluarga_jantung'] ?? 0),
        "jumlah_gejala" => intval($_POST['gejala_batuk'] ?? 0) + intval($_POST['gejala_bbturun'] ?? 0) + intval($_POST['gejala_demam'] ?? 0) + intval($_POST['gejala_lemas'] ?? 0) + intval($_POST['gejala_sesak'] ?? 0)
    ];


    // --- 2. PANGGIL API UNTUK PREDIKSI RISIKO SVM ---
    $api_url = 'http://localhost:5001/predict_multirisk'; 
    
    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($features_for_api));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    
    $api_response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    
    if ($http_code == 200) {
        $result = json_decode($api_response, true);
        
        if ($result['status'] === 'success') {
            // Simpan hasil prediksi SVM
            $_SESSION['multi_risiko'] = $result['results'];
            
            // ===============================================
            // BARU: PANGGIL GENERATIVE AI UNTUK REKOMENDASI TEKS
            // ===============================================
            
            $ai_data_input = [
                "user_input" => $features_for_api, // Raw input user
                "risk_results" => $result['results'] // Hasil SVM
            ];
            
            $ai_url = 'http://localhost:5001/generate_recommendation'; 
            
            $ch_ai = curl_init($ai_url);
            curl_setopt($ch_ai, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch_ai, CURLOPT_POST, true);
            curl_setopt($ch_ai, CURLOPT_POSTFIELDS, json_encode($ai_data_input));
            curl_setopt($ch_ai, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            
            $ai_response = curl_exec($ch_ai);
            curl_close($ch_ai);
            
            $ai_result = json_decode($ai_response, true);
            
            if ($ai_result['status'] === 'success') {
                // Simpan teks rekomendasi AI yang sudah dipersonalisasi
                $_SESSION['ai_recommendation'] = $ai_result['recommendation'];
            } else {
                $_SESSION['ai_recommendation'] = "Error AI: Gagal menghasilkan rekomendasi personal. (Error: " . ($ai_result['message'] ?? 'Unknown error') . ")";
            }
            
            // 4. REDIRECT
            header("Location: ../user/anjurankesehatan.php");
            exit;
        } else {
            die("Gagal prediksi risiko: " . ($result['message'] ?? 'Unknown error'));
        }
    } else {
        die("Gagal terhubung ke engine SVM. Pastikan Microservice Python berjalan di Port 5001. HTTP Code: $http_code");
    }

} else {
    header("Location: ../user/skrining_kesehatan.php");
    exit;
}
?>