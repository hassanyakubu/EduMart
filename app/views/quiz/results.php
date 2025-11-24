<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/quiz_model.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['attempt_id'])) {
    header('Location: ' . url('app/views/quiz/list.php'));
    exit;
}

$quizModel = new quiz_model();
$attempt_id = intval($_GET['attempt_id']);
$results = $quizModel->getAttemptDetails($attempt_id);

if (empty($results)) {
    $_SESSION['error'] = 'Results not found.';
    header('Location: ' . url('app/views/quiz/list.php'));
    exit;
}

$attempt = $results[0];
$score = $attempt['score'];
$total = $attempt['total_questions'];
$percentage = round(($score / $total) * 100, 2);
$time_taken_minutes = floor($attempt['time_taken'] / 60);
$time_taken_seconds = $attempt['time_taken'] % 60;

$page_title = 'Quiz Results';
require_once __DIR__ . '/../layouts/header.php';
?>

<style>
.result-card {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.correct-answer {
    background: #d4edda;
    border-left: 4px solid #28a745;
}

.wrong-answer {
    background: #f8d7da;
    border-left: 4px solid #dc3545;
}

.answer-option {
    padding: 0.8rem;
    margin: 0.5rem 0;
    border-radius: 6px;
    background: #f8f9fa;
}

.answer-option.correct {
    background: #d4edda;
    font-weight: 600;
}

.answer-option.user-wrong {
    background: #f8d7da;
}
</style>

<div class="container" style="max-width: 900px; margin: 3rem auto;">
    <div style="background: linear-gradient(135deg, <?php echo $percentage >= 70 ? '#28a745' : ($percentage >= 50 ? '#ffc107' : '#dc3545'); ?> 0%, <?php echo $percentage >= 70 ? '#20c997' : ($percentage >= 50 ? '#ffb300' : '#c82333'); ?> 100%); padding: 3rem; border-radius: 12px; text-align: center; color: white; margin-bottom: 2rem;">
        <h1 style="margin: 0; font-size: 3rem;">
            <?php echo $percentage >= 70 ? '🎉' : ($percentage >= 50 ? '👍' : '📚'); ?>
        </h1>
        <h2 style="margin: 1rem 0;">Your Score: <?php echo $score; ?>/<?php echo $total; ?></h2>
        <p style="font-size: 2rem; font-weight: 700; margin: 0.5rem 0;"><?php echo $percentage; ?>%</p>
        <p style="margin: 0.5rem 0;">Time Taken: <?php echo $time_taken_minutes; ?>m <?php echo $time_taken_seconds; ?>s</p>
        <p style="margin: 1rem 0 0 0; font-size: 1.2rem;">
            <?php 
            if ($percentage >= 70) {
                echo "Excellent work! You've mastered this topic!";
            } elseif ($percentage >= 50) {
                echo "Good effort! Keep practicing to improve!";
            } else {
                echo "Keep studying! You'll do better next time!";
            }
            ?>
        </p>
    </div>
    
    <h2 style="margin-bottom: 1.5rem;">Detailed Results</h2>
    
    <?php 
    $question_num = 1;
    foreach ($results as $result): 
    ?>
        <div class="result-card <?php echo $result['is_correct'] ? 'correct-answer' : 'wrong-answer'; ?>">
            <h3 style="margin-bottom: 1rem; color: #333;">
                Question <?php echo $question_num++; ?>: <?php echo htmlspecialchars($result['question_text']); ?>
            </h3>
            
            <div class="answer-option <?php echo $result['user_answer'] == 'A' ? ($result['is_correct'] ? 'correct' : 'user-wrong') : ($result['correct_answer'] == 'A' ? 'correct' : ''); ?>">
                A. <?php echo htmlspecialchars($result['option_a']); ?>
                <?php if ($result['user_answer'] == 'A'): ?>
                    <span style="float: right;"><?php echo $result['is_correct'] ? '✓ Your Answer' : '✗ Your Answer'; ?></span>
                <?php elseif ($result['correct_answer'] == 'A'): ?>
                    <span style="float: right;">✓ Correct Answer</span>
                <?php endif; ?>
            </div>
            
            <div class="answer-option <?php echo $result['user_answer'] == 'B' ? ($result['is_correct'] ? 'correct' : 'user-wrong') : ($result['correct_answer'] == 'B' ? 'correct' : ''); ?>">
                B. <?php echo htmlspecialchars($result['option_b']); ?>
                <?php if ($result['user_answer'] == 'B'): ?>
                    <span style="float: right;"><?php echo $result['is_correct'] ? '✓ Your Answer' : '✗ Your Answer'; ?></span>
                <?php elseif ($result['correct_answer'] == 'B'): ?>
                    <span style="float: right;">✓ Correct Answer</span>
                <?php endif; ?>
            </div>
            
            <div class="answer-option <?php echo $result['user_answer'] == 'C' ? ($result['is_correct'] ? 'correct' : 'user-wrong') : ($result['correct_answer'] == 'C' ? 'correct' : ''); ?>">
                C. <?php echo htmlspecialchars($result['option_c']); ?>
                <?php if ($result['user_answer'] == 'C'): ?>
                    <span style="float: right;"><?php echo $result['is_correct'] ? '✓ Your Answer' : '✗ Your Answer'; ?></span>
                <?php elseif ($result['correct_answer'] == 'C'): ?>
                    <span style="float: right;">✓ Correct Answer</span>
                <?php endif; ?>
            </div>
            
            <div class="answer-option <?php echo $result['user_answer'] == 'D' ? ($result['is_correct'] ? 'correct' : 'user-wrong') : ($result['correct_answer'] == 'D' ? 'correct' : ''); ?>">
                D. <?php echo htmlspecialchars($result['option_d']); ?>
                <?php if ($result['user_answer'] == 'D'): ?>
                    <span style="float: right;"><?php echo $result['is_correct'] ? '✓ Your Answer' : '✗ Your Answer'; ?></span>
                <?php elseif ($result['correct_answer'] == 'D'): ?>
                    <span style="float: right;">✓ Correct Answer</span>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
    
    <div style="text-align: center; margin-top: 2rem; display: flex; gap: 1rem; justify-content: center;">
        <a href="<?php echo url('app/views/quiz/list.php'); ?>" class="btn btn-primary">Take Another Quiz</a>
        <a href="<?php echo url('app/views/profile/dashboard.php'); ?>" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
