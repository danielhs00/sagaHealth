<?php
// includes/auth_functions.php

// 1. Pengecekan status sesi sebelum memanggil session_start() (Perbaikan Notice)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Gunakan ob_start() hanya sekali
ob_start();

// PERBAIKAN: Path ini diubah agar mencari di folder yang sama (includes)
require_once __DIR__ . '/koneksi.php'; 

// Fungsi helper untuk merespons JSON dan keluar
function jsonResponse($status, $message, $data = []) {
    ob_clean(); // Bersihkan buffer output sebelum mengirim JSON
    echo json_encode(array_merge(["status" => $status, "message" => $message], $data));
    exit;
}

// Fungsi DB/Logika yang dibutuhkan oleh file lain (misal lupa_sandi.php)
// Fungsi untuk mendapatkan ID pengguna berdasarkan email
function get_user_id_by_email($email) {
    global $conn;
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    return $user['id'] ?? null;
}

// =================================================================
// LOGIKA API UTAMA (Hanya berjalan jika file dipanggil langsung/sebagai endpoint)
// =================================================================
// Cek jika file ini adalah file utama yang dieksekusi, BUKAN di-include
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    
    // Set header Content-Type HANYA untuk respons API
    header('Content-Type: application/json');

    try {
        // Cek koneksi dari db_connect.php
        global $db_error, $conn;
        if (isset($db_error) || empty($conn)) {
            throw new Exception("Koneksi database gagal.");
        }

        // Ambil input JSON (Hanya untuk POST)
        $input = json_decode(file_get_contents('php://input'), true);

        // --- ACTION: GET PROFILE (Metode GET) ---
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_profile') {
            $userId = $_GET['userId'] ?? 0;

            if (empty($userId)) {
                throw new Exception("User ID tidak ada.");
            }
            // Ambil data profil (tanpa password)
            $stmt = $conn->prepare("SELECT id, name, email, phone, created_at, last_login, status FROM users WHERE id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($user = $result->fetch_assoc()) {
                jsonResponse("success", "Data profil berhasil diambil.", ["user" => $user]);
            } else {
                jsonResponse("error", "User tidak ditemukan.");
            }
            $stmt->close();
            exit;
        }

        // Lanjutkan untuk request POST (login, register, update)
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
             // Ini akan menangkap akses langsung ke file ini (GET) tanpa action 'get_profile'
            throw new Exception("Metode request tidak valid.");
        }

        if (!$input || !isset($input['action'])) {
            throw new Exception("Aksi tidak valid.");
        }

        $action = $input['action'];

        // --- ACTION: REGISTER (Menyimpan User Baru) ---
        if ($action === 'register') {
            $name = trim($input['name'] ?? '');
            $email = trim($input['email'] ?? '');
            $password = $input['password'] ?? '';
            $phone = trim($input['phone'] ?? '');

            if (empty($name) || empty($email) || empty($password)) {
                 jsonResponse("error", "Nama, email, dan password wajib diisi.");
            }
            if (strlen($password) < 8) {
                 jsonResponse("error", "Password minimal harus 8 karakter.");
            }

            // Cek email duplikat
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                 jsonResponse("error", "Email sudah terdaftar.");
            }
            $stmt->close();

            // Simpan user baru ke database
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $hashed_password, $phone);
            
            if ($stmt->execute()) {
                 jsonResponse("success", "Registrasi berhasil! Data Anda telah disimpan.");
            } else {
                 throw new Exception("Gagal menyimpan data: " . $stmt->error);
            }
        }
        // ... di dalam blok LOGIKA API UTAMA ...

    // ... Lanjutkan dari aksi update_password ...

    // --- ACTION: GOOGLE LOGIN (Verifikasi ID Token) ---
    elseif ($action === 'google_login') {
        global $conn;
        $id_token = $input['id_token'] ?? '';
        if (empty($id_token)) {
            jsonResponse("error", "ID Token tidak diterima.");
        }

        // 1. Verifikasi ID Token dengan Google
        // Memanggil API Google untuk mendapatkan data user dari token
        $google_response = @file_get_contents("https://oauth2.googleapis.com/tokeninfo?id_token=" . $id_token);
        
        if ($google_response === false) {
             jsonResponse("error", "Gagal memverifikasi token dengan Google. Pastikan server Anda bisa mengakses URL eksternal.");
        }

        $user_data = json_decode($google_response, true);
        
        // 2. Lakukan validasi Client ID
        // PENTING: Ganti nilai ini dengan Client ID Anda yang sebenarnya
        $CLIENT_ID = "542615675120-ghv1c22amb2v5mnq9uesqsp122jq2nrc.apps.googleusercontent.com"; 
        
        if (!isset($user_data['email']) || $user_data['aud'] !== $CLIENT_ID || $user_data['iss'] !== 'https://accounts.google.com') {
             // Ini adalah sumber error: Verifikasi token Google gagal atau Client ID tidak cocok.
             jsonResponse("error", "Verifikasi token Google gagal atau Client ID tidak cocok.");
        }
        
        $email = $user_data['email'];
        // Ambil nama dari data Google, atau buat dari email jika nama tidak tersedia
        $name = $user_data['name'] ?? explode('@', $email)[0];
        
        // 3. Cek user di DB
        $stmt = $conn->prepare("SELECT id, name, email, status FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            // User ditemukan -> Lakukan Login
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            
            // Update last_login
            $update_stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $update_stmt->bind_param("i", $user['id']);
            $update_stmt->execute();
            $update_stmt->close();

            jsonResponse("success", "Login Google berhasil!", ["user" => $user]);

        } else {
            // User tidak ditemukan -> Daftarkan User Baru
            // Buat password dummy acak (karena Google login tidak pakai password lokal)
            $default_password = bin2hex(random_bytes(16)); 
            $hashed_password = password_hash($default_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, status) VALUES (?, ?, ?, 'active')");
            $stmt->bind_param("sss", $name, $email, $hashed_password);
            
            if ($stmt->execute()) {
                $new_user_id = $conn->insert_id;
                
                // Simpan Session Login untuk user baru
                $_SESSION['user_id'] = $new_user_id;
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email;
                
                jsonResponse("success", "Pendaftaran via Google berhasil!", ["user" => [
                    'id' => $new_user_id, 'name' => $name, 'email' => $email
                ]]);
            } else {
                 throw new Exception("Gagal menyimpan data user baru dari Google: " . $stmt->error);
            }
        }
    }
    
    // ... sisa kode error handling Anda
    

        // --- ACTION: LOGIN (Cek User & Simpan Waktu Login) ---
        elseif ($action === 'login') {
            $email = trim($input['email'] ?? '');
            $password = $input['password'] ?? '';

            if (empty($email) || empty($password)) {
                jsonResponse("error", "Email dan password wajib diisi.");
            }

            $stmt = $conn->prepare("SELECT id, name, email, password, phone, status FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($user = $result->fetch_assoc()) {
                // Verifikasi password
                if (password_verify($password, $user['password'])) {

                    // Simpan Session Login
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_email'] = $user['email'];
                    
                    // Update last_login
                    $update_stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                    $update_stmt->bind_param("i", $user['id']);
                    $update_stmt->execute();
                    $update_stmt->close();

                    // Hapus password dari data yang dikirim kembali
                    unset($user['password']);
                    jsonResponse("success", "Login berhasil!", ["user" => $user]);
                } else {
                    jsonResponse("error", "Kata sandi salah.");
                }
            } else {
                jsonResponse("error", "Email tidak ditemukan.");
            }
        }
        
        // --- ACTION: UPDATE PROFILE ---
        elseif ($action === 'update_profile') {
            $userId = $input['userId'] ?? 0;
            $name = trim($input['name'] ?? '');
            $phone = trim($input['phone'] ?? '');

            if (empty($userId) || empty($name)) {
                jsonResponse("error", "User ID dan Nama wajib diisi.");
            }
            
            $stmt = $conn->prepare("UPDATE users SET name = ?, phone = ? WHERE id = ?");
            $stmt->bind_param("ssi", $name, $phone, $userId);
            
            if ($stmt->execute()) {
                 jsonResponse("success", "Profil berhasil diperbarui.");
            } else {
                 throw new Exception("Gagal memperbarui profil: " . $stmt->error);
            }
        }
        
        // --- ACTION: UPDATE PASSWORD ---
        elseif ($action === 'update_password') {
            $userId = $input['userId'] ?? 0;
            $oldPass = $input['old_password'] ?? '';
            $newPass = $input['new_password'] ?? '';

            if (empty($userId) || empty($oldPass) || empty($newPass)) {
                jsonResponse("error", "Semua kolom wajib diisi.");
            }
            if (strlen($newPass) < 8) {
                jsonResponse("error", "Password baru minimal harus 8 karakter.");
           }

            // 1. Verifikasi password lama
            $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($user = $result->fetch_assoc()) {
                if (password_verify($oldPass, $user['password'])) {
                    // 2. Jika benar, update ke password baru
                    $new_hashed_password = password_hash($newPass, PASSWORD_DEFAULT);
                    $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $update_stmt->bind_param("si", $new_hashed_password, $userId);
                    
                    if ($update_stmt->execute()) {
                        jsonResponse("success", "Kata sandi berhasil diperbarui.");
                    } else {
                        throw new Exception("Gagal memperbarui kata sandi: " . $update_stmt->error);
                    }
                } else {
                    jsonResponse("error", "Kata sandi lama Anda salah.");
                }
            } else {
                 jsonResponse("error", "User tidak ditemukan.");
            }
        }
        
        else {
            throw new Exception("Aksi tidak dikenal.");
        }

    } catch (Exception $e) {
        // Tangkap error umum (koneksi, query, dll)
        jsonResponse("error", "Error Server: " . $e->getMessage());
    }
}
?>