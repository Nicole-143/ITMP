<?php 
session_start();

include "db.php";

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

if ($_SESSION['is_verified'] == 0 && $_SESSION['type'] == 'user') {
    header("Location: under_verification.php"); 
    exit();
}

if (isset($_GET['pay'])) {
    $id = $_GET['pay'];
} else {
    die("Missing pay parameter.");
}

// Fetch document info
$sql = "SELECT doc_name, price, shipping_fee FROM document_types WHERE doc_id = $id";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = mysqli_fetch_row($result)) {
        $doc_name      = $row[0]; 
        $price         = $row[1]; 
        $shipping_fee  = $row[2]; 
    }
}

$delivery_mode = $_SESSION['delivery_mode'];

$_SESSION['price'] = $price;
$copies = $_SESSION['copies'];
$total  = $copies * $price;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $_SESSION['copies'] = $copies;
    $total = $copies * $price;

    // -----------------------------------
    // FIX: ONLY APPLY SHIPPING FEE IF DELIVERY
    // -----------------------------------
    if ($delivery_mode == 'Pick-up') {
        $_SESSION['shipping_fee'] = 0;
    } else {
        $_SESSION['shipping_fee'] = $shipping_fee; // 30 pesos from your DB
    }

    $_SESSION['total'] = $total;

    // Where to go next?
    if ($delivery_mode == 'Pick-up') {
        header("Location: request.php?process&pay=" . $id);
        exit();
    } 
    else if ($delivery_mode == 'Delivery') {
        header("Location: payment.php?pay=" . $id);
        exit();
    }
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

    <div class="main-page">
       <div class="form-box request-box valid-id">       
                
            <h1>Checkout</h1>
            <p class="reminder-content title">Reminder:</p>
            <p class="reminder-content">Avoid transacting with online scammers! Barangay Townsville <b>DOES NOT</b> coordinate transactions and payment through FB messenger. No additional fees will be charged aside from what is indicated on your order.</p>
                
            <p class="reminder-content text l"><b>Document Request Details</b></p>

            <table>
                <?php
                echo '<tr>
                        <td><p class="reminder-content text"><b>Document Type: </b></p></td>
                        <td><p class="reminder-content text">'.$doc_name.'</p></td>
                    </tr>';

                echo '<tr>
                        <td><p class="reminder-content text"><b>Number of Copies: </b></p></td>
                        <td><p class="reminder-content text">'.$copies.'</p></td>
                    </tr>';

                echo '<tr>
                        <td><p class="reminder-content text"><b>Total Price: </b></p></td>
                        <td><p class="reminder-content text">₱ '.$total.'.00</p></td>
                    </tr>';
                ?>
            </table>

            <p class="reminder-content text">Please confirm if you’re able to receive the document by presenting an original government-issued ID of the document holder.</p>
            <p class="reminder-content text"><b>If you’re accepting the document on someone else’s behalf, </b>kindly provide your own government-issued ID along with the document owner’s ID and an authorization letter.</p>
                
            <form action="checkout_confirm.php?pay=<?php echo $id ?>" method="POST"> 
                <div class="checkbox-container">
                    <input type="checkbox" id="on-behalf" name="on-behalf" value="on-behalf">
                    <label for="on-behalf">I confirm</label>
                </div>

                <div class="bottom doc-btns">
                    <a href="checkout.php?pay=<?php echo $id; ?>" class="back-btn">Previous</a>
                    <button type="submit">Next</button>
                </div>
            </form>  
        </div>
    </div>
</body>
</html>
