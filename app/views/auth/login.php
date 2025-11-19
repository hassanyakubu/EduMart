<?php
session_start();
$page_title = 'Login';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <div class="form-container">
        <h2 style="text-align: center; margin-bottom: 2rem;">Login to EduMart</h2>
        <form action="/app/views/auth/login_process.php" method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>
        <p style="text-align: center; margin-top: 1rem;">
            Don't have an account? <a href="/app/views/auth/register.php" style="color: #FFD947; font-weight: 600;">Sign Up</a>
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
