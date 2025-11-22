
<?php 
session_start();

include "db.php";

if (!isset($_SESSION['email'])) {
    // Redirect to the login page if not logged in
    header("Location: index.php");
    exit();
}

if ($_SESSION['is_verified'] == 0 && $_SESSION['type'] == 'user') {
    header("Location: under_verification.php"); 
    exit();
}

if (isset($_GET['pickup'])) {
    $id = $_GET['pickup'];
} else {
   $id=$_GET['doortodoor'];
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $user_id = $_SESSION['id'];  // from session
    $doc_id = $id;                     // from GET
    $request_date = date("Y-m-d H:i:s");
    $status = 'pending';
    $delivery_mode = isset($_GET['pickup']) ? 'Pick-up' : 'Delivery';
    $shipping_fee = ($delivery_mode == 'Delivery') ? 30 : 0;
    $is_on_behalf = 0;
    $payment_status = 'Pending';
    $shipping_date = 'NULL';
    $arrival_date = 'NULL';

    $sql = "INSERT INTO requests 
        (user_id, doc_id, request_date, status, delivery_mode, shipping_fee, is_on_behalf, payment_status, shipping_date, arrival_date)
        VALUES
        ('$user_id', '$doc_id', '$request_date', '$status', '$delivery_mode', '$shipping_fee', '$is_on_behalf', '$payment_status', $shipping_date, $arrival_date)";

    if (mysqli_query($conn, $sql)) {
        // Redirect to payment page
        header('Location: payment.php?pay='. $doc_id);
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

if (!empty($_SESSION['fileupload'])) {
    foreach ($_SESSION['fileupload'] as $file) {

        $request_id = $file['request_id'];
        $req_id = $file['req_id'];
        $filename = mysqli_real_escape_string($conn, $file['file_name']); // safe for SQL

        // Insert into database
        $sql = "INSERT INTO uploaded_documents (request_id, req_id, filename)
                VALUES ('$request_id', '$req_id', '$filename')";

        mysqli_query($conn, $sql);
    }

    // Optionally clear the session once inserted
    unset($_SESSION['fileupload']);
}


mysqli_close($conn);


?>


