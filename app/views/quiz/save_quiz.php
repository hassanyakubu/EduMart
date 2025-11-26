<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/quiz_model.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
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

$quiz_title = $_POST['quiz_title'] ?? '';
$category_id = $_POST['category_id'] ?? 0;
$time_limit = intval($_POST['time_limit'] ?? 30);
$questions = $_POST['questions'] ?? [];
$action = $_POST['action'] ?? 'draft';

// Validate
if (empty($quiz_title) || empty($category_id) || empty($questions)) {
    $_SESSION['error'] = 'Please fill in all required fields and add at least one question.';
    header('Location: ' . url('app/views/quiz/create.php'));
    exit;
}

$quizModel = new quiz_model();

// Create quiz
$is_published = ($action === 'publish') ? 1 : 0;
$quiz_id = $quizModel->createManualQuiz($_SESSION['user_id'], $quiz_title, $category_id, $time_limit, $is_published);

if ($quiz_id) {
    // Add questions
    foreach ($questions as $q) {
        if (!empty($q['text']) && !empty($q['option_a']) && !empty($q['option_b']) && 
            !empty($q['option_c']) && !empty($q['option_d']) && !empty($q['correct'])) {
            
            $quizModel->addQuestion(
                $quiz_id,
                $q['text'],
                $q['option_a'],
                $q['option_b'],
                $q['option_c'],
                $q['option_d'],
                $q['correct']
            );
        }
    }
    
    if ($is_published) {
        $_SESSION['success'] = 'Quiz published successfully! Students can now take it.';
    } else {
        $_SESSION['success'] = 'Quiz saved as draft. Publish it when ready.';
    }
    
    header('Location: ' . url('app/views/quiz/my_quizzes.php'));
} else {
    $_SESSION['error'] = 'Failed to create quiz. Please try again.';
    header('Location: ' . url('app/views/quiz/create.php'));
}
exit;
?>
