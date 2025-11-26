<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/quiz_model.php';

// Debug: Log that we reached this file
error_log("Quiz upload process started");

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log("Quiz upload: User not logged in or not POST request");
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

// Debug: Check what files were received
error_log("Quiz upload: FILES array: " . print_r($_FILES, true));
error_log("Quiz upload: POST array: " . print_r($_POST, true));

// Check if file is being received
if (!isset($_FILES['resource_file']) || $_FILES['resource_file']['error'] !== UPLOAD_ERR_OK) {
    $error_msg = 'Resource file is required.';
    error_log("Quiz upload: File error - " . ($_FILES['resource_file']['error'] ?? 'not set'));
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

// Validate file extension (more reliable than MIME type)
$file_extension = strtolower(pathinfo($_FILES['resource_file']['name'], PATHINFO_EXTENSION));
$allowed_extensions = ['pdf', 'txt', 'doc', 'docx'];

if (!in_array($file_extension, $allowed_extensions)) {
    $_SESSION['error'] = 'Only PDF, TXT, DOC, and DOCX files are allowed. You uploaded: .' . $file_extension;
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
$document_text = extractTextFromFile($full_file_path, $file_extension);

// Create quiz in database
$quizModel = new quiz_model();
$filename = basename($_FILES['resource_file']['name']);
$quiz_id = $quizModel->createQuiz($_SESSION['user_id'], $title, $filename, $file_path, $time_limit);

if ($quiz_id) {
    // Calculate number of questions based on time limit
    // Rule: 1 question per 2 minutes (minimum 5, maximum 50)
    $num_questions = max(5, min(50, ceil($time_limit / 2)));
    
    // Generate questions based on document content
    generateQuestionsFromDocument($quiz_id, $quizModel, $document_text, $title, $num_questions);
    
    // Set success message based on extraction result
    if ($document_text) {
        $_SESSION['success'] = "Quiz created successfully with {$num_questions} content-based questions!";
    } else {
        $_SESSION['success'] = "Quiz created with {$num_questions} general questions. Tip: Use TXT files for content-based questions!";
    }
    
    header('Location: ' . url('app/views/quiz/list.php'));
} else {
    $_SESSION['error'] = 'Failed to create quiz in database.';
    header('Location: ' . url('app/views/quiz/upload.php'));
}
exit;

// Function to extract text from uploaded file
function extractTextFromFile($file_path, $file_extension) {
    $text = '';
    
    error_log("Extracting text from: " . $file_path . " (type: " . $file_extension . ")");
    
    try {
        if ($file_extension === 'txt') {
            // Read plain text files - ALWAYS WORKS
            if (file_exists($file_path)) {
                $text = file_get_contents($file_path);
                error_log("TXT file read successfully. Length: " . strlen($text));
            } else {
                error_log("TXT file not found at: " . $file_path);
            }
        } 
        elseif ($file_extension === 'pdf') {
            error_log("Attempting PDF extraction");
            
            // Try multiple methods for PDF extraction
            
            // Method 1: Try pdftotext command (if available)
            if (function_exists('shell_exec') && !in_array('shell_exec', explode(',', ini_get('disable_functions')))) {
                $output = @shell_exec("pdftotext " . escapeshellarg($file_path) . " - 2>&1");
                if ($output && !stripos($output, 'command not found') && !stripos($output, 'not recognized')) {
                    $text = $output;
                    error_log("PDF extracted using pdftotext. Length: " . strlen($text));
                }
            }
            
            // Method 2: Basic PDF text extraction (fallback)
            if (empty($text)) {
                $text = extractPdfTextBasic($file_path);
                error_log("PDF extracted using basic method. Length: " . strlen($text));
            }
        }
        elseif ($file_extension === 'doc' || $file_extension === 'docx') {
            error_log("DOC/DOCX files require conversion to TXT for best results");
            // For DOC/DOCX, we recommend users convert to TXT first
            $text = '';
        }
        
        // Clean the text
        $text = trim($text);
        
        // Remove excessive whitespace
        $text = preg_replace('/\s+/', ' ', $text);
        
        // Limit text length but keep it reasonable for good question generation
        if (strlen($text) > 10000) {
            $text = substr($text, 0, 10000); // Limit to first 10000 characters
            error_log("Text truncated to 10000 characters");
        }
        
        error_log("Final extracted text length: " . strlen($text));
        
        // Log first 200 characters for debugging
        if (strlen($text) > 0) {
            error_log("Text preview: " . substr($text, 0, 200));
        } else {
            error_log("WARNING: No text extracted from file!");
        }
        
    } catch (Exception $e) {
        error_log("Exception during text extraction: " . $e->getMessage());
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
function generateQuestionsFromDocument($quiz_id, $quizModel, $document_text, $quiz_title, $num_questions = 5) {
    $questions = [];
    
    error_log("Generating {$num_questions} questions for quiz {$quiz_id}");
    error_log("Document text length: " . strlen($document_text));
    
    // If we have document text, try to generate content-based questions
    if (!empty($document_text) && strlen($document_text) > 100) {
        error_log("Generating content-based questions from document");
        
        // Split document into sentences
        $sentences = preg_split('/[.!?]+/', $document_text, -1, PREG_SPLIT_NO_EMPTY);
        $sentences = array_map('trim', $sentences);
        $sentences = array_filter($sentences, function($s) { return strlen($s) > 20; });
        
        // Extract key information from the document
        $words = str_word_count(strtolower($document_text), 1);
        $word_freq = array_count_values($words);
        arsort($word_freq);
        
        // Remove common words
        $common_words = ['the', 'a', 'an', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with', 'is', 'are', 'was', 'were', 'be', 'been', 'being', 'have', 'has', 'had', 'do', 'does', 'did', 'will', 'would', 'should', 'could', 'may', 'might', 'must', 'can', 'this', 'that', 'these', 'those', 'it', 'its', 'from', 'by', 'as', 'which', 'who', 'what', 'when', 'where', 'why', 'how'];
        foreach ($common_words as $common) {
            unset($word_freq[$common]);
        }
        
        // Get top keywords (important terms)
        $keywords = array_slice(array_keys($word_freq), 0, 20);
        error_log("Top keywords: " . implode(', ', array_slice($keywords, 0, 10)));
        
        // Extract sentences containing important keywords
        $important_sentences = [];
        foreach ($sentences as $sentence) {
            $sentence_lower = strtolower($sentence);
            foreach (array_slice($keywords, 0, 10) as $keyword) {
                if (stripos($sentence_lower, $keyword) !== false && strlen($sentence) > 30) {
                    $important_sentences[] = $sentence;
                    break;
                }
            }
        }
        
        // Generate questions from important sentences
        foreach (array_slice($important_sentences, 0, min(3, count($important_sentences))) as $sentence) {
            // Find the main keyword in this sentence
            $main_keyword = '';
            foreach ($keywords as $kw) {
                if (stripos($sentence, $kw) !== false) {
                    $main_keyword = $kw;
                    break;
                }
            }
            
            if ($main_keyword) {
                // Create a fill-in-the-blank style question
                $question_text = str_ireplace($main_keyword, '______', $sentence);
                $questions[] = [
                    'question' => 'Complete the statement: ' . $question_text,
                    'options' => [
                        ucfirst($main_keyword),
                        ucfirst($keywords[rand(1, min(5, count($keywords)-1))]),
                        ucfirst($keywords[rand(6, min(10, count($keywords)-1))]),
                        'None of the above'
                    ],
                    'correct' => 'A'
                ];
            }
        }
        
        // Generate keyword-based questions
        for ($i = 0; $i < min(3, count($keywords)); $i++) {
            $keyword = $keywords[$i];
            $questions[] = [
                'question' => 'Which of the following is a key concept discussed in the document?',
                'options' => [
                    ucfirst($keyword),
                    ucfirst($keywords[($i + 5) % count($keywords)]),
                    'Irrelevant topic',
                    'Unrelated subject'
                ],
                'correct' => 'A'
            ];
        }
        
        // Generate context-based questions
        $questions[] = [
            'question' => 'Based on the document "' . htmlspecialchars($quiz_title) . '", what is the primary focus?',
            'options' => [
                ucfirst($keywords[0] ?? 'Main topic') . ' and related concepts',
                'Unrelated general knowledge',
                'Historical events only',
                'Mathematical formulas only'
            ],
            'correct' => 'A'
        ];
        
        // Check for specific patterns in the text
        $has_definition = (stripos($document_text, 'define') !== false || stripos($document_text, 'definition') !== false || stripos($document_text, 'means') !== false);
        $has_example = (stripos($document_text, 'example') !== false || stripos($document_text, 'for instance') !== false || stripos($document_text, 'such as') !== false);
        $has_steps = (stripos($document_text, 'step') !== false || stripos($document_text, 'first') !== false || stripos($document_text, 'process') !== false);
        $has_comparison = (stripos($document_text, 'compare') !== false || stripos($document_text, 'difference') !== false || stripos($document_text, 'versus') !== false);
        $has_importance = (stripos($document_text, 'important') !== false || stripos($document_text, 'significant') !== false || stripos($document_text, 'crucial') !== false);
        
        if ($has_definition) {
            $questions[] = [
                'question' => 'According to the document, which statement about definitions is correct?',
                'options' => [
                    'The document provides clear definitions of key terms',
                    'No definitions are provided',
                    'Only mathematical formulas are defined',
                    'Definitions are intentionally omitted'
                ],
                'correct' => 'A'
            ];
        }
        
        if ($has_example) {
            $questions[] = [
                'question' => 'How does the document support its explanations?',
                'options' => [
                    'Through examples and practical illustrations',
                    'Only through abstract theory',
                    'Without any supporting evidence',
                    'Using only complex diagrams'
                ],
                'correct' => 'A'
            ];
        }
        
        if ($has_steps) {
            $questions[] = [
                'question' => 'What methodology does the document employ?',
                'options' => [
                    'A step-by-step or process-oriented approach',
                    'Random, unstructured presentation',
                    'Only final conclusions',
                    'No clear methodology'
                ],
                'correct' => 'A'
            ];
        }
        
        if ($has_comparison) {
            $questions[] = [
                'question' => 'What analytical technique is used in the document?',
                'options' => [
                    'Comparison and contrast of concepts',
                    'Only single-perspective analysis',
                    'No analytical techniques',
                    'Pure memorization approach'
                ],
                'correct' => 'A'
            ];
        }
        
        if ($has_importance) {
            $questions[] = [
                'question' => 'What does the document emphasize about the topic?',
                'options' => [
                    'The importance and significance of key concepts',
                    'That the topic is not important',
                    'Only trivial details',
                    'No particular emphasis'
                ],
                'correct' => 'A'
            ];
        }
        
        // Add topic-specific questions based on keywords
        if (count($keywords) >= 3) {
            $questions[] = [
                'question' => 'Which combination of topics is covered in the document?',
                'options' => [
                    ucfirst($keywords[0]) . ', ' . ucfirst($keywords[1]) . ', and ' . ucfirst($keywords[2]),
                    'Unrelated random topics',
                    'Only one narrow subject',
                    'No specific topics'
                ],
                'correct' => 'A'
            ];
        }
    } else {
        error_log("No document text available, using fallback questions");
    }
    
    // If we don't have enough questions, add general ones
    while (count($questions) < $num_questions) {
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
            if (count($questions) < $num_questions) {
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
        
        // If we still need more questions, repeat with variations
        if (count($questions) < $num_questions) {
            $variation_questions = [
                [
                    'question' => 'What prerequisite knowledge is needed for this topic?',
                    'options' => ['Basic understanding of fundamentals', 'No prerequisites', 'Advanced expertise', 'Unrelated knowledge'],
                    'correct' => 'A'
                ],
                [
                    'question' => 'How can you verify your understanding of this material?',
                    'options' => ['Practice problems and self-testing', 'Just reading once', 'Skipping exercises', 'Ignoring feedback'],
                    'correct' => 'A'
                ],
                [
                    'question' => 'What is the recommended study strategy?',
                    'options' => ['Consistent practice over time', 'Last-minute cramming', 'Passive reading only', 'Avoiding difficult sections'],
                    'correct' => 'A'
                ],
                [
                    'question' => 'How should you approach challenging concepts?',
                    'options' => ['Break them down into smaller parts', 'Skip them entirely', 'Memorize without understanding', 'Give up immediately'],
                    'correct' => 'A'
                ],
                [
                    'question' => 'What role does repetition play in learning?',
                    'options' => ['Reinforces understanding and retention', 'Wastes time', 'Causes confusion', 'Has no effect'],
                    'correct' => 'A'
                ]
            ];
            
            foreach ($variation_questions as $vq) {
                if (count($questions) < $num_questions) {
                    $questions[] = $vq;
                }
            }
        }
    }
    
    // Ensure we have exactly the requested number
    $questions = array_slice($questions, 0, $num_questions);
    
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
