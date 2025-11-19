<?php
session_start();
require_once __DIR__ . '/../../controllers/admin_controller.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
    header('Location: /app/views/auth/login.php');
    exit;
}

$controller = new admin_controller();
$controller->settings();

$page_title = 'Settings';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <h1 style="margin: 2rem 0;">Platform Settings</h1>
    
    <div style="background: white; border-radius: 12px; padding: 2rem;">
        <form method="POST">
            <div class="form-group">
                <label for="site_name">Platform Name</label>
                <input type="text" id="site_name" name="site_name" value="EduMart">
            </div>
            
            <div class="form-group">
                <label for="logo">Platform Logo</label>
                <input type="file" id="logo" name="logo" accept="image/*">
            </div>
            
            <h3 style="margin: 2rem 0 1rem;">Payment Gateway Settings</h3>
            
            <div class="form-group">
                <label for="momo_api_key">MTN MoMo API Key</label>
                <input type="text" id="momo_api_key" name="momo_api_key" placeholder="Enter API Key">
            </div>
            
            <div class="form-group">
                <label for="vodafone_api_key">Vodafone Cash API Key</label>
                <input type="text" id="vodafone_api_key" name="vodafone_api_key" placeholder="Enter API Key">
            </div>
            
            <div class="form-group">
                <label for="airteltigo_api_key">AirtelTigo API Key</label>
                <input type="text" id="airteltigo_api_key" name="airteltigo_api_key" placeholder="Enter API Key">
            </div>
            
            <button type="submit" class="btn btn-primary">Save Settings</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
