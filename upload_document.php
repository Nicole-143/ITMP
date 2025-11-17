
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

    // Loop through each uploaded file
    foreach ($_FILES['fileupload']['name'] as $document => $file_name) {

        // Reject uploaded file larger than 5MB
        if ($_FILES["fileupload"]["size"][$document] > 5242880) { // 5MB limit
            header("Location: upload_document.php?error=large");
            exit;
        }

        // Use fileinfo to get the mime type and reject unaccepted file types
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime_type = $finfo->file($_FILES["fileupload"]["tmp_name"][$document]);
        $mime_types = ["application/pdf", "image/png", "image/jpeg"];
        if (!in_array($mime_type, $mime_types)) {
            header("Location: upload_document.php?error=invalid");
            exit;
        }

        // Replace any characters not \w- in the original filename
        $pathinfo = pathinfo($file_name);
        $base = preg_replace("/[^\w-]/", "_", $pathinfo["filename"]);
        $filename = $base . "." . $pathinfo["extension"];

        // Check if the file already exists and add a number if it does
        $upload_dir = 'documents/';
        $target_file = $upload_dir . $filename;
        $i = 1;
        while (file_exists($target_file)) {
            // If the file exists, append number to the filename
            $filename = $base . "($i)." . $pathinfo["extension"];
            $target_file = $upload_dir . $filename;
            $i++;
        }

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES["fileupload"]["tmp_name"][$document], $target_file)) {
            // Save the file path in the session
            $_SESSION['fileupload'][] = $filename; // Store multiple filenames
        } else {
            echo "Error uploading file.";
        }
    }

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
       <div class="form-box register-box valid-id">
                
                <h1>Upload Documents</h1>
                <form action="upload_document.php?request=<?php echo $id; ?>" enctype="multipart/form-data" method="POST">
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
                        <li>Maximum file size for each document: <b>5MB</b></li>  
                    </ul>
                    
                    <label>Upload Image</label>
                    <input type="file" id="myFile" name="fileupload[]" accept=".jpg,.png,.pdf" multiple>

                    </div>

                    <div class="right">
                    <h2>Requirements</h2>
                    <ul>
                        <?php

                            include "db.php"; 

                            if (isset($_GET['request'])) {
                                $id = $_GET['request'];     
                            

                            $sql = "SELECT r.req_name
                                    FROM doc_type_requirements dtr
                                    JOIN requirements r ON dtr.req_id = r.req_id
                                    WHERE dtr.doc_id = $id";

                            $result = mysqli_query($conn, $sql);

                            
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_row($result)) {
                                    // Fetch req_name
                                    echo "<li>".$row[0] . "</li>"; 
                                }
                            } else {
                                echo "No requirements found for this document ID.";
                            }
                            }
                            mysqli_close($conn);
                        ?> 
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
                
                <div class="bottom doc-btns">
                    <a href="document_details.php?view=<?php echo $id; ?>" class="back-btn">Previous</a>
                    <button type="submit">Next</button>
                </div>

            </form>
        </div>


    </div>
</body>
</html>