// Login Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const alertMessage = document.getElementById('alertMessage');

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
            e.preventDefault();
            
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const remember = document.getElementById('remember').checked;
            
            // Basic validation
            if (!email || !password) {
                showAlert('Please fill in all fields');
                return;
            }

            if (!isValidEmail(email)) {
                showAlert('Please enter a valid email address');
                return;
            }

            // Show loading state
            const btn = loginForm.querySelector('button[type="submit"]');
            btn.classList.add('loading');
            
            // Simulate API call
            setTimeout(() => {
                // Here you would make an actual API call
                // For now, we'll simulate a response
                
                // Example: Successful login
                // window.location.href = 'index.php?page=home';
                
                // Example: Failed login
                btn.classList.remove('loading');
                showAlert('Invalid email or password');
                
                // For demo purposes, let's redirect after 2 seconds
                // setTimeout(() => {
                //     window.location.href = 'index.php?page=home';
                // }, 2000);
            }, 1500);
        });
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
