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
        <h1 style="margin-bottom: 0.5rem; color: #333;">Create AI-Generated Quiz</h1>
        <p style="color: #666; margin-bottom: 2rem;">Upload a resource and the system will analyze it to generate relevant MCQ questions!</p>
        
        <div style="background: #d4edda; border-left: 4px solid #28a745; padding: 1.2rem; margin-bottom: 1.5rem; border-radius: 4px;">
            <strong style="color: #155724; font-size: 1.1rem;">RECOMMENDED: Use TXT Files</strong>
            <p style="margin: 0.5rem 0 0 0; color: #155724;">
                For best results, upload <strong>.TXT files</strong> instead of PDFs. Text files provide 100% reliable content extraction and better question generation.
            </p>
        </div>
        
        <div style="background: #d1ecf1; border-left: 4px solid #17a2b8; padding: 1rem; margin-bottom: 2rem; border-radius: 4px;">
            <strong>File Format Guide:</strong>
            <ul style="margin: 0.5rem 0 0 1.5rem; color: #0c5460;">
                <li><strong>TXT files (.txt)</strong> - Best choice! 100% reliable extraction</li>
                <li><strong>PDF files (.pdf)</strong> - Supported, but results may vary</li>
                <li><strong>DOC/DOCX files</strong> - Save as TXT first for best results</li>
            </ul>
            <p style="margin: 0.8rem 0 0 0; color: #0c5460; font-size: 0.9rem;">
                <strong>Tip:</strong> Convert PDFs to TXT at <a href="https://www.ilovepdf.com/pdf_to_txt" target="_blank" style="color: #0c5460; text-decoration: underline;">ilovepdf.com</a> or use Word/Google Docs (Save As → Plain Text)
            </p>
        </div>
        
        <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 1rem; margin-bottom: 2rem; border-radius: 4px;">
            <strong>📝 How it works:</strong>
            <ul style="margin: 0.5rem 0 0 1.5rem; color: #856404;">
                <li>Upload your study material</li>
                <li>Set time limit (questions scale automatically: 1 question per 2 minutes)</li>
                <li>System extracts key topics and concepts from the document</li>
                <li>Generates relevant multiple-choice questions based on content</li>
            </ul>
        </div>
        
        <form action="<?php echo url('app/views/quiz/process_upload.php'); ?>" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="quiz_title">Quiz Title</label>
                <input type="text" id="quiz_title" name="quiz_title" required placeholder="e.g., BECE English">
            </div>
            
            <div class="form-group">
                <label for="resource_file">
                    Resource File 
                    <span style="background: #28a745; color: white; padding: 0.2rem 0.5rem; border-radius: 3px; font-size: 0.75rem; margin-left: 0.5rem;">TXT RECOMMENDED</span>
                </label>
                <input type="file" id="resource_file" name="resource_file" accept=".txt,.pdf,.doc,.docx" required>
                <small style="color: #666; display: block; margin-top: 0.5rem;">
                    <strong>Best:</strong> .TXT files | <strong>Supported:</strong> .PDF, .DOC, .DOCX
                </small>
                <small style="color: #28a745; display: block; margin-top: 0.3rem; font-weight: 600;">
                    For best question generation, use TXT format!
                </small>
            </div>
            
            <div class="form-group">
                <label for="time_limit">Time Limit (minutes)</label>
                <input type="number" id="time_limit" name="time_limit" min="1" max="120" value="10" required onchange="updateQuestionCount()">
                <small style="color: #666; display: block;">Set how long students have to complete the quiz</small>
                <small id="question_count" style="color: #28a745; display: block; margin-top: 0.3rem; font-weight: 600;">
                    📝 Will generate 5 questions (1 question per 2 minutes)
                </small>
            </div>
            
            <script>
            function updateQuestionCount() {
                const timeLimit = parseInt(document.getElementById('time_limit').value) || 10;
                const numQuestions = Math.max(5, Math.min(50, Math.ceil(timeLimit / 2)));
                document.getElementById('question_count').textContent = 
                    `📝 Will generate ${numQuestions} questions (1 question per 2 minutes)`;
            }
            </script>
            
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">✨ Generate Quiz</button>
                <a href="<?php echo url('app/views/quiz/list.php'); ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
