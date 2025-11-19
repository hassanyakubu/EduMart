<?php
/**
 * Server Test File
 * Use this to verify your server setup
 */
?>
<!DOCTYPE html>
<html>
<head>
    <title>EduMart Server Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f4f4f4; }
        .box { background: white; padding: 20px; margin: 10px 0; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .info { color: #007bff; }
        h1 { color: #333; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 4px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🧪 EduMart Server Test</h1>
    
    <div class="box">
        <h2>1. PHP Version</h2>
        <p class="success">✅ PHP Version: <?php echo phpversion(); ?></p>
    </div>
    
    <div class="box">
        <h2>2. Server Information</h2>
        <p><strong>Server:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></p>
        <p><strong>Host:</strong> <?php echo $_SERVER['HTTP_HOST'] ?? 'Unknown'; ?></p>
        <p><strong>Document Root:</strong> <?php echo $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown'; ?></p>
        <p><strong>Script Path:</strong> <?php echo __FILE__; ?></p>
    </div>
    
    <div class="box">
        <h2>3. Database Connection Test</h2>
        <?php
        if (file_exists('settings/db_cred.php')) {
            require_once 'settings/db_cred.php';
            
            $conn = @new mysqli(SERVER, USERNAME, PASSWD, DATABASE);
            
            if ($conn->connect_error) {
                echo '<p class="error">❌ Database Connection Failed: ' . $conn->connect_error . '</p>';
                echo '<p class="info">Check your credentials in settings/db_cred.php</p>';
            } else {
                echo '<p class="success">✅ Database Connected Successfully!</p>';
                echo '<p><strong>Server:</strong> ' . SERVER . '</p>';
                echo '<p><strong>Database:</strong> ' . DATABASE . '</p>';
                
                // Test query
                $result = $conn->query("SELECT COUNT(*) as count FROM customer");
                if ($result) {
                    $row = $result->fetch_assoc();
                    echo '<p class="success">✅ Found ' . $row['count'] . ' users in database</p>';
                }
                
                $conn->close();
            }
        } else {
            echo '<p class="error">❌ Database config file not found</p>';
        }
        ?>
    </div>
    
    <div class="box">
        <h2>4. File Permissions</h2>
        <?php
        $uploadDir = 'public/uploads/';
        if (is_dir($uploadDir)) {
            if (is_writable($uploadDir)) {
                echo '<p class="success">✅ Upload directory is writable</p>';
            } else {
                echo '<p class="error">❌ Upload directory is NOT writable</p>';
                echo '<p class="info">Run: chmod -R 777 public/uploads/</p>';
            }
        } else {
            echo '<p class="error">❌ Upload directory does not exist</p>';
            echo '<p class="info">Run: mkdir -p public/uploads/{images,files}</p>';
        }
        ?>
    </div>
    
    <div class="box">
        <h2>5. Required Files Check</h2>
        <?php
        $requiredFiles = [
            'app/config/database.php',
            'app/controllers/home_controller.php',
            'app/models/user_model.php',
            'app/views/home/index.php',
            'app/views/layouts/header.php',
            'public/assets/css/styles.css'
        ];
        
        foreach ($requiredFiles as $file) {
            if (file_exists($file)) {
                echo '<p class="success">✅ ' . $file . '</p>';
            } else {
                echo '<p class="error">❌ ' . $file . ' - NOT FOUND</p>';
            }
        }
        ?>
    </div>
    
    <div class="box">
        <h2>6. URL Configuration</h2>
        <?php
        $isServer = (strpos($_SERVER['HTTP_HOST'], '169.239.251.102') !== false);
        
        if ($isServer) {
            echo '<p class="success">✅ Running on SERVER</p>';
            echo '<p><strong>Base URL:</strong> http://169.239.251.102:442/~hassan.yakubu/EduMart</p>';
            echo '<p><strong>Homepage:</strong> <a href="http://169.239.251.102:442/~hassan.yakubu/EduMart/app/views/home/index.php">Click to test</a></p>';
        } else {
            echo '<p class="info">ℹ️ Running LOCALLY</p>';
            echo '<p><strong>Base URL:</strong> http://localhost:8000</p>';
            echo '<p><strong>Homepage:</strong> <a href="/app/views/home/index.php">Click to test</a></p>';
        }
        ?>
    </div>
    
    <div class="box">
        <h2>7. Next Steps</h2>
        <?php
        if ($isServer) {
            echo '<ol>';
            echo '<li>If all tests pass, go to: <a href="http://169.239.251.102:442/~hassan.yakubu/EduMart/app/views/home/index.php">Homepage</a></li>';
            echo '<li>Register an account: <a href="http://169.239.251.102:442/~hassan.yakubu/EduMart/app/views/auth/register.php">Register</a></li>';
            echo '<li>Login: <a href="http://169.239.251.102:442/~hassan.yakubu/EduMart/app/views/auth/login.php">Login</a></li>';
            echo '<li><strong>DELETE THIS FILE after testing!</strong></li>';
            echo '</ol>';
        } else {
            echo '<ol>';
            echo '<li>If all tests pass, go to: <a href="/app/views/home/index.php">Homepage</a></li>';
            echo '<li>Register an account: <a href="/app/views/auth/register.php">Register</a></li>';
            echo '<li>Login: <a href="/app/views/auth/login.php">Login</a></li>';
            echo '</ol>';
        }
        ?>
    </div>
    
    <div class="box">
        <p style="color: #666; font-size: 0.9em;">
            <strong>⚠️ IMPORTANT:</strong> Delete this test file (test_server.php) after verifying everything works!
        </p>
    </div>
</body>
</html>