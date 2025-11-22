<?php
// Simple upload test
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['testfile'])) {
    echo "<h2>Upload Test Results:</h2>";
    echo "<pre>";
    echo "File Info:\n";
    print_r($_FILES['testfile']);
    
    $upload_dir = __DIR__ . '/public/uploads/files/';
    echo "\nUpload Directory: $upload_dir\n";
    echo "Directory exists: " . (is_dir($upload_dir) ? 'YES' : 'NO') . "\n";
    echo "Directory writable: " . (is_writable($upload_dir) ? 'YES' : 'NO') . "\n";
    
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
        echo "Created directory\n";
    }
    
    if ($_FILES['testfile']['error'] === UPLOAD_ERR_OK) {
        $filename = 'test_' . time() . '_' . basename($_FILES['testfile']['name']);
        $target = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['testfile']['tmp_name'], $target)) {
            echo "\n✅ SUCCESS! File uploaded to: $target\n";
        } else {
            echo "\n❌ FAILED to move file\n";
        }
    } else {
        echo "\n❌ Upload error code: " . $_FILES['testfile']['error'] . "\n";
        echo "Error meanings:\n";
        echo "1 = File too large (exceeds upload_max_filesize)\n";
        echo "2 = File too large (exceeds form MAX_FILE_SIZE)\n";
        echo "3 = File only partially uploaded\n";
        echo "4 = No file uploaded\n";
        echo "6 = Missing temporary folder\n";
        echo "7 = Failed to write to disk\n";
    }
    echo "</pre>";
    echo '<br><a href="test_upload.php">Try Again</a>';
} else {
?>
<!DOCTYPE html>
<html>
<head>
    <title>Upload Test</title>
    <style>
        body { font-family: Arial; padding: 2rem; max-width: 600px; margin: 0 auto; }
        form { background: #f5f5f5; padding: 2rem; border-radius: 8px; }
        input[type="file"] { margin: 1rem 0; display: block; }
        button { background: #FFD947; border: none; padding: 1rem 2rem; border-radius: 8px; cursor: pointer; font-size: 1rem; }
        .info { background: #e3f2fd; padding: 1rem; margin: 1rem 0; border-radius: 8px; }
    </style>
</head>
<body>
    <h1>📤 Upload Test</h1>
    <form method="POST" enctype="multipart/form-data">
        <p>Select any file to test upload:</p>
        <input type="file" name="testfile" required>
        <button type="submit">Test Upload</button>
    </form>
    
    <div class="info">
        <h3>PHP Settings:</h3>
        <p><strong>Max Upload Size:</strong> <?php echo ini_get('upload_max_filesize'); ?></p>
        <p><strong>Max POST Size:</strong> <?php echo ini_get('post_max_size'); ?></p>
        <p><strong>Upload Directory:</strong> <?php echo __DIR__ . '/public/uploads/files/'; ?></p>
    </div>
</body>
</html>
<?php } ?>
