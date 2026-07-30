/* ============================================================
   SLIDER.JS - SMP Al Islam Krian
   Inisialisasi SwiperJS untuk semua slider
   ============================================================ */

document.addEventListener('DOMContentLoaded', function() {
    'use strict';

    // ============================
    // 1. GURU SLIDER
    // ============================
    const guruSwiper = new Swiper('.guru-swiper', {
        slidesPerView: 1,
        spaceBetween: 20,
        loop: true,
        autoplay: {
            delay: 4000,
            disableOnInteraction: true,
        },
        pagination: {
            el: '.guru-swiper .swiper-pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.guru-swiper .swiper-button-next',
            prevEl: '.guru-swiper .swiper-button-prev',
        },
        breakpoints: {
            576: {
                slidesPerView: 2,
                spaceBetween: 20,
            },
            768: {
                slidesPerView: 3,
                spaceBetween: 24,
            },
            1024: {
                slidesPerView: 4,
                spaceBetween: 30,
            },
        },
        on: {
            init: function() {
                // Tambahkan AOS setelah slider siap
                if (typeof AOS !== 'undefined') {
                    AOS.refresh();
                }
            }
        }
    });

    // ============================
    // 2. TESTIMONI SLIDER
    // ============================
    const testimoniSwiper = new Swiper('.testimoni-swiper', {
        slidesPerView: 1,
        spaceBetween: 20,
        loop: true,
        autoplay: {
            delay: 5000,
            disableOnInteraction: true,
        },
        pagination: {
            el: '.testimoni-swiper .swiper-pagination',
            clickable: true,
        },
        breakpoints: {
            576: {
                slidesPerView: 1,
                spaceBetween: 20,
            },
            768: {
                slidesPerView: 2,
                spaceBetween: 24,
            },
            1024: {
                slidesPerView: 3,
                spaceBetween: 30,
            },
        },
        on: {
            init: function() {
                if (typeof AOS !== 'undefined') {
                    AOS.refresh();
                }
            }
        }
    });

    // ============================
    // 3. HERO SLIDER (fallback JS)
    // ============================
    // Sudah di script.js, tapi kita tambahkan fallback
    const heroSlides = document.querySelectorAll('.hero-slide');
    if (heroSlides.length > 0 && !document.querySelector('.hero-slide.active')) {
        heroSlides[0].classList.add('active');
    }

    console.log('Swiper sliders initialized successfully!');
});