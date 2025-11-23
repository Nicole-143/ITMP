
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
    if (isset($_GET['top-up'])) {
    $_SESSION['top-up'] = $_POST['topup_amount'];
    $id = isset($_GET['pay']);

    header("Location: top_up.php?pay=$id");
    exit;
    }
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
                
       
                <h1>My Wallet</h1>
                
                <?php
                include "db.php";
                $user_id = $_SESSION['id'];
                
                $get_wallet_sql= "SELECT balance FROM wallet WHERE user_id = $user_id ";
                $result = mysqli_query($conn, $get_wallet_sql);

                $wallet_balance = '0.00'; // default 

                if ($result && mysqli_num_rows($result) > 0) {
                    $row = mysqli_fetch_row($result);;
                    $wallet_balance = $row[0];
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

                <div class="line bottom-space top-space" ></div>
                <form action="wallet.php?top-up&pay=<?php echo $id ?>" method="POST">

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
                    <a href="payment.php?pay=<?php echo $id; ?>" class="back-btn">Previous</a>
                </div>
                </form>

    </div>
</body>
</html>