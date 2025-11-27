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
    <div class="auth-container-split reverse-mobile">
        <!-- Sisi Kiri: Form Register -->
        <div class="auth-form-side">
            <div class="auth-form-card">
                <div class="auth-header">
                    <h1>Buat Akun Baru</h1>
                    <p>Sudah punya akun? <a href="login.php">Masuk di sini</a></p>
                </div>

                <!-- Alert Message -->
                <div id="auth-message" class="auth-message" style="display: none;">
                    <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
                    <span id="message-text" role="status"></span>
                </div>

                <form id="register-form" novalidate>
                    <!-- Name Input -->
                    <div class="auth-input-group">
                        <label for="reg-name">Nama Lengkap</label>
                        <div class="input-with-icon">
                            <i class="fas fa-user" aria-hidden="true"></i>
                            <input 
                                type="text" 
                                id="reg-name" 
                                name="name"
                                placeholder="Nama Lengkap Anda" 
                                autocomplete="name"
                                required
                                minlength="3">
                        </div>
                        <span class="input-error" id="reg-name-error" aria-live="polite"></span>
                    </div>

                    <!-- Email Input -->
                    <div class="auth-input-group">
                        <label for="reg-email">Email</label>
                        <div class="input-with-icon">
                            <i class="fas fa-envelope" aria-hidden="true"></i>
                            <input 
                                type="email" 
                                id="reg-email" 
                                name="email"
                                placeholder="contoh@email.com" 
                                autocomplete="email"
                                required>
                        </div>
                        <span class="input-error" id="reg-email-error" aria-live="polite"></span>
                    </div>

                    <!-- Phone Input (use tel to support + and leading zeros) -->
                    <div class="auth-input-group">
                        <label for="reg-phone">Nomor HP</label>
                        <div class="input-with-icon">
                            <i class="fas fa-phone" aria-hidden="true"></i>
                            <input 
                                type="tel" 
                                id="reg-phone" 
                                name="phone"
                                placeholder="081234567890" 
                                autocomplete="tel"
                                required
                                pattern="^[0-9+\s\-()]{8,20}$">
                        </div>
                        <span class="input-error" id="reg-phone-error" aria-live="polite"></span>
                    </div>

                    <!-- Password Input -->
                    <div class="auth-input-group">
                        <label for="reg-password">Kata Sandi</label>
                        <div class="input-with-icon">
                            <i class="fas fa-lock" aria-hidden="true"></i>
<<<<<<< HEAD
=======
                            <br>
>>>>>>> 4b250f7 (benerin home,benerin dan integrasiin halaman login, nyiapin integrasi api payment jadi ke dana)
                            <input 
                                type="password" 
                                id="reg-password" 
                                name="password"
                                placeholder="Minimal 8 karakter" 
                                autocomplete="new-password"
                                required 
                                minlength="8">
<<<<<<< HEAD
                            <i class="fas fa-eye toggle-password" onclick="togglePassword('reg-password', this)" title="Tampilkan password" role="button" aria-label="toggle password visibility"></i>
=======
                                <button type="button" class="password-toggle-btn">
>>>>>>> 4b250f7 (benerin home,benerin dan integrasiin halaman login, nyiapin integrasi api payment jadi ke dana)
                        </div>
                        <span class="input-error" id="reg-password-error" aria-live="polite"></span>

                        <!-- Password Strength Indicator -->
                        <div class="password-strength" id="password-strength" style="display: none;">
                            <div class="strength-bar">
                                <div class="strength-bar-fill" id="strength-bar-fill"></div>
                            </div>
                            <span class="strength-text" id="strength-text"></span>
                        </div>
                    </div>

                    <!-- Confirm Password Input -->
                    <div class="auth-input-group">
                        <label for="reg-password-confirm">Konfirmasi Kata Sandi</label>
                        <div class="input-with-icon">
                            <i class="fas fa-lock" aria-hidden="true"></i>
<<<<<<< HEAD
=======
                            <br>
>>>>>>> 4b250f7 (benerin home,benerin dan integrasiin halaman login, nyiapin integrasi api payment jadi ke dana)
                            <input 
                                type="password" 
                                id="reg-password-confirm" 
                                name="password_confirm"
                                placeholder="Ketik ulang kata sandi" 
                                autocomplete="new-password"
                                required>
<<<<<<< HEAD
                            <i class="fas fa-eye toggle-password" onclick="togglePassword('reg-password-confirm', this)" title="Tampilkan password" role="button" aria-label="toggle password visibility"></i>
                        </div>
=======
                                <button type="button" class="password-toggle-btn">
                            </div>
>>>>>>> 4b250f7 (benerin home,benerin dan integrasiin halaman login, nyiapin integrasi api payment jadi ke dana)
                        <span class="input-error" id="reg-password-confirm-error" aria-live="polite"></span>
                    </div>

                    <!-- Terms Checkbox -->
                    <div class="auth-terms">
                        <input type="checkbox" id="reg-terms" required>
                        <label for="reg-terms">Saya menyetujui <a href="../partials/syarat_ketentuan.php" target="_blank" rel="noopener">Syarat & Ketentuan</a> dan <a href="../partials/pemberitahuan_privasi.php" target="_blank" rel="noopener">Kebijakan Privasi</a> SagaHealth.</label>
                    </div>
                    <span class="input-error" id="reg-terms-error" aria-live="polite"></span>

                    <!-- Submit Button -->
                    <button type="submit" class="auth-button" id="reg-btn" aria-live="polite">
                        <span class="btn-text">Daftar Sekarang</span>
                        <i class="fas fa-arrow-right"></i>
                    </button>

                    <!-- Back to Home -->
                    <div class="auth-footer">
                        <p>Kembali <a href="../dashboard/index.php">ke Beranda</a></p>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sisi Kanan: Gambar/Info -->
        <div class="auth-info-side register-side">
            <div class="auth-info-content">
                <img src="../assets/img/logo.png" alt="SagaHealth Logo" class="auth-logo" onerror="this.src='https://placehold.co/150x50/014C63/ffffff?text=SagaHealth'">
                <h2>Mulai Perjalanan Sehatmu</h2>
                <p>Bergabunglah dengan ribuan pengguna lain dan dapatkan akses ke layanan kesehatan terbaik.</p>
                
                <!-- Benefits -->
                <div class="auth-benefits">
                    <div class="benefit-item">
                        <i class="fas fa-check-circle" aria-hidden="true"></i>
                        <span>Akses 24/7 ke dokter profesional</span>
                    </div>
                    <div class="benefit-item">
                        <i class="fas fa-check-circle" aria-hidden="true"></i>
                        <span>Riwayat kesehatan tersimpan aman</span>
                    </div>
                    <div class="benefit-item">
                        <i class="fas fa-check-circle" aria-hidden="true"></i>
                        <span>Pengingat jadwal konsultasi</span>
                    </div>
                </div>
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
        const registerForm = document.getElementById('register-form');
        const authMessage = document.getElementById('auth-message');
        const messageText = document.getElementById('message-text');
        const regBtn = document.getElementById('reg-btn');
        const nameInput = document.getElementById('reg-name');
        const emailInput = document.getElementById('reg-email');
        const phoneInput = document.getElementById('reg-phone');
        const passwordInput = document.getElementById('reg-password');
        const passwordConfirmInput = document.getElementById('reg-password-confirm');
        const termsCheckbox = document.getElementById('reg-terms');
        const passwordStrength = document.getElementById('password-strength');
        const strengthBarFill = document.getElementById('strength-bar-fill');
        const strengthText = document.getElementById('strength-text');

        // Utility: show message
        function showMessage(message, type = 'error') {
            const icon = authMessage.querySelector('i');
            messageText.textContent = message;
            authMessage.className = 'auth-message ' + type;
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
            clearFieldError('reg-name');
            clearFieldError('reg-email');
            clearFieldError('reg-phone');
            clearFieldError('reg-password');
            clearFieldError('reg-password-confirm');
            clearFieldError('reg-terms');
            hideMessage();
        }

        // Email validator
        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        // Phone validator (simple)
        function isValidPhone(phone) {
            if (!phone) return false;
            const phoneNormalized = phone.replace(/\s+/g, '');
            const phoneRegex = /^[0-9+()\-]{8,20}$/;
            return phoneRegex.test(phoneNormalized);
        }

        // Password strength checker
        function checkPasswordStrength(password) {
            let strength = 0;
            if (password.length >= 8) strength += 1;
            if (password.length >= 12) strength += 1;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength += 1;
            if (/\d/.test(password)) strength += 1;
            if (/[^a-zA-Z\d]/.test(password)) strength += 1;

            let label = 'Lemah';
            let color = '#EF4444';
            if (strength <= 1) {
                label = 'Lemah'; color = '#EF4444';
            } else if (strength <= 3) {
                label = 'Sedang'; color = '#F59E0B';
            } else {
                label = 'Kuat'; color = '#10B981';
            }

            return { strength, label, color };
        }

        // Update password strength indicator
        passwordInput.addEventListener('input', () => {
            const password = passwordInput.value;
            if (password.length > 0) {
                passwordStrength.style.display = 'block';
                const { strength, label, color } = checkPasswordStrength(password);
                const percentage = (strength / 5) * 100;
                strengthBarFill.style.width = Math.max(6, percentage) + '%';
                strengthBarFill.style.backgroundColor = color;
                strengthText.textContent = label;
                strengthText.style.color = color;
            } else {
                passwordStrength.style.display = 'none';
            }
            if (password) clearFieldError('reg-password');
        });

        // Client-side validation
        function validateForm() {
            clearAllErrors();
            let valid = true;
            const name = nameInput.value.trim();
            const email = emailInput.value.trim();
            const phone = phoneInput.value.trim();
            const password = passwordInput.value;
            const passwordConfirm = passwordConfirmInput.value;
            const termsAccepted = termsCheckbox.checked;

            if (!name || name.length < 3) {
                showFieldError('reg-name', !name ? 'Nama lengkap harus diisi' : 'Nama minimal 3 karakter');
                valid = false;
            }

            if (!email) {
                showFieldError('reg-email', 'Email harus diisi');
                valid = false;
            } else if (!isValidEmail(email)) {
                showFieldError('reg-email', 'Format email tidak valid');
                valid = false;
            }

            if (!phone) {
                showFieldError('reg-phone', 'Nomor HP harus diisi');
                valid = false;
            } else if (!isValidPhone(phone)) {
                showFieldError('reg-phone', 'Format nomor HP tidak valid');
                valid = false;
            }

            if (!password) {
                showFieldError('reg-password', 'Kata sandi harus diisi');
                valid = false;
            } else if (password.length < 8) {
                showFieldError('reg-password', 'Kata sandi minimal 8 karakter');
                valid = false;
            }

            if (!passwordConfirm) {
                showFieldError('reg-password-confirm', 'Konfirmasi kata sandi harus diisi');
                valid = false;
            } else if (password !== passwordConfirm) {
                showFieldError('reg-password-confirm', 'Kata sandi tidak cocok');
                valid = false;
            }

            if (!termsAccepted) {
                showFieldError('reg-terms', 'Anda harus menyetujui syarat dan ketentuan');
                valid = false;
            }

            return valid;
        }

        // Real-time clears
        nameInput.addEventListener('input', () => { if (nameInput.value.trim()) clearFieldError('reg-name'); });
        emailInput.addEventListener('input', () => { if (emailInput.value.trim()) clearFieldError('reg-email'); });
        phoneInput.addEventListener('input', () => { if (phoneInput.value.trim()) clearFieldError('reg-phone'); });
        passwordConfirmInput.addEventListener('input', () => { if (passwordConfirmInput.value) clearFieldError('reg-password-confirm'); });
        termsCheckbox.addEventListener('change', () => { if (termsCheckbox.checked) clearFieldError('reg-terms'); });

        // Submit handler
        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            if (!validateForm()) return;

            const originalBtnHtml = regBtn.innerHTML;
            regBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span class="btn-text">Mendaftarkan...</span>';
            regBtn.disabled = true;
            clearAllErrors();

            const payload = {
                action: 'register',
                name: nameInput.value.trim(),
                email: emailInput.value.trim(),
                phone: phoneInput.value.trim(),
                password: passwordInput.value
            };

            try {
                const res = await fetch('../includes/auth_functions.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload),
                    credentials: 'include'
                });

                // If not ok, try parse message then throw
                if (!res.ok) {
                    let errMsg = 'Terjadi kesalahan server. Silakan coba lagi.';
                    try {
                        const errData = await res.json();
                        if (errData && errData.message) errMsg = errData.message;
                    } catch (_) { /* ignore parse error */ }
                    throw new Error(errMsg);
                }

                const data = await res.json();

                if (data && data.status === 'success') {
                    showMessage('Registrasi berhasil! Mengalihkan ke halaman login...', 'success');
                    registerForm.reset();
                    passwordStrength.style.display = 'none';

                    // Redirect to login or to server-provided URL
                    const redirectTo = (data.redirectUrl && typeof data.redirectUrl === 'string') ? data.redirectUrl : 'login.php';
                    setTimeout(() => { window.location.href = redirectTo; }, 1400);
                } else {
                    const msg = (data && data.message) ? data.message : 'Registrasi gagal. Silakan coba lagi.';
                    showMessage(msg, 'error');
                    regBtn.innerHTML = originalBtnHtml;
                    regBtn.disabled = false;
                }
            } catch (err) {
                console.error('Register error:', err);
                const friendly = err && err.message ? err.message : 'Terjadi kesalahan koneksi. Silakan coba lagi.';
                showMessage(friendly, 'error');
                regBtn.innerHTML = originalBtnHtml;
                regBtn.disabled = false;
            }
        });
    });
</script>
</body>
</html>
