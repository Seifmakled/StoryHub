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
                    // Redirect to complete profile setup page
                    window.location.href = '/StoryHub/index.php?url=complete-profile';
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
        const boxes = Array.from(document.querySelectorAll('.verification-input'));
        const hidden = document.getElementById('codeHidden');
        
        // Initialize values
        boxes.forEach(b => (b.value = ''));
        if (hidden) hidden.value = '';

        // Helpers to sync hidden and boxes
        function syncHiddenFromBoxes() {
            if (!hidden) return;
            hidden.value = boxes.map(b => (b.value || '').replace(/\D/g, '').slice(0, 1)).join('');
        }
        function fillBoxesFromString(s) {
            const digits = (s || '').replace(/\D/g, '').slice(0, boxes.length);
            boxes.forEach((b, i) => {
                b.value = digits[i] || '';
            });
            // Focus next empty box or last filled
            const nextIdx = Math.min(digits.length, boxes.length - 1);
            boxes[nextIdx].focus();
        }

        // Per-box input behavior: type to auto-advance
        boxes.forEach((box, idx) => {
            // Input: keep one digit, advance
            box.addEventListener('input', (e) => {
                const v = (box.value || '').replace(/\D/g, '');
                // If user pasted multiple chars into a single box, distribute
                if (v.length > 1) {
                    const remaining = v.slice(0, boxes.length - idx);
                    for (let i = 0; i < remaining.length; i++) {
                        const target = boxes[idx + i];
                        if (target) target.value = remaining[i];
                    }
                    const newIdx = Math.min(idx + remaining.length, boxes.length - 1);
                    boxes[newIdx].focus();
                } else {
                    box.value = v.slice(0, 1);
                    if (box.value && idx < boxes.length - 1) boxes[idx + 1].focus();
                }
                syncHiddenFromBoxes();
            });

            // Keydown: handle backspace and arrows
            box.addEventListener('keydown', (e) => {
                const key = e.key;
                if (key === 'Backspace') {
                    if ((box.value || '').length === 0 && idx > 0) {
                        boxes[idx - 1].focus();
                        boxes[idx - 1].value = '';
                        syncHiddenFromBoxes();
                        e.preventDefault();
                    }
                    return; // allow default deletion when not empty
                }
                if (key === 'ArrowLeft' && idx > 0) {
                    boxes[idx - 1].focus();
                    e.preventDefault();
                } else if (key === 'ArrowRight' && idx < boxes.length - 1) {
                    boxes[idx + 1].focus();
                    e.preventDefault();
                }
            });

            // Paste: distribute digits starting at current index
            box.addEventListener('paste', (e) => {
                const text = (e.clipboardData || window.clipboardData)?.getData('text') || '';
                const digits = text.replace(/\D/g, '');
                if (!digits) return;
                e.preventDefault();
                const max = Math.min(digits.length, boxes.length - idx);
                for (let i = 0; i < max; i++) {
                    boxes[idx + i].value = digits[i];
                }
                const nextIdx = Math.min(idx + max, boxes.length - 1);
                boxes[nextIdx].focus();
                syncHiddenFromBoxes();
            });
        });

        // If hidden input exists, listen for OTP autofill and mirror into boxes
        if (hidden) {
            hidden.addEventListener('input', () => {
                hidden.value = hidden.value.replace(/\D/g, '').slice(0, boxes.length);
                fillBoxesFromString(hidden.value);
            });

            hidden.addEventListener('paste', () => {
                setTimeout(() => {
                    hidden.value = hidden.value.replace(/\D/g, '').slice(0, boxes.length);
                    fillBoxesFromString(hidden.value);
                }, 0);
            });
        }

        // Focus first visible box for typing
        const first = document.getElementById('code1');
        if (first) first.focus();
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
        const hidden = document.getElementById('codeHidden');
        if (hidden) hidden.value = '';
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

    // Auto-focus first input on load (guarded)
    const firstBox = document.getElementById('code1');
    if (firstBox) firstBox.focus();
});
