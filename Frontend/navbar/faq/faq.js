document.addEventListener('DOMContentLoaded', function () {
    AOS.init({
        duration: 800,
        once: true,
        offset: 100,
        easing: 'ease-out-cubic'
    });
});

// ===== FILTER FAQ =====
document.addEventListener('DOMContentLoaded', function () {
    const categoryButtons = document.querySelectorAll('.btn-category');
    const faqGroups = document.querySelectorAll('.faq-group');

    categoryButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            // Remove active class dari semua button
            categoryButtons.forEach(function (btn) {
                btn.classList.remove('active');
            });
            // Add active class ke button yang diklik
            this.classList.add('active');

            const category = this.getAttribute('data-category');

            faqGroups.forEach(function (group) {
                const groupCategory = group.getAttribute('data-category');
                if (category === 'all' || groupCategory === category) {
                    group.style.display = 'block';
                    // Tambahkan animasi fade in
                    group.style.animation = 'fadeIn 0.5s ease';
                } else {
                    group.style.display = 'none';
                }
            });
        });
    });
});