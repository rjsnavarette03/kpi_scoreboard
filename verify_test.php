<?php
$hash = '$2y$10$V5FFaUtkgkDwfZfIDIS/JehXW4mfOiR29dMhNYowFarxyFhCO5je2';
if (password_verify('admin1234', $hash)) {
    echo "✅ Password matches!";
} else {
    echo "❌ Password invalid.";
}
?>