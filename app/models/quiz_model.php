<?php
require_once __DIR__ . '/../config/database.php';

class quiz_model {
    private $conn;
    
    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }
    
    public function createQuiz($user_id, $title, $filename, $filepath, $time_limit) {
        $stmt = $this->conn->prepare("INSERT INTO quizzes (user_id, quiz_title, resource_filename, resource_path, time_limit) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isssi", $user_id, $title, $filename, $filepath, $time_limit);
        
        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }
        return false;
    }
    
    public function addQuestion($quiz_id, $question_text, $option_a, $option_b, $option_c, $option_d, $correct_answer) {
        $stmt = $this->conn->prepare("INSERT INTO quiz_questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_answer) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssss", $quiz_id, $question_text, $option_a, $option_b, $option_c, $option_d, $correct_answer);
        return $stmt->execute();
    }
    
    public function getQuizById($quiz_id) {
        $stmt = $this->conn->prepare("SELECT * FROM quizzes WHERE quiz_id = ?");
        $stmt->bind_param("i", $quiz_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function getQuizQuestions($quiz_id) {
        $stmt = $this->conn->prepare("SELECT * FROM quiz_questions WHERE quiz_id = ? ORDER BY question_id");
        $stmt->bind_param("i", $quiz_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getAllQuizzes() {
        $result = $this->conn->query("SELECT q.*, c.customer_name as creator_name FROM quizzes q JOIN customer c ON q.user_id = c.customer_id ORDER BY q.created_at DESC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function createAttempt($quiz_id, $user_id, $score, $total_questions, $time_taken) {
        $stmt = $this->conn->prepare("INSERT INTO quiz_attempts (quiz_id, user_id, score, total_questions, time_taken) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiii", $quiz_id, $user_id, $score, $total_questions, $time_taken);
        
        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }
        return false;
    }
    
    public function saveAnswer($attempt_id, $question_id, $user_answer, $is_correct) {
        $stmt = $this->conn->prepare("INSERT INTO quiz_answers (attempt_id, question_id, user_answer, is_correct) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iisi", $attempt_id, $question_id, $user_answer, $is_correct);
        return $stmt->execute();
    }
    
    public function getUserAttempts($user_id) {
        $stmt = $this->conn->prepare("SELECT qa.*, q.quiz_title, q.time_limit FROM quiz_attempts qa JOIN quizzes q ON qa.quiz_id = q.quiz_id WHERE qa.user_id = ? ORDER BY qa.completed_at DESC");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getAttemptDetails($attempt_id) {
        $stmt = $this->conn->prepare("
            SELECT qa.*, qans.question_id, qans.user_answer, qans.is_correct, 
                   qq.question_text, qq.option_a, qq.option_b, qq.option_c, qq.option_d, qq.correct_answer
            FROM quiz_attempts qa
            JOIN quiz_answers qans ON qa.attempt_id = qans.attempt_id
            JOIN quiz_questions qq ON qans.question_id = qq.question_id
            WHERE qa.attempt_id = ?
            ORDER BY qq.question_id
        ");
        $stmt->bind_param("i", $attempt_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>
