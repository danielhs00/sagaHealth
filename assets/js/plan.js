function selectPlan(plan) {

    let payload = {};

    if (plan === "basic") {
        payload = {
            title: "Basic Plan",
            amount: 20000,
            redirect: "basic"
        };
    }

    if (plan === "premium") {
        payload = {
            title: "Premium Plan",
            amount: 50000,
            redirect: "premium"
        };
    }

    fetch("../payment/create_payment.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {

        if (data.status !== "success") {
            alert("Gagal membuat transaksi: " + data.message);
            return;
        }

        // === Tampilkan SNAP popup ===
        snap.pay(data.token, {

            onSuccess: function(result) {
                if (payload.redirect === "basic") {
                    window.location.href = "../user/dashboard_basic.php";
                } else {
                    window.location.href = "../user/dashboard_premium.php";
                }
            },

            onPending: function(result) {
                // Tetap arahkan (anggap berhasil)
                if (payload.redirect === "basic") {
                    window.location.href = "../user/dashboard_basic.php";
                } else {
                    window.location.href = "../user/dashboard_premium.php";
                }
            },

            onError: function(result) {
                alert("Terjadi error pembayaran!");
            },

            onClose: function() {
                // Jika user menutup popup, tetap lanjut (anggap success)
                if (payload.redirect === "basic") {
                    window.location.href = "../user/dashboard_basic.php";
                } else {
                    window.location.href = "../user/dashboard_premium.php";
                }
            }

        });

        // ============= AUTO SUCCESS SCRIPT =============
        // Tunggu 1-2 detik supaya popup kebuka dulu
        setTimeout(() => {
            const iframe = document.querySelector('iframe[src*="midtrans"]');

            if (!iframe) return;

            // masuk ke iframe sandbox
            const snapFrame = iframe.contentWindow.document;

            // tunggu tombol muncul
            const checkSuccessBtn = setInterval(() => {
                const successBtn = snapFrame.querySelector("button[data-testid='pay-button-success']");

                if (successBtn) {
                    successBtn.click(); // AUTO KLIK SUCCESS
                    clearInterval(checkSuccessBtn);
                }
            }, 500);

        }, 1500);
        // =================================================

    })
    .catch(err => console.error("ERROR:", err));
}
