<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk | SagaHealth</title>
    <link rel="icon" href="../assets/img/tittle.png" type="image/png">
    <link rel="stylesheet" href="../assets/style/auth.css">

    <script src="../assets/js/app.js"></script> 

    <script src="https://accounts.google.com/gsi/client" async defer></script>
</head>
<body>
    <div class="auth-container-split">
        <div class="auth-info-side login-side">
            <div class="auth-info-content">
                <img src="../assets/img/logo.png" alt="SagaHealth Logo" class="auth-logo" onerror="this.src='https://placehold.co/150x50/014C63/ffffff?text=SagaHealth'">
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
                            <input 
                                type="email" 
                                id="login-email" 
                                name="email"
                                placeholder="contoh@email.com" 
                                autocomplete="email"
                                required>
                        </div>
                        <span class="input-error" id="email-error"></span>
                    </div>

                    <div class="auth-input-group">
                        <div class="label-row">
                            <label for="login-password">Kata Sandi</label>
                            <a href="lupa_sandi.php" class="forgot-password">Lupa Kata Sandi?</a>
                        </div>
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input 
                                type="password" 
                                id="login-password" 
                                name="password"
                                placeholder="••••••••" 
                                autocomplete="current-password"
                                required>
                            <button type="button" class="password-toggle-btn">
                                <i class="fas fa-eye"></i> </button>
                        </div>
                        <span class="input-error" id="password-error"></span>
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

                <div class="divider-or" style="text-align:center; margin: 1.5rem 0; color:#aaa;">ATAU</div>

<<<<<<< HEAD
        function computeDefaultRedirect() {
            const userPlan = sessionStorage.getItem('userPlan') || localStorage.getItem('userPlan');
            // Jika premium → redirect ke dashboard premium
             if (userPlan === 'premium') {
                return '../dashboard/plan.php';
            }

    // Jika tidak → dashboard biasa
    return '../user/plan.php';
}

        document.addEventListener('DOMContentLoaded', () => {
            const loginForm = document.getElementById('login-form');
            const authMessage = document.getElementById('auth-message');
            const messageText = document.getElementById('message-text');
            const loginBtn = document.getElementById('login-btn');
            const emailInput = document.getElementById('login-email');
            const passwordInput = document.getElementById('login-password');
=======
                <div id="g_id_onload"
                    data-client_id="542615675120-ghv1c22amb2v5mnq9uesqsp122jq2nrc.apps.googleusercontent.com" 
                    data-context="signin"
                    data-ux_mode="popup"
                    data-callback="handleGoogleSignIn" data-auto_prompt="false">
                </div>
>>>>>>> 4b250f7 (benerin home,benerin dan integrasiin halaman login, nyiapin integrasi api payment jadi ke dana)

                <div class="g_id_signin"
                    data-type="standard"
                    data-shape="rectangular"
                    data-theme="outline"
                    data-text="signin_with"
                    data-size="large"
                    data-logo_alignment="left"
                    style="width: 100%;"> 
                </div>
            </div> </div> </div> <script>
        // HANYA simpan fungsi-fungsi yang TIDAK ada di app.js di sini. 
        // Contoh: fungsi-fungsi DOMContentLoaded dan helpers login biasa Anda.

<<<<<<< HEAD
            // Hide message function
            function hideMessage() {
                authMessage.style.display = 'none';
            }

            // Show field error
            function showFieldError(fieldId, message) {
                const errorElement = document.getElementById(fieldId + '-error');
                const inputElement = document.getElementById(fieldId);
                if (errorElement && inputElement) {
                    errorElement.textContent = message;
                    errorElement.style.display = 'block';
                    inputElement.classList.add('input-error-state');
                }
            }

            // Clear field error
            function clearFieldError(fieldId) {
                const errorElement = document.getElementById(fieldId + '-error');
                const inputElement = document.getElementById(fieldId);
                if (errorElement && inputElement) {
                    errorElement.textContent = '';
                    errorElement.style.display = 'none';
                    inputElement.classList.remove('input-error-state');
                }
            }

            // Clear all errors
            function clearAllErrors() {
                clearFieldError('login-email');
                clearFieldError('login-password');
                hideMessage();
            }

            // Validate email format
            function isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }

            // Client-side validation
            function validateForm() {
                clearAllErrors();
                let isValid = true;

                const email = emailInput.value.trim();
                const password = passwordInput.value;

                // Validate email
                if (!email) {
                    showFieldError('login-email', 'Email harus diisi');
                    isValid = false;
                } else if (!isValidEmail(email)) {
                    showFieldError('login-email', 'Format email tidak valid');
                    isValid = false;
                }

                // Validate password
                if (!password) {
                    showFieldError('login-password', 'Kata sandi harus diisi');
                    isValid = false;
                } else if (password.length < 6) {
                    showFieldError('login-password', 'Kata sandi minimal 6 karakter');
                    isValid = false;
                }

                return isValid;
            }

            // Real-time validation on input
            emailInput.addEventListener('input', () => {
                if (emailInput.value.trim()) {
                    clearFieldError('login-email');
                }
            });

            passwordInput.addEventListener('input', () => {
                if (passwordInput.value) {
                    clearFieldError('login-password');
                }
            });

            // Form submission
            loginForm.addEventListener('submit', async (e) => {
                e.preventDefault();

                // Validate form
                if (!validateForm()) {
                    return;
                }

                // Disable button and show loading
                const originalText = loginBtn.innerHTML;
                loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span class="btn-text">Memproses...</span>';
                loginBtn.disabled = true;
                clearAllErrors();

                const email = emailInput.value.trim();
                const password = passwordInput.value;
                const remember = document.getElementById('remember-me').checked;

                try {
                    const res = await fetch('../includes/auth_functions.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ 
                            action: 'login', 
                            email, 
                            password,
                            remember 
                        })
                    });

                    // Check if response is ok
                    if (!res.ok) {
                        throw new Error('Server error: ' + res.status);
                    }

                    const data = await res.json();

                    if (data.status === 'success') {
                        // Store session data
                        sessionStorage.setItem('isLoggedIn', 'true');
                        sessionStorage.setItem('userId', data.user.id);
                        sessionStorage.setItem('userName', data.user.name);
                        sessionStorage.setItem('userEmail', data.user.email);
                        
                        // If remember me is checked, store in localStorage
                        if (remember) {
                            localStorage.setItem('rememberUser', 'true');
                            localStorage.setItem('userId', data.user.id);
                        }

                    showMessage('Login berhasil! Mengalihkan...', 'success');

                    // Use server-provided redirect if available, otherwise fallback
                    const redirectUrl = (data.redirectUrl && typeof data.redirectUrl === 'string')
                        ? data.redirectUrl
                        : computeDefaultRedirect();

                    setTimeout(() => {
                        window.location.href = redirectUrl;
                    }, 1200);
                } else {
                    // Login failed: show server message if provided
                    const msg = (data && data.message) ? data.message : 'Login gagal. Silakan coba lagi.';
                    showMessage(msg, 'error');
                    loginBtn.innerHTML = originalText;
                    loginBtn.disabled = false;
                }
            } catch (err) {
                console.error('Login error:', err);
                const friendly = err && err.message ? err.message : 'Terjadi kesalahan koneksi. Silakan coba lagi.';
                showMessage(friendly, 'error');
                loginBtn.innerHTML = originalText;
                loginBtn.disabled = false;
            }
        });

        // Auto-redirect if already logged in (session or remember)
        const alreadyLoggedIn = sessionStorage.getItem('isLoggedIn') === 'true' ||
                                localStorage.getItem('rememberUser') === 'true';
        if (alreadyLoggedIn) {
            showMessage('Anda sudah login. Mengalihkan...', 'info');
            // If localStorage has userId and a plan page exists, optionally redirect there.
            const fallbackRedirect = computeDefaultRedirect();
            setTimeout(() => {
                window.location.href = fallbackRedirect;
            }, 900);
        }
    });
</script>
=======
        // ... semua fungsi Anda yang lain (togglePassword, computeDefaultRedirect, dll) ...
        
        // Catatan: Jika Anda memindahkan logic login ini ke app.js, 
        // pastikan file app.js di <head> adalah satu-satunya sumber JavaScript.
        
        // Untuk saat ini, saya akan menyarankan Anda untuk memindahkan semua logika login ini ke app.js 
        // agar hanya ada satu sumber kebenaran.
    </script>
>>>>>>> 4b250f7 (benerin home,benerin dan integrasiin halaman login, nyiapin integrasi api payment jadi ke dana)
</body>
</html>