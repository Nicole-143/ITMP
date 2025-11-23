
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

 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $copies = $_POST['copies'];
    $_SESSION['copies'] = $copies;
    
    header("Location: checkout_confirm.php?pay=" . $id); 
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


    <div class="main-page">
       <div class="form-box request-box valid-id">       
       <form action="checkout.php?pay=<?php echo $id?>" method="POST">
                <h1>Checkout</h1>
                <p class="reminder-content title">Reminder:</p>
                <p class="reminder-content">Avoid transacting with online scammers! Barangay Townsville <b>DOES NOT</b> coordinate transactions and payment through FB messenger. No additional fees will be charged aside from what is indicated on your order.</p>
                
                <p class="reminder-content text l"><b>How many copies will you request for?</b></p>

                <?php $selected = isset($_SESSION['copies']) && $_SESSION['copies'] !== '' ? $_SESSION['copies'] : 1; ?>
                <div class="copy-container">
                    <select id="copies" name="copies" class="display-amount">
                    <option value="1" <?php if ($selected == 1) echo 'selected'; ?>>1</option>
                    <option value="2" <?php if ($selected == 2) echo 'selected'; ?>>2</option>
                    <option value="3" <?php if ($selected == 3) echo 'selected'; ?>>3</option>
                    <option value="4" <?php if ($selected == 4) echo 'selected'; ?>>4</option>
                    <option value="5" <?php if ($selected == 5) echo 'selected'; ?>>5</option>
                </select>
                </div>
                <div class="bottom doc-btns">
                    <a href="document_details.php?view=<?php echo $id; ?>" class="back-btn">Previous</a>
                    <button type="submit">Next</button>

                </div>
        </form>


        
    </div>
</body>
</html>
