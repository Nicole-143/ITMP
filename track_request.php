<?php
session_start();


// Ensure user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

if ($_SESSION['is_verified'] == 0 && $_SESSION['type'] == 'user') {
    header("Location: under_verification.php"); 
    exit();
}



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
            <li><a href="view_wallet.php">Wallet</a></li>
            <li><a href="track_request.php">Track Requests</a></li> 
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
    </header>

    <div class="main-dashboard">
       <div class="page-container">
            <h2><a href="dashboard.php"><b><-</b></a></h2>
            <h2>Track Document Requests</h2>
        </div>
       <div class="table-container">

                <?php

                
                include "db.php";

                $user_id = $_SESSION['id'];

                
                $sql = "SELECT r.request_id, r.request_date, r.status, r.delivery_mode, r.payment_mode, r.payment_status, r.shipping_date, r.arrival_date, 
                            d.doc_name, 
                            p.amount, p.payment_date, p.transaction_type, r.is_on_behalf
                        FROM requests r
                        JOIN document_types d ON r.doc_id = d.doc_id
                        LEFT JOIN payments p ON r.request_id = p.request_id
                        WHERE r.user_id = '$user_id'"; 

                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    echo'
                        <table class="user-table">
                        <tr class="head">
                            <th>Document Type</th>
                            <th>Status</th>
                            <th>Delivery Mode</th>
                            <th>Amount</th>
                            <th>Payment Mode</th>
                            <th>Payment Status</th>
                            <th>On Behalf</th>
                            <th>Request Date</th>
                            <th>Shipping Date</th>
                            <th>Arrival Date</th>
                        </tr>
                        
                        ';
                    while ($row = mysqli_fetch_row($result)) {
                        

                        $request_id = $row[0];
                        $request_date = date("m/d/Y", strtotime($row[1]));
                        $status = $row[2];
                        $delivery_mode = $row[3];

                        if ($row[4]== 'Cash_On_Delivery'){
                            $payment_mode = 'Cash On Delivery';
                        }
                        else{
                                $payment_mode = 'Cash'; // If payment_mode is NULL, show Cash
                        }
                        
                        $payment_status = $row[5];
                        
                        $shipping_date = $row[6] ? $row[6] : 'N/A'; 
                        $arrival_date = $row[7] ? $row[7] : 'N/A'; 
                        $doc_name = $row[8];
                        $amount = $row[9] ? $row[9] : 'N/A'; 
                        $payment_date = $row[10];
                        $transaction_type = $row[11];
                        $is_on_behalf = $row[12] ? 'Yes' : 'No'; // 'Yes' if receive on behalf, 'No' otherwise

                        
                        echo "<tr>
                                <td>$doc_name</td>
                                <td>$status</td>
                                <td>$delivery_mode</td>
                                <td>$amount</td>
                                <td>$payment_mode</td>
                                <td>$payment_status</td>
                                <td>$is_on_behalf</td>
                                <td>$request_date</td>
                                <td>$shipping_date</td>
                                <td>$arrival_date</td>
                              </tr>
                              
                              
                              ";
                    }
                    echo'</table>';
                } else {
                    echo '<p class="not-found"><b>No requests found</b></p>';
                }

                // Close DB connection
                $conn->close();
                ?>
            
            
        </div>
    </div>

</body>
</html>