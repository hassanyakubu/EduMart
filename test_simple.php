<?php
/**
 * Simple Server Test - No dependencies
 */
?>
<!DOCTYPE html>
<html>
<head>
    <title>EduMart Simple Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f4f4f4; }
        .box { background: white; padding: 20px; margin: 10px 0; border-radius: 8px; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
    </style>
</head>
<body>
    <h1>🧪 EduMart Simple Test</h1>
    
    <div class="box">
        <h2>1. PHP is Working</h2>
        <p class="success">✅ PHP Version: <?php echo phpversion(); ?></p>
        <p class="success">✅ This page loaded successfully!</p>
    </div>
    
    <div class="box">
        <h2>2. Server Info</h2>
        <p><strong>Host:</strong> <?php echo $_SERVER['HTTP_HOST']; ?></p>
        <p><strong>Script:</strong> <?php echo $_SERVER['SCRIPT_NAME']; ?></p>
        <p><strong>Document Root:</strong> <?php echo $_SERVER['DOCUMENT_ROOT']; ?></p>
    </div>
    
    <div class="box">
        <h2>3. File Check</h2>
        <?php
        $files = [
            'index.php' => file_exists('index.php'),
            'app/config/database.php' => file_exists('app/config/database.php'),
            'settings/db_cred.php' => file_exists('settings/db_cred.php'),
            'app/views/home/index.php' => file_exists('app/views/home/index.php')
        ];
        
        foreach ($files as $file => $exists) {
            if ($exists) {
                echo '<p class="success">✅ ' . $file . '</p>';
            } else {
                echo '<p class="error">❌ ' . $file . ' - NOT FOUND</p>';
            }
        }
        ?>
    </div>
    
    <div class="box">
        <h2>4. Database Test</h2>
        <?php
        if (file_exists('settings/db_cred.php')) {
            echo '<p class="success">✅ Database config file exists</p>';
            
            // Try to load it
            try {
                require_once 'settings/db_cred.php';
                echo '<p class="success">✅ Database config loaded</p>';
                
                // Try to connect
                $conn = @new mysqli(SERVER, USERNAME, PASSWD, DATABASE);
                
                if ($conn->connect_error) {
                    echo '<p class="error">❌ Connection failed: ' . htmlspecialchars($conn->connect_error) . '</p>';
                } else {
                    echo '<p class="success">✅ Database connected!</p>';
                    echo '<p>Database: ' . DATABASE . '</p>';
                    $conn->close();
                }
            } catch (Exception $e) {
                echo '<p class="error">❌ Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
            }
        } else {
            echo '<p class="error">❌ Database config not found</p>';
        }
        ?>
    </div>
    
    <div class="box">
        <h2>5. Next Steps</h2>
        <p>If all tests pass, try these links:</p>
        <ul>
            <li><a href="app/views/home/index.php">Homepage</a></li>
            <li><a href="app/views/auth/login.php">Login</a></li>
            <li><a href="app/views/auth/register.php">Register</a></li>
        </ul>
        <p style="color: #dc3545; font-weight: bold;">⚠️ DELETE THIS FILE after testing!</p>
    </div>
</body>
</html>
