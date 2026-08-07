/* ============================================================
   EKSTRAKURIKULER.JS - SMP Al Islam Krian
   Fungsi untuk halaman Ekstrakurikuler
   ============================================================ */

document.addEventListener('DOMContentLoaded', function() {
    'use strict';

    // ============================
    // 1. AOS INIT
    // ============================
    AOS.init({
        duration: 800,
        once: true,
        offset: 100,
        easing: 'ease-out-cubic'
    });

    // ============================
    // 2. FILTER EKSKURIKULER
    // ============================
    const categoryButtons = document.querySelectorAll('.btn-category');
    const ekskulCards = document.querySelectorAll('#ekskulGrid .col-md-6');

    categoryButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            // Remove active class dari semua button
            categoryButtons.forEach(function(btn) {
                btn.classList.remove('active');
            });
            // Add active class ke button yang diklik
            this.classList.add('active');

            const category = this.getAttribute('data-category');

            ekskulCards.forEach(function(card) {
                const cardCategory = card.getAttribute('data-category');
                if (category === 'all' || cardCategory === category) {
                    card.style.display = 'block';
                    // Tambahkan animasi fade in
                    card.style.animation = 'fadeIn 0.5s ease';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    // ============================
    // 3. COUNTER ANIMATION (untuk statistik)
    // ============================
    const statNumbers = document.querySelectorAll('.stat-card-ekskul .stat-number');

    function animateCounter(el) {
        const text = el.textContent;
        // Jika teks mengandung '+', ambil angka sebelum '+'
        let target = parseInt(text.replace('+', ''));
        if (isNaN(target)) {
            // Jika bukan angka (misal "100%"), skip
            return;
        }
        
        const duration = 1500;
        const step = Math.max(1, Math.floor(target / 30));
        let current = 0;

        const update = () => {
            current += step;
            if (current >= target) {
                el.textContent = target + (text.includes('+') ? '+' : '');
                return;
            }
            el.textContent = current + (text.includes('+') ? '+' : '');
            requestAnimationFrame(update);
        };
        update();
    }

    // Jalankan saat statistik terlihat
    const statsSection = document.querySelector('.ekskul-stats');
    if (statsSection) {
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    statNumbers.forEach(function(el) {
                        if (!el.dataset.animated) {
                            el.dataset.animated = 'true';
                            animateCounter(el);
                        }
                    });
                }
            });
        }, { threshold: 0.3 });
        observer.observe(statsSection);
    }

    // ============================
    // 4. HOVER EFFECT CARD (opsional)
    // ============================
    const ekskulCardsHover = document.querySelectorAll('.ekskul-card');
    ekskulCardsHover.forEach(function(card) {
        card.addEventListener('mouseenter', function() {
            const icon = this.querySelector('.ekskul-icon-wrapper i');
            if (icon) {
                icon.style.transition = 'transform 0.3s ease';
                icon.style.transform = 'scale(1.2) rotate(5deg)';
            }
        });
        card.addEventListener('mouseleave', function() {
            const icon = this.querySelector('.ekskul-icon-wrapper i');
            if (icon) {
                icon.style.transform = 'scale(1) rotate(0deg)';
            }
        });
    });

    console.log('Ekstrakurikuler page loaded successfully!');
});