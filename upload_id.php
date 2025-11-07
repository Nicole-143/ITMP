<?php 
include "db.php";

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Save the user ID (username) in the session
    $_SESSION['fileupload'] = $_POST['fileupload'];
    header('Location: password.php'); // Redirect to password page
    exit;
}

mysqli_close($conn);
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
            <li><a href="index.php" onclick="">Login</a></li>
        </ul>
    </nav>
    </header>

    <div class="main-page">
       <div class="form-box register-box valid-id">
                
                <h1>Registration - Valid ID</h1>
                <p><i class="tip">Your ID will be used solely for identity verification purposes and will be handled in accordance with data privacy laws.</i></p>
                <form action="upload_id.php" method="POST">
                <div class="top">
                    <div class="left">

                    <h2>Instructions for Uploading Your ID</h2>
                    <ul>
                        <li>The entire ID <b>must be visible</b> (not cropped or cut off).</li>
                        <li>Make sure the image is <b>clear and not blurry.</b></li>
                        <li>The following details must be clearly readable:
                            <ul class="lighter">
                                <li>Full Name</li>
                                <li>Photo</li>
                                <li>Birthdate</li>
                                <li>Area of residency</li>
                            </ul>
                        </li>
                        <li>Avoid using filters or altering the image.</li>
                        <li>Accepted file formats: <b>JPG, PNG, or PDF</b></li>
                        <li>Maximum file size: <b>5MB</b></li>  
                    </ul>
                    
                    <label>Upload Image</label>
                    <input type="file" id="myFile" name="fileupload">

                    </div>

                    <div class="right">
                    <h2>Accepted IDs:</h2>
                    <ul>
                        <li>Driver’s License</li>
                        <li>Passport</li>
                        <li>Voter’s ID</li>
                        <li>National ID</li>
                        <li>SSS ID</li>
                        <li>UMID</li>  
                        <li>Philhealth ID</li>
                        <li>HDMF (Pag-IBIG ID)</li>
                    </ul>
                    </div>

                </div>

                <div class="bottom">
                    <button type="submit">Next</button>
                </div>

            </form>
        </div>


    </div>
</body>
</html>