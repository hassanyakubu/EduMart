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
    <?php 
    // Only apply enhanced styling on non-home pages
    $current_page = basename($_SERVER['PHP_SELF']);
    if ($current_page !== 'index.php'): 
    ?>
    <style>
        /* Enhanced navbar styling for internal pages */
        .navbar {
            background: linear-gradient(135deg, #FFD947 0%, #FFA500 100%);
            box-shadow: 0 4px 20px rgba(255, 217, 71, 0.3);
            border-bottom: 3px solid #FFB700;
        }
        
        .navbar .logo {
            color: #000 !important;
            font-weight: 700;
            font-size: 1.5rem;
            background: none !important;
            -webkit-background-clip: unset !important;
            -webkit-text-fill-color: #000 !important;
            background-clip: unset !important;
        }
        
        .nav-menu {
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
        
        .nav-menu::-webkit-scrollbar {
            display: none;
        }
        
        .nav-menu li {
            margin: 0;
            flex-shrink: 0;
        }
        
        .nav-menu a {
            text-decoration: none !important;
            color: #333;
            font-weight: 600;
            transition: all 0.3s ease;
            padding: 0.7rem 1.2rem;
            border-radius: 8px;
            font-size: 0.95rem;
            display: inline-block;
            white-space: nowrap;
            background: rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.4);
        }
        
        .nav-menu a:hover {
            background: rgba(255, 255, 255, 0.5);
            color: #333;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .nav-menu a.active {
            background: rgba(255, 255, 255, 0.6);
            color: #333;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #333 0%, #555 100%) !important;
            color: white !important;
            text-decoration: none !important;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #555 0%, #777 100%) !important;
            color: white !important;
        }
    </style>
    <?php endif; ?>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="<?php echo url('app/views/home/index.php'); ?>" class="logo">EduMart</a>
            <ul class="nav-menu">
                <?php 
                // Check if we're on the index page
                $is_index_page = (basename($_SERVER['PHP_SELF']) === 'index.php');
                
                if ($is_index_page): 
                    // Index page - show minimal menu for everyone
                ?>
                    <li><a href="<?php echo url('app/views/resources/list.php'); ?>">Browse Resources</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="<?php echo url('app/views/profile/dashboard.php'); ?>">Dashboard</a></li>
                        <li><a href="<?php echo url('app/views/auth/logout.php'); ?>">Logout</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo url('app/views/auth/login.php'); ?>">Login</a></li>
                        <li><a href="<?php echo url('app/views/auth/register.php'); ?>" class="btn-primary">Sign Up</a></li>
                    <?php endif; ?>
                <?php else: 
                    // Full menu for internal pages
                ?>
                    <li><a href="<?php echo url('app/views/home/index.php'); ?>">Home</a></li>
                    <li><a href="<?php echo url('app/views/resources/list.php'); ?>">Browse Resources</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if ($_SESSION['user_role'] == 1): ?>
                            <li><a href="<?php echo url('app/views/admin/dashboard.php'); ?>">Admin</a></li>
                        <?php endif; ?>
                        <?php if ($_SESSION['user_role'] == 1 || (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'creator')): ?>
                            <li><a href="<?php echo url('app/views/resources/upload.php'); ?>">Upload</a></li>
                        <?php endif; ?>
                        
                        <?php 
                        // Define user type variables
                        $isCreator = isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'creator';
                        $isAdmin = $_SESSION['user_role'] == 1;
                        
                        // Show different menu items based on user type
                        if ($isAdmin) {
                            // Admin sees My Resources (purchased items)
                            echo '<li><a href="' . url('app/views/profile/my_resources.php') . '">My Resources</a></li>';
                        } elseif ($isCreator) {
                            // Creator sees My Uploads, My Quizzes, and My Earnings
                            echo '<li><a href="' . url('app/views/profile/my_uploads.php') . '">My Uploads</a></li>';
                            echo '<li><a href="' . url('app/views/quiz/my_quizzes.php') . '">My Quizzes</a></li>';
                            echo '<li><a href="' . url('app/views/profile/earnings.php') . '">My Earnings</a></li>';
                        } else {
                            // Student sees My Resources (purchased items)
                            echo '<li><a href="' . url('app/views/profile/my_resources.php') . '">My Resources</a></li>';
                        }
                        
                        // Show Quizzes for students and admins (not creators unless they're admin)
                        if (!$isCreator || $isAdmin): 
                        ?>
                            <li><a href="<?php echo url('app/views/quiz/list.php'); ?>">Quizzes</a></li>
                        <?php endif; ?>
                        
                        <?php 
                        // Only show Cart for students (not admins or creators)
                        if (!$isAdmin && !$isCreator): 
                        ?>
                        <li style="position: relative;">
                            <a href="<?php echo url('app/views/cart/view.php'); ?>">Cart</a>
                            <?php
                            // Get cart count
                            if (isset($_SESSION['user_id'])) {
                                try {
                                    if (!class_exists('cart_model')) {
                                        require_once __DIR__ . '/../../models/cart_model.php';
                                    }
                                    $cartModel = new cart_model();
                                    $cart_items = $cartModel->getUserCart($_SESSION['user_id']);
                                    $cart_count = count($cart_items);
                                    if ($cart_count > 0):
                            ?>
                                <span class="cart-badge <?php echo isset($_SESSION['cart_updated']) ? 'cart-badge-pulse' : ''; ?>">
                                    <?php echo $cart_count; ?>
                                </span>
                            <?php 
                                    endif;
                                } catch (Exception $e) {
                                    // Silently fail if cart model can't be loaded
                                    error_log("Cart badge error: " . $e->getMessage());
                                }
                                unset($_SESSION['cart_updated']); // Clear the flag
                            }
                            ?>
                        </li>
                        <?php endif; ?>
                        
                        <li><a href="<?php echo url('app/views/profile/dashboard.php'); ?>">Profile</a></li>
                        <li><a href="<?php echo url('app/views/auth/logout.php'); ?>">Logout</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo url('app/views/auth/login.php'); ?>">Login</a></li>
                        <li><a href="<?php echo url('app/views/auth/register.php'); ?>" class="btn-primary">Sign Up</a></li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success toast-notification">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error toast-notification">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    
    <script>
    // Auto-hide toast notifications after 3 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const toasts = document.querySelectorAll('.toast-notification');
        toasts.forEach(toast => {
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(-20px)';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        });
    });
    </script>
    
    <main class="main-content">
