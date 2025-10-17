<?php
$pageTitle = 'Sign Up - StoryHub';
$pageDescription = 'Create your StoryHub account';
$pageCSS = 'login.css';
$pageJS = 'register.js';

include __DIR__ . '/../partials/header.php';
?>

<div class="auth-container">
    <div class="auth-wrapper">
        <!-- Left Side - Branding -->
        <div class="auth-branding">
            <div class="branding-content">
                <div class="brand-logo">
                    <i class="fas fa-feather-alt"></i>
                    <h1>StoryHub</h1>
                </div>
                <h2>Start Your Journey</h2>
                <p>Join thousands of writers sharing their stories with the world.</p>
                <div class="branding-stats">
                    <div class="stat-item">
                        <h3>10K+</h3>
                        <span>Active Writers</span>
                    </div>
                    <div class="stat-item">
                        <h3>50K+</h3>
                        <span>Published Stories</span>
                    </div>
                    <div class="stat-item">
                        <h3>1M+</h3>
                        <span>Monthly Readers</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Register Form -->
        <div class="auth-form-container">
            <div class="auth-form-wrapper">
                <div class="form-header">
                    <h2>Create Account</h2>
                    <p>Fill in your details to get started</p>
                </div>

                <div id="alertMessage" class="alert" style="display: none;"></div>

                <form id="registerForm" class="auth-form" action="/StoryHub/app/controllers/registrationController.php" method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="fullName">Full Name</label>
                            <div class="input-wrapper">
                                <i class="fas fa-user"></i>
                                <input type="text" id="fullName" name="full_name" placeholder="John Doe" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="username">Username</label>
                            <div class="input-wrapper">
                                <i class="fas fa-at"></i>
                                <input type="text" id="username" name="username" placeholder="johndoe" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-wrapper">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="email" name="email" placeholder="john@example.com" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password" name="password" placeholder="Create a strong password" required>
                            <button type="button" class="toggle-password" onclick="togglePassword('password')">
                                <i class="far fa-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                        <div class="password-strength" id="passwordStrength"></div>
                    </div>

                    <div class="form-group">
                        <label for="confirmPassword">Confirm Password</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="confirmPassword" name="confirm_password" placeholder="Repeat your password" required>
                            <button type="button" class="toggle-password" onclick="togglePassword('confirmPassword')">
                                <i class="far fa-eye" id="toggleIconConfirm"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="checkbox-wrapper">
                            <input type="checkbox" id="terms" name="terms" required>
                            <span>I agree to the <a href="#terms">Terms of Service</a> and <a href="#privacy">Privacy Policy</a></span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        <span class="btn-text">Create Account</span>
                        <span class="btn-loader" style="display: none;">
                            <i class="fas fa-spinner fa-spin"></i> Creating account...
                        </span>
                    </button>

                    <div class="divider">
                        <span>OR</span>
                    </div>

                    <div class="social-login">
                        <button type="button" class="btn btn-social btn-google">
                            <i class="fab fa-google"></i>
                            Sign up with Google
                        </button>
                        <button type="button" class="btn btn-social btn-facebook">
                            <i class="fab fa-facebook-f"></i>
                            Sign up with Facebook
                        </button>
                    </div>

                    <div class="form-footer">
                        <p>Already have an account? <a href="index.php?url=login">Sign In</a></p>
                    </div>
                </form>
            </div>

            <div class="auth-links">
                <a href="index.php?url=landing"><i class="fas fa-arrow-left"></i> Back to Home</a>
            </div>
        </div>
    </div>
</div>
