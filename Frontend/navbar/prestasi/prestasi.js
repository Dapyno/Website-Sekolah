// ===== AOS INIT =====
document.addEventListener('DOMContentLoaded', function () {
    AOS.init({
        duration: 800,
        once: true,
        offset: 100,
        easing: 'ease-out-cubic'
    });
});

// ===== FILTER PRESTASI =====
document.addEventListener('DOMContentLoaded', function () {
    const filterButtons = document.querySelectorAll('.btn-filter');
    const prestasiCards = document.querySelectorAll('#prestasiGrid .col-md-6');

    filterButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            // Remove active class dari semua button
            filterButtons.forEach(function (btn) {
                btn.classList.remove('active');
            });
            // Add active class ke button yang diklik
            this.classList.add('active');

            const filterValue = this.getAttribute('data-filter');

            prestasiCards.forEach(function (card) {
                const category = card.getAttribute('data-category');
                if (filterValue === 'all' || category === filterValue) {
                    card.style.display = 'block';
                    card.style.animation = 'fadeIn 0.5s ease';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
});