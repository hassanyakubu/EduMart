<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/admin_controller.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
    header('Location: ' . url('app/views/auth/login.php'));
    exit;
}

$controller = new admin_controller();
$students = $controller->getStudents();

$page_title = 'Manage Students';
require_once __DIR__ . '/../layouts/admin_header.php';
?>

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin: 2rem 0;">
        <h1>🎓 Manage Students</h1>
        <a href="<?php echo url('app/views/admin/dashboard.php'); ?>" class="btn btn-secondary" style="text-decoration: none; color: white;">
            ← Back to Dashboard
        </a>
    </div>
    
    <div style="background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 4px 15px rgba(0,0,0,0.08);">
        <?php if (empty($students)): ?>
            <p style="text-align: center; color: #666; padding: 2rem;">No students found.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Country</th>
                        <th>City</th>
                        <th>Contact</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?php echo $student['customer_id']; ?></td>
                            <td><?php echo htmlspecialchars($student['customer_name']); ?></td>
                            <td><?php echo htmlspecialchars($student['customer_email']); ?></td>
                            <td><?php echo htmlspecialchars($student['customer_country']); ?></td>
                            <td><?php echo htmlspecialchars($student['customer_city']); ?></td>
                            <td><?php echo htmlspecialchars($student['customer_contact']); ?></td>
                            <td>
                                <a href="<?php echo url('app/views/admin/delete_user.php?id=' . $student['customer_id']); ?>" 
                                   class="btn btn-danger" 
                                   style="text-decoration: none; color: white; font-size: 0.9rem; padding: 0.5rem 1rem;"
                                   onclick="return confirm('Are you sure you want to delete this student?');">
                                    🗑️ Delete
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
