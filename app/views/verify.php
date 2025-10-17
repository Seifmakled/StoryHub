<?php
$pageTitle = 'Verify Email - StoryHub';
$pageDescription = 'Verify your email address';
$pageCSS = 'login.css';
$pageJS = 'verify.js';

include '../partials/header.php';
?>

<div class="auth-container">
    <div class="auth-wrapper auth-simple">
        <div class="auth-form-container">
            <div class="auth-form-wrapper">
                <div class="form-icon">
                    <i class="fas fa-envelope-open"></i>
                </div>

                <div class="form-header">
                    <h2>Verify Your Email</h2>
                    <p>We've sent a 6-digit verification code to your email address.</p>
                </div>

                <div id="alertMessage" class="alert" style="display: none;"></div>

                <form id="verifyForm" class="auth-form">
                    <div class="form-group">
                        <label>Verification Code</label>
                        <div class="verification-inputs">
                            <input type="text" class="verification-input" maxlength="1" id="code1" autofocus>
                            <input type="text" class="verification-input" maxlength="1" id="code2">
                            <input type="text" class="verification-input" maxlength="1" id="code3">
                            <input type="text" class="verification-input" maxlength="1" id="code4">
                            <input type="text" class="verification-input" maxlength="1" id="code5">
                            <input type="text" class="verification-input" maxlength="1" id="code6">
                        </div>
                        <p class="help-text">Enter the 6-digit code from your email</p>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        <span class="btn-text">Verify Email</span>
                        <span class="btn-loader" style="display: none;">
                            <i class="fas fa-spinner fa-spin"></i> Verifying...
                        </span>
                    </button>

                    <div class="form-footer">
                        <p>Didn't receive the code?</p>
                        <button type="button" id="resendCode" class="btn-link">Resend Verification Code</button>
                        <div id="resendTimer" style="display: none; margin-top: 10px; color: #666;">
                            Resend available in <span id="countdown">60</span>s
                        </div>
                    </div>
                </form>

                <!-- Success Message -->
                <div id="successMessage" class="success-message" style="display: none;">
                    <div class="success-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h3>Email Verified!</h3>
                    <p>Your email has been verified successfully. You can now access your account.</p>
                    <a href="index.php?page=login" class="btn btn-primary">Continue to Sign In</a>
                </div>
            </div>

            <div class="auth-links">
                <a href="index.php?page=home"><i class="fas fa-arrow-left"></i> Back to Home</a>
            </div>
        </div>
    </div>
</div>
