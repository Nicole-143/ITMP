
<?php 
session_start();

include "db.php";

if (!isset($_SESSION['email'])) {
    // Redirect to the login page if not logged in
    header("Location: index.php");
    exit();
}

if ($_SESSION['type'] == 'user') {
    header("Location: dashboard.php"); 
    exit();
}


$givenname = $_SESSION['givenname']; 

if(isset($_GET['update'])){

}
// Close the database connection
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
            <li>Contact Us</li>
            <li><a href="admin_dashboard.php">Home</a></li> <!--added by Khloe Nov 8-->
            <li><a href="logout.php" onclick="">Logout</a></li>
        </ul>
    </nav>
    </header>

    <div class="main-admin-dashboard">
        <div class="admin-container">
        <h2>Admin Page</h2>
        <p>Welcome back, Admin <?php echo htmlspecialchars($givenname); ?>. Manage requests and approvals here.</p>
        <h3>Operations Request</h3>
        <div class="btn-container b1">

        <a href="view_users.php" class="button-style">
        <b>View Users</b>
        <p>Browse and manage registered user accounts, including details and status information.</p>
        </a>

        <a href="view_shipments.php" class="button-style">
        <b>View Shipments</b>
        <p>Access and review shipment records to track delivery status and history.</p>
        </a>

        </div>
        
        <h3>User Request Approval</h3>
        <div class="btn-container b2">

        <a href="approval.php" class="button-style">
        <b>Account Registration Approval</b>
        <p>Verify and approve new user account registrations to grant access to the system.</p>
        </a>
        <a href="document.php" class="button-style">
        <b>Document Request Approval</b>
        <p>Review and approve submitted document requests to ensure compliance and accuracy before processing.</p>
        </a>

        </div>
        </div>
    </div>
</body>
</html>