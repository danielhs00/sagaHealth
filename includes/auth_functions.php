<?php
// includes/auth_functions.php

ob_start();

// PERBAIKAN: Path ini diubah agar mencari di folder yang sama (includes)
require_once __DIR__ . '/koneksi.php'; 

header('Content-Type: application/json');

// Fungsi helper untuk merespons JSON dan keluar
function jsonResponse($status, $message, $data = []) {
// ... sisa file sama persis dengan yang ada di konteks Anda ...
// ... (Saya akan salin lengkap dari konteks untuk memastikan) ...
    ob_clean(); // Bersihkan buffer output sebelum mengirim JSON
    echo json_encode(array_merge(["status" => $status, "message" => $message], $data));
    exit;
}

try {
    // Cek koneksi dari db_connect.php
    global $db_error;
    if (isset($db_error)) {
        throw new Exception("Koneksi database gagal: " . $db_error);
    }
    if (empty($conn)) {
        throw new Exception("Objek koneksi database tidak ada.");
    }

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
        $phone = trim($input['phone'] ?? ''); // Menangkap nomor telepon

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
        // Status default 'Active' sudah diatur di DB, jadi kita bisa insert phone
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $hashed_password, $phone);
        
        if ($stmt->execute()) {
             jsonResponse("success", "Registrasi berhasil! Data Anda telah disimpan.");
        } else {
             throw new Exception("Gagal menyimpan data: " . $stmt->error);
        }
    }

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
?>