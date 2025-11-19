<?php
session_start();
require_once __DIR__ . '/../../controllers/admin_controller.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
    header('Location: /app/views/auth/login.php');
    exit;
}

$controller = new admin_controller();
$controller->manageUsers();

$page_title = 'Manage Users';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <h1 style="margin: 2rem 0;">Manage Users</h1>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Country</th>
                    <th>City</th>
                    <th>Contact</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['customer_id']; ?></td>
                        <td><?php echo htmlspecialchars($user['customer_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['customer_email']); ?></td>
                        <td><?php echo htmlspecialchars($user['customer_country']); ?></td>
                        <td><?php echo htmlspecialchars($user['customer_city']); ?></td>
                        <td><?php echo htmlspecialchars($user['customer_contact']); ?></td>
                        <td><?php echo $user['user_role'] == 1 ? 'Admin' : 'User'; ?></td>
                        <td>
                            <?php if ($user['customer_id'] != $_SESSION['user_id']): ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $user['customer_id']; ?>">
                                    <button type="submit" name="delete_user" class="btn btn-danger" 
                                            onclick="return confirm('Are you sure you want to delete this user?')">
                                        Delete
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
