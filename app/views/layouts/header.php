<?php require_once __DIR__ . '/../../config/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'EduMart'; ?> - Digital Learning Resources</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo asset('assets/css/styles.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset('assets/css/animations.css'); ?>">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="<?php echo url('app/views/home/index.php'); ?>" class="logo">EduMart</a>
            <ul class="nav-menu">
                <li><a href="<?php echo url('app/views/home/index.php'); ?>">Home</a></li>
                <li><a href="<?php echo url('app/views/resources/list.php'); ?>">Browse Resources</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['user_role'] == 1): ?>
                        <li><a href="<?php echo url('app/views/admin/dashboard.php'); ?>">Admin</a></li>
                    <?php endif; ?>
                    <?php if ($_SESSION['user_role'] == 1 || (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'creator')): ?>
                        <li><a href="<?php echo url('app/views/resources/upload.php'); ?>">Upload</a></li>
                    <?php endif; ?>
                    <li><a href="<?php echo url('app/views/cart/view.php'); ?>">Cart</a></li>
                    <li><a href="<?php echo url('app/views/profile/dashboard.php'); ?>">Profile</a></li>
                    <li><a href="<?php echo url('app/views/auth/logout.php'); ?>">Logout</a></li>
                <?php else: ?>
                    <li><a href="<?php echo url('app/views/auth/login.php'); ?>">Login</a></li>
                    <li><a href="<?php echo url('app/views/auth/register.php'); ?>" class="btn-primary">Sign Up</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    
    <main class="main-content">
