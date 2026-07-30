/* ============================================================
   COUNTER.JS - SMP Al Islam Krian
   Animasi counter up untuk statistik
   ============================================================ */

document.addEventListener('DOMContentLoaded', function() {
    'use strict';

    // ============================
    // COUNTER UP
    // ============================
    const counters = document.querySelectorAll('.counter');
    let countersAnimated = false;

    function animateCounter(counter) {
        const target = parseInt(counter.getAttribute('data-target'));
        const duration = 2000; // ms
        const stepTime = 16; // ~60fps
        const steps = duration / stepTime;
        const increment = target / steps;
        let current = 0;

        const updateCounter = function() {
            current += increment;
            if (current < target) {
                counter.textContent = Math.round(current);
                requestAnimationFrame(updateCounter);
            } else {
                counter.textContent = target.toLocaleString('id-ID');
            }
        };

        updateCounter();
    }

    function startCounters() {
        if (countersAnimated) return;
        countersAnimated = true;

        counters.forEach(function(counter) {
            // Reset counter
            counter.textContent = '0';
            animateCounter(counter);
        });
    }

    // ============================
    // INTERSECTION OBSERVER
    // ============================
    const statSection = document.querySelector('.statistics');
    if (statSection) {
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting && !countersAnimated) {
                    startCounters();
                }
            });
        }, {
            threshold: 0.3,
            rootMargin: '0px 0px -50px 0px'
        });

        observer.observe(statSection);
    }

    // ============================
    // FALLBACK: Jika observer tidak jalan
    // ============================
    // Coba jalankan setelah 3 detik jika belum jalan
    setTimeout(function() {
        if (!countersAnimated) {
            // Cek apakah elemen statistik terlihat
            const rect = statSection ? statSection.getBoundingClientRect() : null;
            if (rect && rect.top < window.innerHeight && rect.bottom > 0) {
                startCounters();
            }
        }
    }, 3000);

    // ============================
    // SCROLL EVENT FALLBACK
    // ============================
    let scrollTimeout;
    window.addEventListener('scroll', function() {
        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(function() {
            if (!countersAnimated && statSection) {
                const rect = statSection.getBoundingClientRect();
                if (rect.top < window.innerHeight - 100 && rect.bottom > 0) {
                    startCounters();
                }
            }
        }, 200);
    });

    console.log('Counter animations initialized!');
});