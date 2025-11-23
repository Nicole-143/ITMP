
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

if (isset($_GET['pay'])) {
    $id = $_GET['pay'];
    
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


include "db.php";

if( $_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_GET['card'])) {    
    $id = isset($_GET['pay']);

    header("Location: wallet_payment.php?pay=$id");
    exit;
    }
}

$paymenttype='';

if (isset($_GET['card'])) {
    $paymenttype = 'card';
} elseif (isset($_GET['gcash'])) {
    $paymenttype = 'gcash';
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


    <div class="main-page">
         
       <div class="form-box payment-box valid-id">
                
                <?php
            
                $user_id = $_SESSION['id'];
                $top_up = $_SESSION['top-up'];

                echo '

                <div class="view-container">

                <div class="left">
                <h1 class="top-space">Top-Up: </h1>
                </div>
                <div class="right align-right">
                <h1 class="top-space">₱  '.$top_up.'</h1>
                </div>

                </div>
                
                ';

                
                ?>

                <div class="line bottom-space top-space" ></div>
                
                <p class="choosepayment">Choose payment method</p>
                <div class="select-request options">
                    <a href="top_up.php?pay=<?php echo $id; ?>&card" 
                    <?php if($paymenttype === 'card'){ echo 'style="background-color: #BDE0F2;"'; } ?>
                    >Credit Card / Debit Card</a>
                    <a href="top_up.php?pay=<?php echo $id; ?>&gcash"
                    <?php if($paymenttype === 'gcash'){ echo 'style="background-color: #BDE0F2;"'; } ?>
                    >GCash</a>
                </div>
                
                <div class="line bottom-space"></div>

                <?php
                
                if (isset($_GET['gcash']))
                {
                    echo'
                    
                    <img class="qr bottom-space" src="./images/payment-qr.png">
                    <div class="line bottom-space"></div>
                    <div class="select-request top-space">
                        <a href="wallet_payment.php?pay='.$id.'" class="request-btn">Next</a>
                    ';
                }
                else if (isset($_GET['card'])){
                    echo'
                    <form id="payment-form" action="top_up.php?pay='.$id.'&card" method="POST">
                
                    <h2 class="card bottom-space">Card Payment Details </h2>
                    <label>Email Address</label><br>
                    <input type="email" class="email" name="email" placeholder="Email Address" required><br>
                    <label>Credit/Debit Card Number</label><br>
                    <input type="number" name="card" placeholder="xxx xxx xxx xxx" required pattern="\d{13,19}" title="Card number must be between 13 and 19 digits"><br>

                    <div class="two-parts">
                        <div class="first-part">
                        <label>Expiry Date</label><br>
                    <input type="text" name="expiry" placeholder="MM-DY" required pattern="^(0[1-9]|1[0-2])-(\d{2})$" title="Please enter a valid expiry date in MM-YY  format"><br>
                        </div>
                        <div class="second-part">
                        <label>CVV</label><br>
                    <input type="number" name="cvv" placeholder="xxx" required pattern="^\d{3,4}$" title="CVV should be 3 or 4 digits"><br>
                        </div>
                    
                    </div>
                    <div class="select-request top-space">
                    <div class="line bottom-space"></div>
                                <button type="submit">Next</button>
                                </form>';
                    
                }
                ?>

                    <a href="wallet.php?pay=<?php echo $id; ?>" class="back-btn">Previous</a>
                </div>
                </form>

                 
    </div>
</body>
</html>