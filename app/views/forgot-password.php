<?php
$pageTitle = 'Forgot Password - StoryHub';
$pageDescription = 'Reset your password';
$pageCSS = 'login.css';
$pageJS = 'forgot-password.js';

include '../partials/header.php';
?>

<div class="auth-container">
    <div class="auth-wrapper auth-simple">
        <div class="auth-form-container">
            <div class="auth-form-wrapper">
                <div class="form-icon">
                    <i class="fas fa-key"></i>
                </div>

                <div class="form-header">
                    <h2>Forgot Password?</h2>
                    <p>No worries! Enter your email and we'll send you a reset code.</p>
                </div>

                <div id="alertMessage" class="alert" style="display: none;"></div>

                <!-- Step 1: Email Input -->
                <form id="forgotPasswordForm" class="auth-form" style="display: block;">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-wrapper">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="email" name="email" placeholder="Enter your registered email" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        <span class="btn-text">Send Reset Code</span>
                        <span class="btn-loader" style="display: none;">
                            <i class="fas fa-spinner fa-spin"></i> Sending...
                        </span>
                    </button>

                    <div class="form-footer">
                        <a href="index.php?url=login"><i class="fas fa-arrow-left"></i> Back to Sign In</a>
                    </div>
                </form>

                <!-- Step 2: Verification Code -->
                <form id="verifyCodeForm" class="auth-form" style="display: none;">
                    <div class="form-group">
                        <label for="resetCode">Verification Code</label>
                        <div class="verification-inputs">
                            <input type="text" class="verification-input" maxlength="1" id="code1">
                            <input type="text" class="verification-input" maxlength="1" id="code2">
                            <input type="text" class="verification-input" maxlength="1" id="code3">
                            <input type="text" class="verification-input" maxlength="1" id="code4">
                            <input type="text" class="verification-input" maxlength="1" id="code5">
                            <input type="text" class="verification-input" maxlength="1" id="code6">
                        </div>
                        <p class="help-text">Enter the 6-digit code sent to your email</p>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        <span class="btn-text">Verify Code</span>
                        <span class="btn-loader" style="display: none;">
                            <i class="fas fa-spinner fa-spin"></i> Verifying...
                        </span>
                    </button>

                    <div class="form-footer">
                        <p>Didn't receive code? <a href="#" id="resendCode">Resend</a></p>
                    </div>
                </form>

                <!-- Step 3: New Password -->
                <form id="resetPasswordForm" class="auth-form" style="display: none;">
                    <div class="form-group">
                        <label for="newPassword">New Password</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="newPassword" name="newPassword" placeholder="Enter new password" required>
                            <button type="button" class="toggle-password" onclick="togglePassword('newPassword')">
                                <i class="far fa-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="confirmNewPassword">Confirm New Password</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="confirmNewPassword" name="confirmNewPassword" placeholder="Confirm new password" required>
                            <button type="button" class="toggle-password" onclick="togglePassword('confirmNewPassword')">
                                <i class="far fa-eye" id="toggleIconConfirm"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        <span class="btn-text">Reset Password</span>
                        <span class="btn-loader" style="display: none;">
                            <i class="fas fa-spinner fa-spin"></i> Resetting...
                        </span>
                    </button>
                </form>

                <!-- Success Message -->
                <div id="successMessage" class="success-message" style="display: none;">
                    <div class="success-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h3>Password Reset Successful!</h3>
                    <p>Your password has been changed successfully.</p>
                    <a href="index.php?url=login" class="btn btn-primary">Go to Sign In</a>
                </div>
            </div>

            <div class="auth-links">
                <a href="index.php?url=landing"><i class="fas fa-arrow-left"></i> Back to Home</a>
            </div>
        </div>
    </div>
</div>
