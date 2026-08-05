// ============================================================
// LOGIN.JS - Admin SMP Al Islam Krian
// ============================================================

document.addEventListener('DOMContentLoaded', function() {
    'use strict';

    // ============================
    // 1. PASSWORD TOGGLE
    // ============================
    document.querySelectorAll('.password-toggle').forEach(function(toggle) {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const inputGroup = this.closest('.input-group');
            const input = inputGroup.querySelector('.form-control');
            const icon = this.querySelector('i');
            
            if (input && input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
                this.setAttribute('aria-label', 'Sembunyikan password');
            } else if (input) {
                input.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
                this.setAttribute('aria-label', 'Tampilkan password');
            }
        });
    });

    // ============================
    // 2. LOADING STATE PADA TOMBOL LOGIN
    // ============================
    const loginForm = document.querySelector('form');
    const loginBtn = document.querySelector('.btn-login');

    if (loginForm && loginBtn) {
        loginForm.addEventListener('submit', function(e) {
            // Cek validitas form
            if (!this.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
                this.classList.add('was-validated');
                
                // Fokus ke input pertama yang invalid
                const firstInvalid = this.querySelector(':invalid');
                if (firstInvalid) {
                    firstInvalid.focus();
                }
                return;
            }

            // Jika valid, tambahkan loading state
            loginBtn.classList.add('loading');
            loginBtn.disabled = true;
            loginBtn.innerHTML = `
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                <span>Memproses...</span>
            `;

            // Submit form akan berjalan normal
        });
    }

    // ============================
    // 3. AUTO FOCUS KE INPUT USERNAME
    // ============================
    const usernameInput = document.getElementById('username');
    if (usernameInput) {
        // Hapus readonly setelah halaman dimuat
        setTimeout(function() {
            usernameInput.removeAttribute('readonly');
        }, 100);
    }

    // ============================
    // 4. CLEAR FORM SAAT LOAD (opsional)
    // ============================
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('reset', function() {
            // Reset form jika diperlukan
        });
    }

    // ============================
    // 5. ENTER KEY UNTUK SUBMIT
    // ============================
    document.querySelectorAll('.form-control').forEach(function(input) {
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const submitBtn = document.querySelector('.btn-login');
                if (submitBtn) {
                    submitBtn.click();
                }
            }
        });
    });

    // ============================
    // 6. REMOVE LOADING STATE JIKA ADA ERROR (dari PHP)
    // ============================
    // Cek apakah ada alert error dari PHP
    const alertDanger = document.querySelector('.alert-danger');
    if (alertDanger && loginBtn) {
        loginBtn.classList.remove('loading');
        loginBtn.disabled = false;
        loginBtn.innerHTML = `
            <i class="bi bi-box-arrow-in-right"></i>
            <span>Login</span>
        `;
    }

    console.log('Login page scripts loaded successfully!');
});

// ============================
// 7. FUNGSI GLOBAL UNTUK PASSWORD TOGGLE (fallback)
// ============================
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleBtn = document.querySelector('.password-toggle');
    
    if (!passwordInput) return;
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        if (toggleBtn) {
            const icon = toggleBtn.querySelector('i');
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        }
    } else {
        passwordInput.type = 'password';
        if (toggleBtn) {
            const icon = toggleBtn.querySelector('i');
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }
}