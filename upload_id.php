
<?php 
include "db.php";

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Reject uploaded file larger than 5MB
    if ($_FILES["fileupload"]["size"] > 5242880) { //1024*1024* n = MB //1 MB = 1048576
        header("Location: upload_id.php?error=large");
        exit;
    }

    // Use fileinfo to get the mime type and reject unaccepted file types
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime_type = $finfo->file($_FILES["fileupload"]["tmp_name"]);
    $mime_types = ["application/pdf", "image/png", "image/jpeg"];
    if ( ! in_array($mime_type, $mime_types)) {
        header("Location: upload_id.php?error=invalid");
        exit;
    }

    // Replace any characters not \w- in the original filename
    $pathinfo = pathinfo($_FILES["fileupload"]["name"]);
    $base = preg_replace("/[^\w-]/", "_", $pathinfo["filename"]);
    $filename = $base . "." . $pathinfo["extension"];
    
    

    // Check if the file already exists and add a number if it does
    $upload_dir = 'uploads/';
    $target_file = $upload_dir . $filename;
    $i = 1;
    while (file_exists($target_file)) {
    // If the file exists, append number to the filename
    $filename = $base . "($i).". $pathinfo["extension"];
    $target_file = $upload_dir . $filename;
    $i++;
    }

    

    // Save the user ID (username) in the session
    if (move_uploaded_file($_FILES["fileupload"]["tmp_name"], $target_file)) {
        // Save the file path in the session
        $_SESSION['fileupload'] = $filename; 
        header('Location: password.php'); // Redirect to the next page
        exit;
    } 
    
}

mysqli_close($conn);
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
            <li><a href="register.php" onclick="">Register</a></li>
            <li><a href="index.php" onclick="">Login</a></li>
        </ul>
    </nav>
    </header>

    <div class="main-page">
       <div class="form-box register-box valid-id">
                
                <h1>Registration - Valid ID</h1>
                <p><i class="tip">Your ID will be used solely for identity verification purposes and will be handled in accordance with data privacy laws.</i></p>
                <form action="upload_id.php" enctype="multipart/form-data" method="POST">
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
                    <input type="file" id="myFile" name="fileupload" accept=".jpg,.png,.pdf">

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

                        <div class="message-container <?php if (isset($_GET['error'])) { echo 'visible'; }?>">

                            <img src="./images/warning.png">
                            <p>
                                <?php
                                    if (isset($_GET['error']) && $_GET['error'] == 'large') {
                                        echo '<p>File is too large (max: 5MB).</p>';
                                    } elseif (isset($_GET['error']) && $_GET['error'] == 'invalid') {
                                        echo '<p>Invalid file type.</p>';
                                    }
                                ?>
                            </p>
                        </div>
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