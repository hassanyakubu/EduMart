<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/quiz_model.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . url('app/views/auth/login.php'));
    exit;
}

$quizModel = new quiz_model();

// Check if user is creator or admin
$isCreator = isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'creator';
$isAdmin = $_SESSION['user_role'] == 1;

// Get quizzes based on user type
if ($isCreator || $isAdmin) {
    // Creators and admins see all published quizzes
    $quizzes = $quizModel->getAllPublishedQuizzes();
} else {
    // Students see only quizzes for categories they've purchased
    $quizzes = $quizModel->getQuizzesForStudent($_SESSION['user_id']);
}

$page_title = 'Available Quizzes';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container" style="margin: 3rem auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h1 style="margin: 0;">Available Quizzes</h1>
        <?php if ($isCreator || $isAdmin): ?>
            <a href="<?php echo url('app/views/quiz/my_quizzes.php'); ?>" class="btn btn-primary">My Quizzes</a>
        <?php endif; ?>
    </div>
    
    <?php if (!$isCreator && !$isAdmin): ?>
    <div style="background: #d1ecf1; border-left: 4px solid #17a2b8; padding: 1rem; margin-bottom: 2rem; border-radius: 4px;">
        <strong style="color: #0c5460;">Access to Quizzes</strong>
        <p style="margin: 0.3rem 0 0 0; color: #0c5460; font-size: 0.95rem;">
            You can only take quizzes for categories where you've purchased resources. Purchase resources to unlock more quizzes!
        </p>
    </div>
    <?php endif; ?>
    
    <?php if (empty($quizzes)): ?>
        <div style="background: white; border-radius: 12px; padding: 3rem; text-align: center;">
            <p style="color: #666; font-size: 1.2rem;">No quizzes available yet. Create your first quiz!</p>
        </div>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 2rem;">
            <?php foreach ($quizzes as $quiz): ?>
                <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 10px rgba(0,0,0,0.1); transition: transform 0.2s;">
                    <h3 style="margin-bottom: 0.5rem; color: #333;"><?php echo htmlspecialchars($quiz['quiz_title']); ?></h3>
                    <p style="color: #666; font-size: 0.9rem; margin-bottom: 1rem;">
                        Created by: <?php echo htmlspecialchars($quiz['creator_name']); ?>
                    </p>
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; color: #666;">
                        <span><?php echo $quiz['time_limit']; ?> minutes</span>
                    </div>
                    <a href="<?php echo url('app/views/quiz/take.php?id=' . $quiz['quiz_id']); ?>" 
                       class="btn btn-primary" style="width: 100%; text-align: center; text-decoration: none;">
                        Start Quiz
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
