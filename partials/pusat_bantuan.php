<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SagaHealth</title>
  <link rel="icon" href="../assets/img/tittle.png" type="image/png">
    
    <link rel="stylesheet" href="../assets/style/partials.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="font-sans bg-white">

<?php include '../partials/header.php'; ?>

    <main>

        <section class="bantuan-hero">
            <div class="container-wrapper text-center">
                <h1>Ada yang bisa kami bantu?</h1>
                <p class="about-subtitle">
                    Temukan jawaban untuk pertanyaan Anda.
                </p>
                
                <div class="bantuan-search-bar">
                    <input type="search" placeholder="Ketik pertanyaan Anda di sini (mis: cara konsultasi)...">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </section>

        <section class="bantuan-kategori">
            <div class="container-wrapper">
                <h2 class="section-title-center">Telusuri Berdasarkan Topik</h2>
                
                <div class="kategori-grid">
                    <a href="#" class="kategori-card">
                        <div class="kategori-icon"><i class="fas fa-user-md"></i></div>
                        <h4>Konsultasi Dokter</h4>
                    </a>
                    <a href="#" class="kategori-card">
                        <div class="kategori-icon"><i class="fas fa-pills"></i></div>
                        <h4>Pembelian Obat</h4>
                    </a>
                    <a href="#" class="kategori-card">
                        <div class="kategori-icon"><i class="fas fa-calendar-check"></i></div>
                        <h4>Janji Medis</h4>
                    </a>
                    <a href="#" class="kategori-card">
                        <div class="kategori-icon"><i class="fas fa-credit-card"></i></div>
                        <h4>Pembayaran & Asuransi</h4>
                    </a>
                    <a href="#" class="kategori-card">
                        <div class="kategori-icon"><i class="fas fa-user-circle"></i></div>
                        <h4>Pengaturan Akun</h4>
                    </a>
                    <a href="#" class="kategori-card">
                        <div class="kategori-icon"><i class="fas fa-tag"></i></div>
                        <h4>Promo & Voucher</h4>
                    </a>
                </div>
            </div>
        </section>
        
        <section class="bantuan-faq">
            <div class="container-wrapper">
                <h2 class="section-title-center">Pertanyaan Umum (FAQ)</h2>
                
                <div class="faq-container">
                    
                    <div class="faq-item">
                        <button class="faq-question">
                            Bagaimana cara konsultasi dengan dokter?
                        </button>
                        <div class="faq-answer">
                            <p>Anda dapat memilih layanan "Chat dengan Dokter" di halaman utama, pilih spesialisasi dokter yang Anda inginkan, lalu klik tombol "Chat" untuk memulai konsultasi.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <button class="faq-question">
                            Apakah resep obat dari dokter pasti tersedia?
                        </button>
                        <div class="faq-answer">
                            <p>Kami bekerja sama dengan ribuan apotek terpercaya. Sebagian besar obat dalam resep akan tersedia. Jika ada obat yang tidak tersedia, apoteker kami akan menghubungi Anda untuk memberikan opsi alternatif atau pengganti yang disetujui dokter.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-question">
                            Berapa lama obat saya akan sampai?
                        </button>
                        <div class="faq-answer">
                            <p>Waktu pengiriman tergantung pada jenis pengiriman yang Anda pilih (Instan, Same Day, atau Reguler) dan ketersediaan stok di apotek mitra. Anda dapat melacak status pesanan Anda secara *real-time* melalui aplikasi.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-question">
                            Bagaimana cara menggunakan asuransi saya?
                        </button>
                        <div class="faq-answer">
                            <p>Anda dapat menghubungkan kartu asuransi Anda di menu "Asuransiku" di profil Anda. Pastikan penyedia asuransi Anda telah bekerja sama dengan kami. Manfaat akan otomatis diterapkan saat Anda melakukan pembayaran.</p>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <section class="bantuan-kontak">
            <div class="container-wrapper">
                <h2 class="section-title-center">Masih Butuh Bantuan?</h2>
                <div class="kontak-grid">
                    <div class="kontak-card">
                        <i class="fas fa-envelope"></i>
                        <h4>Email Kami</h4>
                        <p>Dapatkan balasan dalam 1x24 jam.</p>
                        <a href="mailto:help@halodoc.com" class="btn-kontak">help@halodoc.com</a>
                    </div>
                    <div class="kontak-card">
                        <i class="fas fa-phone-alt"></i>
                        <h4>Telepon</h4>
                        <p>Layanan 24 jam untuk Anda.</p>
                        <a href="tel:021-5095-9900" class="btn-kontak">021-5095-9900</a>
                    </div>
                </div>
            </div>
        </section>

    </main>

 <?php include '../partials/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            
            // === Skrip Menu Mobile (Wajib ada) ===
            const menuToggle = document.getElementById('mobile-menu-toggle');
            const navMenu = document.getElementById('main-nav-menu');

            if (menuToggle && navMenu) {
                menuToggle.addEventListener('click', () => {
                    navMenu.classList.toggle('active');
                });
            }

            // === Skrip untuk FAQ Accordion ===
            const faqQuestions = document.querySelectorAll('.faq-question');

            faqQuestions.forEach(button => {
                button.addEventListener('click', () => {
                    const item = button.parentElement;
                    
                    // Toggle kelas 'active' pada item
                    item.classList.toggle('active');

                    // (Opsional) Tutup FAQ lain saat satu dibuka
                    const allFaqItems = document.querySelectorAll('.faq-item');
                    allFaqItems.forEach(otherItem => {
                        if (otherItem !== item && otherItem.classList.contains('active')) {
                            otherItem.classList.remove('active');
                        }
                    });
                });
            });

        });
    </script>
    
</body>
</html>