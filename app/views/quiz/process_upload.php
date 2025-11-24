<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/quiz_model.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('app/views/auth/login.php'));
    exit;
}

$title = $_POST['quiz_title'] ?? '';
$time_limit = intval($_POST['time_limit'] ?? 10);

if (empty($title) || $time_limit < 1) {
    $_SESSION['error'] = 'Please provide a valid title and time limit.';
    header('Location: ' . url('app/views/quiz/upload.php'));
    exit;
}

// Handle file upload using same function as resources
function uploadFile($file, $folder) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    
    // Current file is in: app/views/quiz/process_upload.php
    // We need to go up 3 levels to get to EduMart root, then into public
    $upload_dir = dirname(dirname(dirname(__DIR__))) . '/public/uploads/' . $folder . '/';
    
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $filename = uniqid() . '_' . basename($file['name']);
    $target = $upload_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $target)) {
        return 'uploads/' . $folder . '/' . $filename;
    }
    
    return null;
}

// Check if file is being received
if (!isset($_FILES['resource_file']) || $_FILES['resource_file']['error'] !== UPLOAD_ERR_OK) {
    $error_msg = 'Resource file is required.';
    if (isset($_FILES['resource_file'])) {
        switch ($_FILES['resource_file']['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $error_msg = 'File is too large. Maximum upload size: ' . ini_get('upload_max_filesize');
                break;
            case UPLOAD_ERR_PARTIAL:
                $error_msg = 'File was only partially uploaded. Please try again.';
                break;
            case UPLOAD_ERR_NO_FILE:
                $error_msg = 'No file was uploaded. Please select a file.';
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $error_msg = 'Missing temporary folder on server.';
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $error_msg = 'Failed to write file to disk.';
                break;
            default:
                $error_msg = 'File upload error (Code: ' . $_FILES['resource_file']['error'] . '). Please try again.';
        }
    }
    $_SESSION['error'] = $error_msg;
    header('Location: ' . url('app/views/quiz/upload.php'));
    exit;
}

// Validate file type
$allowed_types = ['application/pdf', 'text/plain', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
$file_type = $_FILES['resource_file']['type'];

if (!in_array($file_type, $allowed_types)) {
    $_SESSION['error'] = 'Only PDF, TXT, DOC, and DOCX files are allowed.';
    header('Location: ' . url('app/views/quiz/upload.php'));
    exit;
}

// Upload resource file
$file_path = uploadFile($_FILES['resource_file'], 'quiz_resources');

if (!$file_path) {
    $_SESSION['error'] = 'Failed to save resource file. Please try again.';
    header('Location: ' . url('app/views/quiz/upload.php'));
    exit;
}

// Create quiz in database
$quizModel = new quiz_model();
$filename = basename($_FILES['resource_file']['name']);
$quiz_id = $quizModel->createQuiz($_SESSION['user_id'], $title, $filename, $file_path, $time_limit);

if ($quiz_id) {
    // Generate AI questions (simulated for now)
    generateQuestions($quiz_id, $quizModel);
    
    $_SESSION['success'] = 'Quiz created successfully!';
    header('Location: ' . url('app/views/quiz/list.php'));
} else {
    $_SESSION['error'] = 'Failed to create quiz in database.';
    header('Location: ' . url('app/views/quiz/upload.php'));
}
exit;

// Function to generate sample questions
function generateQuestions($quiz_id, $quizModel) {
    // Simulated AI-generated questions
    // In production, this would call an AI API to analyze the uploaded resource
    $sample_questions = [
        [
            'question' => 'What is the main topic covered in this resource?',
            'options' => ['Introduction to Programming', 'Data Structures', 'Web Development', 'Database Management'],
            'correct' => 'A'
        ],
        [
            'question' => 'Which concept is emphasized in the first section?',
            'options' => ['Variables and Data Types', 'Functions', 'Loops', 'Classes'],
            'correct' => 'A'
        ],
        [
            'question' => 'What is the recommended approach for beginners?',
            'options' => ['Start with theory', 'Practice coding daily', 'Read documentation', 'Watch videos'],
            'correct' => 'B'
        ],
        [
            'question' => 'Which programming paradigm is discussed?',
            'options' => ['Functional', 'Object-Oriented', 'Procedural', 'All of the above'],
            'correct' => 'D'
        ],
        [
            'question' => 'What is the key takeaway from this resource?',
            'options' => ['Syntax is important', 'Practice makes perfect', 'Theory over practice', 'Speed over accuracy'],
            'correct' => 'B'
        ]
    ];
    
    foreach ($sample_questions as $q) {
        $quizModel->addQuestion(
            $quiz_id,
            $q['question'],
            $q['options'][0],
            $q['options'][1],
            $q['options'][2],
            $q['options'][3],
            $q['correct']
        );
    }
}
?>
