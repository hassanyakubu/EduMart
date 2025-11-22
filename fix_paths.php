<?php
/**
 * Script to fix all absolute paths in view files
 * Run this once to update all paths to use the config helper functions
 */

$files_to_fix = [
    'app/views/profile/dashboard.php',
    'app/views/orders/invoice.php',
    'app/views/orders/list.php',
    'app/views/cart/view.php',
    'app/views/admin/dashboard.php',
    'app/views/checkout/success.php',
    'app/views/checkout/payment.php',
    'app/views/checkout/payment_simulated.php',
    'app/views/resources/details.php',
    'app/views/resources/list.php',
    'app/views/resources/upload.php'
];

$replacements = [
    // href patterns
    'href="/app/views/' => 'href="<?php echo url(\'app/views/',
    '"); ?>' => '\'); ?>',
    
    // action patterns
    'action="/app/views/' => 'action="<?php echo url(\'app/views/',
    
    // src patterns for images
    'src="/public/' => 'src="<?php echo asset(\''
];

foreach ($files_to_fix as $file) {
    if (!file_exists($file)) {
        echo "File not found: $file\n";
        continue;
    }
    
    $content = file_get_contents($file);
    $original = $content;
    
    // Fix href="/app/views/..." patterns
    $content = preg_replace(
        '/href="\/app\/views\/([^"]+)"/',
        'href="<?php echo url(\'app/views/$1\'); ?>"',
        $content
    );
    
    // Fix action="/app/views/..." patterns
    $content = preg_replace(
        '/action="\/app\/views\/([^"]+)"/',
        'action="<?php echo url(\'app/views/$1\'); ?>"',
        $content
    );
    
    // Fix src="/public/..." patterns
    $content = preg_replace(
        '/src="\/public\/([^"]+)"/',
        'src="<?php echo asset(\'$1\'); ?>"',
        $content
    );
    
    if ($content !== $original) {
        file_put_contents($file, $content);
        echo "Fixed: $file\n";
    } else {
        echo "No changes needed: $file\n";
    }
}

echo "\nDone! All paths have been updated.\n";
?>
