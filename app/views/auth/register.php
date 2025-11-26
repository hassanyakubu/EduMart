<?php
session_start();
$page_title = 'Register';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <div class="form-container">
        <h2 style="text-align: center; margin-bottom: 2rem;">Create Account</h2>
        <form action="register_process.php" method="POST">
            <div class="form-group">
                <label for="user_type">I am a:</label>
                <select id="user_type" name="user_type" required style="padding: 0.8rem; border: 1px solid #ddd; border-radius: 8px; width: 100%; font-size: 1rem;">
                    <option value="">Select your role</option>
                    <option value="student">Student - Browse and purchase resources</option>
                    <option value="creator">Creator - Upload and sell resources</option>
                </select>
                <small style="color: #666; display: block; margin-top: 0.5rem;">
                    Students can browse and purchase. Creators can upload and sell their content.
                </small>
            </div>
            
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required minlength="6">
                <small style="color: #666; display: block; margin-top: 0.5rem;">
                    Minimum 6 characters
                </small>
            </div>
            <div class="form-group">
                <label for="country">Country</label>
                <input type="text" id="country" name="country" value="Ghana" required>
            </div>
            <div class="form-group">
                <label for="city">City</label>
                <input type="text" id="city" name="city" required>
            </div>
            <div class="form-group">
                <label for="contact">Contact Number</label>
                <input type="text" id="contact" name="contact" placeholder="e.g., 0244123456" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Create Account</button>
        </form>
        <p style="text-align: center; margin-top: 1rem;">
            Already have an account? <a href="login.php" style="color: #FFD947; font-weight: 600;">Login</a>
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
