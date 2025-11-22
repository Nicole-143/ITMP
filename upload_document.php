<?php
session_start();
include "db.php";

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

if (isset($_GET['request'])) {
    $id = $_GET['request'];
    
} 

if ($_SESSION['is_verified'] == 0 && $_SESSION['type'] == 'user') {
    header("Location: under_verification.php"); 
    exit();
}

// Determine next parameters
$nextparameters = '';
switch($_SESSION['docType']) {
    case 'own': $nextparameters='&own'; break;
    case 'senior': $nextparameters='&others=senior'; break;
    case 'relative': $nextparameters='&others=relative'; break;
    default: $nextparameters='&others';
}

if (!isset($_SESSION['fileupload'])) {
    $_SESSION['fileupload'] = [];
}

$req_names = [];
$sql = "SELECT req_id, req_name FROM requirements";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_row($result)) {
        $req_id = $row[0];  
        $req_name = $row[1]; 
        $req_names[$req_id] = $req_name;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $errors = [];
    $files = $_FILES['fileupload'] ?? null;

    // Check if any file was selected
    $hasFiles = false;
    foreach (($files['name'] ?? []) as $name) {
        if (!empty($name)) $hasFiles = true;
    }

    if (!$hasFiles) {
        $_SESSION['upload_errors'] = ["No files submitted."];
        header("Location: upload_document.php?request=$id");
        exit();
    }

    foreach (($files['name'] ?? []) as $req_id => $name) {

        $processFile = true; // flag to check if file should be saved

        $tmp_name = $files['tmp_name'][$req_id] ?? null;
        $size = $files['size'][$req_id] ?? 0;

        // Check for empty file
        if (empty($name)) {
            $req_name = $req_names[$req_id] ?? "Unknown Requirement";
            $errors[] = "No file submitted for \"$req_name\".";
            $processFile = false;
        }


        // Check size (5MB max for example)
        if ($size > 5242880) {
            $errors[] = "File \"$name\" is too large (max 5MB).";
            $processFile = false;
        }

        // Check MIME type
        if ($processFile && $tmp_name) {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($tmp_name);
            $allowed = ["application/pdf", "image/png", "image/jpeg", "image/pjpeg"];
            if (!in_array($mime, $allowed)) {
                $errors[] = "File \"$name\" has invalid type.";
                $processFile = false;
            }
        }

       
        if ($processFile && $tmp_name) {
            $pathinfo = pathinfo($name);
            $base = preg_replace("/[^\w-]/", "_", $pathinfo["filename"]);
            $filename = $base . "." . $pathinfo["extension"];

            // Save in session
            $_SESSION['fileupload'][] = [
                'request_id' => $id,
                'req_id' => $req_id,
                'file_name' => $filename,
                'tmp_name' => $tmp_name // need for request submission
            ];
        }
    }

    if (!empty($errors)) {
        $_SESSION['upload_errors'] = $errors;
        header("Location: upload_document.php?request=$id");
        exit();
    }
    
    header("Location: shipping.php?request=$id");
    exit();
}

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


    <div class="main-page adjust-spacing">
       
        <?php
if (!empty($_SESSION['upload_errors'])) {
    foreach ($_SESSION['upload_errors'] as $err) {
        echo '<div class="error-container">';
        echo '<img src="./images/warning.png">';
        echo "<p>$err</p>";
        echo '</div>';
    }
    unset($_SESSION['upload_errors']);
}
?>


       <div class="form-box request-box valid-id">
                
                <h1>Upload Documents</h1>
                <form action="upload_document.php?request=<?php echo $id; ?>" enctype="multipart/form-data" method="POST">
                
                <h2>Requirements</h2>
                    <ul>
                        <?php
$sql = "SELECT req_id, req_name
        FROM requirements
        WHERE req_id IN (SELECT req_id FROM doc_type_requirements WHERE doc_id = $id)";

$result = mysqli_query($conn, $sql);

$requirements = []; // store requirement IDs and names 

 if ($result->num_rows > 0){
    while ($row = mysqli_fetch_row($result)) { 
        $req_id = $row[0];   
        $req_name = $row[1]; 
        $requirements[$req_id] = $req_name;
        echo "<li>$req_name<br>
              <input type='file' name='fileupload[$req_id]' accept='.jpg,.png,.pdf'>
              </li>";
    }
}
// Add additional requirements for seniors or relatives
if ($_SESSION['docType'] == 'senior' || $_SESSION['docType'] == 'relative') {
    $extra_req_ids = [4, 5, 6];
    foreach ($extra_req_ids as $req_id) {
        $res = mysqli_query($conn, "SELECT req_name FROM requirements WHERE req_id = $req_id ");
        if ($row = mysqli_fetch_row($res)) {
            $req_name = $row[0]; 
            $requirements[$req_id] = $req_name;
            echo "<li>$req_name<br>
                  <input type='file' name='fileupload[$req_id]' accept='.jpg,.png,.pdf'>
                  </li>";
        }
    }
}
?>
                    </ul>
                <div class="bottom doc-btns">
                    <a href="request_for.php?request=<?php echo $id.$nextparameters;?>" class="back-btn">Previous</a>
                    <button type="submit">Next</button>
                </div>

            </form>
        </div>


    </div>
</body>
</html>