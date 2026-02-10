<?php
/**
 * OTP Email Verification View
 * Displayed after successful signup - user must enter 6-digit OTP to verify email
 * Follows ReidHub design system: Poppins font, blue primary color (#0466C8), two-section layout
 */
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ReidHub | Verify Email</title>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="/css/auth/globals.css">
        <link rel="stylesheet" href="/css/home/globals.css">
        <style>
            /* OTP Input Styling */
            .otp-container {
                display: flex;
                gap: 8px;
                justify-content: center;
                margin: 1.5rem 0 2rem 0;
            }

            .otp-input {
                width: 50px;
                height: 50px;
                font-size: 24px;
                font-weight: 600;
                text-align: center;
                border: 2px solid #e0e0e0;
                border-radius: 8px;
                font-family: 'Courier New', monospace;
                transition: all 0.3s ease;
                background-color: #f9f9f9;
            }

            .otp-input:focus {
                outline: none;
                border-color: var(--secondary-color, #0466C8);
                box-shadow: 0 0 0 3px rgba(4, 102, 200, 0.15);
                background-color: #EFF3FA;
            }

            .otp-input:hover {
                border-color: #bbb;
            }

            .otp-input.filled {
                background-color: #EFF3FA;
                border-color: var(--secondary-color, #0466C8);
            }

            .otp-input.error {
                border-color: #c0392b;
                background-color: #fdecec;
            }

            /* Resend Section */
            .resend-section {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 1rem;
                margin-top: 2rem;
                padding-top: 1.5rem;
                border-top: 1px solid #e0e0e0;
            }

            .resend-timer {
                font-size: 13px;
                color: #999;
                font-weight: 500;
            }

            .resend-button {
                background-color: transparent;
                color: var(--secondary-color, #0466C8);
                border: 1.5px solid #d0d0d0;
                padding: 8px 16px;
                border-radius: 6px;
                font-size: 0.9rem;
                font-weight: 500;
                cursor: pointer;
                font-family: 'Poppins', sans-serif;
                transition: all 0.3s ease;
            }

            .resend-button:hover:not(:disabled) {
                background-color: #f9f9f9;
                border-color: var(--secondary-color, #0466C8);
                color: var(--secondary-color, #0466C8);
            }

            .resend-button:disabled {
                color: #999;
                border-color: #ddd;
                cursor: not-allowed;
                opacity: 0.6;
            }

            /* Info Box */
            .info-box {
                background-color: #E3F2FD;
                border-left: 4px solid var(--secondary-color, #0466C8);
                padding: 12px 15px;
                border-radius: 4px;
                font-size: 13px;
                color: #0c47a1;
                margin-bottom: 1.5rem;
                line-height: 1.5;
            }

            .info-box strong {
                display: block;
                margin-bottom: 4px;
            }

            /* Error Message */
            .error-message {
                color: #c0392b;
                font-size: 0.9rem;
                margin-top: 8px;
                text-align: center;
            }

            /* Loading State */
            .verify-button:disabled {
                opacity: 0.7;
                cursor: not-allowed;
            }
        </style>
    </head>
    <body>
        <div class="main-container">
            <!-- Left Image Section -->
            <div class="image-section">            </div>

            <!-- Right Form Section -->
            <div class="form-section">
                <a href="/" class="back-link">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    <span>Back</span>
                </a>
                <div class="header">
                    <img src="assets/images/logo-no-text.png" alt="ReidHub Logo">
                    <p>Verify Your Email</p>
                </div>

                <form method="POST" enctype="multipart/form-data" class="form" action="/verify-email" id="otpForm">
                    <p>Check your email and enter the 6-digit code below.</p>

                    <div class="info-box">
                        <strong>📧 Verification Code Sent</strong>
                        A 6-digit code has been sent to <strong><?php echo htmlspecialchars($email ?? ''); ?></strong>
                    </div>

                    <!-- Hidden email field -->
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" />

                    <!-- OTP Input Fields -->
                    <div class="otp-container">
                        <input 
                            type="text" 
                            id="otp1" 
                            name="otp_1" 
                            class="otp-input" 
                            maxlength="1" 
                            inputmode="numeric" 
                            autocomplete="off"
                            aria-label="First digit of OTP"
                        />
                        <input 
                            type="text" 
                            id="otp2" 
                            name="otp_2" 
                            class="otp-input" 
                            maxlength="1" 
                            inputmode="numeric" 
                            autocomplete="off"
                            aria-label="Second digit of OTP"
                        />
                        <input 
                            type="text" 
                            id="otp3" 
                            name="otp_3" 
                            class="otp-input" 
                            maxlength="1" 
                            inputmode="numeric" 
                            autocomplete="off"
                            aria-label="Third digit of OTP"
                        />
                        <input 
                            type="text" 
                            id="otp4" 
                            name="otp_4" 
                            class="otp-input" 
                            maxlength="1" 
                            inputmode="numeric" 
                            autocomplete="off"
                            aria-label="Fourth digit of OTP"
                        />
                        <input 
                            type="text" 
                            id="otp5" 
                            name="otp_5" 
                            class="otp-input" 
                            maxlength="1" 
                            inputmode="numeric" 
                            autocomplete="off"
                            aria-label="Fifth digit of OTP"
                        />
                        <input 
                            type="text" 
                            id="otp6" 
                            name="otp_6" 
                            class="otp-input" 
                            maxlength="1" 
                            inputmode="numeric" 
                            autocomplete="off"
                            aria-label="Sixth digit of OTP"
                        />
                    </div>

                    <!-- Hidden field to combine OTP -->
                    <input type="hidden" id="otp_code" name="otp_code" />

                    <!-- Error Message -->
                    <?php if (!empty($errors['otp_code'])): ?>
                        <div class="error-message">
                            <?php echo htmlspecialchars($errors['otp_code']); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Verify Button -->
                    <div class="signup_button">
                        <button type="submit" class="btn-primary verify-button" id="verifyBtn">Verify Email</button>
                    </div>

                    <!-- Resend Section -->
                    <div class="resend-section">
                        <div class="resend-timer">
                            Didn't receive the code? <span id="resendWait"></span>
                        </div>
                        <button 
                            type="button" 
                            id="resendBtn" 
                            class="resend-button" 
                            disabled
                            onclick="handleResendOTP(event)"
                        >
                            Resend Code
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div id="toast" class="toast" role="alert" aria-live="polite"></div>

        <script>
            // OTP Input Handler - Auto-advance to next field
            const otpInputs = document.querySelectorAll('.otp-input');
            const otpForm = document.getElementById('otpForm');
            const otpCodeField = document.getElementById('otp_code');
            const resendBtn = document.getElementById('resendBtn');
            const resendWait = document.getElementById('resendWait');

            // Auto-advance on input
            otpInputs.forEach((input, index) => {
                input.addEventListener('input', (e) => {
                    const value = e.target.value;
                    
                    // Only allow digits
                    if (!/^\d*$/.test(value)) {
                        e.target.value = '';
                        return;
                    }

                    // Mark as filled
                    if (value) {
                        e.target.classList.add('filled');
                    } else {
                        e.target.classList.remove('filled');
                    }

                    // Auto-advance to next input
                    if (value && index < otpInputs.length - 1) {
                        otpInputs[index + 1].focus();
                    }

                    // Update combined OTP code
                    updateOTPCode();
                });

                // Handle backspace
                input.addEventListener('keydown', (e) => {
                    if (e.key === 'Backspace' && !e.target.value && index > 0) {
                        otpInputs[index - 1].focus();
                    }
                });

                // Handle paste
                input.addEventListener('paste', (e) => {
                    e.preventDefault();
                    const pastedText = (e.clipboardData || window.clipboardData).getData('text');
                    const digits = pastedText.replace(/\D/g, '').split('').slice(0, 6);
                    
                    digits.forEach((digit, i) => {
                        if (otpInputs[i]) {
                            otpInputs[i].value = digit;
                            otpInputs[i].classList.add('filled');
                        }
                    });
                    updateOTPCode();
                });
            });

            // Combine OTP inputs
            function updateOTPCode() {
                const code = Array.from(otpInputs).map(input => input.value).join('');
                otpCodeField.value = code;
            }

            // Form submission
            otpForm.addEventListener('submit', async (e) => {
                e.preventDefault();

                const code = Array.from(otpInputs).map(input => input.value).join('');
                
                if (code.length !== 6) {
                    showError('Please enter all 6 digits');
                    return;
                }

                const formData = new FormData(otpForm);
                const verifyBtn = document.querySelector('.verify-button');
                verifyBtn.disabled = true;
                verifyBtn.textContent = 'Verifying...';

                try {
                    const response = await fetch('/verify-email', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const data = await response.json();

                    if (data.ok) {
                        showSuccess('Email verified! Redirecting...');
                        setTimeout(() => {
                            window.location.href = data.redirect || '/dashboard/user';
                        }, 1500);
                    } else {
                        const errorMsg = data.errors?.otp_code || 'Verification failed';
                        showError(errorMsg);
                        otpInputs.forEach(input => input.classList.add('error'));
                        verifyBtn.disabled = false;
                        verifyBtn.textContent = 'Verify Email';
                    }
                } catch (error) {
                    showError('An error occurred. Please try again.');
                    verifyBtn.disabled = false;
                    verifyBtn.textContent = 'Verify Email';
                }
            });

            // Resend OTP Handler
            async function handleResendOTP(e) {
                e.preventDefault();
                const email = document.querySelector('input[name="email"]').value;

                if (!email) {
                    showError('Email is missing');
                    return;
                }

                resendBtn.disabled = true;
                resendBtn.textContent = 'Sending...';

                try {
                    const formData = new FormData();
                    formData.append('email', email);

                    const response = await fetch('/resend-otp', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const data = await response.json();

                    if (data.ok) {
                        showSuccess('OTP resent successfully!');
                        // Clear previous inputs
                        otpInputs.forEach(input => {
                            input.value = '';
                            input.classList.remove('filled', 'error');
                        });
                        // Start 30-second countdown
                        startResendCountdown();
                    } else {
                        showError(data.error || 'Failed to resend OTP');
                        resendBtn.disabled = false;
                        resendBtn.textContent = 'Resend Code';
                    }
                } catch (error) {
                    showError('An error occurred. Please try again.');
                    resendBtn.disabled = false;
                    resendBtn.textContent = 'Resend Code';
                }
            }

            // Resend Countdown Timer
            function startResendCountdown() {
                let seconds = 30;
                resendBtn.disabled = true;

                const interval = setInterval(() => {
                    if (seconds > 0) {
                        resendWait.textContent = `Retry in ${seconds}s`;
                        seconds--;
                    } else {
                        clearInterval(interval);
                        resendBtn.disabled = false;
                        resendWait.textContent = '';
                        resendBtn.textContent = 'Resend Code';
                    }
                }, 1000);
            }

            // Toast notifications
            function showError(message) {
                const toast = document.getElementById('toast');
                toast.textContent = message;
                toast.style.background = '#c0392b';
                toast.style.color = '#fff';
                toast.style.display = 'block';
                setTimeout(() => { toast.style.display = 'none'; }, 3000);
            }

            function showSuccess(message) {
                const toast = document.getElementById('toast');
                toast.textContent = message;
                toast.style.background = '#27ae60';
                toast.style.color = '#fff';
                toast.style.display = 'block';
                setTimeout(() => { toast.style.display = 'none'; }, 3000);
            }

            // Initial countdown on page load
            window.addEventListener('load', () => {
                startResendCountdown();
            });
        </script>
        <script src="/js/home/auth/response-handle.js"></script>
    </body>
</html>
