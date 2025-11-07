<?php 
session_start();

include "db.php";

if (!isset($_SESSION['email'])) {
    // Redirect to the login page if not logged in
    header("Location: index.php");
    exit();
}

// Check for unverified users added by Khloe Nov 8
if ($_SESSION['is_verified'] == 0 && $_SESSION['user_type'] == 'user') {
    header("Location: under_verification.php"); 
    exit();
}

$givenname = $_SESSION['givenname']; 

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Townsville Baranggay System</title>
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
            <li>Contact Us</li>
            <li><a href="register.php" onclick="">Register</a></li>
            <li><a href="dashboard.php">Home</a></li> <!--added by Khloe Nov 8-->
            <li><a href="request_document.php">Request Document</a></li> <!--NOT DONE YET added by Khloe Nov 8-->
            <li><a href="track_request.php">Track Requests</a></li> <!--NOT DONE YET added by Khloe Nov 8-->
            <li><a href="logout.php" onclick="">Logout</a></li>
        </ul>
    </nav>
    </header>

    <div class="main-page">
        <!--added by Khloe Nov 8-->
        <h2>Welcome, <?php echo htmlspecialchars($givenname); ?>!</h2>
        <p>Your current balance: **(Fetch wallet balance here)**</p>
        <p>Welcome to the main user dashboard. You can now request documents.</p>


    </div>
</body>
</html>
