<?php
$pay_id = isset($_GET['pay']) ? $_GET['pay'] : 0;

if (!isset($_GET['success'])) {
    // Redirect first if not yet marked as success
    header("Location: payment.php?success&pay=$pay_id");
    exit();
} else {
    // Show message after redirect
    echo "Payment completed for request ID: $pay_id";
}
?>
