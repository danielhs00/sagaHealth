// ../assets/js/plan.js

function selectPlan(title, amount, planId) {
    console.log("selectPlan clicked:", { title, amount, planId });

    // Validasi sederhana
    if (!title || !amount || !planId) {
        alert("Data paket tidak lengkap.");
        return;
    }

    // Kalau plan.php dan create_payment.php ada di folder yang sama (/payment/)
    // gunakan path relatif seperti ini:
    fetch("../payment/create_payment.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ title, amount, planId })
    })
    .then(async (res) => {
        const text = await res.text();
        console.log("Raw response from create_payment.php:", text);

        try {
            return JSON.parse(text);
        } catch (e) {
            console.error("SERVER RETURN (bukan JSON valid):", text);
            throw new Error("Response dari server tidak dalam format JSON.");
        }
    })
    .then((data) => {
        console.log("Parsed data:", data);

        if (data.status !== "success") {
            alert("Error: " + (data.message || "Gagal membuat transaksi."));
            return;
        }

        if (!data.token) {
            alert("Token Midtrans tidak ditemukan di response.");
            console.error("Data tanpa token:", data);
            return;
        }

        // 🔹 INI BAGIAN PENTING: panggil Snap UI
        window.snap.pay(data.token, {
            onSuccess: function (result) {
                console.log("SUCCESS:", result);
                // Setelah sukses, redirect ke dashboard sesuai plan
                if (data.redirect) {
                    window.location.href = data.redirect;
                }
            },
            onPending: function (result) {
                console.log("PENDING:", result);
                alert("Pembayaran masih pending. Silakan selesaikan terlebih dahulu.");
            },
            onError: function (result) {
                console.error("ERROR:", result);
                alert("Terjadi kesalahan saat pembayaran.");
            },
            onClose: function () {
                console.log("Snap popup ditutup tanpa menyelesaikan pembayaran.");
            }
        });
    })
    .catch((err) => {
        console.error("REQUEST ERROR:", err);
        alert("Terjadi kesalahan pada server. Silakan coba lagi.");
    });
}
