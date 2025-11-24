<?php
session_start();
require_once __DIR__ . '/../../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . url('app/views/auth/login.php'));
    exit;
}

$page_title = 'Create AI Quiz';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container" style="max-width: 800px; margin: 3rem auto;">
    <div style="background: white; border-radius: 12px; padding: 2.5rem; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <h1 style="margin-bottom: 0.5rem; color: #333;">🤖 Create AI-Generated Quiz</h1>
        <p style="color: #666; margin-bottom: 2rem;">Upload a resource and our AI will generate MCQ questions for you!</p>
        
        <form action="<?php echo url('app/views/quiz/process_upload.php'); ?>" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="quiz_title">Quiz Title</label>
                <input type="text" id="quiz_title" name="quiz_title" required placeholder="e.g., Introduction to Python">
            </div>
            
            <div class="form-group">
                <label for="resource_file">Resource File (PDF, TXT, DOC, DOCX)</label>
                <input type="file" id="resource_file" name="resource_file" accept=".pdf,.txt,.doc,.docx" required>
                <small style="color: #666; display: block; margin-top: 0.5rem;">
                    Upload your study material and AI will analyze it to create quiz questions
                </small>
            </div>
            
            <div class="form-group">
                <label for="time_limit">Time Limit (minutes)</label>
                <input type="number" id="time_limit" name="time_limit" min="1" max="120" value="10" required>
                <small style="color: #666;">Set how long students have to complete the quiz</small>
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">✨ Generate Quiz</button>
                <a href="<?php echo url('app/views/quiz/list.php'); ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
