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

// Get the full file path for text extraction
$full_file_path = dirname(dirname(dirname(__DIR__))) . '/public/' . $file_path;

// Extract text from the uploaded document
$document_text = extractTextFromFile($full_file_path, $_FILES['resource_file']['type']);

// Create quiz in database
$quizModel = new quiz_model();
$filename = basename($_FILES['resource_file']['name']);
$quiz_id = $quizModel->createQuiz($_SESSION['user_id'], $title, $filename, $file_path, $time_limit);

if ($quiz_id) {
    // Generate questions based on document content
    generateQuestionsFromDocument($quiz_id, $quizModel, $document_text, $title);
    
    // Set success message based on extraction result
    if ($document_text) {
        $_SESSION['success'] = 'Quiz created successfully with content-based questions! 🎉';
    } else {
        $_SESSION['success'] = 'Quiz created with general questions. 💡 Tip: Use TXT files for content-based questions!';
    }
    
    header('Location: ' . url('app/views/quiz/list.php'));
} else {
    $_SESSION['error'] = 'Failed to create quiz in database.';
    header('Location: ' . url('app/views/quiz/upload.php'));
}
exit;

// Function to extract text from uploaded file
function extractTextFromFile($file_path, $mime_type) {
    $text = '';
    
    try {
        if ($mime_type === 'text/plain') {
            // Read plain text files - ALWAYS WORKS
            $text = file_get_contents($file_path);
        } 
        elseif ($mime_type === 'application/pdf') {
            // Try multiple methods for PDF extraction
            
            // Method 1: Try pdftotext command (if available)
            if (function_exists('shell_exec') && !in_array('shell_exec', explode(',', ini_get('disable_functions')))) {
                $output = @shell_exec("pdftotext " . escapeshellarg($file_path) . " - 2>&1");
                if ($output && !stripos($output, 'command not found') && !stripos($output, 'not recognized')) {
                    $text = $output;
                }
            }
            
            // Method 2: Basic PDF text extraction (fallback)
            if (empty($text)) {
                $text = extractPdfTextBasic($file_path);
            }
        }
        
        // Clean and limit text
        $text = trim($text);
        if (strlen($text) > 5000) {
            $text = substr($text, 0, 5000); // Limit to first 5000 characters
        }
        
    } catch (Exception $e) {
        // If extraction fails, return empty string (will use fallback questions)
        $text = '';
    }
    
    return $text;
}

// Basic PDF text extraction without external dependencies
function extractPdfTextBasic($file_path) {
    $text = '';
    
    try {
        // Read PDF file
        $content = file_get_contents($file_path);
        
        if ($content) {
            // Very basic text extraction from PDF
            // This extracts text between parentheses and brackets in PDF structure
            
            // Method 1: Extract text from PDF streams
            if (preg_match_all('/\(([^)]+)\)/i', $content, $matches)) {
                $text = implode(' ', $matches[1]);
            }
            
            // Method 2: Try to find readable text
            if (empty($text)) {
                // Remove binary data and extract readable text
                $content = preg_replace('/[^\x20-\x7E\n\r\t]/i', '', $content);
                $text = $content;
            }
            
            // Clean up the extracted text
            $text = str_replace(['\\n', '\\r', '\\t'], ["\n", "\r", "\t"], $text);
            $text = preg_replace('/\s+/', ' ', $text);
            $text = trim($text);
        }
    } catch (Exception $e) {
        $text = '';
    }
    
    return $text;
}

// Function to generate questions from document content
function generateQuestionsFromDocument($quiz_id, $quizModel, $document_text, $quiz_title) {
    $questions = [];
    
    // If we have document text, try to generate content-based questions
    if (!empty($document_text) && strlen($document_text) > 100) {
        // Extract key information from the document
        $words = str_word_count(strtolower($document_text), 1);
        $word_freq = array_count_values($words);
        arsort($word_freq);
        
        // Remove common words
        $common_words = ['the', 'a', 'an', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with', 'is', 'are', 'was', 'were', 'be', 'been', 'being', 'have', 'has', 'had', 'do', 'does', 'did', 'will', 'would', 'should', 'could', 'may', 'might', 'must', 'can', 'this', 'that', 'these', 'those', 'it', 'its', 'from', 'by', 'as'];
        foreach ($common_words as $common) {
            unset($word_freq[$common]);
        }
        
        // Get top keywords
        $keywords = array_slice(array_keys($word_freq), 0, 10);
        
        // Generate questions based on content
        $questions[] = [
            'question' => 'Based on the document, which of the following topics is most emphasized?',
            'options' => [
                ucfirst($keywords[0] ?? 'General concepts'),
                ucfirst($keywords[1] ?? 'Basic principles'),
                ucfirst($keywords[2] ?? 'Advanced topics'),
                'None of the above'
            ],
            'correct' => 'A'
        ];
        
        $questions[] = [
            'question' => 'What is the main subject area covered in "' . htmlspecialchars($quiz_title) . '"?',
            'options' => [
                ucfirst($keywords[0] ?? 'Primary topic'),
                'Unrelated subject',
                'General knowledge',
                'Historical facts'
            ],
            'correct' => 'A'
        ];
        
        // Check for specific patterns in the text
        $has_definition = (stripos($document_text, 'define') !== false || stripos($document_text, 'definition') !== false);
        $has_example = (stripos($document_text, 'example') !== false || stripos($document_text, 'for instance') !== false);
        $has_steps = (stripos($document_text, 'step') !== false || stripos($document_text, 'first') !== false);
        
        if ($has_definition) {
            $questions[] = [
                'question' => 'The document provides definitions for which of the following?',
                'options' => [
                    'Key terms and concepts',
                    'Mathematical formulas only',
                    'Historical dates',
                    'None of the above'
                ],
                'correct' => 'A'
            ];
        }
        
        if ($has_example) {
            $questions[] = [
                'question' => 'How does the document illustrate its concepts?',
                'options' => [
                    'Through examples and illustrations',
                    'Only through theory',
                    'Without any explanations',
                    'Using only diagrams'
                ],
                'correct' => 'A'
            ];
        }
        
        if ($has_steps) {
            $questions[] = [
                'question' => 'What approach does the document use to explain the topic?',
                'options' => [
                    'Step-by-step methodology',
                    'Random order presentation',
                    'Only conclusions',
                    'No structured approach'
                ],
                'correct' => 'A'
            ];
        }
    }
    
    // If we don't have enough questions, add general ones
    while (count($questions) < 5) {
        $general_questions = [
            [
                'question' => 'What is the primary purpose of studying this material?',
                'options' => [
                    'To understand the core concepts',
                    'To memorize facts only',
                    'To pass time',
                    'No specific purpose'
                ],
                'correct' => 'A'
            ],
            [
                'question' => 'Which learning approach is most effective for this topic?',
                'options' => [
                    'Active reading and practice',
                    'Passive listening only',
                    'Ignoring the material',
                    'Skipping difficult parts'
                ],
                'correct' => 'A'
            ],
            [
                'question' => 'How should you apply the knowledge from this resource?',
                'options' => [
                    'Practice and real-world application',
                    'Just read once',
                    'Forget after exam',
                    'Share without understanding'
                ],
                'correct' => 'A'
            ],
            [
                'question' => 'What is the best way to retain information from this material?',
                'options' => [
                    'Regular review and practice',
                    'One-time reading',
                    'Cramming before exam',
                    'Not reviewing at all'
                ],
                'correct' => 'A'
            ],
            [
                'question' => 'Why is understanding this topic important?',
                'options' => [
                    'It builds foundational knowledge',
                    'It is not important',
                    'Only for exam purposes',
                    'No particular reason'
                ],
                'correct' => 'A'
            ]
        ];
        
        // Add questions that haven't been added yet
        foreach ($general_questions as $gq) {
            if (count($questions) < 5) {
                $already_exists = false;
                foreach ($questions as $existing) {
                    if ($existing['question'] === $gq['question']) {
                        $already_exists = true;
                        break;
                    }
                }
                if (!$already_exists) {
                    $questions[] = $gq;
                }
            }
        }
    }
    
    // Limit to 5 questions
    $questions = array_slice($questions, 0, 5);
    
    // Save questions to database
    foreach ($questions as $q) {
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
