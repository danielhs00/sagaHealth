/**
 * assets/js/plan.js
 * Mode: DEVELOPER AUTO-BYPASS (Fixed)
 */

async function selectPlan(planName, amount, planId) {
    // 1. UI Loading pada tombol
    const btnSelector = planId === 'premium' ? '.btn-plan.premium' : '.btn-plan:not(.premium)';
    const btn = document.querySelector(btnSelector);
    const originalText = btn ? btn.innerHTML : 'Pilih';

    if (btn) {
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
        btn.disabled = true;
    }

    try {
        // 2. Minta Token Transaksi ke Backend
        const response = await fetch('../payment/create_payment.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                planId: planId, 
                amount: amount, 
                title: planName
            })
        });

        const text = await response.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            throw new Error("Server Error: Respon tidak valid.");
        }

        if (data.status !== 'success') {
            throw new Error(data.message || 'Gagal membuat transaksi');
        }

        // 3. Tampilkan Popup Midtrans
        window.snap.pay(data.token, {
            
            // A. SUKSES (Bayar Beneran)
            onSuccess: function(result) {
                console.log('Midtrans Real Success');
                processSuccessOnBackend(planId, amount, data.order_id);
            },

            // B. PENDING (FIX: Trigger Bypass juga disini)
            // Midtrans sering melempar status 'pending' saat popup ditutup
            onPending: function(result) {
                console.log('Midtrans Pending -> Auto Bypass Triggered');
                processSuccessOnBackend(planId, amount, data.order_id);
            },

            // C. ERROR
            onError: function(result) {
                alert("Midtrans Error!");
                resetButton(btn, originalText);
            },

            // D. CLOSE (Tutup Popup)
            onClose: function() {
                console.log('Popup Closed -> Auto Bypass Triggered');
                processSuccessOnBackend(planId, amount, data.order_id);
            }
        });

    } catch (error) {
        console.error(error);
        alert("Error: " + error.message);
        resetButton(btn, originalText);
    }
}

// Fungsi Aktivasi Paket ke Database
async function processSuccessOnBackend(planId, amount, orderId) {
    // Overlay Loading Full Screen
    const overlay = document.createElement('div');
    overlay.style.cssText = "position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.9);z-index:9999;display:flex;justify-content:center;align-items:center;color:#fff;flex-direction:column;gap:15px;";
    
    overlay.innerHTML = `
        <i class="fas fa-magic fa-3x" style="color: #E02474;"></i>
        <h2 style="margin:0;">DEV MODE: BYPASS AKTIF</h2>
        <p>Mengaktifkan paket secara otomatis...</p>
        <i class="fas fa-spinner fa-spin"></i>
    `;
    document.body.appendChild(overlay);

    try {
        // Pastikan file ini ADA!
        const res = await fetch('../payment/process_success.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                plan_type: planId,
                amount: amount,
                order_id: orderId
            })
        });
        
        const data = await res.json();

        if (data.status === 'success') {
            setTimeout(() => {
                window.location.href = data.redirect_url;
            }, 1000); 
        } else {
            alert("Gagal update database: " + data.message);
            document.body.removeChild(overlay);
            // Reload agar tombol reset
            window.location.reload();
        }
    } catch (e) {
        console.error(e);
        // Cek apakah file process_success.php benar-benar ada
        alert("Gagal menghubungi server. Pastikan file 'payment/process_success.php' sudah dibuat!");
        document.body.removeChild(overlay);
        window.location.reload();
    }
}

function resetButton(btn, text) {
    if (btn) {
        btn.disabled = false;
        btn.innerHTML = text;
    }
}