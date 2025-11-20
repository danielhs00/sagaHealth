// plan.js — Improved & Optimized Version

/* --------------------------------------------------------
   GLOBAL STATE
--------------------------------------------------------- */
let currentPlan = {
    name: "",
    price: "",
    type: "",
    billing: "monthly",
};

/* --------------------------------------------------------
   INITIALIZATION
--------------------------------------------------------- */
document.addEventListener("DOMContentLoaded", () => {
    checkAuthentication();
    displayUserInfo();
    setupEventListeners();
    hidePageLoader();
});

/* --------------------------------------------------------
   AUTH CHECK
--------------------------------------------------------- */
function checkAuthentication() {
    const loggedIn = sessionStorage.getItem("isLoggedIn") === "true";
    const remember = localStorage.getItem("rememberUser") === "true";

    if (!loggedIn && !remember) {
        window.location.replace("../auth/login.php");
    }
}

/* --------------------------------------------------------
   USER DISPLAY
--------------------------------------------------------- */
function displayUserInfo() {
    const userName =
        sessionStorage.getItem("userName") ||
        localStorage.getItem("userName") ||
        "User";

    const welcomeMessage = document.getElementById("welcome-message");
    const userDisplay = document.getElementById("user-name-display");

    welcomeMessage && (welcomeMessage.textContent = `Halo, ${userName}!`);
    userDisplay && (userDisplay.textContent = userName);
}

/* --------------------------------------------------------
   PAGE LOADER
--------------------------------------------------------- */
function hidePageLoader() {
    setTimeout(() => {
        document.getElementById("page-loader")?.classList.add("hidden");
    }, 500);
}

/* --------------------------------------------------------
   EVENT LISTENERS
--------------------------------------------------------- */
function setupEventListeners() {

    document.getElementById("billing-period")?.addEventListener("change", handleBillingToggle);

    document.getElementById("show-comparison")?.addEventListener("click", toggleComparison);

    document.getElementById("logout-btn")?.addEventListener("click", handleLogout);

    const termsCheck = document.getElementById("payment-terms-check");
    const confirmBtn = document.getElementById("confirm-payment-btn");

    if (termsCheck && confirmBtn) {
        termsCheck.addEventListener("change", () => {
            confirmBtn.disabled = !termsCheck.checked;
        });
    }

    // Close modal on clicking outside
    document.getElementById("payment-modal")?.addEventListener("click", (e) => {
        if (e.target === e.currentTarget) closePaymentModal();
    });
}

/* --------------------------------------------------------
   BILLING PERIOD SWITCH
--------------------------------------------------------- */
function handleBillingToggle(e) {
    const isYearly = e.target.checked;
    currentPlan.billing = isYearly ? "yearly" : "monthly";

    document.querySelectorAll(".plan-card").forEach((card) => {
        const amount = card.querySelector(".amount");
        const period = card.querySelector(".period");
        if (!amount || !period) return;

        amount.textContent = isYearly ? amount.dataset.yearly : amount.dataset.monthly;
        period.textContent = isYearly ? "/tahun" : "/bulan";
    });
}

/* --------------------------------------------------------
   COMPARISON TABLE
--------------------------------------------------------- */
function toggleComparison() {
    const table = document.getElementById("comparison-table");
    const btn = document.getElementById("show-comparison");

    if (!table || !btn) return;

    const isVisible = table.style.display !== "none";

    if (isVisible) {
        table.style.display = "none";
        btn.innerHTML = `<span>Lihat Perbandingan Detail</span><i class="fas fa-chevron-down"></i>`;
        btn.classList.remove("active");
    } else {
        table.style.display = "block";
        btn.innerHTML = `<span>Sembunyikan Perbandingan</span><i class="fas fa-chevron-up"></i>`;
        btn.classList.add("active");

        setTimeout(() => {
            table.scrollIntoView({ behavior: "smooth", block: "start" });
        }, 120);
    }
}

/* --------------------------------------------------------
   PAYMENT ROUTING
--------------------------------------------------------- */
const PAYMENT_URLS = {
    basic: "https://sagahealth-84305.myr.id/membership/basic-68873",
    premium: "https://sagahealth.myr.id/pl/planpaymentpremium",
};

function selectPlan(planName, price, planType) {
    const billingToggle = document.getElementById("billing-period");
    const billing = billingToggle?.checked ? "yearly" : "monthly";

    const planCard = document.querySelector(`[data-plan="${planType}"]`);
    const actualPrice = planCard?.querySelector(".amount")?.textContent || price;

    sessionStorage.setItem("selectedPlan", planName);
    sessionStorage.setItem("selectedPlanType", planType);
    sessionStorage.setItem("selectedPrice", actualPrice);
    sessionStorage.setItem("selectedBilling", billing);

    async function selectPlan(planName, price, planType) {
    try {
        // Kirim request ke backend untuk membuat payment link
        const response = await fetch("/SagaHealth/payment/create_payment.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                amount: price,
                title: planName
            })
        });

        const result = await response.json();

        if (!result.payment_url) {
            console.log(result);
            return alert("Gagal membuat link pembayaran!");
        }

        // Tampilkan loading animasi
        showRedirectNotification(planName, result.payment_url);

        // Redirect ke Mayar
        setTimeout(() => {
            window.location.href = result.payment_url;
        }, 1500);

    } catch (error) {
        alert("Terjadi error: " + error);
        console.error(error);
    }
}

}

/* --------------------------------------------------------
   REDIRECT NOTIFICATION
--------------------------------------------------------- */
function showRedirectNotification(planName, url) {
    const wrapper = document.createElement("div");
    wrapper.className = "redirect-notification";

    wrapper.innerHTML = `
        <div class="notification-content">
            <div class="spinner-large"></div>
            <h3>Mengarahkan ke Halaman Pembayaran</h3>
            <p>Anda akan diarahkan untuk paket <strong>${planName}</strong></p>
            <p class="notification-url">${url}</p>
            <div class="notification-progress"><div class="progress-bar"></div></div>
        </div>
    `;

    document.body.appendChild(wrapper);

    requestAnimationFrame(() => {
        wrapper.querySelector(".progress-bar").style.width = "100%";
    });

    injectRedirectStyles();
}

/* --------------------------------------------------------
   STYLE INJECTOR (only once)
--------------------------------------------------------- */
function injectRedirectStyles() {
    if (document.getElementById("redirect-notification-styles")) return;

    const style = document.createElement("style");
    style.id = "redirect-notification-styles";
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
            width: 90%; max-width: 460px;
            text-align: center;
            animation: slideUp .35s ease;
        }

        .notification-progress { 
            margin-top: 1.4rem; 
            height: 4px; background: #ddd; border-radius: 2px; overflow: hidden; 
        }

        .progress-bar {
            width: 0; height: 100%;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            transition: width 1.5s ease;
        }
    `;

    document.head.appendChild(style);
}

/* --------------------------------------------------------
   LOGOUT HANDLING
--------------------------------------------------------- */
async function handleLogout() {
    if (!confirm("Apakah Anda yakin ingin keluar?")) return;

    try {
        const res = await fetch("includes/auth_functions.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ action: "logout" }),
        });

        const data = await res.json();

        if (data.status === "success") {
            sessionStorage.clear();
            localStorage.removeItem("rememberUser");
            localStorage.removeItem("userId");
            localStorage.removeItem("userName");
            return (window.location.href = "../auth/login.php");
        }

        alert("Logout gagal. Silakan coba lagi.");
    } catch (error) {
        console.error("Logout error:", error);
        sessionStorage.clear();
        localStorage.clear();
        window.location.href = "../auth/login.php";
    }
}

/* --------------------------------------------------------
   DEPRECATED COMPATIBILITY FUNCTIONS
--------------------------------------------------------- */
function closePaymentModal() { }
function processPayment() { }
function showPaymentSuccess() { }

/* --------------------------------------------------------
   BACK-NAVIGATION HANDLER
--------------------------------------------------------- */
window.addEventListener("pageshow", (event) => {
    if (event.persisted) checkAuthentication();
});

document.addEventListener("visibilitychange", () => {
    if (!document.hidden) checkAuthentication();
});

/* --------------------------------------------------------
   EXPORT GLOBAL ACCESS
--------------------------------------------------------- */
window.selectPlan = selectPlan;
window.handleLogout = handleLogout;
window.closePaymentModal = closePaymentModal;
window.processPayment = processPayment;