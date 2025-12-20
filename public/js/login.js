// Login Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const alertMessage = document.getElementById('alertMessage');
    const credentialsInput = document.getElementById('email_or_username');

    // Toggle Password Visibility
    window.togglePassword = function() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    };

    // Show Alert Message
    function showAlert(message, type = 'error') {
        alertMessage.className = `alert ${type}`;
        alertMessage.innerHTML = `
            <i class="fas fa-${type === 'error' ? 'exclamation-circle' : 'check-circle'}"></i>
            <span>${message}</span>
        `;
        alertMessage.style.display = 'flex';
        
        // Auto hide after 5 seconds
        setTimeout(() => {
            alertMessage.style.display = 'none';
        }, 5000);
    }

    // Form Submission
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            const emailOrUsername = credentialsInput ? credentialsInput.value.trim() : '';
            const password = document.getElementById('password').value;

            // Basic validation
            if (!emailOrUsername || !password) {
                e.preventDefault();
                showAlert('Please fill in all fields');
                return;
            }

            if (emailOrUsername.includes('@') && !isValidEmail(emailOrUsername)) {
                e.preventDefault();
                showAlert('Please enter a valid email address');
                return;
            }
        });
    }

    // Surface server-side login errors as toasts
    const params = new URLSearchParams(window.location.search);
    const error = params.get('error');
    if (error) {
        const messages = {
            empty_fields: 'Please fill in all fields',
            user_not_found: 'Email or username not found',
            invalid_credentials: 'Incorrect password',
            invalid_request: 'Please submit the login form to sign in',
            db_error: 'Something went wrong. Please try again'
        };
        showAlert(messages[error] || 'Unable to sign in', 'error');
    }

    // Email validation
    function isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    // Social login buttons
    document.querySelectorAll('.btn-social').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const provider = this.querySelector('i').classList.contains('fa-google') ? 'Google' : 'Facebook';
            showAlert(`${provider} login is not yet implemented`, 'info');
        });
    });
});
