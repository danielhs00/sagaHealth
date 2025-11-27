// plan.js — Merged, optimized version

/* --------------------------------------------------------
   GLOBAL STATE
--------------------------------------------------------- */
let currentPlan = {
    name: '',
    price: '',
    type: '',
    billing: 'monthly'
};

const PAYMENT_URLS = {
    // static fallback payment links (client-side redirect)
<<<<<<< HEAD
    basic: 'https://pay.doku.com/p-link/p/95TgIPK2v4',
    premium: 'https://pay.doku.com/p-link/p/gwDpiBLhsH'
=======
    basic: '',
    premium: ''
>>>>>>> 4b250f7 (benerin home,benerin dan integrasiin halaman login, nyiapin integrasi api payment jadi ke dana)
};

/* --------------------------------------------------------
   INITIALIZATION
--------------------------------------------------------- */
document.addEventListener('DOMContentLoaded', () => {
    initializePage();
    setupEventListeners();
});

/* --------------------------------------------------------
   PAGE INITIALIZER
--------------------------------------------------------- */
function initializePage() {
    checkAuthentication();
    displayUserInfo();
    hidePageLoader();
}

/* --------------------------------------------------------
   AUTH CHECK
--------------------------------------------------------- */
function checkAuthentication() {
    const isLoggedIn = sessionStorage.getItem('isLoggedIn') === 'true';
    const rememberUser = localStorage.getItem('rememberUser') === 'true';

    if (!isLoggedIn && !rememberUser) {
        // compute best guess for login path depending on current location
        const loginUrl = computeLoginUrl();
        window.location.replace(loginUrl);
    }
}

function computeLoginUrl() {
    // If current file is in /auth/ folder, redirect to login.php (same folder).
    // If not, prefer ../auth/login.php — this tries to be robust across pages.
    try {
        const path = window.location.pathname || '';
        if (path.includes('/auth/')) return 'login.php';
    } catch (e) { /* ignore */ }
    return '../auth/login.php';
}

/* --------------------------------------------------------
   USER DISPLAY
--------------------------------------------------------- */
function displayUserInfo() {
    const userName =
        sessionStorage.getItem('userName') ||
        localStorage.getItem('userName') ||
        'User';

    // optional: read email if available (not required for display)
    const userEmail = sessionStorage.getItem('userEmail') || localStorage.getItem('userEmail') || '';

    const welcomeMessage = document.getElementById('welcome-message');
    const userDisplay = document.getElementById('user-name-display');

    if (welcomeMessage) welcomeMessage.textContent = `Halo, ${userName}!`;
    if (userDisplay) userDisplay.textContent = userName;
}

/* --------------------------------------------------------
   PAGE LOADER
--------------------------------------------------------- */
function hidePageLoader() {
    setTimeout(() => {
        const loader = document.getElementById('page-loader');
        if (loader) loader.classList.add('hidden');
    }, 500);
}

/* --------------------------------------------------------
   EVENT LISTENERS
--------------------------------------------------------- */
function setupEventListeners() {
    const billingToggle = document.getElementById('billing-period');
    if (billingToggle) billingToggle.addEventListener('change', handleBillingToggle);

    const comparisonBtn = document.getElementById('show-comparison');
    if (comparisonBtn) comparisonBtn.addEventListener('click', toggleComparison);

    const logoutBtn = document.getElementById('logout-btn');
    if (logoutBtn) logoutBtn.addEventListener('click', handleLogout);

    const termsCheck = document.getElementById('payment-terms-check');
    const confirmBtn = document.getElementById('confirm-payment-btn');
    if (termsCheck && confirmBtn) {
        termsCheck.addEventListener('change', () => {
            confirmBtn.disabled = !termsCheck.checked;
        });
    }

    // Close modal on clicking outside (if modal exists)
    const modal = document.getElementById('payment-modal');
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closePaymentModal();
        });
    }

    // Re-check auth when page is shown from cache
    window.addEventListener('pageshow', (event) => {
        if (event.persisted) checkAuthentication();
    });

    // Check auth when user returns to tab
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) checkAuthentication();
    });
}

/* --------------------------------------------------------
   BILLING PERIOD SWITCH
--------------------------------------------------------- */
function handleBillingToggle(e) {
    const isYearly = !!e.target.checked;
    currentPlan.billing = isYearly ? 'yearly' : 'monthly';

    document.querySelectorAll('.plan-card').forEach((card) => {
        const amount = card.querySelector('.amount');
        const period = card.querySelector('.period');

        if (!amount) return;

        const monthly = amount.dataset && amount.dataset.monthly ? amount.dataset.monthly : amount.textContent;
        const yearly = amount.dataset && amount.dataset.yearly ? amount.dataset.yearly : amount.textContent;

        amount.textContent = isYearly ? yearly : monthly;

        if (period) {
            period.textContent = isYearly ? '/tahun' : '/bulan';
        }
    });
}

/* --------------------------------------------------------
   COMPARISON TABLE
--------------------------------------------------------- */
function toggleComparison() {
    const table = document.getElementById('comparison-table');
    const btn = document.getElementById('show-comparison');

    if (!table || !btn) return;

    const isVisible = table.style.display !== 'none';

    if (isVisible) {
        table.style.display = 'none';
        btn.innerHTML = `<span>Lihat Perbandingan Detail</span><i class="fas fa-chevron-down"></i>`;
        btn.classList.remove('active');
    } else {
        table.style.display = 'block';
        btn.innerHTML = `<span>Sembunyikan Perbandingan</span><i class="fas fa-chevron-up"></i>`;
        btn.classList.add('active');

        setTimeout(() => {
            table.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }, 120);
    }
}

/* --------------------------------------------------------
   PAYMENT: selectPlan (single, robust implementation)
   - Tries server-generated payment link first (secure)
   - Falls back to client-built static payment URL if server not available
--------------------------------------------------------- */
async function selectPlan(planName, price, planType) {
    const billing = document.getElementById('billing-period')?.checked ? 'yearly' : 'monthly';
    currentPlan.name = planName;
    currentPlan.type = planType;
    currentPlan.billing = billing;

    // Validate user session (good to have)
    const userId = sessionStorage.getItem('userId') || localStorage.getItem('userId');
    if (!userId) {
        alert('Sesi login tidak ditemukan. Silakan login kembali.');
        window.location.href = computeLoginUrl();
        return;
    }

    // Get actual price from DOM or fallback to passed price
    const actualPrice = getActualPriceForPlan(planType, price);

    // Save selection locally for tracking
    try {
        sessionStorage.setItem('selectedPlan', planName);
        sessionStorage.setItem('selectedPlanType', planType);
        sessionStorage.setItem('selectedPrice', actualPrice);
        sessionStorage.setItem('selectedBilling', billing);
    } catch (e) {
        // storage may fail in private mode — ignore but continue
        console.warn('sessionStorage set failed', e);
    }

    // Prefer server-generated payment link if backend exists
    const serverPaymentEndpoint = '/SagaHealth/payment/create_payment.php';
    try {
        const resp = await fetch(serverPaymentEndpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                amount: actualPrice,
                title: planName,
                plan: planType,
                billing
            }),
            credentials: 'include' // include cookies if needed for auth
        });

        // If backend responded with non-JSON or error status, fall back
        if (!resp.ok) throw new Error('Server payment endpoint returned non-OK status');

        const result = await resp.json();

        if (result && result.payment_url) {
            const paymentUrl = result.payment_url;
            showLoadingNotification(planName, paymentUrl);
            setTimeout(() => {
                window.location.href = paymentUrl;
            }, 1500);
            return;
        } else {
            // fallback to client-built flow below
            console.warn('Server did not return payment_url, falling back to client redirect', result);
        }
    } catch (err) {
        // server endpoint not available or failed; fallback to client-built URL
        console.warn('Server payment link creation failed, falling back to client redirection.', err);
    }

    // FALLBACK: client-built redirect using PAYMENT_URLS
    const basePaymentUrl = PAYMENT_URLS[planType];
    if (!basePaymentUrl) {
        alert('Payment URL tidak ditemukan untuk plan ini.');
        return;
    }

    const params = new URLSearchParams({
        status: 'success',
        plan: planType,
        billing,
        user_id: userId,
        payref: 'DEV-' + Date.now(),
        amount: actualPrice
    });

    const redirectUrl = `${basePaymentUrl}?${params.toString()}`;

    showLoadingNotification(planName, redirectUrl);
    console.log('Redirecting to payment (fallback):', {
        plan: planName,
        type: planType,
        price: actualPrice,
        billing,
        url: redirectUrl
    });

    setTimeout(() => {
        window.location.href = redirectUrl;
    }, 1500);
}

/* --------------------------------------------------------
   Helper: read actual price from DOM safely
--------------------------------------------------------- */
function getActualPriceForPlan(planType, fallbackPrice = '') {
    try {
        const planCard = document.querySelector(`[data-plan="${planType}"]`);
        const amountEl = planCard?.querySelector('.amount');
        if (amountEl) return amountEl.textContent.trim();
    } catch (e) {
        // ignore and return fallback
    }
    return fallbackPrice;
}

/* --------------------------------------------------------
   REDIRECT / LOADING NOTIFICATION (single, safe injector)
--------------------------------------------------------- */
function showLoadingNotification(planName, url) {
    // Avoid duplicate notifications
    if (document.querySelector('.redirect-notification')) return;

    const wrapper = document.createElement('div');
    wrapper.className = 'redirect-notification';

    wrapper.innerHTML = `
        <div class="notification-content" role="dialog" aria-modal="true" aria-label="Redirect ke pembayaran">
            <div class="spinner-large" aria-hidden="true"></div>
            <h3>Mengarahkan ke Halaman Pembayaran</h3>
            <p>Anda akan diarahkan ke halaman pembayaran untuk paket <strong>${escapeHtml(planName)}</strong></p>
            <p class="notification-url">${escapeHtml(url)}</p>
            <div class="notification-progress"><div class="progress-bar"></div></div>
        </div>
    `;

    document.body.appendChild(wrapper);

    // animate progress bar
    requestAnimationFrame(() => {
        const bar = wrapper.querySelector('.progress-bar');
        if (bar) bar.style.width = '100%';
    });

    injectRedirectStyles();
}

/* --------------------------------------------------------
   Styles injection (id-guarded)
--------------------------------------------------------- */
function injectRedirectStyles() {
    if (document.getElementById('redirect-notification-styles')) return;

    const style = document.createElement('style');
    style.id = 'redirect-notification-styles';
    style.textContent = `
        .redirect-notification {
            position: fixed; inset: 0;
            background: rgba(0, 0, 0, 0.85);
            display: flex; justify-content: center; align-items: center;
            z-index: 99999;
            animation: fadeIn .3s ease;
        }
        .notification-content {
            background: #fff;
            padding: 2.5rem;
            border-radius: 14px;
            width: 90%; max-width: 520px;
            text-align: center;
            animation: slideUp .35s ease;
            box-shadow: 0 8px 30px rgba(0,0,0,0.25);
        }
        .spinner-large { 
            width: 48px; height: 48px; margin: 0 auto; border-radius: 50%;
            border: 5px solid rgba(0,0,0,0.08); border-top-color: var(--primary, #4f46e5);
            animation: spin 1s linear infinite;
        }
        .notification-content h3 { margin: 1rem 0 0.5rem; }
        .notification-url {
            font-size: 0.85rem; color: #6b7280; word-break: break-all;
            padding: 0.5rem; background: #f3f4f6; border-radius: 8px; margin-bottom: 0.5rem;
        }
        .notification-progress { 
            margin-top: 1.2rem; height: 4px; background: #e5e7eb; border-radius: 2px; overflow: hidden; 
        }
        .progress-bar { width: 0; height: 100%; background: linear-gradient(90deg, var(--primary, #4f46e5), var(--secondary, #06b6d4)); transition: width 1.5s ease; }
        @keyframes spin { to { transform: rotate(360deg); } }
        @keyframes fadeIn { from { opacity: 0 } to { opacity: 1 } }
        @keyframes slideUp { from { transform: translateY(8px); opacity: 0 } to { transform: translateY(0); opacity: 1 } }
    `;
    document.head.appendChild(style);
}

/* --------------------------------------------------------
   LOGOUT HANDLING
--------------------------------------------------------- */
async function handleLogout() {
    if (!confirm('Apakah Anda yakin ingin keluar?')) return;

    try {
        const res = await fetch('includes/auth_functions.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'logout' }),
            credentials: 'include'
        });

        const data = await res.json();

        if (data && data.status === 'success') {
            sessionStorage.clear();
            localStorage.removeItem('rememberUser');
            localStorage.removeItem('userId');
            localStorage.removeItem('userName');
            window.location.href = computeLoginUrl();
            return;
        }

        alert('Logout gagal. Silakan coba lagi.');
    } catch (error) {
        console.error('Logout error:', error);
        // Force clear client-side and redirect
        try {
            sessionStorage.clear();
            localStorage.removeItem('rememberUser');
            localStorage.removeItem('userId');
            localStorage.removeItem('userName');
        } catch (e) { /* ignore */ }
        window.location.href = computeLoginUrl();
    }
}
// assets/js/plan.js - Handler untuk inisiasi pembayaran DANA
document.addEventListener('DOMContentLoaded', function() {
    // Target semua tombol dengan class 'select-plan-btn'
    document.querySelectorAll('.select-plan-btn').forEach(button => {
        button.addEventListener('click', async function(e) {
            e.preventDefault();

<<<<<<< HEAD
=======
            // Ambil data dari data attribute tombol
            const planName = this.dataset.planName || 'Plan Default';
            const price = this.dataset.price || '0';
            const planType = this.dataset.planType || 'monthly';
            const paymentMethod = this.dataset.paymentMethod || 'dana'; // Harusnya 'dana'

            if (paymentMethod !== 'dana') return;
            
            this.disabled = true;
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';

            try {
                const response = await fetch('../payment/create_payment.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        plan_name: planName,
                        price: price,
                        plan_type: planType
                    })
                });

                const data = await response.json();

                if (data.status === 'success' && data.redirect_url) {
                    // Berhasil, arahkan pengguna ke halaman pembayaran DANA
                    window.location.href = data.redirect_url;
                } else {
                    alert('Pembayaran gagal diinisiasi: ' + (data.message || 'Error tidak diketahui.'));
                    console.error('Payment Initiation Error:', data);
                }

            } catch (error) {
                alert('Terjadi kesalahan koneksi saat memulai pembayaran.');
                console.error('Fetch Error:', error);
            } finally {
                this.disabled = false;
                this.innerHTML = originalText;
            }
        });
    });
});
>>>>>>> 4b250f7 (benerin home,benerin dan integrasiin halaman login, nyiapin integrasi api payment jadi ke dana)
/* --------------------------------------------------------
   DEPRECATED / COMPATIBILITY FUNCTIONS
--------------------------------------------------------- */
function closePaymentModal() {
    // Deprecated: kept for backward compatibility
    console.log('closePaymentModal called (deprecated).');
}

async function processPayment() {
    // Deprecated: kept for backward compatibility
    console.log('processPayment called (deprecated).');
}

function showPaymentSuccess() {
    // Deprecated: kept for backward compatibility
    console.log('showPaymentSuccess called (deprecated).');
}

/* --------------------------------------------------------
   UTIL: small helpers
--------------------------------------------------------- */
function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}

/* --------------------------------------------------------
   EXPORT GLOBAL ACCESS (for inline onclick handlers)
--------------------------------------------------------- */
window.selectPlan = selectPlan;
window.closePaymentModal = closePaymentModal;
window.processPayment = processPayment;
window.handleLogout = handleLogout;
