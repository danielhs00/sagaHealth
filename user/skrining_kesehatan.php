<?php include '../user/partials/header.php'; ?>

<style>
    /* Style dasar form (sesuai file asli) */
    .html { display: none; } /* Hack kecil jika file css asli bentrok */
    
    /* Overlay Loading Full Screen */
    #loading-overlay {
        display: none; /* Hidden by default */
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.95);
        z-index: 9999;
        flex-direction: column;
        justify_content: center;
        align-items: center;
        text-align: center;
    }

    .spinner {
        width: 60px;
        height: 60px;
        border: 6px solid #f3f3f3;
        border-top: 6px solid #3498db; /* Warna SAGAHEALTH */
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-bottom: 20px;
    }

    .loading-text {
        font-family: 'Segoe UI', sans-serif;
        color: #2c3e50;
        font-size: 1.2rem;
        font-weight: 600;
    }

    .loading-subtext {
        color: #7f8c8d;
        font-size: 0.9rem;
        margin-top: 10px;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Pastikan container form tetap rapi */
    .container {
        max-width: 800px;
        margin: 40px auto;
        padding: 30px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    }
</style>

<link rel="stylesheet" href="../assets/style/form_skrining.css">
<link rel="stylesheet" type="text/css" href="../assets/style/auth.css">

<div id="loading-overlay">
    <div class="spinner"></div>
    <div class="loading-text" id="loading-main-text">Sedang Menghubungkan...</div>
    <div class="loading-subtext" id="loading-sub-text">Mohon jangan tutup halaman ini.</div>
</div>

<div class="container">
  <h1>🩺 Form Skrining Kesehatan SAGAHEALTH</h1>
  <p style="margin-bottom: 20px; color: #666;">Isi data berikut untuk mendapatkan analisis risiko 6 penyakit dan rekomendasi AI personal.</p>

  <form id="skrining-form" method="POST" action="../testing_svm/process_skrining.php">

    <div class="section">
      <h2>Identitas Diri</h2>
      <div class="form-group">
          <label>Nama Lengkap:</label>
          <input type="text" name="nama" class="form-control" required placeholder="Masukkan nama Anda">
      </div>
    </div>

    <div class="section">
      <h2>Pengukuran Fisik</h2>
      <div style="display: flex; gap: 20px;">
          <div style="flex:1;">
              <label>Berat Badan (Kg):</label>
              <input type="number" name="bb" id="bb" required step="0.1" min="1" placeholder="Contoh: 65.5">
          </div>
          <div style="flex:1;">
              <label>Tinggi Badan (Cm):</label>
              <input type="number" name="tb" id="tb" required step="0.1" min="50" placeholder="Contoh: 170">
          </div>
      </div>
    </div>

    <div class="section">
      <h2>Skrining Tekanan Darah</h2>
      
      <div class="question">
        <p>1. Apakah dokter pernah menyatakan Anda memiliki tekanan darah tinggi?</p>
        <label><input type="radio" name="riwayat_hip" value="1"> Ya</label>
        <label><input type="radio" name="riwayat_hip" value="0" checked> Tidak</label>
      </div>

      <div class="question">
        <p>2. Apakah Anda sering begadang (tidur larut malam)?</p>
        <label><input type="radio" name="begadang" value="1"> Ya</label>
        <label><input type="radio" name="begadang" value="0" checked> Tidak</label>
      </div>

      <div class="question">
        <p>3. Status konsumsi obat hipertensi:</p>
        <label><input type="radio" name="konsumsi_obat" value="2"> Rutin</label>
        <label><input type="radio" name="konsumsi_obat" value="1"> Tidak Rutin</label>
        <label><input type="radio" name="konsumsi_obat" value="0" checked> Tidak/Belum pernah</label>
      </div>

      <div class="question">
        <p>4. Hasil pengukuran tensi terakhir (Jika tidak tahu, biarkan default):</p>
        <div style="display: flex; gap: 20px;">
            <div style="flex:1;">
                <label>Sistolik (Atas):</label>
                <input type="number" name="sistolik" value="120" required>
            </div>
            <div style="flex:1;">
                <label>Diastolik (Bawah):</label>
                <input type="number" name="diastolik" value="80" required>
            </div>
        </div>
      </div>

      <div class="question">
        <p>5. Keluhan dalam 30 hari terakhir (Pilih yang sesuai):</p>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
            <label><input type="checkbox" name="keluhan_list[]" value="1"> Sakit kepala berat</label>
            <label><input type="checkbox" name="keluhan_list[]" value="1"> Tengkuk kaku</label>
            <label><input type="checkbox" name="keluhan_list[]" value="1"> Pandangan kabur</label>
            <label><input type="checkbox" name="keluhan_list[]" value="1"> Nyeri dada / Sesak</label>
        </div>
      </div>
    </div>

    <div class="section">
      <h2>Riwayat Medis Pribadi</h2>
      <table class="table-custom" style="width:100%; text-align: left;">
        <tr><th>Kondisi</th><th style="text-align:center;">Ya</th><th style="text-align:center;">Tidak</th></tr>
        
        <?php 
        $penyakit = [
            'pribadi_dm' => 'Diabetes', 
            'pribadi_hip' => 'Hipertensi', 
            'pribadi_jantung' => 'Jantung', 
            'pribadi_stroke' => 'Stroke',
            'pribadi_asma' => 'Asma/PPOK',
            'pribadi_kanker' => 'Kanker'
        ];
        foreach($penyakit as $name => $label) {
            echo "<tr>
                <td>$label</td>
                <td style='text-align:center;'><input type='radio' name='$name' value='1'></td>
                <td style='text-align:center;'><input type='radio' name='$name' value='0' checked></td>
            </tr>";
        }
        ?>
      </table>
    </div>

    <div class="section">
      <h2>Riwayat Keluarga (Orang Tua/Saudara)</h2>
      <table class="table-custom" style="width:100%; text-align: left;">
        <tr><th>Kondisi</th><th style="text-align:center;">Ya</th><th style="text-align:center;">Tidak</th></tr>
        <?php 
        $keluarga = [
            'keluarga_dm' => 'Diabetes', 
            'keluarga_hip' => 'Hipertensi', 
            'keluarga_jantung' => 'Jantung'
        ];
        foreach($keluarga as $name => $label) {
            echo "<tr>
                <td>$label</td>
                <td style='text-align:center;'><input type='radio' name='$name' value='1'></td>
                <td style='text-align:center;'><input type='radio' name='$name' value='0' checked></td>
            </tr>";
        }
        ?>
      </table>
    </div>

    <div class="section">
      <h2>Gejala Lainnya</h2>
      <table class="table-custom" style="width:100%; text-align: left;">
        <?php 
        $gejala = [
            'gejala_batuk' => 'Batuk > 2 minggu', 
            'gejala_bbturun' => 'BB turun drastis', 
            'gejala_demam' => 'Demam tanpa sebab',
            'gejala_lemas' => 'Sering lemas',
            'gejala_sesak' => 'Sesak napas aktivitas ringan'
        ];
        foreach($gejala as $name => $label) {
            echo "<tr>
                <td>$label</td>
                <td style='text-align:center; width:50px;'><input type='radio' name='$name' value='1'> Ya</td>
                <td style='text-align:center; width:60px;'><input type='radio' name='$name' value='0' checked> Tidak</td>
            </tr>";
        }
        ?>
      </table>
    </div>
    
    <button type="submit" class="btn-submit" style="width:100%; padding:15px; background:#3498db; color:white; border:none; border-radius:8px; font-size:16px; cursor:pointer; margin-top:20px;">
        🔍 Analisis Kesehatan Saya Sekarang
    </button>
  </form>
</div>

<script>
document.getElementById('skrining-form').addEventListener('submit', function(e) {
    // Validasi sederhana (opsional, karena required sudah ada di HTML)
    const bb = document.getElementById('bb').value;
    const tb = document.getElementById('tb').value;
    
    if(!bb || !tb) {
        alert("Mohon lengkapi Berat Badan dan Tinggi Badan.");
        e.preventDefault();
        return;
    }

    // Tampilkan Overlay Loading
    const overlay = document.getElementById('loading-overlay');
    const mainText = document.getElementById('loading-main-text');
    const subText = document.getElementById('loading-sub-text');
    
    overlay.style.display = 'flex';

    // Simulasi Progress Bar Text agar user sabar menunggu LLM
    // Karena PHP backend menjalankan 6 SVM + 1 LLM, butuh waktu ~10-20 detik.
    
    const messages = [
        { t: 0, msg: "Mengumpulkan data kesehatan Anda..." },
        { t: 2000, msg: "Menghitung Indeks Massa Tubuh (IMT)..." },
        { t: 4000, msg: "Menjalankan 6 Model Machine Learning..." },
        { t: 7000, msg: "Menganalisis risiko Hipertensi, Diabetes, & Jantung..." },
        { t: 10000, msg: "Menghubungkan ke SagaBot AI..." },
        { t: 12000, msg: "SagaBot sedang mengetik saran personal untuk Anda..." },
        { t: 18000, msg: "Finalisasi laporan..." }
    ];

    messages.forEach(item => {
        setTimeout(() => {
            mainText.innerText = item.msg;
        }, item.t);
    });
});
</script>
