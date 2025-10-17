// Verify Email JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const verifyForm = document.getElementById('verifyForm');
    const resendCodeBtn = document.getElementById('resendCode');
    const resendTimer = document.getElementById('resendTimer');
    const countdown = document.getElementById('countdown');
    const alertMessage = document.getElementById('alertMessage');
    const successMessage = document.getElementById('successMessage');

    let timer = null;
    let timeLeft = 60;

    // Setup Verification Inputs
    setupVerificationInputs();

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

    // Form Submission
    if (verifyForm) {
        verifyForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const code = getVerificationCode();
            
            if (code.length !== 6) {
                showAlert('Please enter the complete verification code');
                return;
            }

            const btn = this.querySelector('button[type="submit"]');
            btn.classList.add('loading');
            
            // Simulate API call
            setTimeout(() => {
                btn.classList.remove('loading');
                
                // In real app, verify with backend
                // For demo, accept code "123456"
                if (code === '123456') {
                    verifyForm.style.display = 'none';
                    successMessage.style.display = 'block';
                } else {
                    showAlert('Invalid verification code. Please try again.');
                    clearVerificationInputs();
                }
            }, 1500);
        });
    }

    // Resend Code
    if (resendCodeBtn) {
        resendCodeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Disable button and start countdown
            resendCodeBtn.style.display = 'none';
            resendTimer.style.display = 'block';
            startCountdown();
            
            // Simulate sending code
            showAlert('A new verification code has been sent to your email', 'success');
        });
    }

    // Helper Functions
    function setupVerificationInputs() {
        const inputs = document.querySelectorAll('.verification-input');
        
        inputs.forEach((input, index) => {
            // Auto-focus next input
            input.addEventListener('input', function() {
                if (this.value.length === 1) {
                    if (index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                }
            });

            // Handle backspace
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && this.value === '') {
                    if (index > 0) {
                        inputs[index - 1].focus();
                    }
                }
            });

            // Handle paste
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

            // Only allow numbers
            input.addEventListener('keypress', function(e) {
                if (!/[0-9]/.test(e.key)) {
                    e.preventDefault();
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

    function clearVerificationInputs() {
        for (let i = 1; i <= 6; i++) {
            document.getElementById(`code${i}`).value = '';
        }
        document.getElementById('code1').focus();
    }

    function startCountdown() {
        timeLeft = 60;
        countdown.textContent = timeLeft;
        
        timer = setInterval(() => {
            timeLeft--;
            countdown.textContent = timeLeft;
            
            if (timeLeft <= 0) {
                clearInterval(timer);
                resendTimer.style.display = 'none';
                resendCodeBtn.style.display = 'inline';
            }
        }, 1000);
    }

    // Auto-focus first input on load
    document.getElementById('code1').focus();
});
