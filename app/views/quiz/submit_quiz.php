<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/quiz_model.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('app/views/auth/login.php'));
    exit;
}

$quiz_id = $_POST['quiz_id'] ?? 0;
$answers = $_POST['answers'] ?? [];
$time_taken = $_POST['time_taken'] ?? 0;

$quizModel = new quiz_model();
$quiz = $quizModel->getQuizById($quiz_id);
$questions = $quizModel->getQuizQuestions($quiz_id);

if (!$quiz || empty($questions)) {
    $_SESSION['error'] = 'Invalid quiz.';
    header('Location: ' . url('app/views/quiz/list.php'));
    exit;
}

// Calculate score
$score = 0;
$results = [];

foreach ($questions as $question) {
    $user_answer = $answers[$question['question_id']] ?? null;
    $is_correct = ($user_answer === $question['correct_answer']);
    
    if ($is_correct) {
        $score++;
    }
    
    $results[] = [
        'question_id' => $question['question_id'],
        'question_text' => $question['question_text'],
        'option_a' => $question['option_a'],
        'option_b' => $question['option_b'],
        'option_c' => $question['option_c'],
        'option_d' => $question['option_d'],
        'user_answer' => $user_answer,
        'correct_answer' => $question['correct_answer'],
        'is_correct' => $is_correct
    ];
}

$total_questions = count($questions);

// Save attempt
$attempt_id = $quizModel->createAttempt($quiz_id, $_SESSION['user_id'], $score, $total_questions, $time_taken);

if ($attempt_id) {
    // Save individual answers
    foreach ($results as $result) {
        $quizModel->saveAnswer(
            $attempt_id,
            $result['question_id'],
            $result['user_answer'],
            $result['is_correct'] ? 1 : 0
        );
    }
    
    // Store results in session for display
    $_SESSION['quiz_results'] = [
        'quiz_title' => $quiz['quiz_title'],
        'score' => $score,
        'total' => $total_questions,
        'percentage' => round(($score / $total_questions) * 100, 1),
        'time_taken' => $time_taken,
        'results' => $results
    ];
    
    header('Location: ' . url('app/views/quiz/show_results.php'));
} else {
    $_SESSION['error'] = 'Failed to save quiz results.';
    header('Location: ' . url('app/views/quiz/list.php'));
}
exit;
?>
