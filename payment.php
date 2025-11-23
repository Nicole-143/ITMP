
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


if (isset($_GET['cod'])) {
    $id = $_GET['pay'];
    $_SESSION['payment_mode'] = 'Cash_On_Delivery';
    header("Location: request.php?process&pay=" . $id); 
    exit();
}

if (isset($_GET['wallet'])) {
    $id = $_GET['pay'];
    $_SESSION['payment_mode'] = 'Wallet';
    header("Location: wallet.php?pay=" . $id); 
    exit();
}
$conn->close();
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

<?php echo '<pre>';
        print_r($_SESSION); // or var_dump($_SESSION);
    echo '</pre>';?>
    <div class="main-page">
         
       <div class="form-box payment-box valid-id">
                
       
                <h1>Select Payment Option:</h1>
                <p class="bottom-space">You will not be charged until you review this order on the next page</p>

                <table class="fee-display">
                
                <?php
                
                $copies = $_SESSION['copies'];
                $total=$_SESSION['total'];
                $shipping_fee=$_SESSION['shipping_fee'];

                echo '<tr>
                        <td><p class="reminder-content text"><b>Total: </b></p></td>
                        <td><p class="reminder-content text align-right">₱ '.$total.'.00</p></td>
                    </tr>';
                echo '<tr>
                        <td><p class="reminder-content text "><b>Shipping fee: </b></p></td>
                        <td><p class="reminder-content text align-right">₱ '.$shipping_fee.'</p></td>
                    </tr>';

                $subtotal=($total+$shipping_fee);
                ?>
               
                </table>

                <div class="line"></div>
                <div class="view-container">

                <div class="left">
                <h1 class="top-space">Subtotal: </h1>
                </div>
                <div class="right  align-right">
                <h1 class="top-space">₱  <?php echo $subtotal?>.00</h1>
                </div>

                </div>
                
        <div class="select-request bottom-space">
            <a href="wallet_payment.php?pay=<?php echo $id?>" class="request-btn">Wallet Payment</a>
            <a href="payment.php?cod&process&pay=<?php echo $id?>" class="request-btn">Cash on Delivery</a>
        </div>

                <div class="top-space bottom-space">
                    <div class="line"></div>
                </div>
                
                <a href="checkout_confirm.php?pay=<?php echo $id; ?>" class="back-btn top-space">Previous</a>

    </div>
</body>
</html>