<?php
session_start();
require_once __DIR__ . '/../../config/config.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['quiz_results'])) {
    header('Location: ' . url('app/views/quiz/list.php'));
    exit;
}

$results = $_SESSION['quiz_results'];
$score = $results['score'];
$total = $results['total'];
$percentage = $results['percentage'];
$time_taken = $results['time_taken'];
$minutes = floor($time_taken / 60);
$seconds = $time_taken % 60;

// Determine performance level
if ($percentage >= 80) {
    $performance = 'Excellent';
    $color = '#28a745';
} elseif ($percentage >= 60) {
    $performance = 'Good';
    $color = '#ffc107';
} elseif ($percentage >= 40) {
    $performance = 'Fair';
    $color = '#ff9800';
} else {
    $performance = 'Needs Improvement';
    $color = '#dc3545';
}

$page_title = 'Quiz Results';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container" style="max-width: 900px; margin: 3rem auto;">
    <!-- Score Summary -->
    <div style="background: white; border-radius: 12px; padding: 3rem; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 2rem;">
        <h1 style="margin: 0 0 1rem 0;"><?php echo htmlspecialchars($results['quiz_title']); ?></h1>
        
        <div style="background: <?php echo $color; ?>; color: white; padding: 2rem; border-radius: 12px; margin: 2rem 0;">
            <div style="font-size: 4rem; font-weight: 700; margin-bottom: 0.5rem;">
                <?php echo $percentage; ?>%
            </div>
            <div style="font-size: 1.5rem; font-weight: 600;">
                <?php echo $performance; ?>
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 2rem; margin-top: 2rem;">
            <div>
                <div style="font-size: 2rem; font-weight: 700; color: #333;"><?php echo $score; ?>/<?php echo $total; ?></div>
                <div style="color: #666; margin-top: 0.5rem;">Correct Answers</div>
            </div>
            <div>
                <div style="font-size: 2rem; font-weight: 700; color: #333;"><?php echo $total - $score; ?></div>
                <div style="color: #666; margin-top: 0.5rem;">Incorrect</div>
            </div>
            <div>
                <div style="font-size: 2rem; font-weight: 700; color: #333;"><?php echo $minutes; ?>m <?php echo $seconds; ?>s</div>
                <div style="color: #666; margin-top: 0.5rem;">Time Taken</div>
            </div>
        </div>
    </div>
    
    <!-- Detailed Results -->
    <div style="background: white; border-radius: 12px; padding: 2.5rem; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <h2 style="margin: 0 0 2rem 0;">Detailed Results</h2>
        
        <?php foreach ($results['results'] as $index => $result): ?>
            <div style="background: <?php echo $result['is_correct'] ? '#d4edda' : '#f8d7da'; ?>; 
                        border-left: 4px solid <?php echo $result['is_correct'] ? '#28a745' : '#dc3545'; ?>; 
                        padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                    <h3 style="margin: 0; color: #333;">Question <?php echo $index + 1; ?></h3>
                    <span style="background: <?php echo $result['is_correct'] ? '#28a745' : '#dc3545'; ?>; 
                                 color: white; padding: 0.3rem 1rem; border-radius: 20px; font-size: 0.9rem; font-weight: 600;">
                        <?php echo $result['is_correct'] ? 'Correct' : 'Incorrect'; ?>
                    </span>
                </div>
                
                <p style="font-size: 1.1rem; margin-bottom: 1.5rem; color: #333;">
                    <?php echo htmlspecialchars($result['question_text']); ?>
                </p>
                
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <?php 
                    $options = [
                        'A' => $result['option_a'],
                        'B' => $result['option_b'],
                        'C' => $result['option_c'],
                        'D' => $result['option_d']
                    ];
                    foreach ($options as $key => $value): 
                        $is_user_answer = ($key === $result['user_answer']);
                        $is_correct_answer = ($key === $result['correct_answer']);
                        
                        $bg_color = '#fff';
                        $border_color = '#ddd';
                        $text_color = '#333';
                        
                        if ($is_correct_answer) {
                            $bg_color = '#d4edda';
                            $border_color = '#28a745';
                            $text_color = '#155724';
                        } elseif ($is_user_answer && !$is_correct_answer) {
                            $bg_color = '#f8d7da';
                            $border_color = '#dc3545';
                            $text_color = '#721c24';
                        }
                    ?>
                        <div style="padding: 0.75rem 1rem; background: <?php echo $bg_color; ?>; 
                                    border: 2px solid <?php echo $border_color; ?>; border-radius: 8px; 
                                    color: <?php echo $text_color; ?>;">
                            <strong><?php echo $key; ?>.</strong> <?php echo htmlspecialchars($value); ?>
                            <?php if ($is_correct_answer): ?>
                                <span style="float: right; font-weight: 600;">✓ Correct Answer</span>
                            <?php elseif ($is_user_answer): ?>
                                <span style="float: right; font-weight: 600;">✗ Your Answer</span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div style="text-align: center; margin-top: 2rem;">
        <a href="<?php echo url('app/views/quiz/list.php'); ?>" class="btn btn-primary" style="padding: 1rem 3rem; font-size: 1.1rem;">
            Back to Quizzes
        </a>
    </div>
</div>

<?php 
// Clear results from session
unset($_SESSION['quiz_results']);
require_once __DIR__ . '/../layouts/footer.php'; 
?>
