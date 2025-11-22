<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>SagaHealth</title>
    <link rel="icon" href="../assets/img/tittle.png" type="image/png" />
    <link rel="stylesheet" href="../assets/style/auth.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>
    <div class="auth-container-split">
        <!-- Sisi Kiri: Gambar/Info -->
        <div class="auth-info-side login-side">
            <div class="auth-info-content">
                <img src="../assets/img/logo.png" alt="SagaHealth Logo" class="auth-logo"
                     onerror="this.src='https://placehold.co/150x50/014C63/ffffff?text=SagaHealth'">
                <h2>Selamat Datang Kembali!</h2>
                <p>Masuk untuk melanjutkan perjalanan kesehatan Anda bersama SagaHealth.</p>

                <!-- Fitur tambahan di info side -->
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

        <!-- Sisi Kanan: Form Login -->
        <div class="auth-form-side">
            <div class="auth-form-card">
                <div class="auth-header">
                    <h1>Masuk Akun</h1>
                    <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
                </div>

                <!-- Alert Message -->
                <div id="auth-message" class="auth-message" style="display: none;">
                    <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
                    <span id="message-text" role="status"></span>
                </div>

                <form id="login-form" novalidate>
                    <!-- Email Input -->
                    <div class="auth-input-group">
                        <label for="login-email">Email</label>
                        <div class="input-with-icon">
                            <i class="fas fa-envelope" aria-hidden="true"></i>
                            <input
                                type="email"
                                id="login-email"
                                name="email"
                                placeholder="contoh@email.com"
                                autocomplete="email"
                                required>
                        </div>
                        <span class="input-error" id="login-email-error" aria-live="polite"></span>
                    </div>

                    <!-- Password Input -->
                    <div class="auth-input-group">
                        <div class="label-row">
                            <label for="login-password">Kata Sandi</label>
                            <a href="forgot-password.php" class="forgot-password">Lupa Kata Sandi?</a>
                        </div>
                        <div class="input-with-icon">
                            <i class="fas fa-lock" aria-hidden="true"></i>
                            <input
                                type="password"
                                id="login-password"
                                name="password"
                                placeholder="••••••••"
                                autocomplete="current-password"
                                required>
                            <i class="fas fa-eye toggle-password" onclick="togglePassword('login-password', this)"
                               title="Tampilkan password" role="button" aria-label="toggle password visibility"></i>
                        </div>
                        <span class="input-error" id="login-password-error" aria-live="polite"></span>
                    </div>

                    <!-- Remember Me -->
                    <div class="auth-checkbox">
                        <input type="checkbox" id="remember-me" name="remember">
                        <label for="remember-me">Ingat saya</label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="auth-button" id="login-btn" aria-live="polite">
                        <span class="btn-text">Masuk Sekarang</span>
                        <i class="fas fa-arrow-right"></i>
                    </button>

                    <!-- Back to Home -->
                    <div class="auth-footer">
                        <p>Kembali <a href="../dashboard/index.php">ke Beranda</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script>
    // Toggle Password Visibility
    function togglePassword(inputId, icon) {
        const input = document.getElementById(inputId);
        if (!input) return;
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
            icon.title = "Sembunyikan password";
        } else {
            input.type = "password";
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
            icon.title = "Tampilkan password";
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const loginForm = document.getElementById('login-form');
        const authMessage = document.getElementById('auth-message');
        const messageText = document.getElementById('message-text');
        const loginBtn = document.getElementById('login-btn');
        const emailInput = document.getElementById('login-email');
        const passwordInput = document.getElementById('login-password');
        const rememberCheckbox = document.getElementById('remember-me');

        // Utility: show message
        function showMessage(message, type = 'error') {
            const icon = authMessage.querySelector('i');
            messageText.textContent = message;
            authMessage.className = 'auth-message ' + type;

            // Update icon based on type
            if (type === 'success') {
                icon.className = 'fas fa-check-circle';
            } else if (type === 'error') {
                icon.className = 'fas fa-exclamation-circle';
            } else {
                icon.className = 'fas fa-info-circle';
            }

            authMessage.style.display = 'flex';
            authMessage.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        // Utility: hide message
        function hideMessage() {
            authMessage.style.display = 'none';
        }

        // Show field error
        function showFieldError(fieldEl, message) {
            if (!fieldEl) return;
            const errorElement = document.getElementById(fieldEl.id + '-error');
            if (errorElement) {
                errorElement.textContent = message;
                errorElement.style.display = 'block';
            }
            fieldEl.classList.add('input-error-state');
        }

        // Clear field error
        function clearFieldError(fieldEl) {
            if (!fieldEl) return;
            const errorElement = document.getElementById(fieldEl.id + '-error');
            if (errorElement) {
                errorElement.textContent = '';
                errorElement.style.display = 'none';
            }
            fieldEl.classList.remove('input-error-state');
        }

        // Clear all errors
        function clearAllErrors() {
            clearFieldError(emailInput);
            clearFieldError(passwordInput);
            hideMessage();
        }

        // Email validator
        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        // Validate form
        function validateForm() {
            clearAllErrors();
            let valid = true;
            const email = emailInput.value.trim();
            const password = passwordInput.value;

            if (!email) {
                showFieldError(emailInput, 'Email harus diisi');
                valid = false;
            } else if (!isValidEmail(email)) {
                showFieldError(emailInput, 'Format email tidak valid');
                valid = false;
            }

            if (!password) {
                showFieldError(passwordInput, 'Kata sandi harus diisi');
                valid = false;
            } else if (password.length < 6) {
                showFieldError(passwordInput, 'Kata sandi minimal 6 karakter');
                valid = false;
            }

            return valid;
        }

        // Real-time clearing
        emailInput.addEventListener('input', () => {
            if (emailInput.value.trim()) clearFieldError(emailInput);
        });
        passwordInput.addEventListener('input', () => {
            if (passwordInput.value) clearFieldError(passwordInput);
        });

        // Compute redirect fallback (server can override by returning data.redirectUrl)
        function computeDefaultRedirect() {
            // prefer dashboard by default
            return '../user/dashboard.php';
        }

        // Submit handler
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            if (!validateForm()) return;

            // Disable UI while processing
            const originalBtnHtml = loginBtn.innerHTML;
            loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span class="btn-text">Memproses...</span>';
            loginBtn.disabled = true;

            const payload = {
                action: 'login',
                email: emailInput.value.trim(),
                password: passwordInput.value,
                remember: !!rememberCheckbox.checked
            };

            try {
                const res = await fetch('../includes/auth_functions.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload),
                    credentials: 'include'
                });

                // If server returned non-2xx, try to parse message, else show generic
                if (!res.ok) {
                    let errMsg = 'Terjadi kesalahan server. Silakan coba lagi.';
                    try {
                        const errData = await res.json();
                        if (errData && errData.message) errMsg = errData.message;
                    } catch (_) { /* ignore parse error */ }
                    throw new Error(errMsg);
                }

                const data = await res.json();

                if (data && data.status === 'success' && data.user) {
                    // Save session info (short-lived)
                    sessionStorage.setItem('isLoggedIn', 'true');
                    sessionStorage.setItem('userId', String(data.user.id));
                    sessionStorage.setItem('userName', data.user.name || '');
                    sessionStorage.setItem('userEmail', data.user.email || '');

                    // Remember me: store minimal non-sensitive info
                    if (payload.remember) {
                        localStorage.setItem('rememberUser', 'true');
                        localStorage.setItem('userId', String(data.user.id));
                        localStorage.setItem('userName', data.user.name || '');
                        localStorage.setItem('userEmail', data.user.email || '');
                    } else {
                        // clear any previous remember flags
                        localStorage.removeItem('rememberUser');
                        localStorage.removeItem('userId');
                        localStorage.removeItem('userName');
                        localStorage.removeItem('userEmail');
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
                    loginBtn.innerHTML = originalBtnHtml;
                    loginBtn.disabled = false;
                }
            } catch (err) {
                console.error('Login error:', err);
                const friendly = err && err.message ? err.message : 'Terjadi kesalahan koneksi. Silakan coba lagi.';
                showMessage(friendly, 'error');
                loginBtn.innerHTML = originalBtnHtml;
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
</body>
</html>
