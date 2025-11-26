<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/category_model.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . url('app/views/auth/login.php'));
    exit;
}

// Check if user is creator or admin
$isCreator = isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'creator';
$isAdmin = $_SESSION['user_role'] == 1;

if (!$isCreator && !$isAdmin) {
    $_SESSION['error'] = 'Only creators can create quizzes.';
    header('Location: ' . url('app/views/home/index.php'));
    exit;
}

$categoryModel = new category_model();
$categories = $categoryModel->getAll();

$page_title = 'Create Quiz';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container" style="max-width: 900px; margin: 3rem auto;">
    <div style="background: white; border-radius: 12px; padding: 2.5rem; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <h1 style="margin-bottom: 0.5rem; color: #333;">Create New Quiz</h1>
        <p style="color: #666; margin-bottom: 2rem;">Create a multiple-choice quiz for students who have purchased resources in a specific category.</p>
        
        <form action="<?php echo url('app/views/quiz/save_quiz.php'); ?>" method="POST" id="quizForm">
            <div class="form-group">
                <label for="quiz_title">Quiz Title</label>
                <input type="text" id="quiz_title" name="quiz_title" required placeholder="e.g., BECE English Practice Test">
            </div>
            
            <div class="form-group">
                <label for="category_id">Category</label>
                <select id="category_id" name="category_id" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['cat_id']; ?>"><?php echo htmlspecialchars($cat['cat_name']); ?></option>
                    <?php endforeach; ?>
                </select>
                <small style="color: #666; display: block; margin-top: 0.5rem;">
                    Only students who purchased resources in this category can take this quiz
                </small>
            </div>
            
            <div class="form-group">
                <label for="time_limit">Time Limit (minutes)</label>
                <input type="number" id="time_limit" name="time_limit" min="1" max="180" value="30" required>
                <small style="color: #666; display: block; margin-top: 0.5rem;">
                    Students will see a countdown timer
                </small>
            </div>
            
            <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
                <h3 style="margin-bottom: 1rem;">Questions</h3>
                <div id="questionsContainer"></div>
                <button type="button" onclick="addQuestion()" class="btn btn-secondary" style="margin-top: 1rem;">
                    Add Question
                </button>
            </div>
            
            <div style="display: flex; gap: 1rem; justify-content: space-between;">
                <a href="<?php echo url('app/views/quiz/list.php'); ?>" class="btn btn-secondary">Cancel</a>
                <div style="display: flex; gap: 1rem;">
                    <button type="submit" name="action" value="draft" class="btn btn-secondary">Save as Draft</button>
                    <button type="submit" name="action" value="publish" class="btn btn-primary">Publish Quiz</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
let questionCount = 0;

function addQuestion() {
    questionCount++;
    const container = document.getElementById('questionsContainer');
    const questionDiv = document.createElement('div');
    questionDiv.className = 'question-block';
    questionDiv.style.cssText = 'background: white; padding: 1.5rem; border-radius: 8px; margin-bottom: 1rem; border: 1px solid #ddd;';
    questionDiv.innerHTML = `
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h4 style="margin: 0;">Question ${questionCount}</h4>
            <button type="button" onclick="removeQuestion(this)" class="btn btn-danger" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Remove</button>
        </div>
        
        <div class="form-group">
            <label>Question Text</label>
            <textarea name="questions[${questionCount}][text]" required rows="3" placeholder="Enter your question here"></textarea>
        </div>
        
        <div class="form-group">
            <label>Option A</label>
            <input type="text" name="questions[${questionCount}][option_a]" required placeholder="First option">
        </div>
        
        <div class="form-group">
            <label>Option B</label>
            <input type="text" name="questions[${questionCount}][option_b]" required placeholder="Second option">
        </div>
        
        <div class="form-group">
            <label>Option C</label>
            <input type="text" name="questions[${questionCount}][option_c]" required placeholder="Third option">
        </div>
        
        <div class="form-group">
            <label>Option D</label>
            <input type="text" name="questions[${questionCount}][option_d]" required placeholder="Fourth option">
        </div>
        
        <div class="form-group">
            <label>Correct Answer</label>
            <select name="questions[${questionCount}][correct]" required>
                <option value="">Select correct answer</option>
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
                <option value="D">D</option>
            </select>
        </div>
    `;
    container.appendChild(questionDiv);
}

function removeQuestion(button) {
    button.closest('.question-block').remove();
}

// Add first question on load
document.addEventListener('DOMContentLoaded', function() {
    addQuestion();
});

// Validate form before submit
document.getElementById('quizForm').addEventListener('submit', function(e) {
    const questions = document.querySelectorAll('.question-block');
    if (questions.length < 1) {
        e.preventDefault();
        alert('Please add at least one question to the quiz.');
        return false;
    }
});
</script>

<style>
.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #333;
}

.form-group input[type="text"],
.form-group input[type="number"],
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 1rem;
}

.form-group textarea {
    resize: vertical;
    font-family: inherit;
}

.btn-danger {
    background: #dc3545;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-danger:hover {
    background: #c82333;
}
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
