document.addEventListener('DOMContentLoaded', function() {
    'use strict';

    // ============================
    // 1. LOADING SCREEN
    // ============================
    const loadingScreen = document.getElementById('loading-screen');
    window.addEventListener('load', function() {
        setTimeout(function() {
            loadingScreen.classList.add('hidden');
        }, 1200);
    });

    // ============================
    // 2. NAVBAR SCROLL EFFECT
    // ============================
    const navbar = document.getElementById('mainNav');
    let lastScroll = 0;

    window.addEventListener('scroll', function() {
        const currentScroll = window.pageYOffset || document.documentElement.scrollTop;

        if (currentScroll > 80) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }

        // Scroll progress bar
        const scrollProgress = document.getElementById('scroll-progress-bar');
        const scrollHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        const progress = (currentScroll / scrollHeight) * 100;
        scrollProgress.style.width = progress + '%';

        lastScroll = currentScroll;
    });

    // ============================
    // 3. BACK TO TOP
    // ============================
    const backToTopBtn = document.getElementById('backToTop');

    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 400) {
            backToTopBtn.classList.add('show');
        } else {
            backToTopBtn.classList.remove('show');
        }
    });

    backToTopBtn.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });

    // ============================
    // 4. LIVE CLOCK
    // ============================
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const clockElement = document.getElementById('clock');
        if (clockElement) {
            clockElement.textContent = hours + ':' + minutes + ':' + seconds;
        }
    }
    updateClock();
    setInterval(updateClock, 1000);

    // ============================
    // 5. RIPPLE BUTTON EFFECT
    // ============================
    document.querySelectorAll('.ripple').forEach(function(button) {
        button.addEventListener('click', function(e) {
            const rect = button.getBoundingClientRect();
            const ripple = document.createElement('span');
            ripple.classList.add('ripple-effect');
            const size = Math.max(rect.width, rect.height);
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = (e.clientX - rect.left - size/2) + 'px';
            ripple.style.top = (e.clientY - rect.top - size/2) + 'px';
            button.appendChild(ripple);
            setTimeout(function() {
                ripple.remove();
            }, 600);
        });
    });

    // ============================
    // 6. DARK MODE TOGGLE
    // ============================
    const darkModeToggle = document.getElementById('darkModeToggle');
    const icon = darkModeToggle.querySelector('i');

    // Check saved preference
    if (localStorage.getItem('darkMode') === 'enabled') {
        document.body.classList.add('dark-mode');
        icon.classList.replace('bi-moon-fill', 'bi-sun-fill');
    }

    darkModeToggle.addEventListener('click', function() {
        document.body.classList.toggle('dark-mode');
        const isDark = document.body.classList.contains('dark-mode');
        if (isDark) {
            icon.classList.replace('bi-moon-fill', 'bi-sun-fill');
            localStorage.setItem('darkMode', 'enabled');
        } else {
            icon.classList.replace('bi-sun-fill', 'bi-moon-fill');
            localStorage.setItem('darkMode', 'disabled');
        }
    });

    // ============================
    // 7. CUSTOM CURSOR (Desktop only)
    // ============================
    if (window.innerWidth >= 1024) {
        const cursor = document.createElement('div');
        cursor.classList.add('custom-cursor');
        document.body.appendChild(cursor);

        document.addEventListener('mousemove', function(e) {
            cursor.style.left = e.clientX + 'px';
            cursor.style.top = e.clientY + 'px';
            cursor.style.display = 'block';
        });

        // Hover effect on interactive elements
        document.querySelectorAll('a, button, .quick-card, .program-card, .fasilitas-card, .berita-card, .guru-card, .galeri-item, .timeline-content, .nav-link, .dropdown-item, .social-icons a, .list-group-item').forEach(function(el) {
            el.addEventListener('mouseenter', function() {
                cursor.classList.add('active');
            });
            el.addEventListener('mouseleave', function() {
                cursor.classList.remove('active');
            });
        });

        document.addEventListener('mouseleave', function() {
            cursor.style.display = 'none';
        });
    }

    // ============================
    // 8. SMOOTH SCROLL FOR NAV LINKS
    // ============================
    document.querySelectorAll('.navbar .nav-link, .footer ul li a, .dropdown-item').forEach(function(link) {
        link.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            if (targetId && targetId.startsWith('#')) {
                e.preventDefault();
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    const offsetTop = targetElement.getBoundingClientRect().top + window.pageYOffset - 76;
                    window.scrollTo({
                        top: offsetTop,
                        behavior: 'smooth'
                    });
                    // Close mobile menu
                    const navbarCollapse = document.querySelector('.navbar-collapse');
                    if (navbarCollapse.classList.contains('show')) {
                        navbarCollapse.classList.remove('show');
                    }
                }
            }
        });
    });

    // ============================
    // 9. ACTIVE MENU LINK
    // ============================
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.navbar .nav-link');

    window.addEventListener('scroll', function() {
        let current = '';
        sections.forEach(function(section) {
            const sectionTop = section.offsetTop - 100;
            if (window.pageYOffset >= sectionTop) {
                current = section.getAttribute('id');
            }
        });

        navLinks.forEach(function(link) {
            link.classList.remove('active');
            if (link.getAttribute('href') === '#' + current) {
                link.classList.add('active');
            }
        });
    });

    // ============================
    // 10. LAZY LOAD IMAGES
    // ============================
    document.querySelectorAll('img[loading="lazy"]').forEach(function(img) {
        img.classList.add('lazy');
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    const imgElement = entry.target;
                    imgElement.src = imgElement.dataset.src || imgElement.src;
                    imgElement.classList.add('loaded');
                    observer.unobserve(imgElement);
                }
            });
        });
        observer.observe(img);
    });

    // ============================
    // 11. AOS INIT
    // ============================
    AOS.init({
        duration: 800,
        once: true,
        offset: 100,
        easing: 'ease-out-cubic',
        disable: window.innerWidth < 768 ? true : false
    });

    // ============================
    // 12. SWIPER SLIDER INIT (dari slider.js)
    // ============================
    // Dipindahkan ke slider.js

    // ============================
    // 13. COUNTER UP (dari counter.js)
    // ============================
    // Dipindahkan ke counter.js

    // ============================
    // 14. HERO SLIDER (auto slideshow)
    // ============================
    let slideIndex = 0;
    const slides = document.querySelectorAll('.hero-slide');
    if (slides.length > 0) {
        function showSlide(index) {
            slides.forEach(function(slide, i) {
                slide.classList.remove('active');
                if (i === index) {
                    slide.classList.add('active');
                }
            });
        }

        function nextSlide() {
            slideIndex = (slideIndex + 1) % slides.length;
            showSlide(slideIndex);
        }

        // Auto play setiap 5 detik
        setInterval(nextSlide, 5000);
        showSlide(0);
    }

    console.log('SMP Al Islam Krian - Website loaded successfully!');
});