<?php
session_start();


include "db.php";

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

// Always define $id (doc_id from URL)
$id = isset($_GET['pay']) ? intval($_GET['pay']) : 0;

if (isset($_GET['process'])) {

    if ($id <= 0) {
        die("Missing or invalid document ID (pay parameter).");
    }

    if (!isset($_SESSION['id'])) {
        die("User not logged in properly (missing session id).");
    }

    if (!isset($_SESSION['delivery_mode']) || !isset($_SESSION['copies'])) {
        die("Missing delivery_mode or copies in session.");
    }

    $user_id      = $_SESSION['id'];
    $doc_id       = $id;
    $request_date = date("Y-m-d H:i:s");
    $status       = 'Pending';

    $delivery_mode = ($_SESSION['delivery_mode'] == 'Pick-up') ? 'Pick-up' : 'Delivery';
    $copies        = (int) $_SESSION['copies'];

    // Payment mode: may not be set for some flows
    $payment_mode = $_SESSION['payment_mode'] ?? null;  // NULL is allowed in DB

    $is_on_behalf = !empty($_SESSION['on_behalf']) ? 1 : 0;

    if ($_SESSION['on_behalf']==1){
        $represented_surname = $_SESSION['represented_surname'];
        $represented_givenname = $_SESSION['represented_givenname'];
        $represented_middlename = $_SESSION['represented_middlename'];
        $represented_birthdate = $_SESSION['represented_birthdate'];
        $represented_relationship = $_SESSION['relationship'];
    } else {
    // If not on behalf, set to NULL
    $represented_surname = $represented_givenname = $represented_middlename = $represented_birthdate = $represented_relationship = NULL;
    }

    // Payment status logic
    if ($delivery_mode == 'Pick-up' || $payment_mode == 'Cash_On_Delivery') {
        $payment_status = 'Pending';
    } else {
        $payment_status = 'Paid';
    }

    // Build proper SQL (shipping_date & arrival_date use NULL)
    $payment_mode_sql = $payment_mode ? "'" . $conn->real_escape_string($payment_mode) . "'" : "NULL";

    $sql = "
        INSERT INTO Requests 
        (user_id, doc_id, request_date, status, delivery_mode, payment_mode, copies, is_on_behalf, represented_surname, represented_givenname, represented_middlename, 
     represented_birthdate, represented_relationship, payment_status, shipping_date, arrival_date)
        VALUES
        ('$user_id', '$doc_id', '$request_date', '$status', '$delivery_mode', $payment_mode_sql, '$copies', '$is_on_behalf', '$represented_surname', '$represented_givenname', 
     '$represented_middlename', '$represented_birthdate', '$represented_relationship', '$payment_status', NULL, NULL)
    ";

    if (!mysqli_query($conn, $sql)) {
        die('Insert into Requests failed: ' . mysqli_error($conn));
    }

    // NEW request id
    $new_request_id = mysqli_insert_id($conn);

    // ---------- PAYMENTS ----------
    $amount           = ($_SESSION['total'] ?? 0) + ($_SESSION['shipping_fee'] ?? 0);
    $payment_date     = date("Y-m-d H:i:s");
    $transaction_type = 'Request Payment';

    $sql_payment = "
        INSERT INTO Payments (user_id, amount, payment_date, request_id, transaction_type)
        VALUES ('$user_id', '$amount', '$payment_date', '$new_request_id', '$transaction_type')
    ";

    if (!mysqli_query($conn, $sql_payment)) {
        die('Insert into Payments failed: ' . mysqli_error($conn));
    }

    // ---------- UPLOADED DOCUMENTS ----------
    // Files are already moved in upload_document.php; just store paths
    if (!empty($_SESSION['fileupload'])) {
        foreach ($_SESSION['fileupload'] as $file) {
            $req_id    = $file['req_id'];
            $file_path = $conn->real_escape_string($file['file_path']);

            $sql2 = "
                INSERT INTO Uploaded_Documents (request_id, req_id, file_path)
                VALUES ('$new_request_id', '$req_id', '$file_path')
            ";

            if (!mysqli_query($conn, $sql2)) {
                die('Insert into Uploaded_Documents failed: ' . mysqli_error($conn));
            }
        }
    }

    // Optional: clear fileupload session so refresh doesn't duplicate
    unset($_SESSION['fileupload']);

    // If everything is OK, show success page
    header("Location: request.php?success");
    exit();
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Townsville Barangay System</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
</head>
<body>
    <header class="main-header">
        <div class="logo">
            <img src="./images/logo.png">
            <div>
                <h4>BRGY. Townsville</h4>
                <h4>Document Request</h4>
            </div>
        </div>
        <nav class="navigation-menu">
            <ul>
                <li>About</li>
                <li>FAQS</li>
                <li><a href="dashboard.php">Home</a></li>
                <li><a href="track_request.php">Track Requests</a></li>
                <li><a href="logout.php" onclick="">Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="main-page success">
        <div class="form-box success-box valid-id">
            <?php
            if (isset($_GET['success'])) {

                $keep = [
                    'id'          => $_SESSION['id'] ?? null,
                    'email'       => $_SESSION['email'] ?? null,
                    'givenname'   => $_SESSION['givenname'] ?? null,
                    'type'        => $_SESSION['type'] ?? null,
                    'is_verified' => $_SESSION['is_verified'] ?? null,
                    'comment'     => $_SESSION['comment'] ?? null
                ];

                session_unset();

                foreach ($keep as $key => $value) {
                    $_SESSION[$key] = $value;
                }

                echo '
                <div class="brgy-logo">
                    <img src="./images/success.png">
                </div> 
                <h1>Order Complete!</h1>
                <p class="check-message">Feel free to check the progress of your documents anytime on the Track Request page.</p>
                <a class="request-btn" href="dashboard.php">Done</a>
                ';
            }
            ?>
        </div>
    </div>
</body>
</html>
