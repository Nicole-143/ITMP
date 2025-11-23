<?php
session_start();
include "db.php";

// Ensure user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

if ($_SESSION['is_verified'] == 0 && $_SESSION['type'] == 'user') {
    header("Location: under_verification.php"); 
    exit();
}


// Check if the top-up action is requested
if (isset($_GET['top-up'])) {
    // Get user id from session and the top-up amount from POST
    $user_id = $_SESSION['id'];
    $update_topup = $_POST['topup_amount'] ?? 0;
    $_SESSION['update_topup']=$update_topup;
    header("Location: top_up_wallet.php?payment");
    exit;
}

// Close DB connection
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
            <li><a href="view_wallet.php">Wallet</a></li>
            <li><a href="track_request.php">Track Requests</a></li> 
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
    </header>


    <div class="main-page">
         
       <div class="form-box payment-box valid-id">
                
       
                <h1>My Wallet</h1>
                
                <?php
                 include "db.php";
                $user_id = $_SESSION['id'];
                $get_wallet_sql = "SELECT balance FROM wallet WHERE user_id = $user_id";
                $result = mysqli_query($conn, $get_wallet_sql);
                $wallet_balance = '0.00';  // Default to 0

                if ($result && mysqli_num_rows($result) > 0) {
                    $row = mysqli_fetch_row($result);
                    $wallet_balance = $row[0];  // Set wallet balance
                }
                
                echo '

                <div class="view-container">

                <div class="left">
                <h2 class="top-space">Current Balance: </h2>
                </div>
                <div class="right align-right">
                <h2 class="top-space">₱  '.$wallet_balance.'</h2>
                </div>

                </div>
                
                ';

                mysqli_close($conn);
                ?>

                <div class="line bottom-space top-space"></div>
                <?php
                include "db.php";

                if (isset($_GET['payment'])){

                    $user_id = $_SESSION['id'];
                    $update_topup = $_SESSION['update_topup']?? 0;
                    $new_balance = $wallet_balance + $update_topup;
                    $payment_date = date("Y-m-d H:i:s");
                    $transaction_type = 'Wallet Load';

                    if ($update_topup != 0)
                    { 
                    $update_wallet_sql = "UPDATE wallet SET balance = $new_balance WHERE user_id = $user_id";
                    $update_result = mysqli_query($conn, $update_wallet_sql);
            
                            if ($update_result) {

                                $request_id='NULL';
                            
                                $sql_payment_record = "INSERT INTO payments (user_id, amount, payment_date, transaction_type)
                                    VALUES ('$user_id', '$update_topup', '$payment_date','$transaction_type')";
                            
                                $payment_result = mysqli_query($conn, $sql_payment_record);

                                // Check if payment was successfully inserted
                                if ($payment_result) {
                                    // Unset the session top-up after successful payment
                                    unset($_SESSION['update_topup']);
                                    header("Location: view_wallet.php");
                                    exit();
                                } else {
                                    echo "<p>Error inserting payment record.</p>";
                                }
                                
                            } else {
                        echo "<p>Error updating wallet balance.</p>";
                            }
                    }

                    
                }

                else{
                    
                    echo'
                    <form action="view_wallet.php?top-up" method="POST">
                        <div class="amount-container">
                            <div class="left">
                                <h2>Enter Top-Up Amount: </h2>
                            </div>
                            <div class="right align-right">
                                <input type="number" name="topup_amount" id="topup_amount" min="1" step="0.01" class="top-space align-right" required>
                            </div>
                        </div>
                    
                    
                        <div class="line bottom-spacer"></div>
                        <div class="select-request top-space">
                            <button type="submit" class="request-btn">Top-Up</button>
                        </div>
                    </form>
                    
                    ';
                }

                mysqli_close($conn);
                ?>
                
                
        
      </div>
    </div>
</body>
</html>