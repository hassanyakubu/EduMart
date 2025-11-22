<?php
session_start();
require_once __DIR__ . '/../../controllers/home_controller.php';

$controller = new home_controller();
$controller->index();

$page_title = 'Home';
require_once __DIR__ . '/../layouts/header.php';
?>

<style>
.hero-landing {
    position: relative;
    min-height: 600px;
    background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), 
                url('<?php echo asset('assets/images/WASSCE.jpg'); ?>') center/cover no-repeat;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    color: white;
    margin: -2rem -2rem 3rem -2rem;
    padding: 4rem 2rem;
}

.hero-content {
    max-width: 800px;
    animation: fadeInUp 1s ease-out;
}

.hero-landing h1 {
    font-size: 3.5rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.hero-landing p {
    font-size: 1.5rem;
    margin-bottom: 2.5rem;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
}

.hero-buttons {
    display: flex;
    gap: 1.5rem;
    justify-content: center;
    flex-wrap: wrap;
}

.hero-buttons .btn {
    padding: 1rem 2.5rem;
    font-size: 1.1rem;
    font-weight: 600;
    text-decoration: none;
    border-radius: 50px;
    transition: all 0.3s ease;
}

.btn-hero-primary {
    background: var(--primary-yellow);
    color: #333;
}

.btn-hero-primary:hover {
    background: #ffd000;
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(255, 217, 71, 0.4);
}

.btn-hero-secondary {
    background: transparent;
    color: white;
    border: 2px solid white;
}

.btn-hero-secondary:hover {
    background: white;
    color: #333;
    transform: translateY(-3px);
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 768px) {
    .hero-landing h1 {
        font-size: 2.5rem;
    }
    
    .hero-landing p {
        font-size: 1.2rem;
    }
    
    .hero-buttons {
        flex-direction: column;
        align-items: center;
    }
    
    .hero-buttons .btn {
        width: 100%;
        max-width: 300px;
    }
}
</style>

<div class="hero-landing">
    <div class="hero-content">
        <h1>Welcome to EduMart</h1>
        <p>Your trusted marketplace for digital learning resources</p>
        
        <div class="hero-buttons">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?php echo url('app/views/resources/list.php'); ?>" class="btn btn-hero-primary">
                    Browse Resources
                </a>
                <a href="<?php echo url('app/views/profile/dashboard.php'); ?>" class="btn btn-hero-secondary">
                    My Dashboard
                </a>
            <?php else: ?>
                <a href="<?php echo url('app/views/auth/register.php'); ?>" class="btn btn-hero-primary">
                    Sign Up
                </a>
                <a href="<?php echo url('app/views/auth/login.php'); ?>" class="btn btn-hero-secondary">
                    Log In
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
