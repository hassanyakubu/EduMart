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
    
    public function createManualQuiz($user_id, $title, $category_id, $time_limit, $is_published = 0) {
        $stmt = $this->conn->prepare("INSERT INTO quizzes (user_id, quiz_title, category_id, time_limit, is_published) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isiii", $user_id, $title, $category_id, $time_limit, $is_published);
        
        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }
        return false;
    }
    
    public function getPublishedQuizzesByCategory($category_id) {
        $stmt = $this->conn->prepare("SELECT q.*, c.customer_name as creator_name, cat.cat_name as category_name 
                                       FROM quizzes q 
                                       JOIN customer c ON q.user_id = c.customer_id 
                                       JOIN categories cat ON q.category_id = cat.cat_id
                                       WHERE q.category_id = ? AND q.is_published = 1 
                                       ORDER BY q.created_at DESC");
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getQuizzesByCreator($user_id) {
        $stmt = $this->conn->prepare("SELECT q.*, cat.cat_name as category_name,
                                       (SELECT COUNT(*) FROM quiz_questions WHERE quiz_id = q.quiz_id) as question_count
                                       FROM quizzes q 
                                       LEFT JOIN categories cat ON q.category_id = cat.cat_id
                                       WHERE q.user_id = ? 
                                       ORDER BY q.created_at DESC");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    public function publishQuiz($quiz_id, $user_id) {
        $stmt = $this->conn->prepare("UPDATE quizzes SET is_published = 1 WHERE quiz_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $quiz_id, $user_id);
        return $stmt->execute();
    }
    
    public function unpublishQuiz($quiz_id, $user_id) {
        $stmt = $this->conn->prepare("UPDATE quizzes SET is_published = 0 WHERE quiz_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $quiz_id, $user_id);
        return $stmt->execute();
    }
    
    public function deleteQuiz($quiz_id, $user_id) {
        $stmt = $this->conn->prepare("DELETE FROM quizzes WHERE quiz_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $quiz_id, $user_id);
        return $stmt->execute();
    }
    
    public function getAllPublishedQuizzes() {
        $result = $this->conn->query("SELECT q.*, c.customer_name as creator_name, cat.cat_name as category_name 
                                       FROM quizzes q 
                                       JOIN customer c ON q.user_id = c.customer_id 
                                       LEFT JOIN categories cat ON q.category_id = cat.cat_id
                                       WHERE q.is_published = 1 
                                       ORDER BY q.created_at DESC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getQuizzesForStudent($user_id) {
        // Get quizzes for categories where student has purchased resources
        $stmt = $this->conn->prepare("SELECT DISTINCT q.*, c.customer_name as creator_name, cat.cat_name as category_name 
                                       FROM quizzes q 
                                       JOIN customer c ON q.user_id = c.customer_id 
                                       JOIN categories cat ON q.category_id = cat.cat_id
                                       WHERE q.is_published = 1 
                                       AND q.category_id IN (
                                           SELECT DISTINCT r.cat_id 
                                           FROM order_items oi
                                           JOIN resources r ON oi.resource_id = r.resource_id
                                           JOIN purchases p ON oi.purchase_id = p.purchase_id
                                           WHERE p.customer_id = ?
                                       )
                                       ORDER BY q.created_at DESC");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    public function canStudentTakeQuiz($user_id, $quiz_id) {
        // Check if student has purchased resources in the quiz's category
        $stmt = $this->conn->prepare("SELECT COUNT(*) as can_take
                                       FROM quizzes q
                                       WHERE q.quiz_id = ?
                                       AND q.category_id IN (
                                           SELECT DISTINCT r.cat_id 
                                           FROM order_items oi
                                           JOIN resources r ON oi.resource_id = r.resource_id
                                           JOIN purchases p ON oi.purchase_id = p.purchase_id
                                           WHERE p.customer_id = ?
                                       )");
        $stmt->bind_param("ii", $quiz_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['can_take'] > 0;
    }
    
    public function saveAnswer($attempt_id, $question_id, $user_answer, $is_correct) {
        $stmt = $this->conn->prepare("INSERT INTO quiz_answers (attempt_id, question_id, user_answer, is_correct) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iisi", $attempt_id, $question_id, $user_answer, $is_correct);
        return $stmt->execute();
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
        $stmt = $this->conn->prepare("SELECT qa.*, q.quiz_title, q.time_limit FROM quiz_attempts qa JOIN quizzes q ON qa.quiz_id = q.quiz_id WHERE qa.user_id = ? ORDER BY qa.started_at DESC");
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
