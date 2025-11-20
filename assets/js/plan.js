// plan.js - Plan Page JavaScript

// State management
let currentPlan = {
    name: '',
    price: '',
    type: '',
    billing: 'monthly'
};

// DOM Content Loaded
document.addEventListener('DOMContentLoaded', () => {
    initializePage();
    setupEventListeners();
});

// Initialize page
function initializePage() {
    // Check authentication
    checkAuthentication();
    
    // Display user info
    displayUserInfo();
    
    // Hide page loader
    setTimeout(() => {
        const loader = document.getElementById('page-loader');
        if (loader) {
            loader.classList.add('hidden');
        }
    }, 500);
}

// Check if user is authenticated
function checkAuthentication() {
    const isLoggedIn = sessionStorage.getItem('isLoggedIn') === 'true';
    const rememberUser = localStorage.getItem('rememberUser') === 'true';
    
    if (!isLoggedIn && !rememberUser) {
        window.location.replace('../auth/login.php');
    }
}

// Display user information
function displayUserInfo() {
    const userName = sessionStorage.getItem('userName') || localStorage.getItem('userName') || 'User';
    const userEmail = sessionStorage.getItem('userEmail') || localStorage.getItem('userEmail') || '';
    
    // Update welcome message
    const welcomeMessage = document.getElementById('welcome-message');
    if (welcomeMessage) {
        welcomeMessage.textContent = `Halo, ${userName}!`;
    }
    
    // Update user display in header
    const userDisplay = document.getElementById('user-name-display');
    if (userDisplay) {
        userDisplay.textContent = userName;
    }
}

// Setup event listeners
function setupEventListeners() {
    // Billing period toggle
    const billingToggle = document.getElementById('billing-period');
    if (billingToggle) {
        billingToggle.addEventListener('change', handleBillingToggle);
    }
    
    // Comparison table toggle
    const comparisonBtn = document.getElementById('show-comparison');
    if (comparisonBtn) {
        comparisonBtn.addEventListener('click', toggleComparison);
    }
    
    // Logout button
    const logoutBtn = document.getElementById('logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', handleLogout);
    }
    
    // Payment terms checkbox
    const termsCheck = document.getElementById('payment-terms-check');
    const confirmBtn = document.getElementById('confirm-payment-btn');
    if (termsCheck && confirmBtn) {
        termsCheck.addEventListener('change', () => {
            confirmBtn.disabled = !termsCheck.checked;
        });
    }
    
    // Close modal on outside click
    const modal = document.getElementById('payment-modal');
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closePaymentModal();
            }
        });
    }
}

// Handle billing period toggle (monthly/yearly)
function handleBillingToggle(e) {
    const isYearly = e.target.checked;
    currentPlan.billing = isYearly ? 'yearly' : 'monthly';
    
    // Update all plan prices
    const planCards = document.querySelectorAll('.plan-card');
    planCards.forEach(card => {
        const amountEl = card.querySelector('.amount');
        if (amountEl) {
            const monthlyPrice = amountEl.dataset.monthly;
            const yearlyPrice = amountEl.dataset.yearly;
            
            if (isYearly) {
                amountEl.textContent = yearlyPrice;
                card.querySelector('.period').textContent = '/tahun';
            } else {
                amountEl.textContent = monthlyPrice;
                card.querySelector('.period').textContent = '/bulan';
            }
        }
    });
}

// Toggle comparison table
function toggleComparison() {
    const table = document.getElementById('comparison-table');
    const btn = document.getElementById('show-comparison');
    
    if (table && btn) {
        const isVisible = table.style.display !== 'none';
        
        if (isVisible) {
            table.style.display = 'none';
            btn.innerHTML = '<span>Lihat Perbandingan Detail</span><i class="fas fa-chevron-down"></i>';
            btn.classList.remove('active');
        } else {
            table.style.display = 'block';
            btn.innerHTML = '<span>Sembunyikan Perbandingan</span><i class="fas fa-chevron-up"></i>';
            btn.classList.add('active');
            
            // Smooth scroll to table
            setTimeout(() => {
                table.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }, 100);
        }
    }
}

// Payment URLs
const PAYMENT_URLS = {
    basic: 'https://pay.doku.com/p-link/p/95TgIPK2v4',
    premium: 'https://pay.doku.com/p-link/p/gwDpiBLhsH'
};

// Select plan and redirect to payment
function selectPlan(planName, price, planType) {
    // Save plan info to sessionStorage
    const billing = document.getElementById('billing-period')?.checked ? 'yearly' : 'monthly';

    const userId = sessionStorage.getItem('userId') || localStorage.getItem('userId');
    if (!userId) {
        alert('Sesi login tidak ditemukan. Silakan login kembali.');
        window.location.href = '../auth/login.php';
        return;
    }

    // Get actual price based on billing period
    const planCard = document.querySelector(`[data-plan="${planType}"]`);
    const amountEl = planCard?.querySelector('.amount');
    const actualPrice = amountEl?.textContent || price;
    
    // Save to sessionStorage for tracking
    sessionStorage.setItem('selectedPlan', planName);
    sessionStorage.setItem('selectedPlanType', planType);
    sessionStorage.setItem('selectedPrice', actualPrice);
    sessionStorage.setItem('selectedBilling', billing);
    
    // Get payment URL
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
        payref: 'DEV-' + Date.now()
    });

    const redirectUrl = `${basePaymentUrl}?${params.toString()}`;

    showLoadingNotification(planName, redirectUrl);
    console.log('Redirecting to payment:', {
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

// Show loading notification before redirect
function showLoadingNotification(planName, url) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = 'redirect-notification';
    notification.innerHTML = `
        <div class="notification-content">
            <div class="spinner-large"></div>
            <h3>Mengarahkan ke Halaman Pembayaran</h3>
            <p>Anda akan diarahkan ke halaman pembayaran untuk paket <strong>${planName}</strong></p>
            <p class="notification-url">${url}</p>
            <div class="notification-progress">
                <div class="progress-bar"></div>
            </div>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate progress bar
    const progressBar = notification.querySelector('.progress-bar');
    setTimeout(() => {
        progressBar.style.width = '100%';
    }, 100);
    
    // Add styles if not exist
    if (!document.getElementById('redirect-notification-styles')) {
        const style = document.createElement('style');
        style.id = 'redirect-notification-styles';
        style.textContent = `
            .redirect-notification {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.9);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 10000;
                animation: fadeIn 0.3s ease;
            }
            
            .notification-content {
                background: white;
                padding: 3rem;
                border-radius: 16px;
                text-align: center;
                max-width: 500px;
                width: 90%;
                animation: slideUp 0.3s ease;
            }
            
            .notification-content h3 {
                color: var(--primary);
                font-size: 1.5rem;
                margin: 1.5rem 0 1rem;
            }
            
            .notification-content p {
                color: var(--text-medium);
                margin-bottom: 1rem;
            }
            
            .notification-url {
                font-size: 0.85rem;
                color: var(--text-light);
                word-break: break-all;
                padding: 0.5rem;
                background: var(--bg-page);
                border-radius: 8px;
            }
            
            .notification-progress {
                width: 100%;
                height: 4px;
                background: var(--border-color);
                border-radius: 2px;
                overflow: hidden;
                margin-top: 1.5rem;
            }
            
            .progress-bar {
                height: 100%;
                width: 0%;
                background: linear-gradient(90deg, var(--primary), var(--secondary));
                transition: width 1.5s ease;
            }
        `;
        document.head.appendChild(style);
    }
}

// Close payment modal (deprecated - keeping for compatibility)
function closePaymentModal() {
    // Modal tidak digunakan lagi, tapi fungsi tetap ada untuk compatibility
    console.log('Payment modal functionality has been replaced with direct redirect');
}

// Process payment (deprecated - keeping for compatibility)
async function processPayment() {
    // Fungsi ini tidak digunakan lagi karena redirect langsung ke payment page
    console.log('Direct payment redirect is now used instead of modal');
}

// Show payment success (deprecated - keeping for compatibility)
function showPaymentSuccess() {
    // Tidak digunakan lagi
    console.log('Payment success is handled by external payment page');
}

// Handle logout
async function handleLogout() {
    if (!confirm('Apakah Anda yakin ingin keluar?')) {
        return;
    }
    
    try {
        // Call logout API
        const response = await fetch('includes/auth_functions.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'logout' })
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            // Clear session storage
            sessionStorage.clear();
            
            // Clear local storage (if remember me was used)
            localStorage.removeItem('rememberUser');
            localStorage.removeItem('userId');
            localStorage.removeItem('userName');
            
            // Redirect to login
            window.location.href = 'login.php';
        } else {
            alert('Logout gagal. Silakan coba lagi.');
        }
    } catch (error) {
        console.error('Logout error:', error);
        
        // Force logout on client side
        sessionStorage.clear();
        localStorage.removeItem('rememberUser');
        localStorage.removeItem('userId');
        localStorage.removeItem('userName');
        
        window.location.href = 'login.php';
    }
}

// Prevent back navigation after logout
window.addEventListener('pageshow', (event) => {
    if (event.persisted) {
        // Page was loaded from cache (back/forward button)
        checkAuthentication();
    }
});

// Handle visibility change (tab switching)
document.addEventListener('visibilitychange', () => {
    if (!document.hidden) {
        // Check if still authenticated when user returns to tab
        checkAuthentication();
    }
});

// Export functions for global access
window.selectPlan = selectPlan;
window.closePaymentModal = closePaymentModal;
window.processPayment = processPayment;
window.handleLogout = handleLogout;