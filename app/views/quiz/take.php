<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/quiz_model.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . url('app/views/auth/login.php'));
    exit;
}

$quiz_id = $_GET['id'] ?? 0;
$quizModel = new quiz_model();
$quiz = $quizModel->getQuizById($quiz_id);

if (!$quiz) {
    $_SESSION['error'] = 'Quiz not found.';
    header('Location: ' . url('app/views/quiz/list.php'));
    exit;
}

// Check if user is creator/admin or student with access
$isCreator = isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'creator';
$isAdmin = $_SESSION['user_role'] == 1;

if (!$isCreator && !$isAdmin) {
    // Student - check if they have access
    if (!$quizModel->canStudentTakeQuiz($_SESSION['user_id'], $quiz_id)) {
        $_SESSION['error'] = 'You need to purchase resources in this category to take this quiz.';
        header('Location: ' . url('app/views/quiz/list.php'));
        exit;
    }
}

$questions = $quizModel->getQuizQuestions($quiz_id);

if (empty($questions)) {
    $_SESSION['error'] = 'This quiz has no questions yet.';
    header('Location: ' . url('app/views/quiz/list.php'));
    exit;
}

$page_title = $quiz['quiz_title'];
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container" style="max-width: 900px; margin: 3rem auto;">
    <div style="background: white; border-radius: 12px; padding: 2.5rem; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 2px solid #f0f0f0;">
            <div>
                <h1 style="margin: 0 0 0.5rem 0;"><?php echo htmlspecialchars($quiz['quiz_title']); ?></h1>
                <p style="margin: 0; color: #666;"><?php echo count($questions); ?> Questions</p>
            </div>
            <div id="timer" style="background: #FFD947; padding: 1rem 2rem; border-radius: 12px; font-size: 1.5rem; font-weight: 700; color: #333;">
                <span id="timeDisplay"><?php echo $quiz['time_limit']; ?>:00</span>
            </div>
        </div>
        
        <form id="quizForm" action="<?php echo url('app/views/quiz/submit_quiz.php'); ?>" method="POST">
            <input type="hidden" name="quiz_id" value="<?php echo $quiz_id; ?>">
            <input type="hidden" name="start_time" value="<?php echo time(); ?>">
            <input type="hidden" id="timeTaken" name="time_taken" value="0">
            
            <?php foreach ($questions as $index => $question): ?>
                <div style="background: #f8f9fa; padding: 2rem; border-radius: 12px; margin-bottom: 2rem;">
                    <h3 style="margin: 0 0 1.5rem 0; color: #333;">
                        Question <?php echo $index + 1; ?>
                    </h3>
                    <p style="font-size: 1.1rem; margin-bottom: 1.5rem; color: #333; line-height: 1.6;">
                        <?php echo htmlspecialchars($question['question_text']); ?>
                    </p>
                    
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <?php 
                        $options = [
                            'A' => $question['option_a'],
                            'B' => $question['option_b'],
                            'C' => $question['option_c'],
                            'D' => $question['option_d']
                        ];
                        foreach ($options as $key => $value): 
                        ?>
                            <label style="display: flex; align-items: center; padding: 1rem; background: white; border: 2px solid #ddd; border-radius: 8px; cursor: pointer; transition: all 0.3s;" 
                                   onmouseover="this.style.borderColor='#FFD947'" 
                                   onmouseout="if(!this.querySelector('input').checked) this.style.borderColor='#ddd'"
                                   onclick="this.style.borderColor='#FFD947'; this.style.background='#fffbf0'">
                                <input type="radio" 
                                       name="answers[<?php echo $question['question_id']; ?>]" 
                                       value="<?php echo $key; ?>" 
                                       required
                                       style="margin-right: 1rem; width: 20px; height: 20px; cursor: pointer;">
                                <span style="font-size: 1rem; color: #333;">
                                    <strong><?php echo $key; ?>.</strong> <?php echo htmlspecialchars($value); ?>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <div style="display: flex; gap: 1rem; justify-content: space-between; margin-top: 2rem;">
                <a href="<?php echo url('app/views/quiz/list.php'); ?>" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary" style="padding: 1rem 3rem; font-size: 1.1rem;">
                    Submit Quiz
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Timer functionality
let timeLimit = <?php echo $quiz['time_limit'] * 60; ?>; // Convert to seconds
let timeRemaining = timeLimit;
let startTime = Date.now();

function updateTimer() {
    const minutes = Math.floor(timeRemaining / 60);
    const seconds = timeRemaining % 60;
    document.getElementById('timeDisplay').textContent = 
        minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
    
    // Change color when time is running out
    const timerDiv = document.getElementById('timer');
    if (timeRemaining <= 60) {
        timerDiv.style.background = '#dc3545';
        timerDiv.style.color = 'white';
    } else if (timeRemaining <= 300) {
        timerDiv.style.background = '#ffc107';
    }
    
    if (timeRemaining <= 0) {
        // Time's up - auto submit
        alert('Time is up! Your quiz will be submitted automatically.');
        document.getElementById('quizForm').submit();
    } else {
        timeRemaining--;
        setTimeout(updateTimer, 1000);
    }
}

// Start timer
updateTimer();

// Update time taken before submit
document.getElementById('quizForm').addEventListener('submit', function() {
    const timeTaken = Math.floor((Date.now() - startTime) / 1000);
    document.getElementById('timeTaken').value = timeTaken;
});

// Warn before leaving page
window.addEventListener('beforeunload', function(e) {
    e.preventDefault();
    e.returnValue = '';
    return '';
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
