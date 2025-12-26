<?php
// includes/auth_functions.php

// 1. Mulai sesi jika belum aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Gunakan ob_start() untuk mencegah output tidak sengaja merusak JSON
ob_start();

// Pastikan path ke koneksi benar
require_once __DIR__ . '/koneksi.php'; 

// Fungsi helper untuk kirim JSON
function jsonResponse($status, $message, $data = []) {
    ob_clean(); // Bersihkan buffer sebelum kirim JSON
    echo json_encode(array_merge(["status" => $status, "message" => $message], $data));
    exit;
}

// =================================================================
// LOGIKA API UTAMA
// =================================================================
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    
    header('Content-Type: application/json');

    try {
        global $conn;
        if (empty($conn)) {
            throw new Exception("Koneksi database gagal.");
        }

        $input = json_decode(file_get_contents('php://input'), true);

        // --- ACTION: GET PROFILE (Metode GET) ---
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_profile') {
            $userId = $_GET['userId'] ?? 0;
            if (empty($userId)) throw new Exception("User ID tidak ada.");

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

        // Validasi Request POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new Exception("Metode request tidak valid.");
        }
        if (!$input || !isset($input['action'])) {
            throw new Exception("Aksi tidak valid.");
        }

        $action = $input['action'];

        // --- ACTION: REGISTER (Manual) ---
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

            // Simpan user baru
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone, status) VALUES (?, ?, ?, ?, 'active')");
            $stmt->bind_param("ssss", $name, $email, $hashed_password, $phone);
            
            if ($stmt->execute()) {
                 jsonResponse("success", "Registrasi berhasil! Silakan login.");
            } else {
                 throw new Exception("Gagal menyimpan data: " . $stmt->error);
            }
        }

        // --- ACTION: GOOGLE LOGIN (FIXED) ---
        elseif ($action === 'google_login') {
            $id_token = $input['id_token'] ?? '';
            
            if (empty($id_token)) {
                jsonResponse("error", "ID Token tidak diterima.");
            }

            // 1. Verifikasi Token ke Google
            $google_response = @file_get_contents("https://oauth2.googleapis.com/tokeninfo?id_token=" . $id_token);
            if ($google_response === false) {
                 jsonResponse("error", "Gagal verifikasi token ke Google. Cek koneksi server.");
            }

            $user_data = json_decode($google_response, true);
            
            // PENTING: Sesuaikan Client ID ini dengan yang ada di Google Cloud Console Anda
            $CLIENT_ID = "542615675120-ghv1c22amb2v5mnq9uesqsp122jq2nrc.apps.googleusercontent.com"; 
            
            // Validasi Audience
            if (!isset($user_data['email']) || $user_data['aud'] !== $CLIENT_ID) {
                 jsonResponse("error", "Token Google tidak valid untuk Client ID ini.");
            }
            
            $email = $user_data['email'];
            // Gunakan nama dari Google, atau fallback ke bagian depan email
            $name = $user_data['name'] ?? explode('@', $email)[0];
            
            // 2. Cek User di Database
            $stmt = $conn->prepare("SELECT id, name, email, status FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();

            if ($user) {
                // User SUDAH ADA -> Update last_login
                $user_id = $user['id'];
                $conn->query("UPDATE users SET last_login = NOW() WHERE id = $user_id");
            } else {
                // User BARU -> Buat akun otomatis
                $default_password = bin2hex(random_bytes(16)); 
                $hashed_password = password_hash($default_password, PASSWORD_DEFAULT);
                
                $stmt_ins = $conn->prepare("INSERT INTO users (name, email, password, status) VALUES (?, ?, ?, 'active')");
                $stmt_ins->bind_param("sss", $name, $email, $hashed_password);
                
                if ($stmt_ins->execute()) {
                    $user_id = $conn->insert_id;
                    $user = ['id' => $user_id, 'name' => $name, 'email' => $email, 'status' => 'active'];
                } else {
                     throw new Exception("Gagal membuat user Google: " . $stmt_ins->error);
                }
                $stmt_ins->close();
            }

            // 3. Cek Status Langganan (Subscriptions)
            $stmt_sub = $conn->prepare("
                SELECT plan_name 
                FROM subscriptions 
                WHERE user_id = ? AND status = 'active' AND end_date > NOW() 
                ORDER BY end_date DESC LIMIT 1
            ");
            $stmt_sub->bind_param("i", $user_id);
            $stmt_sub->execute();
            $res_sub = $stmt_sub->get_result();
            $active_sub = $res_sub->fetch_assoc();
            $stmt_sub->close();

            $plan_type = $active_sub ? strtolower($active_sub['plan_name']) : 'none';

            // 4. Set Session PHP
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_name'] = $user['name'] ?? $name;
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['plan_type'] = $plan_type;
            $_SESSION['isLoggedIn'] = true;

            // 5. Tentukan Redirect URL
            $redirectUrl = '../user/plan.php'; // Default untuk user baru/tanpa paket
            if ($plan_type === 'premium') {
                $redirectUrl = '../user/dashboard_premium.php';
            } elseif ($plan_type === 'basic') {
                $redirectUrl = '../user/dashboard_basic.php';
            }

            jsonResponse("success", "Login Google berhasil!", [
                "user" => array_merge($user, ['plan_type' => $plan_type]),
                "redirectUrl" => $redirectUrl
            ]);
        }

        // --- ACTION: LOGIN BIASA ---
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
                if (password_verify($password, $user['password'])) {
                    
                    // Update last_login
                    $conn->query("UPDATE users SET last_login = NOW() WHERE id = {$user['id']}");

                    // Cek Subscription
                    $stmt_sub = $conn->prepare("
                        SELECT plan_name 
                        FROM subscriptions 
                        WHERE user_id = ? AND status = 'active' AND end_date > NOW() 
                        ORDER BY end_date DESC LIMIT 1
                    ");
                    $stmt_sub->bind_param("i", $user['id']);
                    $stmt_sub->execute();
                    $res_sub = $stmt_sub->get_result();
                    $active_sub = $res_sub->fetch_assoc();
                    $stmt_sub->close();

                    $plan_type = $active_sub ? strtolower($active_sub['plan_name']) : 'none';

                    // Set Session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['plan_type'] = $plan_type;
                    $_SESSION['isLoggedIn'] = true;

                    // Tentukan Redirect URL
                    $redirectUrl = '../user/plan.php';
                    if ($plan_type === 'premium') {
                        $redirectUrl = '../user/dashboard_premium.php';
                    } elseif ($plan_type === 'basic') {
                        $redirectUrl = '../user/dashboard_basic.php';
                    }

                    unset($user['password']);
                    jsonResponse("success", "Login berhasil!", [
                        "user" => array_merge($user, ['plan_type' => $plan_type]),
                        "redirectUrl" => $redirectUrl
                    ]);
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

            $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($user = $result->fetch_assoc()) {
                if (password_verify($oldPass, $user['password'])) {
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
        jsonResponse("error", "Error Server: " . $e->getMessage());
    }
}
?>