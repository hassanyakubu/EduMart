<?php
session_start();
require_once __DIR__ . '/../models/quiz_model.php';

class quiz_controller {
    private $quizModel;
    
    public function __construct() {
        $this->quizModel = new quiz_model();
    }
    
    public function uploadForm() {
        if (!isset($_SESSION['user_id'])) {
            require_once __DIR__ . '/../config/config.php';
            header('Location: ' . url('app/views/auth/login.php'));
            exit;
        }
        
        require_once __DIR__ . '/../views/quiz/upload.php';
    }
    
    // Upload processing is now handled in app/views/quiz/process_upload.php
    // This matches the pattern used in resources upload
    
    public function listQuizzes() {
        if (!isset($_SESSION['user_id'])) {
            require_once __DIR__ . '/../config/config.php';
            header('Location: ' . url('app/views/auth/login.php'));
            exit;
        }
        
        $quizzes = $this->quizModel->getAllQuizzes();
        require_once __DIR__ . '/../views/quiz/list.php';
    }
    
    public function takeQuiz() {
        if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
            require_once __DIR__ . '/../config/config.php';
            header('Location: ' . url('app/views/quiz/list.php'));
            exit;
        }
        
        $quiz_id = intval($_GET['id']);
        $quiz = $this->quizModel->getQuizById($quiz_id);
        $questions = $this->quizModel->getQuizQuestions($quiz_id);
        
        if (!$quiz || empty($questions)) {
            $_SESSION['error'] = 'Quiz not found.';
            require_once __DIR__ . '/../config/config.php';
            header('Location: ' . url('app/views/quiz/list.php'));
            exit;
        }
        
        require_once __DIR__ . '/../views/quiz/take.php';
    }
    
    public function submitQuiz() {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            require_once __DIR__ . '/../config/config.php';
            header('Location: ' . url('app/views/quiz/list.php'));
            exit;
        }
        
        $quiz_id = intval($_POST['quiz_id'] ?? 0);
        $time_taken = intval($_POST['time_taken'] ?? 0);
        
        $questions = $this->quizModel->getQuizQuestions($quiz_id);
        $score = 0;
        
        foreach ($questions as $question) {
            $user_answer = $_POST['question_' . $question['question_id']] ?? '';
            if ($user_answer === $question['correct_answer']) {
                $score++;
            }
        }
        
        $attempt_id = $this->quizModel->createAttempt($quiz_id, $_SESSION['user_id'], $score, count($questions), $time_taken);
        
        if ($attempt_id) {
            foreach ($questions as $question) {
                $user_answer = $_POST['question_' . $question['question_id']] ?? '';
                $is_correct = ($user_answer === $question['correct_answer']) ? 1 : 0;
                $this->quizModel->saveAnswer($attempt_id, $question['question_id'], $user_answer, $is_correct);
            }
        }
        
        require_once __DIR__ . '/../config/config.php';
        header('Location: ' . url('app/views/quiz/results.php?attempt_id=' . $attempt_id));
        exit;
    }
    
    public function viewResults() {
        if (!isset($_SESSION['user_id']) || !isset($_GET['attempt_id'])) {
            require_once __DIR__ . '/../config/config.php';
            header('Location: ' . url('app/views/quiz/list.php'));
            exit;
        }
        
        $attempt_id = intval($_GET['attempt_id']);
        $results = $this->quizModel->getAttemptDetails($attempt_id);
        
        if (empty($results)) {
            $_SESSION['error'] = 'Results not found.';
            require_once __DIR__ . '/../config/config.php';
            header('Location: ' . url('app/views/quiz/list.php'));
            exit;
        }
        
        require_once __DIR__ . '/../views/quiz/results.php';
    }
}
?>
