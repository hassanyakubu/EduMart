<?php require_once __DIR__ . '/../../config/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Admin'; ?> - EduMart</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo asset('assets/css/styles.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset('assets/css/animations.css'); ?>">
</head>
<body>
    <nav class="navbar" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container">
            <a href="<?php echo url('app/views/admin/dashboard.php'); ?>" class="logo">
                ⚙️ EduMart Admin
            </a>
            <ul class="nav-menu">
                <li><a href="<?php echo url('app/views/home/index.php'); ?>" style="color: white;">🏠 Exit Admin</a></li>
                <li><a href="<?php echo url('app/views/auth/logout.php'); ?>" style="color: white;">🚪 Logout</a></li>
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
