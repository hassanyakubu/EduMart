<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/quiz_model.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . url('app/views/auth/login.php'));
    exit;
}

// Check if user is creator or admin
$isCreator = isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'creator';
$isAdmin = $_SESSION['user_role'] == 1;

if (!$isCreator && !$isAdmin) {
    $_SESSION['error'] = 'Only creators can manage quizzes.';
    header('Location: ' . url('app/views/home/index.php'));
    exit;
}

$quizModel = new quiz_model();
$quizzes = $quizModel->getQuizzesByCreator($_SESSION['user_id']);

// Handle publish/unpublish/delete actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quiz_id = $_POST['quiz_id'] ?? 0;
    $action = $_POST['action'] ?? '';
    
    if ($action === 'publish') {
        if ($quizModel->publishQuiz($quiz_id, $_SESSION['user_id'])) {
            $_SESSION['success'] = 'Quiz published successfully!';
        }
    } elseif ($action === 'unpublish') {
        if ($quizModel->unpublishQuiz($quiz_id, $_SESSION['user_id'])) {
            $_SESSION['success'] = 'Quiz unpublished successfully!';
        }
    } elseif ($action === 'delete') {
        if ($quizModel->deleteQuiz($quiz_id, $_SESSION['user_id'])) {
            $_SESSION['success'] = 'Quiz deleted successfully!';
        }
    }
    
    header('Location: ' . url('app/views/quiz/my_quizzes.php'));
    exit;
}

$page_title = 'My Quizzes';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container" style="margin: 3rem auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1 style="margin: 0;">My Quizzes</h1>
        <a href="<?php echo url('app/views/quiz/create.php'); ?>" class="btn btn-primary">Create New Quiz</a>
    </div>
    
    <?php if (empty($quizzes)): ?>
        <div style="background: white; border-radius: 12px; padding: 3rem; text-align: center;">
            <p style="color: #666; font-size: 1.1rem; margin-bottom: 1.5rem;">You haven't created any quizzes yet.</p>
            <a href="<?php echo url('app/views/quiz/create.php'); ?>" class="btn btn-primary">Create Your First Quiz</a>
        </div>
    <?php else: ?>
        <div style="display: grid; gap: 1.5rem;">
            <?php foreach ($quizzes as $quiz): ?>
                <div style="background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                        <div style="flex: 1;">
                            <h3 style="margin: 0 0 0.5rem 0; color: #333;"><?php echo htmlspecialchars($quiz['quiz_title']); ?></h3>
                            <div style="display: flex; gap: 1rem; flex-wrap: wrap; color: #666; font-size: 0.9rem;">
                                <span>Category: <strong><?php echo htmlspecialchars($quiz['category_name'] ?? 'N/A'); ?></strong></span>
                                <span>Questions: <strong><?php echo $quiz['question_count']; ?></strong></span>
                                <span>Time: <strong><?php echo $quiz['time_limit']; ?> min</strong></span>
                                <span>Created: <strong><?php echo date('M d, Y', strtotime($quiz['created_at'])); ?></strong></span>
                            </div>
                        </div>
                        <div>
                            <?php if ($quiz['is_published']): ?>
                                <span style="background: #28a745; color: white; padding: 0.4rem 1rem; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">Published</span>
                            <?php else: ?>
                                <span style="background: #ffc107; color: #333; padding: 0.4rem 1rem; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">Draft</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                        <?php if ($quiz['is_published']): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="quiz_id" value="<?php echo $quiz['quiz_id']; ?>">
                                <button type="submit" name="action" value="unpublish" class="btn btn-secondary">Unpublish</button>
                            </form>
                        <?php else: ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="quiz_id" value="<?php echo $quiz['quiz_id']; ?>">
                                <button type="submit" name="action" value="publish" class="btn btn-primary">Publish</button>
                            </form>
                        <?php endif; ?>
                        
                        <a href="<?php echo url('app/views/quiz/edit.php?id=' . $quiz['quiz_id']); ?>" class="btn btn-secondary">Edit</a>
                        
                        <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this quiz? This action cannot be undone.');">
                            <input type="hidden" name="quiz_id" value="<?php echo $quiz['quiz_id']; ?>">
                            <button type="submit" name="action" value="delete" class="btn btn-danger" style="background: #dc3545;">Delete</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.btn-danger {
    background: #dc3545;
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s;
    font-weight: 600;
    text-decoration: none;
    display: inline-block;
}

.btn-danger:hover {
    background: #c82333;
    transform: translateY(-2px);
}
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
