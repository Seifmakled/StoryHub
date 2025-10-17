<?php
$pageTitle = 'Sign In - StoryHub';
$pageDescription = 'Sign in to your StoryHub account';
$pageCSS = 'login.css';
$pageJS = 'login.js';

include '../partials/header.php';
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
                <h2>Welcome Back!</h2>
                <p>Continue your journey of sharing and discovering amazing stories.</p>
                <div class="branding-features">
                    <div class="feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Write and publish articles</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Connect with readers</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Build your audience</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="auth-form-container">
            <div class="auth-form-wrapper">
                <div class="form-header">
                    <h2>Sign In</h2>
                    <p>Enter your credentials to access your account</p>
                </div>

                <div id="alertMessage" class="alert" style="display: none;"></div>

                <form id="loginForm" class="auth-form" action="/StoryHub/app/controllers/loginController.php" method="POST">
                    <div class="form-group">
                        <label for="email_or_username">Email or Username</label>
                        <div class="input-wrapper">
                            <i class="fas fa-envelope"></i>
                            <input type="text" id="email_or_username" name="email_or_username" placeholder="Enter your email or username" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password" name="password" placeholder="Enter your password" required>
                            <button type="button" class="toggle-password" onclick="togglePassword()">
                                <i class="far fa-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-options">
                        <label class="checkbox-wrapper">
                            <input type="checkbox" id="remember" name="remember">
                            <span>Remember me</span>
                        </label>
                        <a href="index.php?url=forgot-password" class="forgot-link">Forgot Password?</a>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        <span class="btn-text">Sign In</span>
                        <span class="btn-loader" style="display: none;">
                            <i class="fas fa-spinner fa-spin"></i> Signing in...
                        </span>
                    </button>

                    <div class="divider">
                        <span>OR</span>
                    </div>

                    <div class="social-login">
                        <button type="button" class="btn btn-social btn-google">
                            <i class="fab fa-google"></i>
                            Continue with Google
                        </button>
                        <button type="button" class="btn btn-social btn-facebook">
                            <i class="fab fa-facebook-f"></i>
                            Continue with Facebook
                        </button>
                    </div>

                    <div class="form-footer">
                        <p>Don't have an account? <a href="index.php?url=register">Sign Up</a></p>
                    </div>
                </form>
            </div>

            <div class="auth-links">
                <a href="index.php?url=landing"><i class="fas fa-arrow-left"></i> Back to Home</a>
            </div>
        </div>
    </div>
</div>
