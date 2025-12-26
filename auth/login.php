<?php
session_start();

// SESSION GUARD: Jika user sudah login, arahkan ke dashboard yang sesuai
if (isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn'] === true) {
    $plan = $_SESSION['plan_type'] ?? 'none';
    
    if ($plan === 'premium') {
        header("Location: ../user/dashboard_premium.php");
    } elseif ($plan === 'basic') {
        header("Location: ../user/dashboard_basic.php");
    } else {
        header("Location: ../user/plan.php");
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk | SagaHealth</title>

    <link rel="icon" href="../assets/img/tittle.png" type="image/png">
    
    <link rel="stylesheet" href="../assets/style/auth.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="https://accounts.google.com/gsi/client" async defer></script>
</head>

<body>
<div class="auth-container-split">

    <div class="auth-info-side login-side">
        <div class="auth-info-content">
            <img src="../assets/img/logo.png" alt="SagaHealth Logo" class="auth-logo"
                 onerror="this.src='https://placehold.co/200x70/014C63/ffffff?text=SagaHealth'">
            <h2>Selamat Datang Kembali!</h2>
            <p>Masuk untuk melanjutkan perjalanan kesehatan Anda bersama SagaHealth.</p>

            <div class="auth-features">
                <div class="feature-item">
                    <i class="fas fa-shield-alt"></i>
                    <span>Keamanan Data Terjamin</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-user-md"></i>
                    <span>Konsultasi Ahli</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-robot"></i>
                    <span>AI Assistant 24/7</span>
                </div>
            </div>
        </div>
    </div>

    <div class="auth-form-side">
        <div class="auth-form-card">

            <div class="auth-header">
                <h1>Masuk Akun</h1>
                <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
            </div>

            <div id="auth-message" class="auth-message" style="display: none;">
                <i class="fas fa-exclamation-circle"></i>
                <span id="message-text"></span>
            </div>

            <form id="login-form" novalidate>

                <div class="auth-input-group">
                    <label for="login-email">Email</label>
                    <div class="input-with-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="login-email" name="email" placeholder="contoh@email.com"
                               autocomplete="email" required>
                    </div>
                    <span class="input-error" id="login-email-error"></span>
                </div>

                <div class="auth-input-group">
                    <div class="label-row">
                        <label for="login-password">Kata Sandi</label>
                        <a href="lupa_sandi.php" class="forgot-password">Lupa Kata Sandi?</a>
                    </div>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="login-password" name="password" placeholder="••••••••"
                               autocomplete="current-password" required>
                        <i class="fas fa-eye toggle-password" onclick="togglePasswordVisibility()" title="Lihat Password"></i>
                    </div>
                    <span class="input-error" id="login-password-error"></span>
                </div>

                <div class="auth-checkbox">
                    <input type="checkbox" id="remember-me" name="remember">
                    <label for="remember-me">Ingat saya</label>
                </div>

                <button type="submit" class="auth-button" id="login-btn">
                    <span class="btn-text">Masuk Sekarang</span>
                    <i class="fas fa-arrow-right"></i>
                </button>

                <div class="auth-footer">
                    <p>Kembali <a href="../dashboard/index.php">ke Beranda</a></p>
                </div>
            </form>

            <div class="divider-or">ATAU</div>

            <div id="g_id_onload"
                 data-client_id="542615675120-ghv1c22amb2v5mnq9uesqsp122jq2nrc.apps.googleusercontent.com"
                 data-context="signin"
                 data-ux_mode="popup"
                 data-callback="handleGoogleSignIn"
                 data-auto_prompt="false">
            </div>

            <div class="g_id_signin"
                 data-type="standard"
                 data-shape="rectangular"
                 data-theme="outline"
                 data-text="signin_with"
                 data-size="large"
                 data-logo_alignment="left"
                 style="width: 100%;">
            </div>

        </div>
    </div>
</div>

<script>
    // ------------------------------------------------------------
    // 1. TOGGLE PASSWORD VISIBILITY
    // ------------------------------------------------------------
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('login-password');
        const icon = document.querySelector('.toggle-password');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // ------------------------------------------------------------
    // 2. GOOGLE SIGN-IN HANDLER (SUDAH DIPERBAIKI)
    // ------------------------------------------------------------
    async function handleGoogleSignIn(response) {
        console.log("Google JWT Credential received.");

        // UI Loading
        const loginBtn = document.getElementById('login-btn');
        const originalBtnHtml = loginBtn.innerHTML;
        loginBtn.disabled = true;
        loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memverifikasi Google...';

        try {
            // Panggil API Backend dengan parameter yang BENAR
            const res = await fetch('../includes/auth_functions.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    action: 'google_login',
                    id_token: response.credential // <--- Sesuai dengan yang diminta PHP
                })
            });
            
            const data = await res.json();
            
            // Kembalikan tombol normal
            loginBtn.disabled = false;
            loginBtn.innerHTML = originalBtnHtml;

            // Proses Respon
            handleLoginResponse(data);

        } catch (error) {
            console.error(error);
            loginBtn.disabled = false;
            loginBtn.innerHTML = originalBtnHtml;
            showMessage('Gagal menghubungi server untuk login Google.', 'error');
        }
    }

    // ------------------------------------------------------------
    // 3. MAIN LOGIC & HANDLER
    // ------------------------------------------------------------
    const authMessage = document.getElementById('auth-message');
    const messageText = document.getElementById('message-text');

    function showMessage(msg, type = 'error') {
        authMessage.className = `auth-message ${type}`; 
        authMessage.style.display = 'flex';
        messageText.textContent = msg;
    }

    function hideMessage() {
        authMessage.style.display = 'none';
    }

    // Fungsi Handler Pusat setelah Login Berhasil
    function handleLoginResponse(data) {
        if (data.status === 'success') {
            showMessage('Login berhasil! Mengalihkan...', 'success');
            
            // Simpan session client-side (opsional)
            sessionStorage.setItem('isLoggedIn', 'true');
            if(data.user) {
                sessionStorage.setItem('userName', data.user.name);
                sessionStorage.setItem('planType', data.user.plan_type); 
            }

            // REDIRECT: Prioritaskan URL dari server (auth_functions.php)
            let targetUrl = data.redirectUrl;
            
            if (!targetUrl) {
                // Fallback jika server lupa kirim URL
                const plan = data.user?.plan_type || 'none';
                if (plan === 'premium') targetUrl = '../user/dashboard_premium.php';
                else if (plan === 'basic') targetUrl = '../user/dashboard_basic.php';
                else targetUrl = '../user/plan.php';
            }

            setTimeout(() => { window.location.href = targetUrl; }, 1000);
        } else {
            showMessage(data.message || 'Login gagal.', 'error');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const loginForm = document.getElementById('login-form');
        const loginBtn = document.getElementById('login-btn');

        // Submit Form Login Biasa
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            hideMessage();

            const email = document.getElementById('login-email').value.trim();
            const password = document.getElementById('login-password').value.trim();

            if (!email || !password) {
                showMessage('Email dan password wajib diisi.', 'error');
                return;
            }

            // UI Loading
            const originalBtnHtml = loginBtn.innerHTML;
            loginBtn.disabled = true;
            loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span class="btn-text">Memproses...</span>';

            try {
                const res = await fetch('../includes/auth_functions.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({
                        action: 'login',
                        email: email,
                        password: password
                    })
                });

                if (!res.ok) throw new Error('Terjadi kesalahan server.');
                
                const data = await res.json();
                loginBtn.disabled = false;
                loginBtn.innerHTML = originalBtnHtml;
                
                handleLoginResponse(data);

            } catch (err) {
                console.error(err);
                showMessage('Terjadi kesalahan koneksi. Silakan coba lagi.', 'error');
                loginBtn.disabled = false;
                loginBtn.innerHTML = originalBtnHtml;
            }
        });
    });
</script>
</body>
</html>