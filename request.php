<?php

session_start();
include "db.php";

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

if (isset($_GET['pay'])) {
    $id = $_GET['pay'];
    
} 

if (isset($_GET['process'])) {
    $user_id=$_SESSION['id'];
    $doc_id=$id;
    $request_date = date("Y-m-d H:i:s");
    $status = 'Pending';
    $delivery_mode = ($_SESSION['delivery_mode']=='Pick-up') ? 'Pick-up' : 'Delivery';
    $copies=$_SESSION['copies'];
    $payment_mode = 'NULL';
    
    if (($_SESSION['payment_mode']=='Cash_On_Delivery')){
        $payment_mode = 'Cash_On_Delivery';
    }else if ($_SESSION['payment_mode']=='Wallet'){
        $payment_mode = 'Wallet';;
    }
    
    $is_on_behalf = $_SESSION['on_behalf']? 1 : 0;

    if (($_SESSION['delivery_mode']=='Pick-up')|| ($_SESSION['payment_mode']=='Cash_On_Delivery'))
    {
        $payment_status = 'Pending';
    }
    else{
        $payment_status = 'Paid';
    }
    
    $shipping_date = 'NULL';
    $arrival_date = 'NULL';

    $sql = "INSERT INTO requests 
        (user_id, doc_id, request_date, status, delivery_mode, payment_mode, copies, is_on_behalf, payment_status, shipping_date, arrival_date)
        VALUES
        ('$user_id', '$doc_id', '$request_date', '$status', '$delivery_mode', '$payment_mode', '$copies', '$is_on_behalf', '$payment_status', $shipping_date, $arrival_date)";

    if (mysqli_query($conn, $sql)) {

        //upload files
        $new_request_id = mysqli_insert_id($conn);
        
        $amount = $_SESSION['total'] + $_SESSION['shipping_fee']; // total + shipping
        $payment_date = date("Y-m-d H:i:s");
        $transaction_type = 'Request Payment';
        
        $sql_payment = "INSERT INTO payments (user_id, amount, payment_date, request_id, transaction_type)
                    VALUES ('$user_id', '$amount', '$payment_date', '$new_request_id', '$transaction_type')";
    
        mysqli_query($conn, $sql_payment);

        // Insert uploaded documents
        if (!empty($_SESSION['fileupload'])) {
            foreach ($_SESSION['fileupload'] as $file) {

                $req_id = $file['req_id'];
                $filename = $file['file_name'];
                $tmp = $file['tmp_name'];

                // Move file to uploads folder
                $destination = "documents/" . $filename;
                move_uploaded_file($tmp, $destination);

                // Insert into table
                $sql2 = "INSERT INTO uploaded_documents (request_id, req_id, filename)
                         VALUES ('$new_request_id', '$req_id', '$filename')";
                mysqli_query($conn, $sql2);
            }
        }
        // Redirect to payment page

        header("Location: request.php?success");
        exit();
        
    }
}
    


mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Under Verification</title>
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

            

            // If account is verified
            if (isset($_GET['success'])) {

                $email = $_SESSION['email'];

                $keep = [
                    'id' => $_SESSION['id'] ?? null,
                    'email' => $_SESSION['email'] ?? null,
                    'givenname' => $_SESSION['givenname'] ?? null,
                    'type' => $_SESSION['type'] ?? null,
                    'is_verified' => $_SESSION['is_verified'] ?? null,
                    'comment' => $_SESSION['comment'] ?? null
                ];

                session_unset();

                foreach ($keep as $key => $value) {
                    $_SESSION[$key] = $value;
                }


                echo'
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

    </div>
</body>
</html>