<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/user_model.php';
require_once __DIR__ . '/../../models/download_model.php';
require_once __DIR__ . '/../../models/order_model.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . url('app/views/auth/login.php'));
    exit;
}

$userModel = new user_model();
$downloadModel = new download_model();
$orderModel = new order_model();

$user = $userModel->getById($_SESSION['user_id']);
$downloads = $downloadModel->getUserDownloads($_SESSION['user_id']);
$orders = $orderModel->getOrdersByUser($_SESSION['user_id']);

$page_title = 'My Dashboard';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <div style="background: linear-gradient(135deg, #FFD947 0%, #ffd000 100%); padding: 2rem; border-radius: 12px; margin: 2rem 0; box-shadow: 0 4px 15px rgba(255, 217, 71, 0.3);">
        <h1 style="margin: 0; color: #333; font-size: 2.5rem;">Hello, <?php echo htmlspecialchars(explode(' ', $user['customer_name'])[0]); ?>! 👋</h1>
        <p style="margin: 0.5rem 0 0 0; color: #555; font-size: 1.1rem;">
            <?php 
            $user_type = $user['user_type'] ?? 'student';
            $icon = $user_type == 'creator' ? '✍️' : '🎓';
            echo $icon . ' ' . ucfirst($user_type) . ' Account';
            ?>
        </p>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin-bottom: 2rem;">
        <div style="background: white; padding: 2rem; border-radius: 12px; text-align: center;">
            <h3 style="color: #666;">Total Orders</h3>
            <p style="font-size: 2rem; font-weight: 700; color: #FFD947;"><?php echo count($orders); ?></p>
        </div>
        <div style="background: white; padding: 2rem; border-radius: 12px; text-align: center;">
            <h3 style="color: #666;">Downloads</h3>
            <p style="font-size: 2rem; font-weight: 700; color: #FFD947;"><?php echo count($downloads); ?></p>
        </div>
    </div>
    
    <div style="background: white; border-radius: 12px; padding: 2rem; margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 style="margin: 0;">Profile Information</h2>
            <button onclick="toggleEditMode()" id="editBtn" class="btn btn-primary">✏️ Edit Profile</button>
        </div>
        
        <!-- View Mode -->
        <div id="viewMode">
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
                <div>
                    <strong style="color: #666;">Name:</strong><br>
                    <span style="font-size: 1.1rem;"><?php echo htmlspecialchars($user['customer_name']); ?></span>
                </div>
                <div>
                    <strong style="color: #666;">Email:</strong><br>
                    <span style="font-size: 1.1rem;"><?php echo htmlspecialchars($user['customer_email']); ?></span>
                </div>
                <div>
                    <strong style="color: #666;">Country:</strong><br>
                    <span style="font-size: 1.1rem;"><?php echo htmlspecialchars($user['customer_country']); ?></span>
                </div>
                <div>
                    <strong style="color: #666;">City:</strong><br>
                    <span style="font-size: 1.1rem;"><?php echo htmlspecialchars($user['customer_city']); ?></span>
                </div>
                <div>
                    <strong style="color: #666;">Contact:</strong><br>
                    <span style="font-size: 1.1rem;"><?php echo htmlspecialchars($user['customer_contact']); ?></span>
                </div>
            </div>
        </div>
        
        <!-- Edit Mode -->
        <div id="editMode" style="display: none;">
            <form action="<?php echo url('app/views/profile/update.php'); ?>" method="POST">
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['customer_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['customer_email']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="country">Country</label>
                        <input type="text" id="country" name="country" value="<?php echo htmlspecialchars($user['customer_country']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($user['customer_city']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="contact">Contact</label>
                        <input type="text" id="contact" name="contact" value="<?php echo htmlspecialchars($user['customer_contact']); ?>" required>
                    </div>
                </div>
                <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                    <button type="submit" class="btn btn-primary">💾 Save Changes</button>
                    <button type="button" onclick="toggleEditMode()" class="btn btn-secondary">❌ Cancel</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
    function toggleEditMode() {
        const viewMode = document.getElementById('viewMode');
        const editMode = document.getElementById('editMode');
        const editBtn = document.getElementById('editBtn');
        
        if (viewMode.style.display === 'none') {
            viewMode.style.display = 'block';
            editMode.style.display = 'none';
            editBtn.style.display = 'block';
        } else {
            viewMode.style.display = 'none';
            editMode.style.display = 'block';
            editBtn.style.display = 'none';
        }
    }
    </script>
    
    <div style="background: white; border-radius: 12px; padding: 2rem;">
        <h2 style="margin-bottom: 1rem;">Recent Orders</h2>
        <?php if (empty($orders)): ?>
            <p style="color: #666;">No orders yet.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Date</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (array_slice($orders, 0, 5) as $order): ?>
                        <tr>
                            <td>#<?php echo $order['invoice_no']; ?></td>
                            <td><?php echo date('M d, Y', strtotime($order['purchase_date'])); ?></td>
                            <td><?php echo $order['resource_count']; ?></td>
                            <td>₵<?php echo number_format($order['total_amount'], 2); ?></td>
                            <td>
                                <span style="padding: 0.3rem 0.8rem; border-radius: 20px; background: <?php echo $order['order_status'] == 'completed' ? '#d4edda' : '#fff3cd'; ?>; color: <?php echo $order['order_status'] == 'completed' ? '#155724' : '#856404'; ?>;">
                                    <?php echo ucfirst($order['order_status']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?php echo url('app/views/orders/invoice.php?id=' . $order['purchase_id']); ?>" 
                                   class="btn btn-secondary" style="text-decoration: none; color: white;">
                                    View
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
