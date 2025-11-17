<?php
session_start();

// added by Khloe Nov 8 please check everything

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

// Checks if theyre verified
if ($_SESSION['is_verified'] == 1) {
    header("Location: dashboard.php");
    exit();
}

$givenname = $_SESSION['givenname'];
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
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
    </header>

    <div class="main-page">
        <div class="form-box login-box verification">
                
                    <div class="brgy-logo">
                        <img src="./images/logo.png">
                    </div> 
                    
                        <h2>Account Under Verification</h2>
                        <p class="greeting">Hello, <b><?php echo htmlspecialchars($givenname); ?></b>.</p>
            <p>Your registration is currently being reviewed by the Barangay Admin. You will not be able to request documents until your account is approved.</p>
            <p>Please check back later.</p>
                   
        </div>

    </div>
</body>
</html>