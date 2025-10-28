<?php
// Simple helper to generate password hash. Upload, open in browser, then delete for security.
if (!isset($_GET['pw'])) {
    echo "Usage: generate_hash.php?pw=yourpassword";
    exit;
}
$pw = $_GET['admin1234'];
echo password_hash($pw, PASSWORD_DEFAULT);
?>