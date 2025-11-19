
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


if (isset($_GET['request'])) {
    $id = $_GET['request'];
    
} 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Redirect to the next page
    header('Location: payment.php?pay='. $id); // Redirect to the next page after successful upload
    exit;
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
       <div class="form-box register-box">
                
                <h1>Please input your delivery address and contact information</h1>
                <ul>
                <li>Use an address where you will be available to personally receive your birth certificate</li>
                <li>We will keep you updated via email and SMS about the status of your order</li>
                <li>This is not your place of birth</li>
                </ul>
          
                

                <form action="register.php" method="POST">
                <div class="main-form">
                    <label>Recipient</label><br>
                    <input type="text" name="recipient" placeholder="Given Name" required><br>
                    <label>House Number and Street Name</label><br>
                    <input type="text" name="address" placeholder="Residential Address" required><br>
                    <label>Subdivision or Building Name</label><br>
                    <input type="text" name="building" placeholder="Subdivision/Building" required><br>
                    <label>Mobile Number</label><br>
                    <input type="number" name="mobile"placeholder="Phone Number" required><br>
                    <label>Email Address</label><br>
                    <input type="email" class="email" name="email" placeholder="Email Address" required><br>
            <div class="bottom doc-btns">
                    <a href="shipping.php?view=<?php echo $id; ?>" class="back-btn">Previous</a>
                    <button type="submit">Next</button>
                </div>
            
                </form>

            
        </div>


    </div>
</body>
</html>