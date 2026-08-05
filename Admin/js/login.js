// ===== PASSWORD TOGGLE =====
document.querySelectorAll('.password-toggle').forEach(function(toggle) {
    toggle.addEventListener('click', function() {
        const input = this.closest('.input-group').querySelector('.form-control');
        const icon = this.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    });
});

// ===== LOADING STATE =====
document.querySelector('.btn-login').addEventListener('click', function(e) {
    if (this.closest('form').checkValidity()) {
        this.classList.add('loading');
        // Remove loading setelah 2 detik (simulasi)
        setTimeout(() => {
            this.classList.remove('loading');
        }, 2000);
    }
});