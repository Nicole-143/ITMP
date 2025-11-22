
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

$nextparameters='';

if($_SESSION['docType'] == 'own')
{
    $nextparameters='&own';
} elseif($_SESSION['docType'] == 'senior')
{
    $nextparameters='&others=senior';
}
elseif($_SESSION['docType'] == 'relative')
{
    $nextparameters='&others=relative';
}else{
    $nextparameters='&others';
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


    <div class="main-dashboard">
        <div class="select-title">
            <h2><a href="upload_document.php?request=<?php echo $id.$nextparameters;?>"><b><-</b></a></h2>
            <h2>Please select a delivery option</h2>
        </div>
        <table class="deliveryoptions">
            <tr>
                <td>
                <h3>Door-to-door Delivery</h3>
                <p>Your document will be delivered straight to your delivery address</p>
                <a href="delivery.php?doortodoor=<?php echo $id; ?>">Next</a>
                </td>
                <td>
                <h3>Pick-up</h3>  
                <p>You may pick up your document at the barangay hall during regular office hours</p>  
                <a href="delivery.php?pickup=<?php echo $id; ?>">Next</a>
                </td>
            </tr>
        </table>
        
    </div>
</body>
</html>