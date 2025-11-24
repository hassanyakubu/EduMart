<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/quiz_model.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header('Location: ' . url('app/views/quiz/list.php'));
    exit;
}

$quizModel = new quiz_model();
$quiz_id = intval($_GET['id']);
$quiz = $quizModel->getQuizById($quiz_id);
$questions = $quizModel->getQuizQuestions($quiz_id);

if (!$quiz || empty($questions)) {
    $_SESSION['error'] = 'Quiz not found.';
    header('Location: ' . url('app/views/quiz/list.php'));
    exit;
}

$page_title = 'Take Quiz';
require_once __DIR__ . '/../layouts/header.php';
?>

<style>
.quiz-timer {
    position: fixed;
    top: 80px;
    right: 20px;
    background: #FFD947;
    padding: 1rem 2rem;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    font-size: 1.5rem;
    font-weight: 700;
    z-index: 1000;
}

.quiz-timer.warning {
    background: #ff6b6b;
    color: white;
    animation: pulse 1s infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.question-card {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.option-label {
    display: block;
    padding: 1rem;
    margin: 0.5rem 0;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
}

.option-label:hover {
    border-color: #FFD947;
    background: #fffef0;
}

.option-label input[type="radio"]:checked + span {
    font-weight: 600;
}

.option-label input[type="radio"]:checked {
    accent-color: #FFD947;
}
</style>

<div class="quiz-timer" id="timer">
    <span id="time-display"><?php echo $quiz['time_limit']; ?>:00</span>
</div>

<div class="container" style="max-width: 900px; margin: 3rem auto; padding-bottom: 3rem;">
    <div style="background: linear-gradient(135deg, #FFD947 0%, #ffd000 100%); padding: 2rem; border-radius: 12px; margin-bottom: 2rem;">
        <h1 style="margin: 0; color: #333;"><?php echo htmlspecialchars($quiz['quiz_title']); ?></h1>
        <p style="margin: 0.5rem 0 0 0; color: #555;">
            <?php echo count($questions); ?> Questions • <?php echo $quiz['time_limit']; ?> Minutes
        </p>
    </div>
    
    <form id="quizForm" action="<?php echo url('app/views/quiz/submit.php'); ?>" method="POST">
        <input type="hidden" name="quiz_id" value="<?php echo $quiz_id; ?>">
        <input type="hidden" name="time_taken" id="time_taken" value="0">
        
        <?php foreach ($questions as $index => $question): ?>
            <div class="question-card">
                <h3 style="margin-bottom: 1.5rem; color: #333;">
                    Question <?php echo $index + 1; ?>: <?php echo htmlspecialchars($question['question_text']); ?>
                </h3>
                
                <label class="option-label">
                    <input type="radio" name="question_<?php echo $question['question_id']; ?>" value="A" required>
                    <span> A. <?php echo htmlspecialchars($question['option_a']); ?></span>
                </label>
                
                <label class="option-label">
                    <input type="radio" name="question_<?php echo $question['question_id']; ?>" value="B" required>
                    <span> B. <?php echo htmlspecialchars($question['option_b']); ?></span>
                </label>
                
                <label class="option-label">
                    <input type="radio" name="question_<?php echo $question['question_id']; ?>" value="C" required>
                    <span> C. <?php echo htmlspecialchars($question['option_c']); ?></span>
                </label>
                
                <label class="option-label">
                    <input type="radio" name="question_<?php echo $question['question_id']; ?>" value="D" required>
                    <span> D. <?php echo htmlspecialchars($question['option_d']); ?></span>
                </label>
            </div>
        <?php endforeach; ?>
        
        <div style="text-align: center; margin-top: 2rem;">
            <button type="submit" class="btn btn-primary" style="font-size: 1.2rem; padding: 1rem 3rem;">
                Submit Quiz
            </button>
        </div>
    </form>
</div>

<script>
let timeLimit = <?php echo $quiz['time_limit'] * 60; ?>; // Convert to seconds
let timeRemaining = timeLimit;
let startTime = Date.now();

function updateTimer() {
    const minutes = Math.floor(timeRemaining / 60);
    const seconds = timeRemaining % 60;
    const display = `${minutes}:${seconds.toString().padStart(2, '0')}`;
    
    document.getElementById('time-display').textContent = display;
    
    const timerElement = document.getElementById('timer');
    if (timeRemaining <= 60) {
        timerElement.classList.add('warning');
    }
    
    if (timeRemaining <= 0) {
        autoSubmit();
        return;
    }
    
    timeRemaining--;
}

function autoSubmit() {
    const timeTaken = Math.floor((Date.now() - startTime) / 1000);
    document.getElementById('time_taken').value = timeTaken;
    
    // Auto-select first option for unanswered questions
    const questions = document.querySelectorAll('.question-card');
    questions.forEach(question => {
        const radios = question.querySelectorAll('input[type="radio"]');
        const isAnswered = Array.from(radios).some(radio => radio.checked);
        if (!isAnswered && radios.length > 0) {
            radios[0].checked = true;
        }
    });
    
    document.getElementById('quizForm').submit();
}

// Update timer every second
setInterval(updateTimer, 1000);

// Handle manual submission
document.getElementById('quizForm').addEventListener('submit', function(e) {
    const timeTaken = Math.floor((Date.now() - startTime) / 1000);
    document.getElementById('time_taken').value = timeTaken;
});

// Warn before leaving page
window.addEventListener('beforeunload', function(e) {
    e.preventDefault();
    e.returnValue = '';
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
