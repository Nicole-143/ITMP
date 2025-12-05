<?php

session_start();
include "db.php";

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

$id = $_SESSION['id'];

if (isset($_GET['logout']) && $_GET['logout'] == 'true') {

$sql_delete_user = "DELETE FROM wallet WHERE user_id = $id";

if (mysqli_query($conn, $sql_delete_user)) {

    $sql_delete = "DELETE FROM users WHERE id = $id";
     if (mysqli_query($conn, $sql_delete)) {
    session_unset(); 
    session_destroy();
    header("Location: index.php");  
    exit();
     }
}
}

// If the account is denied (has a comment)
if ((!empty($_SESSION['comment']) && !isset($_GET['denied']))){
    header("Location: under_verification.php?denied");
    exit();
}

// If account is verified
if (!empty($_SESSION['is_verified']) && $_SESSION['is_verified'] == 1) {
    header("Location: dashboard.php");
    exit();
}

$givenname = $_SESSION['givenname'];
$comment = $_SESSION['comment'];


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
    
    </header>

    <div class="main-page">
        <div class="form-box login-box verification">
                
                    <div class="brgy-logo">
                        <img src="./images/logo.png">
                    </div> 
                    
                        <h2>Account Under Verification</h2>
                        <p class="greeting">Hello, <b><?php echo htmlspecialchars($givenname); ?></b>.</p>
            <?php if (isset($_GET['denied'])){
                    echo '<p style="margin-bottom:12px;" >Your registration has been denied.';
                    echo '<p style="margin-bottom:17px;"><b>Reason: '. $comment. '</b></p>';
                    echo '<p style="margin-bottom:17px;" >Your account will be deleted upon logout <br>Please register again. </p>';
                    echo '<a class="request-btn" href="under_verification.php?logout=true">Logout</a>';
                    
                    
            }
            else{
                echo '<p style="margin-bottom:12px;">Your registration is currently being reviewed by the Barangay Admin. You will not be able to request documents until your account is approved.</p>';
                echo '<p style="margin-bottom:17px;">Please check back later.</p>';
                echo '<a class="request-btn" href="logout.php">Logout</a>';
            }
            ?>
               
        </div>
        
                   
        </div>

    </div>
</body>
</html>