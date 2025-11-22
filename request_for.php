
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
                
       
                <h1>Are you requesting for your own document or for someone else?</h1>
        <div class="select-request options">
            <a href="request_for.php?request=<?php echo $id; ?>&own" 
               <?php if($docType === 'own'){ echo 'style="background-color: #BDE0F2;"'; } ?>>My Own Document</a>
            <a href="request_for.php?request=<?php echo $id; ?>&others" 
               <?php if ($docType === 'relative' || $docType === 'senior'|| $docType === 'others'){ echo 'style="background-color: #BDE0F2;"'; } ?>>For Someone Else</a>
        </div>

        <?php 
        if ($docType === 'senior' || $docType === 'relative' || $docType === 'others') {
            echo '<div class="select-request options">';
            echo '<h1>Whose document are you requesting for?</h1>';
            echo '<a href="request_for.php?request='.$id.'&others=senior"';
            if ($docType === 'senior') {
                echo ' style="background-color: #BDE0F2;"'; 
            }
            echo '>My parent or grandparent who is a senior citizen (60 and above)</a>';

            echo '<a href="request_for.php?request='.$id.'&others=relative"';
            if ( $docType === 'relative') {
                echo ' style="background-color: #BDE0F2;"';
            }
            echo '>My first- or second-degree relative</a>';
            echo '</div>';
        }
        ?>
                
                <div class="bottom doc-btns">
                    <a href="document_details.php?view=<?php echo $id; ?>" class="back-btn">Previous</a>
            <a href="upload_document.php?request=<?php echo $id?>" class="request-btn">Next</a>

        </div>


    </div>
</body>
</html>