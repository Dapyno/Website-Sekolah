// ===== FILTER GURU =====
document.addEventListener('DOMContentLoaded', function () {
    const filterButtons = document.querySelectorAll('.btn-filter');
    const guruCards = document.querySelectorAll('#guruGrid .col-md-6');

    filterButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            // Remove active class dari semua button
            filterButtons.forEach(function (btn) {
                btn.classList.remove('active');
            });
            // Add active class ke button yang diklik
            this.classList.add('active');

            const filterValue = this.getAttribute('data-filter');

            guruCards.forEach(function (card) {
                const category = card.getAttribute('data-category');
                if (filterValue === 'all' || category === filterValue) {
                    card.style.display = 'block';
                    // Tambahkan animasi fade in
                    card.style.animation = 'fadeIn 0.5s ease';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    // ===== COUNTER ANIMATION =====
    const statNumbers = document.querySelectorAll('.stat-number[data-count]');

    function animateCounter(el) {
        const target = parseInt(el.getAttribute('data-count'));
        const duration = 1500;
        const step = Math.max(1, Math.floor(target / 30));
        let current = 0;

        const update = () => {
            current += step;
            if (current >= target) {
                el.textContent = target + '+';
                return;
            }
            el.textContent = current + '+';
            requestAnimationFrame(update);
        };
        update();
    }

    // Jalankan saat statistik terlihat
    const statsSection = document.querySelector('.guru-stats-modern');
    if (statsSection) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    statNumbers.forEach(el => {
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

    // ===== AOS INIT =====
    AOS.init({
        duration: 800,
        once: true,
        offset: 100,
        easing: 'ease-out-cubic'
    });
});

// ===== SEARCH GURU =====
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchGuru');
    const guruItems = document.querySelectorAll('.guru-item');
    const searchResult = document.getElementById('searchResult');

    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            const searchTerm = this.value.toLowerCase().trim();
            let hasResult = false;

            guruItems.forEach(function (item) {
                const nama = item.getAttribute('data-nama') || '';
                if (nama.includes(searchTerm) || searchTerm === '') {
                    item.style.display = 'block';
                    hasResult = true;
                } else {
                    item.style.display = 'none';
                }
            });

            // Tampilkan pesan jika tidak ada hasil
            if (searchResult) {
                if (!hasResult && searchTerm !== '') {
                    searchResult.style.display = 'block';
                } else {
                    searchResult.style.display = 'none';
                }
            }
        });
    }

    // ===== AOS INIT =====
    AOS.init({
        duration: 800,
        once: true,
        offset: 100,
        easing: 'ease-out-cubic'
    });
});

// ===== SEARCH GURU =====
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchGuru');
    const guruItems = document.querySelectorAll('.guru-item');
    const searchResult = document.getElementById('searchResult');

    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            const searchTerm = this.value.toLowerCase().trim();
            let hasResult = false;

            guruItems.forEach(function (item) {
                const nama = item.getAttribute('data-nama') || '';
                if (nama.includes(searchTerm) || searchTerm === '') {
                    item.style.display = 'block';
                    hasResult = true;
                } else {
                    item.style.display = 'none';
                }
            });

            if (searchResult) {
                if (!hasResult && searchTerm !== '') {
                    searchResult.style.display = 'block';
                } else {
                    searchResult.style.display = 'none';
                }
            }
        });
    }

    // ===== AOS INIT =====
    AOS.init({
        duration: 800,
        once: true,
        offset: 100,
        easing: 'ease-out-cubic'
    });
});