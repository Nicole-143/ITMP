<?php
include 'db.php';

// Validate ID exists and is a positive integer
if (!isset($_POST['request_id']) || empty($_POST['request_id'])) {
    header("Location: document_processing.php?error=invalid_id");
    exit();
}

$id = $_POST['request_id'];
if ($id === false || $id <= 0) {
    header("Location: document_processing.php?error=invalid_id");
    exit();
}

$sql = "UPDATE Requests SET status='Ready for Shipping' WHERE request_id=?";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: document.php");
        exit();
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo "Error preparing statement: " . mysqli_error($conn);
}

mysqli_close($conn);
?>