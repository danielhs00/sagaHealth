<?php
session_start();
session_unset();
session_destroy();
header("location: login.php"); // Ini akan kembali ke auth/login.php
exit;
?>
<!DOCTYPE html>
<html>
<head>
  <title>Logging out...</title>
  <script>
    // Bersihkan semua data login client-side
    sessionStorage.clear();
    localStorage.clear();
    
    // Redirect ke halaman login setelah dibersihkan
    window.location.href = 'login.php';
  </script>
</head>
<body>
  <p>Logging out... Jika tidak otomatis redirect, <a href="login.php">klik di sini</a>.</p>
</body>
</html>