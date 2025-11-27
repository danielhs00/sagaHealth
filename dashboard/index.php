<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SagaHealth</title>
  <link rel="icon" href="../assets/img/tittle.png" type="image/png">
    
    <link rel="stylesheet" href="../assets/style/styles.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="font-sans bg-white">
<?php include '../partials/header.php'; ?>

    
<section class="wave-section">
    <div class="container-wrapper">
        
        <div class="banner-layout-single" id="poster-container">
            
            <div class="poster-carousel card" id="main-poster-carousel">
                
                <div class="carousel-track">
                    
                    <div class="carousel-item">
                        <img src="../assets/img/poster1.png" 
                             alt="Poster 1 Kemenkes" 
                             class="banner-image">
                    </div>
                    
                    <div class="carousel-item">
                        <img src="../assets/img/poster2.png" 
                             alt="Poster 2 Layanan Baru" 
                             class="banner-image">
                    </div>
                    
                    <div class="carousel-item">
                        <img src="../assets/img/poster3.png" 
                             alt="Poster 3 Promosi" 
                             class="banner-image">
                    </div>
                     <div class="carousel-item">
                        <img src="../assets/img/poster4.png" 
                             alt="Poster 3 Promosi" 
                             class="banner-image">
                    </div>
                </div>
                
                <button class="carousel-button prev-button" aria-label="Previous Slide">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="carousel-button next-button" aria-label="Next Slide">
                    <i class="fas fa-chevron-right"></i>
                </button>

                <div class="carousel-indicators"></div>
            </div>
            
            <div class="single-poster-mobile" id="single-poster-mobile">
                <img src="../assets/img/poster1.png" alt="Poster Mobile" class="banner-image">
            </div>
        </div>
    </div>
</section>

<br>    
<section class="services-section">
    <div class="container-wrapper pt-16 pb-8">
        <br>
        <h2 class="text-xl font-semibold mb-6">Solusi Kesehatan di Tanganmu</h2>
        <br>
        
        
        <div class="services-grid">

            <a href="chatbot.php" class="service-card primary-card">
                <div class="icon-circle bg-white">
                    <i class="fas fa-comment-dots text-lg"></i>
                </div>
                <p class="font-bold text-sm">Chat dengan Asisten mu</p>
                <span class="text-xs">Mulai 24 jam</span>
                <br>
            </a>

            
            <a href="../dashboard/skrining_kesehatan.php" class="service-card default-card">
                <div class="icon-circle">
                    <i class="fas fa-pills text-lg"></i>
                </div>
                <p class="font-bold text-sm">Periksa Kesehatan Fisik mu</p>
                <span class="text-xs">Ayoo Test Kesehatan mu yaa</span>
                <br>
            </a>


            <a href="../dashboard/mood.php" class="service-card default-card">
                <div class="icon-circle">
                    <i class="fas fa-clipboard-list text-lg"></i>
                </div>
                <p class="font-bold text-sm">Periksa Mood mu</p>
                <span class="text-xs">Semoga dengan ini mood kamu baik yaa </span>
                <br> 
            </a>

            
            <!-- <a href="#" class="service-card default-card">
                <div class="icon-circle">
                    <i class="fas fa-briefcase-medical text-lg"></i>
                </div>
                <p class="font-bold text-sm">Asuransiku</p>
                <span class="text-xs">Bayar ringan & praktis</span>
            </a> -->
        </div>
    </div>
    <br>
</section>

<?php
// =========================================================
// SECTION ARTIKEL TERBARU DARI RSS FEED
// =========================================================
$feed_url = "https://septiawanhadi.blogspot.com/feeds/posts/default";
$max_articles = 6;
$article_count = 0;

// Mengambil dan mem-parse feed menggunakan SimpleXML (native PHP)
// Simulasikan logika parsing yang sama dengan yang Anda inginkan dari Python
$feed = @simplexml_load_file($feed_url);

if ($feed && $feed->entry) {
    echo '<section class="article-section">';
    echo '<div class="container-wrapper">';
    echo '<h2>Artikel Terbaru SagaHealth</h2>';
    echo '<div class="article-grid">';

    foreach ($feed->entry as $entry) {
        if ($article_count >= $max_articles) {
            break;
        }

        // 1. Mengambil Judul dan Link
        $title = (string)$entry->title;
        $link = '';
        foreach ($entry->link as $link_tag) {
            // Link utama selalu memiliki rel="alternate"
            if ($link_tag->attributes()->rel == 'alternate') {
                $link = (string)$link_tag->attributes()->href;
                break;
            }
        }
        
        // 2. Mengambil Summary dan Membersihkan HTML (simulasi BeautifulSoup)
        $summary = (string)$entry->summary;
        // Hapus tag HTML dan potong teksnya
        $preview = strip_tags($summary);
        $preview = substr($preview, 0, 250) . '...'; // Ambil 150 karakter pertama

        // Menampilkan kartu artikel
        echo '<a href="' . htmlspecialchars($link) . '" class="article-card">';
        echo '<h3>' . htmlspecialchars($title) . '</h3>';
        echo '<p>' . htmlspecialchars($preview) . '</p>';
        echo '<span>Baca Selengkapnya <i class="fas fa-arrow-right"></i></span>';
        echo '</a>';

        $article_count++;
    }

    echo '</div>'; // .article-grid
    echo '</div>'; // .container-wrapper
    echo '</section>'; // .article-section
} else {
    // Pesan jika feed gagal dimuat
    echo '<section class="article-section"><div class="container-wrapper"><p>Maaf, gagal memuat artikel terbaru dari Blogger.</p></div></section>';
}
?>

    
<section class="promo-section">
    <div class="container-wrapper">
        <h2 class="text-xl font-semibold mb-6 text-center">Promo & Penawaran Hari Ini</h2> 
        <br>
        
        
        <div class="promo-container"> 
            <div class="promo-card">
                <img src="../assets/img/basic.png" alt="Promo Card" class="w-full h-auto">
            </div>
            
            <div class="promo-card">
                <img src="../assets/img/premium.png" alt="Promo Card" class="w-full h-auto">
            </div>
        </div>
    </div>
</section>

<?php 
    include '../partials/footer.php'; 
    ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // === BAGIAN CAROUSEL POSTER UTAMA (SUDAH ADA) ===
            const carousel = document.getElementById('main-poster-carousel');
            const singlePosterMobile = document.getElementById('single-poster-mobile');
            let autoSlideInterval;

            function initCarousel() {
                if (!carousel) return;

                const track = carousel.querySelector('.carousel-track');
                const items = carousel.querySelectorAll('.carousel-item');
                const indicatorsContainer = carousel.querySelector('.carousel-indicators');
                const prevButton = carousel.querySelector('.prev-button');
                const nextButton = carousel.querySelector('.next-button');
                
                const itemCount = items.length;
                let currentIndex = 0;
                const intervalTime = 4000;
                
                let startX = 0;
                let isDragging = false;
                let currentTranslate = 0;
                let prevTranslate = 0;
                let animationID;

                function createIndicators() {
                    indicatorsContainer.innerHTML = '';
                    for (let i = 0; i < itemCount; i++) {
                        const dot = document.createElement('div');
                        dot.classList.add('indicator-dot');
                        dot.dataset.index = i;
                        if (i === 0) dot.classList.add('active');
                        indicatorsContainer.appendChild(dot);
                    }
                    indicatorsContainer.addEventListener('click', (e) => {
                        if (e.target.classList.contains('indicator-dot')) {
                            const index = parseInt(e.target.dataset.index);
                            goToSlide(index);
                            resetAutoSlide();
                        }
                    });
                }

                function updateIndicators() {
                    const dots = indicatorsContainer.querySelectorAll('.indicator-dot');
                    dots.forEach((dot, index) => {
                        dot.classList.toggle('active', index === currentIndex);
                    });
                }

                function updateCarousel() {
                    const itemWidth = carousel.offsetWidth;
                    currentTranslate = -currentIndex * itemWidth;
                    setSliderPosition();
                    updateIndicators();
                }

                function setSliderPosition() {
                    track.style.transform = `translateX(${currentTranslate}px)`;
                }

                function goToSlide(index) {
                    currentIndex = index;
                    updateCarousel();
                }

                function nextSlide() {
                    currentIndex = (currentIndex + 1) % itemCount;
                    updateCarousel();
                }

                function prevSlide() {
                    currentIndex = (currentIndex - 1 + itemCount) % itemCount;
                    updateCarousel();
                }
                
                function setItemWidths() {
                    const itemWidth = carousel.offsetWidth;
                    items.forEach(item => {
                        item.style.width = `${itemWidth}px`;
                    });
                    updateCarousel(); 
                }

                function startAutoSlide() {
                    clearInterval(autoSlideInterval); // Pastikan interval sebelumnya berhenti
                    autoSlideInterval = setInterval(nextSlide, intervalTime);
                }

                function resetAutoSlide() {
                    clearInterval(autoSlideInterval);
                    startAutoSlide();
                }

                prevButton.addEventListener('click', () => {
                    prevSlide();
                    resetAutoSlide();
                });

                nextButton.addEventListener('click', () => {
                    nextSlide();
                    resetAutoSlide();
                });
                
                carousel.addEventListener('mousedown', startDrag);
                carousel.addEventListener('mouseup', endDrag);
                carousel.addEventListener('mouseleave', endDrag);
                carousel.addEventListener('mousemove', drag);
                carousel.addEventListener('touchstart', startDrag);
                carousel.addEventListener('touchend', endDrag);
                carousel.addEventListener('touchmove', drag);

                function getPositionX(event) {
                    return event.type.includes('mouse') ? event.pageX : event.touches[0].clientX;
                }

                function startDrag(event) {
                    if (event.target.closest('button')) return;
                    isDragging = true;
                    startX = getPositionX(event);
                    prevTranslate = currentTranslate;
                    track.style.transition = 'none';
                    cancelAnimationFrame(animationID);
                    resetAutoSlide();
                }

                function drag(event) {
                    if (!isDragging) return;
                    const currentPosition = getPositionX(event);
                    const movement = currentPosition - startX;
                    currentTranslate = prevTranslate + movement;
                    setSliderPosition();
                }

                function endDrag() {
                    if (!isDragging) return;
                    isDragging = false;
                    track.style.transition = 'transform 0.5s ease-in-out';
                    const movedBy = currentTranslate - prevTranslate;
                    const itemWidth = carousel.offsetWidth;
                    
                    if (movedBy < -itemWidth / 5) {
                        currentIndex = (currentIndex + 1) % itemCount;
                    } else if (movedBy > itemWidth / 5) {
                        currentIndex = (currentIndex - 1 + itemCount) % itemCount;
                    }
                    
                    goToSlide(currentIndex);
                    startAutoSlide();
                }

                createIndicators();
                setItemWidths();
                window.addEventListener('resize', setItemWidths); 
                startAutoSlide();
            }

            // Fungsi untuk menghentikan carousel
            function stopCarousel() {
                clearInterval(autoSlideInterval);
                if (carousel) {
                    carousel.removeEventListener('mousedown', startDrag);
                    carousel.removeEventListener('mouseup', endDrag);
                    carousel.removeEventListener('mouseleave', endDrag);
                    carousel.removeEventListener('mousemove', drag);
                    carousel.removeEventListener('touchstart', startDrag);
                    carousel.removeEventListener('touchend', endDrag);
                    carousel.removeEventListener('touchmove', drag);
                    // Sembunyikan tombol dan indikator
                    carousel.querySelector('.carousel-button.prev-button').style.display = 'none';
                    carousel.querySelector('.carousel-button.next-button').style.display = 'none';
                    carousel.querySelector('.carousel-indicators').style.display = 'none';
                }
            }
            
            // Fungsi untuk memulai carousel kembali
            function resumeCarousel() {
                if (carousel) {
                    // Tampilkan kembali tombol dan indikator
                    carousel.querySelector('.carousel-button.prev-button').style.display = '';
                    carousel.querySelector('.carousel-button.next-button').style.display = '';
                    carousel.querySelector('.carousel-indicators').style.display = '';
                }
                initCarousel(); // Panggil ulang inisialisasi untuk memastikan event listener terpasang
            }

            // Media Query untuk menampilkan/menyembunyikan carousel atau poster tunggal
            const mediaQueryMobile = window.matchMedia('(max-width: 640px)');

            function handleMobilePoster(e) {
                if (e.matches) {
                    // Mode mobile: tampilkan poster tunggal, sembunyikan carousel
                    if (singlePosterMobile) singlePosterMobile.style.display = 'block';
                    if (carousel) carousel.style.display = 'none';
                    stopCarousel();
                } else {
                    // Mode desktop: tampilkan carousel, sembunyikan poster tunggal
                    if (singlePosterMobile) singlePosterMobile.style.display = 'none';
                    if (carousel) carousel.style.display = 'block';
                    resumeCarousel(); // Mulai kembali carousel jika masuk mode desktop
                }
            }

            // Jalankan saat pertama kali dimuat
            handleMobilePoster(mediaQueryMobile);
            // Tambahkan listener untuk perubahan ukuran layar
            mediaQueryMobile.addEventListener('change', handleMobilePoster);


            // === SCRIPT BARU UNTUK MOBILE MENU ===
            const menuToggle = document.getElementById('mobile-menu-toggle');
            const navMenu = document.getElementById('main-nav-menu');

            if (menuToggle && navMenu) {
                menuToggle.addEventListener('click', () => {
                    navMenu.classList.toggle('active');
                });
            }
        });
    </script>
    
</body>
</html>