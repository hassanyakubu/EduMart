<?php
// Diagnostic page to check upload settings
session_start();
require_once __DIR__ . '/../../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . url('app/views/auth/login.php'));
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Upload Diagnostics</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 2rem; max-width: 800px; margin: 0 auto; }
        .info { background: #e3f2fd; padding: 1rem; margin: 1rem 0; border-radius: 8px; }
        .warning { background: #fff3cd; padding: 1rem; margin: 1rem 0; border-radius: 8px; }
        .error { background: #f8d7da; padding: 1rem; margin: 1rem 0; border-radius: 8px; }
        .success { background: #d4edda; padding: 1rem; margin: 1rem 0; border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; margin: 1rem 0; }
        td { padding: 0.5rem; border: 1px solid #ddd; }
        td:first-child { font-weight: bold; width: 40%; }
    </style>
</head>
<body>
    <h1>📊 Upload Diagnostics</h1>
    
    <div class="info">
        <h3>PHP Upload Settings</h3>
        <table>
            <tr>
                <td>Max Upload Size</td>
                <td><?php echo ini_get('upload_max_filesize'); ?></td>
            </tr>
            <tr>
                <td>Max POST Size</td>
                <td><?php echo ini_get('post_max_size'); ?></td>
            </tr>
            <tr>
                <td>Max File Uploads</td>
                <td><?php echo ini_get('max_file_uploads'); ?></td>
            </tr>
            <tr>
                <td>Memory Limit</td>
                <td><?php echo ini_get('memory_limit'); ?></td>
            </tr>
            <tr>
                <td>Max Execution Time</td>
                <td><?php echo ini_get('max_execution_time'); ?> seconds</td>
            </tr>
        </table>
    </div>
    
    <div class="info">
        <h3>Upload Directory Status</h3>
        <?php
        $upload_dirs = [
            'Images' => __DIR__ . '/../../public/uploads/images/',
            'Files' => __DIR__ . '/../../public/uploads/files/'
        ];
        
        foreach ($upload_dirs as $name => $dir) {
            echo "<p><strong>$name Directory:</strong> ";
            if (is_dir($dir)) {
                echo "✅ Exists | ";
                echo is_writable($dir) ? "✅ Writable" : "❌ Not Writable";
            } else {
                echo "❌ Does not exist";
            }
            echo "<br><small>$dir</small></p>";
        }
        ?>
    </div>
    
    <div class="info">
        <h3>Session Information</h3>
        <table>
            <tr>
                <td>User ID</td>
                <td><?php echo $_SESSION['user_id'] ?? 'Not set'; ?></td>
            </tr>
            <tr>
                <td>User Name</td>
                <td><?php echo $_SESSION['user_name'] ?? 'Not set'; ?></td>
            </tr>
            <tr>
                <td>User Role</td>
                <td><?php echo $_SESSION['user_role'] ?? 'Not set'; ?></td>
            </tr>
            <tr>
                <td>User Type</td>
                <td><?php echo $_SESSION['user_type'] ?? 'Not set'; ?></td>
            </tr>
        </table>
    </div>
    
    <div class="warning">
        <h3>⚠️ Common Issues</h3>
        <ul>
            <li>If max upload size is too small (e.g., 2M), increase it in php.ini</li>
            <li>If directories are not writable, run: <code>chmod 777 public/uploads -R</code></li>
            <li>If POST size is smaller than upload size, increase post_max_size</li>
        </ul>
    </div>
    
    <div style="margin-top: 2rem;">
        <a href="<?php echo url('app/views/resources/upload.php'); ?>" style="padding: 1rem 2rem; background: #FFD947; color: #333; text-decoration: none; border-radius: 8px; display: inline-block;">
            ← Back to Upload
        </a>
    </div>
</body>
</html>
