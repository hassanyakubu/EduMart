<?php
require_once __DIR__ . '/settings/db_cred.php';

echo "Testing database connection...\n";
echo "Host: " . SERVER . "\n";
echo "User: " . USERNAME . "\n";
echo "Database: " . DATABASE . "\n\n";

$conn = new mysqli(SERVER, USERNAME, PASSWD, DATABASE);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . "\n");
}

echo "✓ Connected successfully!\n";

// Check if purchases table exists
$result = $conn->query("DESCRIBE purchases");
if ($result) {
    echo "\n✓ Purchases table exists\n";
    echo "Columns:\n";
    while ($row = $result->fetch_assoc()) {
        echo "  - {$row['Field']} ({$row['Type']})\n";
    }
}

$conn->close();
?>
