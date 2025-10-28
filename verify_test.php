<?php
$hash = '$2y$10$oYoPm0Tn716FTXYnaPGtMOaUpvR.yGEXOcdbtnHtW6HysHnGaTUsi';
if (password_verify('123456', $hash)) {
    echo "✅ Password matches!";
} else {
    echo "❌ Password invalid.";
}
?>