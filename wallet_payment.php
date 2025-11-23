
<?php 
session_start();



if (!isset($_SESSION['email'])) {
    // Redirect to the login page if not logged in
    header("Location: index.php");
    exit();
}

if ($_SESSION['is_verified'] == 0 && $_SESSION['type'] == 'user') {
    header("Location: under_verification.php"); 
    exit();
}



if (isset($_GET['own'])) {
    $_SESSION['docType'] = 'own';
} elseif (isset($_GET['others'])) {
    if ($_GET['others'] == 'senior') {
        $_SESSION['docType'] = 'senior';
    } elseif ($_GET['others'] == 'relative') {
        $_SESSION['docType'] = 'relative';
    } else {
        $_SESSION['docType'] = 'others';
    }
}

// Get selection for highlighting and next button
$docType = $_SESSION['docType'] ?? '';

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


    <div class="main-page">
         
       <div class="form-box payment-box valid-id">
                
       
                <h1>Wallet Payment</h1>
                
                <?php
                include "db.php";

                if (isset($_GET['pay'])) {
                    $id = $_GET['pay'];
                    
                } 
                $user_id = $_SESSION['id'];
                $top_up = $_SESSION['top-up']?? 0;

                $get_wallet_sql= "SELECT balance FROM wallet WHERE user_id = $user_id ";
                $result = mysqli_query($conn, $get_wallet_sql);

                $wallet_balance = '0.00'; // default 

                if ($result && mysqli_num_rows($result) > 0) {
                    $row = mysqli_fetch_row($result);;
                    $wallet_balance = $row[0];
                }

                $new_balance = $wallet_balance + $top_up;
               
                $update_wallet_sql = "UPDATE wallet SET balance = $new_balance WHERE user_id = $user_id";
                $update_result = mysqli_query($conn, $update_wallet_sql);

                 if ($update_result) {
                     $_SESSION['wallet_balance'] = $new_balance;
                 }

                $subtotal = ($_SESSION['total']*$_SESSION['copies'])+$_SESSION['shipping_fee'];  
                $remaining_balance = $new_balance - $subtotal;  

                echo '

                <div class="view-container">

                <div class="left">
                
                <h2 class="top-space">Price of Document: </h2>
                <h2 class="top-space">Number of Copies: </h2>
                <h2 class="top-space">Total Price of Document: </h2>
                <h2 class="top-space">Shipping Fee: </h2>
                
                </div>

                <div class="right align-right">
                
                <h2 class="top-space">₱  '.$_SESSION['price'].'</h2>
                <h2 class="top-space">x  '.$_SESSION['copies'].'</h2>
                <h2 class="top-space">₱  '.number_format($_SESSION['total'], 2).'</h2>
                <h2 class="top-space">  '.$_SESSION['shipping_fee'].'</h2>
                </div>

                </div>
                
                <div class="line bottom-space top-space" ></div>
                
                <div class="view-container">

                <div class="left">
                <h2 class="top-space">Current Balance: </h2>
                <h2 class="top-space">Subtotal: </h2>
                
                </div>
                <div class="right align-right">
                <h2 class="top-space">₱  '.number_format($new_balance, 2).'</h2>
                <h2 class="top-space">₱  '.number_format($subtotal, 2).'</h2>
                
                 
                </div>

                </div>
                
                
                ';
                
                if ($remaining_balance >= 0)
                {
                    $_SESSION['balance'] = $remaining_balance;
                    echo 
                    '
                    <div class="line top-spacer"></div>
                    <p class="choosepayment top-space align-center">You have enough balance. Proceed with payment.</p>
                    <div class="line top-space"></div>
                    <div class="select-request top-space">
                        <form method="POST">
                        <button type="submit" name="pay" class="request-btn">Pay</button>
                        </form>
                        <a href="payment.php?pay='. $id.'" class="back-btn">Previous</a>
                </div>
                    ';
                }

                else{
                    echo 
                    '<div class="line top-spacer"></div>
                    <p class="choosepayment top-space align-center">Insufficient funds in wallet. Please top-up</p>
                    <div class="line top-space"></div>
                    <div class="select-request top-space">
                        
                        <a href="wallet.php?pay='. $id.'" class="request-btn">Top-Up</a>
                        <a href="payment.php?pay='. $id.'" class="back-btn">Previous</a>
                </div>

                
                    ';
                 }

                
                

                if (isset($_POST['pay'])) {
                        
                        $new_balance_after_payment = $remaining_balance;

                        $update_wallet_sql = "UPDATE wallet SET balance = $new_balance_after_payment WHERE user_id = $user_id";
                        $update_result = mysqli_query($conn, $update_wallet_sql);

                        if ($update_result) {
                            $_SESSION['payment_mode']='Wallet';
                            $_SESSION['wallet_balance'] = $new_balance_after_payment;
                            header("Location: request.php?process&pay=$id");
                            exit;
                        } else {
                            echo "<p>Error processing payment. Please try again.</p>";
                        }
                    }
                

                mysqli_close($conn);
                ?>

                
    </div>
</body>
</html>