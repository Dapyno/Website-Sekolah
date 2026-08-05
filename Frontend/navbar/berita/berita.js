document.addEventListener('DOMContentLoaded', function () {
    AOS.init({ duration: 800, once: true, offset: 100, easing: 'ease-out-cubic' });
});

document.addEventListener('DOMContentLoaded', function () {
    const filterButtons = document.querySelectorAll('.btn-filter');
    const beritaCards = document.querySelectorAll('#beritaGrid .col-md-6');

    filterButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            filterButtons.forEach(function (btn) { btn.classList.remove('active'); });
            this.classList.add('active');

            const filterValue = this.getAttribute('data-filter');

            beritaCards.forEach(function (card) {
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