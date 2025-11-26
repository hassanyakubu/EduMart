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
    <style>
        /* Admin-specific styling */
        .admin-navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
            border-bottom: 3px solid #5a67d8;
        }
        
        .admin-navbar .logo {
            color: white;
            font-weight: 700;
            font-size: 1.5rem;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .admin-nav-menu {
            display: flex;
            list-style: none;
            gap: 0.3rem;
            align-items: center;
            overflow-x: auto;
            overflow-y: hidden;
            white-space: nowrap;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            -ms-overflow-style: none;
            padding: 0.5rem 0;
        }
        
        .admin-nav-menu::-webkit-scrollbar {
            display: none;
        }
        
        .admin-nav-menu li {
            margin: 0;
            flex-shrink: 0;
        }
        
        .admin-nav-menu a {
            text-decoration: none;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 600;
            transition: all 0.3s ease;
            padding: 0.7rem 1.2rem;
            border-radius: 8px;
            position: relative;
            font-size: 0.95rem;
            display: inline-block;
            white-space: nowrap;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .admin-nav-menu a:hover {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
        
        .admin-nav-menu a.active {
            background: rgba(255, 255, 255, 0.25);
            color: white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }
        
        /* Admin body styling */
        body.admin-body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            background-attachment: fixed;
        }
        
        /* Admin cards */
        .admin-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15);
            border: 1px solid rgba(102, 126, 234, 0.1);
            transition: all 0.3s ease;
        }
        
        .admin-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 35px rgba(102, 126, 234, 0.2);
        }
        
        /* Admin buttons */
        .admin-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .admin-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }
    </style>
</head>
<body class="admin-body">
    <nav class="navbar admin-navbar">
        <div class="container">
            <a href="<?php echo url('app/views/admin/dashboard.php'); ?>" class="logo">EduMart Admin</a>
            <ul class="nav-menu admin-nav-menu">
                <li><a href="<?php echo url('app/views/admin/dashboard.php'); ?>" <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'class="active"' : ''; ?>>Dashboard</a></li>
                <li><a href="<?php echo url('app/views/admin/analytics.php'); ?>" <?php echo (basename($_SERVER['PHP_SELF']) == 'analytics.php') ? 'class="active"' : ''; ?>>Analytics</a></li>
                <li><a href="<?php echo url('app/views/admin/quiz_analytics.php'); ?>" <?php echo (basename($_SERVER['PHP_SELF']) == 'quiz_analytics.php') ? 'class="active"' : ''; ?>>Quiz Analytics</a></li>
                <li><a href="<?php echo url('app/views/admin/users.php'); ?>" <?php echo (basename($_SERVER['PHP_SELF']) == 'users.php') ? 'class="active"' : ''; ?>>Users</a></li>
                <li><a href="<?php echo url('app/views/admin/resources.php'); ?>" <?php echo (basename($_SERVER['PHP_SELF']) == 'resources.php') ? 'class="active"' : ''; ?>>Resources</a></li>
                <li><a href="<?php echo url('app/views/admin/orders.php'); ?>" <?php echo (basename($_SERVER['PHP_SELF']) == 'orders.php') ? 'class="active"' : ''; ?>>Orders</a></li>
                <li><a href="<?php echo url('app/views/admin/categories.php'); ?>" <?php echo (basename($_SERVER['PHP_SELF']) == 'categories.php') ? 'class="active"' : ''; ?>>Categories</a></li>
                <li><a href="<?php echo url('app/views/admin/settings.php'); ?>" <?php echo (basename($_SERVER['PHP_SELF']) == 'settings.php') ? 'class="active"' : ''; ?>>Settings</a></li>
                <li><a href="<?php echo url('app/views/home/index.php'); ?>">Main Site</a></li>
                <li><a href="<?php echo url('app/views/auth/logout.php'); ?>">Logout</a></li>
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
