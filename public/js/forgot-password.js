// Forgot Password JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const forgotPasswordForm = document.getElementById('forgotPasswordForm');
    const verifyCodeForm = document.getElementById('verifyCodeForm');
    const resetPasswordForm = document.getElementById('resetPasswordForm');
    const successMessage = document.getElementById('successMessage');
    const alertMessage = document.getElementById('alertMessage');

    // Toggle Password Visibility
    window.togglePassword = function(fieldId) {
        const input = document.getElementById(fieldId);
        const icon = fieldId === 'newPassword' ? 
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

    // Show Alert
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

    // Step 1: Send Reset Code
    if (forgotPasswordForm) {
        forgotPasswordForm.addEventListener('submit', function(e) {
            const email = document.getElementById('email').value.trim();
            
            if (!email || !isValidEmail(email)) {
                e.preventDefault();
                showAlert('Please enter a valid email address');
                return;
            }
            // Form will submit normally to the controller
        });
    }

    // Step 2: Verify Code
    if (verifyCodeForm) {
        verifyCodeForm.addEventListener('submit', function(e) {
            const code = document.getElementById('resetCode').value.trim();
            
            if (code.length !== 6) {
                e.preventDefault();
                showAlert('Please enter the complete 6-digit verification code');
                return;
            }
            // Form will submit normally to the controller
        });
    }

    // Resend Code
    const resendCode = document.getElementById('resendCode');
    if (resendCode) {
        resendCode.addEventListener('click', function(e) {
            e.preventDefault();
            showAlert('Verification code resent to your email!', 'success');
        });
    }

    // Step 3: Reset Password
    if (resetPasswordForm) {
        resetPasswordForm.addEventListener('submit', function(e) {
            const newPassword = document.getElementById('newPassword').value;
            const confirmNewPassword = document.getElementById('confirmNewPassword').value;
            
            if (!newPassword || !confirmNewPassword) {
                e.preventDefault();
                showAlert('Please fill in all fields');
                return;
            }

            if (newPassword.length < 8) {
                e.preventDefault();
                showAlert('Password must be at least 8 characters long');
                return;
            }

            if (newPassword !== confirmNewPassword) {
                e.preventDefault();
                showAlert('Passwords do not match');
                return;
            }
            // Form will submit normally to the controller
        });
    }

    // Helper Functions
    function isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    function setupVerificationInputs() {
        const inputs = document.querySelectorAll('.verification-input');
        
        inputs.forEach((input, index) => {
            input.addEventListener('input', function() {
                if (this.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });

            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && this.value === '' && index > 0) {
                    inputs[index - 1].focus();
                }
            });

            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const pasteData = e.clipboardData.getData('text');
                const digits = pasteData.replace(/\D/g, '').slice(0, 6);
                
                digits.split('').forEach((digit, i) => {
                    if (inputs[i]) {
                        inputs[i].value = digit;
                    }
                });
                
                if (digits.length === 6) {
                    inputs[5].focus();
                }
            });
        });
    }

    function getVerificationCode() {
        let code = '';
        for (let i = 1; i <= 6; i++) {
            code += document.getElementById(`code${i}`).value;
        }
        return code;
    }
});
