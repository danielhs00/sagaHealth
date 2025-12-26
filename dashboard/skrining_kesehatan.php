<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Form Skrining Kesehatan - SAGAHEALTH</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/style/form_skrining.css">
   <link rel="stylesheet" href="../assets/style/style.css">
</head>
<body>
  <!-- LOGIN WALL (MODAL) -->
<div id="login-wall">
  <div class="login-box">
    <!-- Ikon Gembok dari Font Awesome -->
    <i class="fas fa-lock login-icon"></i>
    
    <!-- Judul Pesan -->
    <h2>Fitur Ini Memerlukan Login</h2>
    
    <!-- Deskripsi Pesan -->
    <p>Silakan login atau register untuk menikmati fitur mood tracker ini.</p>
    
    <!-- Tombol Aksi (Link ke halaman login) -->
    <a href="../auth/login.php" class="login-button-link">Login atau Register</a>
    <br>
    <a href="../dashboard/index.php" class="login-button">Kembali</a>
  </div>
</div>
  <?php include '../partials/header.php'; ?>
  <div class="container">
    <h1>🩺 Form Skrining Kesehatan SAGAHEALTH</h1>

    <!-- Identitas Diri -->
    <div class="section">
      <h2>Identitas Diri</h2>
      <label>Nama Lengkap:</label>
      <input type="text" name="nama" required>

      <label>Tanggal Lahir / Usia:</label>
      <input type="text" name="ttl">

      <label>Jenis Kelamin:</label>
      <select name="gender">
        <option value="">Pilih</option>
        <option>Laki-laki</option>
        <option>Perempuan</option>
      </select>

      <label>Alamat:</label>
      <textarea name="alamat" rows="2"></textarea>

      <label>No. HP:</label>
      <input type="text" name="nohp">
    </div>

    <!-- Pengukuran Diri -->
    <div class="section">
      <h2>Pengukuran Diri</h2>
      <label>Berat Badan (Kg):</label>
      <input type="number" name="bb">

      <label>Tinggi Badan (Cm):</label>
      <input type="number" name="tb">
    </div>

    <!-- Skrining Tekanan Darah -->
    <div class="section">
      <h2>Skrining Mandiri Tekanan Darah (Hipertensi)</h2>
      <div class="question">
        1. Apakah dokter pernah memberi tahu Anda bahwa Anda memiliki tekanan darah tinggi?<br>
        <label><input type="radio" name="hipertensi1"> Ya</label>
        <label><input type="radio" name="hipertensi1"> Tidak</label>
        <label><input type="radio" name="hipertensi1"> Tidak Tahu / Tidak Yakin</label>
      </div>

      <div class="question">
        2. Apakah Anda memiliki kebiasaan begadang?<br>
        <label><input type="radio" name="begadang"> Ya</label>
        <label><input type="radio" name="begadang"> Tidak</label>
      </div>

      <div class="question">
        3. Apakah Anda sedang atau rutin mengonsumsi obat penurun tekanan darah?<br>
        <label><input type="radio" name="obat"> Ya, rutin</label>
        <label><input type="radio" name="obat"> Ya, tapi tidak rutin</label>
        <label><input type="radio" name="obat"> Tidak</label>
      </div>

      <div class="question">
        4. Apakah Anda mengukur tekanan darah dalam 1 bulan terakhir?<br>
        <label><input type="radio" name="ukur"> Ya</label>
        <label><input type="radio" name="ukur"> Tidak</label>
      </div>

      <div class="question">
        5. Jika YA, berapa hasil terakhir Anda?<br>
        <input type="text" placeholder="Contoh: 120 / 80">
      </div>

      <div class="question">
        6. Dalam 30 hari terakhir, apakah Anda sering mengalami keluhan berikut? (boleh lebih dari satu)<br>
        <label><input type="checkbox"> Sakit kepala berat / pusing</label><br>
        <label><input type="checkbox"> Pegal / kaku di tengkuk</label><br>
        <label><input type="checkbox"> Pandangan kabur tiba-tiba</label><br>
        <label><input type="checkbox"> Mudah lelah / sesak napas ringan</label><br>
        <label><input type="checkbox"> Tidak ada keluhan di atas</label>
      </div>
    </div>

    <!-- Riwayat Penyakit Pribadi -->
    <div class="section">
      <h2>Riwayat Penyakit Pribadi</h2>
      <table>
        <tr><th>Penyakit</th><th>Ya</th><th>Tidak</th></tr>
        <tr><td>Diabetes Melitus</td><td><input type="radio" name="dm"></td><td><input type="radio" name="dm"></td></tr>
        <tr><td>Hipertensi</td><td><input type="radio" name="hip"></td><td><input type="radio" name="hip"></td></tr>
        <tr><td>Penyakit Jantung</td><td><input type="radio" name="jantung"></td><td><input type="radio" name="jantung"></td></tr>
        <tr><td>Stroke</td><td><input type="radio" name="stroke"></td><td><input type="radio" name="stroke"></td></tr>
        <tr><td>Asma / PPOK</td><td><input type="radio" name="asma"></td><td><input type="radio" name="asma"></td></tr>
        <tr><td>Kanker</td><td><input type="radio" name="kanker"></td><td><input type="radio" name="kanker"></td></tr>
      </table>
    </div>

    <!-- Riwayat Penyakit Keluarga -->
    <div class="section">
      <h2>Riwayat Penyakit Keluarga</h2>
      <table>
        <tr><th>Penyakit</th><th>Ya</th><th>Tidak</th><th>Tidak Tahu</th></tr>
        <tr><td>Diabetes Melitus</td><td><input type="radio" name="kel_dm"></td><td><input type="radio" name="kel_dm"></td><td><input type="radio" name="kel_dm"></td></tr>
        <tr><td>Hipertensi</td><td><input type="radio" name="kel_hip"></td><td><input type="radio" name="kel_hip"></td><td><input type="radio" name="kel_hip"></td></tr>
        <tr><td>Penyakit Jantung</td><td><input type="radio" name="kel_jantung"></td><td><input type="radio" name="kel_jantung"></td><td><input type="radio" name="kel_jantung"></td></tr>
      </table>
    </div>

    <!-- Keluhan Fisik -->
    <div class="section">
      <h2>Keluhan dan Gejala Fisik</h2>
      <table>
        <tr><th>Gejala</th><th>Ya</th><th>Tidak</th></tr>
        <tr><td>Batuk berdahak ≥ 2 minggu</td><td><input type="radio" name="batuk"></td><td><input type="radio" name="batuk"></td></tr>
        <tr><td>Berat badan turun / nafsu makan turun</td><td><input type="radio" name="bbturun"></td><td><input type="radio" name="bbturun"></td></tr>
        <tr><td>Demam tanpa sebab jelas</td><td><input type="radio" name="demam"></td><td><input type="radio" name="demam"></td></tr>
        <tr><td>Sering lemas / lesu</td><td><input type="radio" name="lemas"></td><td><input type="radio" name="lemas"></td></tr>
        <tr><td>Sesak napas</td><td><input type="radio" name="sesak"></td><td><input type="radio" name="sesak"></td></tr>
      </table>
    </div>

    <!-- Kesehatan Mental -->
    <div class="section">
      <h2>Kesehatan Mental (Mood)</h2>
      <p>Jawablah sesuai kondisi Anda dalam 7 hari terakhir:</p>
      <table>
        <tr><th>No</th><th>Pertanyaan</th><th>Ya</th><th>Tidak</th></tr>
        <tr><td>1</td><td>Apakah Anda sering menderita sakit kepala?</td><td><input type="radio" name="m1"></td><td><input type="radio" name="m1"></td></tr>
        <tr><td>2</td><td>Apakah Anda kehilangan nafsu makan?</td><td><input type="radio" name="m2"></td><td><input type="radio" name="m2"></td></tr>
        <tr><td>3</td><td>Apakah tidur Anda tidak nyenyak?</td><td><input type="radio" name="m3"></td><td><input type="radio" name="m3"></td></tr>
        <tr><td>4</td><td>Apakah Anda mudah merasa takut?</td><td><input type="radio" name="m4"></td><td><input type="radio" name="m4"></td></tr>
        <tr><td>5</td><td>Apakah Anda merasa cemas, tegang, atau khawatir?</td><td><input type="radio" name="m5"></td><td><input type="radio" name="m5"></td></tr>
        <tr><td>6</td><td>Apakah Anda sulit berpikir jernih?</td><td><input type="radio" name="m8"></td><td><input type="radio" name="m8"></td></tr>
        <tr><td>7</td><td>Apakah Anda merasa tidak bahagia?</td><td><input type="radio" name="m9"></td><td><input type="radio" name="m9"></td></tr>
        <tr><td>8</td><td>Apakah Anda kehilangan minat pada banyak hal?</td><td><input type="radio" name="m15"></td><td><input type="radio" name="m15"></td></tr>
      </table>
    </div>

    <button class="btn-submit">Kirim Form Skrining</button>
  </div>
  <?php include '../partials/footer.php'; ?>
</body>
</html>