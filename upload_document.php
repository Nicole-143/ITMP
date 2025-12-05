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

// 🔹 Reset uploaded docs for this document-request flow
$_SESSION['fileupload'] = [];

// Determine next parameters
$nextparameters = '';
switch ($_SESSION['docType']) {
    case 'own':
        $nextparameters = '&own';
        break;
    case 'senior':
        $nextparameters = '&others=senior';
        break;
    case 'relative':
        $nextparameters = '&others=relative';
        break;
    default:
        $nextparameters = '&others';
}

// Map req_id → req_name (for error messages)
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

    if (isset($_FILES['fileupload'])) {
        $files = $_FILES['fileupload'];
    } else {
        $files = null;
    }

    // Check if any file was selected
    $hasFiles = false;
    if ($files) {
        foreach ($files['name'] as $name) {
            if (!empty($name)) {
                $hasFiles = true;
            }
        }
    }

    if (!$hasFiles) {
        if (!empty($requirements)) {
            $_SESSION['upload_errors'] = ["No files submitted."];
            header("Location: upload_document.php?request=$id");
            exit();
        }
    }

    if ($files) {
        foreach ($files['name'] as $req_id => $name) {
            $processFile = true;

            $tmp_name = $files['tmp_name'][$req_id] ?? null;
            $size     = $files['size'][$req_id] ?? 0;

            // Check for empty file
            if (empty($name)) {
                if (isset($req_names[$req_id])) {
                    $req_name = $req_names[$req_id];
                } else {
                    $req_name = "Unknown Requirement";
                }
                $errors[] = "No file submitted for \"$req_name\".";
                $processFile = false;
            }

            // Check size (max 5MB)
            if ($size > 5242880) {
                $errors[] = "File \"$name\" is too large (max 5MB).";
                $processFile = false;
            }

            // Check MIME type
            if ($processFile && $tmp_name) {
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime  = $finfo->file($tmp_name);
                $allowed = ["application/pdf", "image/png", "image/jpeg", "image/pjpeg"];
                if (!in_array($mime, $allowed)) {
                    $errors[] = "File \"$name\" has invalid type.";
                    $processFile = false;
                }
            }

            // 🔹 Move file NOW and store final path in session
            if ($processFile && $tmp_name) {
                $pathinfo = pathinfo($name);
                $base = preg_replace("/[^\w-]/", "_", $pathinfo["filename"]);
                $filename = $base . "." . $pathinfo["extension"];

                // Ensure documents/ exists
                $upload_dir = 'documents/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                // Avoid duplicate filenames
                $target_file = $upload_dir . $filename;
                $i = 1;
                while (file_exists($target_file)) {
                    $filename    = $base . "($i)." . $pathinfo["extension"];
                    $target_file = $upload_dir . $filename;
                    $i++;
                }

                if (!move_uploaded_file($tmp_name, $target_file)) {
                    $errors[] = "Could not save file \"$name\".";
                } else {
                    // Store only final path + req_id
                    $_SESSION['fileupload'][] = [
                        'request_id' => $id,
                        'req_id'     => $req_id,
                        'file_path'  => $target_file
                    ];
                }
            }
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
                <li><a href="logout.php">Logout</a></li>
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

                    if ($result && $result->num_rows > 0) {
                        while ($row = mysqli_fetch_row($result)) {
                            $req_id   = $row[0];
                            $req_name = $row[1];
                            $requirements[$req_id] = $req_name;
                            echo "<li>$req_name<br>
                                    <input type='file' name='fileupload[$req_id]' accept='.jpg,.png,.pdf'>
                                  </li>";
                        }
                    }

                    // Extra requirements for senior / relative / guardian
                    if ($_SESSION['docType'] == 'senior' || $_SESSION['docType'] == 'relative'|| $_SESSION['docType'] == 'guardian') {
                        $extra_req_ids = [4, 5, 6];
                        foreach ($extra_req_ids as $req_id) {
                            $res = mysqli_query($conn, "SELECT req_name FROM requirements WHERE req_id = $req_id");
                            if ($row = mysqli_fetch_row($res)) {
                                $req_name = $row[0];
                                $requirements[$req_id] = $req_name;
                                echo "<li>$req_name<br>
                                        <input type='file' name='fileupload[$req_id]' accept='.jpg,.png,.pdf'>
                                      </li>";
                            }
                        }
                    }

                    if (empty($requirements)) {
                        echo '<li>No requirements for this document. Please proceed to the next step.</li>';
                    }
                    ?>
                </ul>
                <div class="bottom doc-btns">
                    <a href="request_for.php?request=<?php echo $id . $nextparameters; ?>" class="back-btn">Previous</a>
                    <button type="submit">Next</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
