<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/quiz_model.php';
require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
    header('Location: ' . url('app/views/auth/login.php'));
    exit;
}

$quizModel = new quiz_model();
$db = Database::getInstance()->getConnection();

// Get quiz statistics
$total_quizzes = $db->query("SELECT COUNT(*) as count FROM quizzes")->fetch_assoc()['count'];
$published_quizzes = $db->query("SELECT COUNT(*) as count FROM quizzes WHERE is_published = 1")->fetch_assoc()['count'];
$total_attempts = $db->query("SELECT COUNT(*) as count FROM quiz_attempts")->fetch_assoc()['count'];
$total_questions = $db->query("SELECT COUNT(*) as count FROM quiz_questions")->fetch_assoc()['count'];

// Get quiz performance data
$quiz_stats = $db->query("
    SELECT 
        q.quiz_id,
        q.quiz_title,
        c.cat_name,
        cr.customer_name as creator_name,
        COUNT(DISTINCT qa.attempt_id) as total_attempts,
        AVG(qa.score) as avg_score,
        AVG(qa.total_questions) as avg_questions,
        AVG(qa.time_taken) as avg_time
    FROM quizzes q
    LEFT JOIN categories c ON q.category_id = c.cat_id
    LEFT JOIN customer cr ON q.user_id = cr.customer_id
    LEFT JOIN quiz_attempts qa ON q.quiz_id = qa.quiz_id
    GROUP BY q.quiz_id
    ORDER BY total_attempts DESC
")->fetch_all(MYSQLI_ASSOC);

// Get recent quiz attempts
$recent_attempts = $db->query("
    SELECT 
        qa.attempt_id,
        qa.score,
        qa.total_questions,
        qa.time_taken,
        qa.completed_at,
        q.quiz_title,
        c.customer_name as student_name
    FROM quiz_attempts qa
    JOIN quizzes q ON qa.quiz_id = q.quiz_id
    JOIN customer c ON qa.user_id = c.customer_id
    ORDER BY qa.completed_at DESC
    LIMIT 10
")->fetch_all(MYSQLI_ASSOC);

// Get top performers
$top_performers = $db->query("
    SELECT 
        c.customer_name,
        COUNT(qa.attempt_id) as total_attempts,
        AVG(qa.score / qa.total_questions * 100) as avg_percentage
    FROM quiz_attempts qa
    JOIN customer c ON qa.user_id = c.customer_id
    GROUP BY qa.user_id
    ORDER BY avg_percentage DESC
    LIMIT 10
")->fetch_all(MYSQLI_ASSOC);

$page_title = 'Quiz Analytics';
require_once __DIR__ . '/../layouts/admin_header.php';
?>

<div class="container" style="margin: 3rem auto;">
    <h1 style="margin-bottom: 2rem; color: #667eea;">📊 Quiz Analytics</h1>
    
    <!-- Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2rem; margin-bottom: 3rem;">
        <div class="admin-card" style="text-align: center;">
            <h3 style="color: #667eea; margin-bottom: 1rem;">Total Quizzes</h3>
            <p style="font-size: 2.5rem; font-weight: 700; color: #667eea;"><?php echo $total_quizzes; ?></p>
            <p style="color: #666; font-size: 0.9rem;"><?php echo $published_quizzes; ?> published</p>
        </div>
        
        <div class="admin-card" style="text-align: center;">
            <h3 style="color: #667eea; margin-bottom: 1rem;">Total Attempts</h3>
            <p style="font-size: 2.5rem; font-weight: 700; color: #FFD947;"><?php echo $total_attempts; ?></p>
        </div>
        
        <div class="admin-card" style="text-align: center;">
            <h3 style="color: #667eea; margin-bottom: 1rem;">Total Questions</h3>
            <p style="font-size: 2.5rem; font-weight: 700; color: #28a745;"><?php echo $total_questions; ?></p>
        </div>
        
        <div class="admin-card" style="text-align: center;">
            <h3 style="color: #667eea; margin-bottom: 1rem;">Avg. Questions/Quiz</h3>
            <p style="font-size: 2.5rem; font-weight: 700; color: #17a2b8;">
                <?php echo $total_quizzes > 0 ? round($total_questions / $total_quizzes, 1) : 0; ?>
            </p>
        </div>
    </div>
    
    <!-- Quiz Performance Table -->
    <div class="admin-card" style="margin-bottom: 3rem;">
        <h2 style="margin-bottom: 1.5rem; color: #667eea;">Quiz Performance</h2>
        
        <?php if (empty($quiz_stats)): ?>
            <p style="color: #666; text-align: center; padding: 2rem;">No quiz data available yet.</p>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                            <th style="padding: 1rem; text-align: left;">Quiz Title</th>
                            <th style="padding: 1rem; text-align: left;">Category</th>
                            <th style="padding: 1rem; text-align: left;">Creator</th>
                            <th style="padding: 1rem; text-align: center;">Attempts</th>
                            <th style="padding: 1rem; text-align: center;">Avg Score</th>
                            <th style="padding: 1rem; text-align: center;">Avg Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($quiz_stats as $stat): ?>
                            <tr style="border-bottom: 1px solid #dee2e6;">
                                <td style="padding: 1rem;"><?php echo htmlspecialchars($stat['quiz_title']); ?></td>
                                <td style="padding: 1rem;"><?php echo htmlspecialchars($stat['cat_name'] ?? 'N/A'); ?></td>
                                <td style="padding: 1rem;"><?php echo htmlspecialchars($stat['creator_name']); ?></td>
                                <td style="padding: 1rem; text-align: center;">
                                    <span style="background: #667eea; color: white; padding: 0.25rem 0.75rem; border-radius: 12px;">
                                        <?php echo $stat['total_attempts']; ?>
                                    </span>
                                </td>
                                <td style="padding: 1rem; text-align: center;">
                                    <?php if ($stat['total_attempts'] > 0): ?>
                                        <?php 
                                        $percentage = ($stat['avg_score'] / $stat['avg_questions']) * 100;
                                        $color = $percentage >= 70 ? '#28a745' : ($percentage >= 50 ? '#FFD947' : '#dc3545');
                                        ?>
                                        <span style="color: <?php echo $color; ?>; font-weight: 600;">
                                            <?php echo round($percentage, 1); ?>%
                                        </span>
                                    <?php else: ?>
                                        <span style="color: #999;">-</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 1rem; text-align: center;">
                                    <?php if ($stat['total_attempts'] > 0): ?>
                                        <?php echo round($stat['avg_time'] / 60, 1); ?> min
                                    <?php else: ?>
                                        <span style="color: #999;">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Two Column Layout -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
        <!-- Recent Attempts -->
        <div class="admin-card">
            <h2 style="margin-bottom: 1.5rem; color: #667eea;">Recent Quiz Attempts</h2>
            
            <?php if (empty($recent_attempts)): ?>
                <p style="color: #666; text-align: center; padding: 2rem;">No attempts yet.</p>
            <?php else: ?>
                <div style="max-height: 400px; overflow-y: auto;">
                    <?php foreach ($recent_attempts as $attempt): ?>
                        <?php 
                        $percentage = ($attempt['score'] / $attempt['total_questions']) * 100;
                        $color = $percentage >= 70 ? '#28a745' : ($percentage >= 50 ? '#FFD947' : '#dc3545');
                        ?>
                        <div style="padding: 1rem; border-bottom: 1px solid #eee;">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                                <div>
                                    <strong><?php echo htmlspecialchars($attempt['student_name']); ?></strong>
                                    <p style="color: #666; font-size: 0.9rem; margin: 0.25rem 0;">
                                        <?php echo htmlspecialchars($attempt['quiz_title']); ?>
                                    </p>
                                </div>
                                <span style="background: <?php echo $color; ?>; color: white; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.9rem;">
                                    <?php echo $attempt['score']; ?>/<?php echo $attempt['total_questions']; ?>
                                </span>
                            </div>
                            <p style="color: #999; font-size: 0.85rem; margin: 0;">
                                <?php echo date('M j, Y g:i A', strtotime($attempt['completed_at'])); ?>
                                • <?php echo round($attempt['time_taken'] / 60, 1); ?> min
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Top Performers -->
        <div class="admin-card">
            <h2 style="margin-bottom: 1.5rem; color: #667eea;">🏆 Top Performers</h2>
            
            <?php if (empty($top_performers)): ?>
                <p style="color: #666; text-align: center; padding: 2rem;">No data yet.</p>
            <?php else: ?>
                <div style="max-height: 400px; overflow-y: auto;">
                    <?php foreach ($top_performers as $index => $performer): ?>
                        <div style="padding: 1rem; border-bottom: 1px solid #eee; display: flex; align-items: center; gap: 1rem;">
                            <div style="font-size: 1.5rem; font-weight: 700; color: <?php echo $index < 3 ? '#FFD947' : '#999'; ?>; min-width: 30px;">
                                <?php echo $index + 1; ?>
                            </div>
                            <div style="flex: 1;">
                                <strong><?php echo htmlspecialchars($performer['customer_name']); ?></strong>
                                <p style="color: #666; font-size: 0.9rem; margin: 0.25rem 0;">
                                    <?php echo $performer['total_attempts']; ?> attempts
                                </p>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 1.25rem; font-weight: 700; color: #28a745;">
                                    <?php echo round($performer['avg_percentage'], 1); ?>%
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/admin_footer.php'; ?>
