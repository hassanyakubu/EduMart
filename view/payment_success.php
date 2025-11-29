<?php
require_once '../settings/core.php';
require_once '../controllers/order_controller.php';

require_login('../login/login.php');

$customer_id = get_user_id();
$invoice_no = isset($_GET['invoice']) ? htmlspecialchars($_GET['invoice']) : '';
$reference = isset($_GET['reference']) ? htmlspecialchars($_GET['reference']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - EduMart</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Inter', sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .navbar { 
            background: rgba(255, 255, 255, 0.95); 
            padding: 20px 0; 
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1); 
        }
        .nav-container { 
            max-width: 1400px; 
            margin: 0 auto; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            padding: 0 40px; 
        }
        .logo { 
            font-size: 28px; 
            font-weight: 800;
            color: #667eea;
            text-decoration: none; 
        }
        
        .container { max-width: 800px; margin: 40px auto; padding: 0 20px; }
        
        .success-card { 
            background: white;
            border-radius: 24px; 
            padding: 60px 40px; 
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            position: relative;
            overflow: hidden;
        }
        
        .success-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #10b981, #3b82f6, #8b5cf6);
        }
        
        .check-circle { 
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 50%;
            margin: 0 auto 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: scaleUp 0.5s ease-out;
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);
        }
        
        @keyframes scaleUp {
            0% { transform: scale(0); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        .checkmark {
            width: 50px;
            height: 50px;
            border: 4px solid white;
            border-top: none;
            border-left: none;
            transform: rotate(45deg);
            animation: drawCheck 0.4s ease-out 0.3s both;
        }
        
        @keyframes drawCheck {
            0% { height: 0; width: 0; }
            100% { height: 50px; width: 25px; }
        }
        
        h1 { 
            font-size: 2.5rem; 
            color: #1f2937; 
            margin-bottom: 10px;
            font-weight: 800;
        }
        
        .subtitle { 
            font-size: 18px; 
            color: #6b7280; 
            margin-bottom: 40px; 
        }
        
        .order-info { 
            background: #f9fafb;
            padding: 30px; 
            border-radius: 16px; 
            margin: 30px 0; 
            text-align: left;
        }
        
        .info-row { 
            display: flex; 
            justify-content: space-between; 
            padding: 15px 0; 
            border-bottom: 1px solid #e5e7eb;
        }
        
        .info-row:last-child { border-bottom: none; }
        .info-label { font-weight: 600; color: #374151; }
        .info-value { color: #6b7280; }
        
        .btn { 
            padding: 14px 32px; 
            border: none; 
            border-radius: 12px; 
            font-size: 16px; 
            font-weight: 600; 
            cursor: pointer; 
            transition: all 0.3s ease; 
            text-decoration: none; 
            display: inline-block;
            margin: 0 8px;
        }
        
        .btn-primary { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white; 
        }
        
        .btn-primary:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary { 
            background: white; 
            color: #667eea; 
            border: 2px solid #667eea; 
        }
        
        .btn-secondary:hover { 
            background: #f3f4f6; 
        }
        
        .action-buttons { 
            display: flex; 
            justify-content: center; 
            margin-top: 40px; 
            flex-wrap: wrap;
            gap: 12px;
        }
        
        .success-badge { 
            background: #d1fae5;
            color: #065f46; 
            padding: 12px 24px; 
            border-radius: 50px; 
            font-weight: 600;
            display: inline-block;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="../index.php" class="logo">EduMart</a>
            <div style="display: flex; gap: 20px;">
                <a href="all_product.php" style="color: #374151; text-decoration: none;">← Continue Shopping</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="success-card">
            <div class="check-circle">
                <div class="checkmark"></div>
            </div>
            
            <div class="success-badge">✓ Payment Confirmed</div>
            
            <h1>Payment Successful!</h1>
            <p class="subtitle">Thank you for your purchase. Your order is being processed.</p>
            
            <div class="order-info">
                <div class="info-row">
                    <span class="info-label">Invoice Number</span>
                    <span class="info-value"><?php echo $invoice_no; ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Payment Reference</span>
                    <span class="info-value"><?php echo $reference; ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Order Date</span>
                    <span class="info-value"><?php echo date('F j, Y'); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status</span>
                    <span class="info-value" style="color: #10b981; font-weight: 600;">Completed</span>
                </div>
            </div>
            
            <div class="action-buttons">
                <a href="orders.php" class="btn btn-primary">View My Orders</a>
                <a href="all_product.php" class="btn btn-secondary">Browse Resources</a>
            </div>
        </div>
    </div>
</body>
</html>
