  <?php include '../user/partials/header.php'; ?>
  <div class="html">
    <link rel="stylesheet" href="../assets/style/form_skrining.css">
    <link rel="stylesheet" type="text/css" href="../assets/style/auth.css">
  </div>
  <div class="container">
    <h1>🩺 Form Skrining Kesehatan SAGAHEALTH</h1>

    <form id="skrining-form" method="POST" action="../testing_svm/process_skrining.php">

    <div class="section">
      <h2>Identitas Diri</h2>
      <label>Nama Lengkap:</label>
      <input type="text" name="nama" required>
      </div>

    <div class="section">
      <h2>Pengukuran Diri</h2>
      <label>Berat Badan (Kg):</label>
      <input type="number" name="bb" id="bb" required>
      <label>Tinggi Badan (Cm):</label>
      <input type="number" name="tb" id="tb" required>
    </div>

    <div class="section">
      <h2>Skrining Mandiri Tekanan Darah (Hipertensi)</h2>
      
      <div class="question">
        1. Apakah dokter pernah memberi tahu Anda bahwa Anda memiliki tekanan darah tinggi?<br>
        <label><input type="radio" name="riwayat_hip" value="1"> Ya</label>
        <label><input type="radio" name="riwayat_hip" value="0" checked> Tidak</label>
      </div>

      <div class="question">
        2. Apakah Anda memiliki kebiasaan begadang?<br>
        <label><input type="radio" name="begadang" value="1"> Ya</label>
        <label><input type="radio" name="begadang" value="0" checked> Tidak</label>
      </div>

      <div class="question">
        3. Apakah Anda sedang atau rutin mengonsumsi obat penurun tekanan darah?<br>
        <label><input type="radio" name="konsumsi_obat" value="2"> Ya, rutin</label>
        <label><input type="radio" name="konsumsi_obat" value="1"> Ya, tapi tidak rutin</label>
        <label><input type="radio" name="konsumsi_obat" value="0" checked> Tidak</label>
      </div>

      <div class="question">
        4. Apakah Anda mengukur tekanan darah dalam 1 bulan terakhir?<br>
        <label><input type="radio" name="ukur_terakhir" value="1"> Ya</label>
        <label><input type="radio" name="ukur_terakhir" value="0"> Tidak</label>
      </div>

      <div class="question">
        5. Jika YA (nomor 4), berapa hasil terakhir Anda? (Isi 0 jika TIDAK)<br>
        <label>Sistolik (Angka Atas):</label><input type="number" name="sistolik" value="120" required>
        <label>Diastolik (Angka Bawah):</label><input type="number" name="diastolik" value="80" required>
      </div>

      <div class="question">
        6. Dalam 30 hari terakhir, apakah Anda sering mengalami keluhan berikut? (boleh lebih dari satu, maks 4)<br>
        <label><input type="checkbox" name="keluhan_list[]" value="1"> Sakit kepala berat / pusing</label><br>
        <label><input type="checkbox" name="keluhan_list[]" value="1"> Pegal / kaku di tengkuk</label><br>
        <label><input type="checkbox" name="keluhan_list[]" value="1"> Pandangan kabur tiba-tiba</label><br>
        <label><input type="checkbox" name="keluhan_list[]" value="1"> Mudah lelah / sesak napas ringan</label><br>
      </div>
    </div>

    <div class="section">
      <h2>Riwayat Penyakit Pribadi</h2>
      <table>
        <tr><th>Penyakit</th><th>Ya</th><th>Tidak</th></tr>
        <tr><td>Diabetes Melitus</td><td><input type="radio" name="pribadi_dm" value="1"></td><td><input type="radio" name="pribadi_dm" value="0" checked></td></tr>
        <tr><td>Hipertensi</td><td><input type="radio" name="pribadi_hip" value="1"></td><td><input type="radio" name="pribadi_hip" value="0" checked></td></tr>
        <tr><td>Penyakit Jantung</td><td><input type="radio" name="pribadi_jantung" value="1"></td><td><input type="radio" name="pribadi_jantung" value="0" checked></td></tr>
        <tr><td>Stroke</td><td><input type="radio" name="pribadi_stroke" value="1"></td><td><input type="radio" name="pribadi_stroke" value="0" checked></td></tr>
        <tr><td>Asma / PPOK</td><td><input type="radio" name="pribadi_asma" value="1"></td><td><input type="radio" name="pribadi_asma" value="0" checked></td></tr>
        <tr><td>Kanker</td><td><input type="radio" name="pribadi_kanker" value="1"></td><td><input type="radio" name="pribadi_kanker" value="0" checked></td></tr>
      </table>
    </div>

    <div class="section">
      <h2>Riwayat Penyakit Keluarga</h2>
      <table>
        <tr><th>Penyakit</th><th>Ya</th><th>Tidak</th><th>Tidak Tahu</th></tr>
        <tr><td>Diabetes Melitus</td><td><input type="radio" name="keluarga_dm" value="1"></td><td><input type="radio" name="keluarga_dm" value="0"></td><td><input type="radio" name="keluarga_dm" value="0" checked></td></tr>
        <tr><td>Hipertensi</td><td><input type="radio" name="keluarga_hip" value="1"></td><td><input type="radio" name="keluarga_hip" value="0"></td><td><input type="radio" name="keluarga_hip" value="0" checked></td></tr>
        <tr><td>Penyakit Jantung</td><td><input type="radio" name="keluarga_jantung" value="1"></td><td><input type="radio" name="keluarga_jantung" value="0"></td><td><input type="radio" name="keluarga_jantung" value="0" checked></td></tr>
      </table>
    </div>

    <div class="section">
      <h2>Keluhan dan Gejala Fisik</h2>
      <table>
        <tr><th>Gejala</th><th>Ya</th><th>Tidak</th></tr>
        <tr><td>Batuk berdahak ≥ 2 minggu</td><td><input type="radio" name="gejala_batuk" value="1"></td><td><input type="radio" name="gejala_batuk" value="0" checked></td></tr>
        <tr><td>Berat badan turun / nafsu makan turun</td><td><input type="radio" name="gejala_bbturun" value="1"></td><td><input type="radio" name="gejala_bbturun" value="0" checked></td></tr>
        <tr><td>Demam tanpa sebab jelas</td><td><input type="radio" name="gejala_demam" value="1"></td><td><input type="radio" name="gejala_demam" value="0" checked></td></tr>
        <tr><td>Sering lemas / lesu</td><td><input type="radio" name="gejala_lemas" value="1"></td><td><input type="radio" name="gejala_lemas" value="0" checked></td></tr>
        <tr><td>Sesak napas</td><td><input type="radio" name="gejala_sesak" value="1"></td><td><input type="radio" name="gejala_sesak" value="0" checked></td></tr>
      </table>
    </div>
    
    <button type="submit" class="btn-submit">Kirim Form Skrining</button>
    </form>
  </div>