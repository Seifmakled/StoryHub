// Register Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.getElementById('registerForm');
    const alertMessage = document.getElementById('alertMessage');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirmPassword');
    const passwordStrength = document.getElementById('passwordStrength');

    // Toggle Password Visibility
    window.togglePassword = function(fieldId) {
        const input = document.getElementById(fieldId);
        const icon = fieldId === 'password' ? 
            document.getElementById('toggleIcon') : 
            document.getElementById('toggleIconConfirm');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    };

    // Password Strength Checker
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strength = checkPasswordStrength(password);
            
            passwordStrength.className = 'password-strength show';
            
            if (strength.score === 0) {
                passwordStrength.classList.add('weak');
            } else if (strength.score === 1) {
                passwordStrength.classList.add('medium');
            } else {
                passwordStrength.classList.add('strong');
            }
        });
    }

    function checkPasswordStrength(password) {
        let score = 0;
        
        if (password.length >= 8) score++;
        if (password.match(/[a-z]/) && password.match(/[A-Z]/)) score++;
        if (password.match(/[0-9]/)) score++;
        if (password.match(/[^a-zA-Z0-9]/)) score++;
        
        return {
            score: Math.min(score, 2),
            strength: score === 0 ? 'weak' : score === 1 ? 'medium' : 'strong'
        };
    }

    // Show Alert Message
    function showAlert(message, type = 'error') {
        alertMessage.className = `alert ${type}`;
        alertMessage.innerHTML = `
            <i class="fas fa-${type === 'error' ? 'exclamation-circle' : type === 'success' ? 'check-circle' : 'info-circle'}"></i>
            <span>${message}</span>
        `;
        alertMessage.style.display = 'flex';
        
        setTimeout(() => {
            alertMessage.style.display = 'none';
        }, 5000);
    }

    // Form Submission
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            // We'll validate client-side, then allow the real POST to proceed
            
            const fullName = document.getElementById('fullName').value.trim();
            const username = document.getElementById('username').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            const terms = document.getElementById('terms').checked;
            
            // Validation
            if (!fullName || !username || !email || !password || !confirmPassword) {
                showAlert('Please fill in all fields');
                return;
            }

            if (!isValidEmail(email)) {
                showAlert('Please enter a valid email address');
                return;
            }

            if (username.length < 3) {
                showAlert('Username must be at least 3 characters long');
                return;
            }

            if (password.length < 8) {
                showAlert('Password must be at least 8 characters long');
                return;
            }

            if (password !== confirmPassword) {
                showAlert('Passwords do not match');
                return;
            }

            if (!terms) {
                showAlert('You must agree to the Terms of Service');
                return;
            }

            // Pass validation -> allow native POST submit to controller
            // Optional: add loading state
            const btn = registerForm.querySelector('button[type="submit"]');
            btn.classList.add('loading');
            // Do not prevent default; let form submit
        });
    }

    // Email validation
    function isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    // Social registration buttons
    document.querySelectorAll('.btn-social').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const provider = this.querySelector('i').classList.contains('fa-google') ? 'Google' : 'Facebook';
            showAlert(`${provider} registration is not yet implemented`, 'info');
        });
    });
});