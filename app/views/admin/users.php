<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/user_model.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
    header('Location: ' . url('app/views/auth/login.php'));
    exit;
}

$userModel = new user_model();
$message = '';
$message_type = '';

// Handle role change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_role'])) {
    $user_id = $_POST['user_id'];
    $new_role = $_POST['new_role'];
    
    // Prevent admin from changing their own role
    if ($user_id == $_SESSION['user_id']) {
        $message = 'You cannot change your own role!';
        $message_type = 'error';
    } else {
        if ($userModel->updateUserRole($user_id, $new_role)) {
            $message = 'User role updated successfully!';
            $message_type = 'success';
        } else {
            $message = 'Failed to update user role.';
            $message_type = 'error';
        }
    }
}

// Handle user deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];
    
    // Prevent admin from deleting themselves
    if ($user_id == $_SESSION['user_id']) {
        $message = 'You cannot delete your own account!';
        $message_type = 'error';
    } else {
        if ($userModel->delete($user_id)) {
            $message = 'User deleted successfully!';
            $message_type = 'success';
        } else {
            $message = 'Failed to delete user.';
            $message_type = 'error';
        }
    }
}

$users = $userModel->getAll();

// Count users by role
$admins = array_filter($users, function($u) { return $u['user_role'] == 1; });
$creators = array_filter($users, function($u) { return $u['user_role'] == 2; });
$students = array_filter($users, function($u) { return $u['user_role'] == 3; });

$page_title = 'Manage Users';
require_once __DIR__ . '/../layouts/admin_header.php';
?>

<div class="container" style="margin: 3rem auto;">
    <h1 style="margin-bottom: 2rem; color: #667eea;">Manage Users</h1>
    
    <?php if ($message): ?>
        <div style="background: <?php echo $message_type === 'success' ? '#d4edda' : '#f8d7da'; ?>; 
                    border-left: 4px solid <?php echo $message_type === 'success' ? '#28a745' : '#dc3545'; ?>; 
                    padding: 1rem; margin-bottom: 2rem; border-radius: 8px; color: <?php echo $message_type === 'success' ? '#155724' : '#721c24'; ?>;">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>
    
    <!-- User Statistics -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2rem; margin-bottom: 3rem;">
        <div class="admin-card" style="text-align: center;">
            <h3 style="color: #667eea; margin-bottom: 1rem;">Total Users</h3>
            <p style="font-size: 2.5rem; font-weight: 700; color: #667eea;"><?php echo count($users); ?></p>
        </div>
        
        <div class="admin-card" style="text-align: center;">
            <h3 style="color: #667eea; margin-bottom: 1rem;">Admins</h3>
            <p style="font-size: 2.5rem; font-weight: 700; color: #764ba2;"><?php echo count($admins); ?></p>
        </div>
        
        <div class="admin-card" style="text-align: center;">
            <h3 style="color: #667eea; margin-bottom: 1rem;">Creators</h3>
            <p style="font-size: 2.5rem; font-weight: 700; color: #4CAF50;"><?php echo count($creators); ?></p>
        </div>
        
        <div class="admin-card" style="text-align: center;">
            <h3 style="color: #667eea; margin-bottom: 1rem;">Students</h3>
            <p style="font-size: 2.5rem; font-weight: 700; color: #FFD947;"><?php echo count($students); ?></p>
        </div>
    </div>
    
    <!-- Users Table -->
    <div class="admin-card">
        <h2 style="margin-bottom: 1.5rem; color: #667eea;">All Users</h2>
        
        <?php if (empty($users)): ?>
            <p style="color: #666; text-align: center; padding: 2rem;">No users found.</p>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                            <th style="padding: 1rem; text-align: left; border-radius: 8px 0 0 8px;">ID</th>
                            <th style="padding: 1rem; text-align: left;">Name</th>
                            <th style="padding: 1rem; text-align: left;">Email</th>
                            <th style="padding: 1rem; text-align: left;">Country</th>
                            <th style="padding: 1rem; text-align: left;">Contact</th>
                            <th style="padding: 1rem; text-align: center;">Current Role</th>
                            <th style="padding: 1rem; text-align: center;">Change Role</th>
                            <th style="padding: 1rem; text-align: center; border-radius: 0 8px 8px 0;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): 
                            $role_badge_color = '#667eea';
                            $role_text = 'Student';
                            if ($user['user_role'] == 1) {
                                $role_badge_color = '#764ba2';
                                $role_text = 'Admin';
                            } elseif ($user['user_role'] == 2) {
                                $role_badge_color = '#4CAF50';
                                $role_text = 'Creator';
                            }
                            
                            $is_current_user = ($user['customer_id'] == $_SESSION['user_id']);
                        ?>
                            <tr style="border-bottom: 1px solid #eee; transition: background 0.2s;" 
                                onmouseover="this.style.background='rgba(102, 126, 234, 0.05)'" 
                                onmouseout="this.style.background='transparent'">
                                <td style="padding: 1rem; font-weight: 600;"><?php echo $user['customer_id']; ?></td>
                                <td style="padding: 1rem;">
                                    <?php echo htmlspecialchars($user['customer_name']); ?>
                                    <?php if ($is_current_user): ?>
                                        <span style="background: #FFD947; color: #333; padding: 0.2rem 0.5rem; border-radius: 12px; font-size: 0.75rem; margin-left: 0.5rem; font-weight: 600;">YOU</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 1rem;"><?php echo htmlspecialchars($user['customer_email']); ?></td>
                                <td style="padding: 1rem;"><?php echo htmlspecialchars($user['customer_country']); ?></td>
                                <td style="padding: 1rem;"><?php echo htmlspecialchars($user['customer_contact']); ?></td>
                                <td style="padding: 1rem; text-align: center;">
                                    <span style="background: <?php echo $role_badge_color; ?>; color: white; padding: 0.4rem 1rem; border-radius: 20px; font-weight: 600; display: inline-block;">
                                        <?php echo $role_text; ?>
                                    </span>
                                </td>
                                <td style="padding: 1rem; text-align: center;">
                                    <?php if (!$is_current_user): ?>
                                        <form method="POST" style="display: inline-block;">
                                            <input type="hidden" name="user_id" value="<?php echo $user['customer_id']; ?>">
                                            <select name="new_role" style="padding: 0.5rem; border: 2px solid #667eea; border-radius: 8px; font-weight: 600; background: white; cursor: pointer;" onchange="this.form.submit()">
                                                <option value="">Change to...</option>
                                                <option value="1" <?php echo $user['user_role'] == 1 ? 'disabled' : ''; ?>>Admin</option>
                                                <option value="2" <?php echo $user['user_role'] == 2 ? 'disabled' : ''; ?>>Creator</option>
                                                <option value="3" <?php echo $user['user_role'] == 3 ? 'disabled' : ''; ?>>Student</option>
                                            </select>
                                            <input type="hidden" name="change_role" value="1">
                                        </form>
                                    <?php else: ?>
                                        <span style="color: #999; font-style: italic;">-</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 1rem; text-align: center;">
                                    <?php if (!$is_current_user): ?>
                                        <form method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                                            <input type="hidden" name="user_id" value="<?php echo $user['customer_id']; ?>">
                                            <button type="submit" name="delete_user" style="background: #FF6B6B; color: white; border: none; padding: 0.5rem 1rem; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s;" onmouseover="this.style.background='#ff5252'" onmouseout="this.style.background='#FF6B6B'">
                                                Delete
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span style="color: #999; font-style: italic;">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Role Information -->
    <div style="margin-top: 2rem; display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 2rem;">
        <div class="admin-card">
            <h3 style="margin-bottom: 1rem; color: #764ba2;">Admin Role</h3>
            <ul style="color: #666; line-height: 1.8;">
                <li>Full platform access</li>
                <li>Manage all users</li>
                <li>View analytics</li>
                <li>Manage resources & orders</li>
            </ul>
        </div>
        
        <div class="admin-card">
            <h3 style="margin-bottom: 1rem; color: #4CAF50;">Creator Role</h3>
            <ul style="color: #666; line-height: 1.8;">
                <li>Upload resources</li>
                <li>Create quizzes</li>
                <li>View earnings (80%)</li>
                <li>Manage own content</li>
            </ul>
        </div>
        
        <div class="admin-card">
            <h3 style="margin-bottom: 1rem; color: #FFD947;">Student Role</h3>
            <ul style="color: #666; line-height: 1.8;">
                <li>Browse resources</li>
                <li>Purchase content</li>
                <li>Take quizzes</li>
                <li>View purchased items</li>
            </ul>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
