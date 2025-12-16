<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk | SagaHealth</title>

    <link rel="icon" href="../assets/img/tittle.png" type="image/png">
    <link rel="stylesheet" href="../assets/style/auth.css">

    <!-- Main JS -->
    <script src="../assets/js/app.js"></script>

    <!-- Google OAuth -->
    <script src="https://accounts.google.com/gsi/client" async defer></script>
</head>

<body>
<div class="auth-container-split">

    <!-- LEFT INFO SIDE -->
    <div class="auth-info-side login-side">
        <div class="auth-info-content">
            <img src="../assets/img/logo.png" alt="SagaHealth Logo" class="auth-logo"
                 onerror="this.src='https://placehold.co/200x70/014C63/ffffff?text=SagaHealth'">
            <h2>Selamat Datang Kembali!</h2>
            <p>Masuk untuk melanjutkan perjalanan kesehatan Anda bersama SagaHealth.</p>

            <div class="auth-features">
                <div class="feature-item">
                    <i class="fas fa-shield-alt"></i>
                    <span>Keamanan Terjamin</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-user-md"></i>
                    <span>Konsultasi Dokter</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-calendar-check"></i>
                    <span>Jadwal Fleksibel</span>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT FORM SIDE -->
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

            <!-- LOGIN FORM -->
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
                        <button type="button" class="password-toggle-btn">
                            <i class="fas fa-eye"></i>
                        </button>
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

            <div class="divider-or" style="text-align:center; margin:1.5rem 0; color:#aaa;">ATAU</div>

            <!-- GOOGLE LOGIN -->
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
                 style="width:100%;">
            </div>

        </div>
    </div>
</div>

<script>
// ------------------------------------------------------------
// HELPER: Redirect Default
// ------------------------------------------------------------
function computeDefaultRedirect() {
    const userPlan = sessionStorage.getItem('userPlan') || localStorage.getItem('userPlan');
    return (userPlan === 'premium')
        ? '../dashboard/plan.php'
        : '../user/plan.php';
}

// ------------------------------------------------------------
// DOM READY LOGIN LOGIC
// ------------------------------------------------------------
document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('login-form');
    const loginBtn = document.getElementById('login-btn');
    const authMessage = document.getElementById('auth-message');
    const messageText = document.getElementById('message-text');

    const emailInput = document.getElementById('login-email');
    const passwordInput = document.getElementById('login-password');

    //-------------------------
    // Show Message
    //-------------------------
    function showMessage(msg, type = 'error') {
        authMessage.className = `auth-message ${type}`;
        authMessage.style.display = 'flex';
        messageText.textContent = msg;
    }

    function hideMessage() {
        authMessage.style.display = 'none';
    }

    //-------------------------
    // Field Error Helpers
    //-------------------------
    function showFieldError(field, msg) {
        document.getElementById(`${field}-error`).textContent = msg;
    }

    function clearFieldError(field) {
        document.getElementById(`${field}-error`).textContent = '';
    }

    function clearAllErrors() {
        clearFieldError('login-email');
        clearFieldError('login-password');
        hideMessage();
    }

    //-------------------------
    // Email validation
    //-------------------------
    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    //-------------------------
    // Form Validation
    //-------------------------
    function validateForm() {
        clearAllErrors();
        let valid = true;

        const email = emailInput.value.trim();
        const pass = passwordInput.value.trim();

        if (!email) {
            showFieldError('login-email', 'Email harus diisi');
            valid = false;
        } else if (!isValidEmail(email)) {
            showFieldError('login-email', 'Format email tidak valid');
            valid = false;
        }

        if (!pass) {
            showFieldError('login-password', 'Kata sandi harus diisi');
            valid = false;
        } else if (pass.length < 6) {
            showFieldError('login-password', 'Minimal 6 karakter');
            valid = false;
        }

        return valid;
    }

    //-------------------------
    // Real-time validation
    //-------------------------
    emailInput.addEventListener('input', () => clearFieldError('login-email'));
    passwordInput.addEventListener('input', () => clearFieldError('login-password'));

    //-------------------------
    // Submit Login Form
    //-------------------------
    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (!validateForm()) return;

        const originalBtn = loginBtn.innerHTML;
        loginBtn.disabled = true;
        loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span class="btn-text">Memproses...</span>';

        try {
            const res = await fetch('../includes/auth_functions.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    action: 'login',
                    email: emailInput.value.trim(),
                    password: passwordInput.value.trim(),
                    remember: document.getElementById('remember-me').checked
                })
            });

            if (!res.ok) throw new Error('Server error: ' + res.status);
            const data = await res.json();

            if (data.status === 'success') {
                sessionStorage.setItem('isLoggedIn', 'true');
                sessionStorage.setItem('userId', data.user.id);
                sessionStorage.setItem('userName', data.user.name);
                sessionStorage.setItem('userEmail', data.user.email);

                if (data.user.plan) {
                    sessionStorage.setItem('userPlan', data.user.plan);
                }

                showMessage('Login berhasil! Mengalihkan...', 'success');

                const redirectUrl = data.redirectUrl || computeDefaultRedirect();
                setTimeout(() => window.location.href = redirectUrl, 1200);

            } else {
                showMessage(data.message || 'Login gagal.');
                loginBtn.disabled = false;
                loginBtn.innerHTML = originalBtn;
            }

        } catch (err) {
            showMessage('Terjadi kesalahan koneksi, coba lagi.');
            loginBtn.disabled = false;
            loginBtn.innerHTML = originalBtn;
        }
    });
});
</script>
</body>
</html>
