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

if (isset($_SESSION['request_limit_reached']) && $_SESSION['request_limit_reached'] == true) {
    
    header("Location: document_details.php?view=$id");
    exit();
}


if (isset($_GET['own'])) {
    $_SESSION['docType'] = 'own';
} elseif (isset($_GET['others'])) {
    if ($_GET['others'] == 'senior') {
        $_SESSION['docType'] = 'senior';
        $_SESSION['relationship'] = 'senior'; 
    } elseif ($_GET['others'] == 'relative') {
        $_SESSION['docType'] = 'relative';
        $_SESSION['relationship'] = 'relative';
    } elseif ($_GET['others'] == 'guardian') {
        $_SESSION['docType'] = 'guardian';
        $_SESSION['relationship'] = 'guardian';
    } else {
        $_SESSION['docType'] = 'others';
    }
    $_SESSION['on_behalf'] = 1;
}

// Get selection for highlighting and next button
$docType = $_SESSION['docType'] ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['surname']) && isset($_POST['givenname']) && isset($_POST['middlename']) && isset($_POST['birthdate'])) {
        $_SESSION['represented_surname'] = $_POST['surname'];
        $_SESSION['represented_givenname'] = $_POST['givenname'];
        $_SESSION['represented_middlename'] = $_POST['middlename'];
        $_SESSION['represented_birthdate'] = $_POST['birthdate'];

        
        header("Location: upload_document.php?request=$id");
        exit();
    }
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
                <li><a href="logout.php">Logout</a></li>
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
                   <?php if ($docType === 'relative' || $docType === 'senior' || $docType === 'guardian' || $docType === 'others'){ echo 'style="background-color: #BDE0F2;"'; } ?>>For Someone Else</a>
            </div>

            <?php 
            if ($docType === 'own' || $docType === '')  {
                echo '
                <div class="bottom doc-btns">
                    <a href="document_details.php?view='.$id.'" class="back-btn">Previous</a>
                    <a href="upload_document.php?request='.$id.'" class="request-btn">Next</a>
                </div>';
            }

            if ($docType === 'senior' || $docType === 'relative' || $docType ==='guardian'|| $docType === 'others') {
                echo '<form method="POST" action="request_for.php?request='.$id.'&others='.$docType.'">
                        <div class="select-request options">
                            <h1>Whose document are you requesting for?</h1>

                             <a href="request_for.php?request='.$id.'&others=guardian"';
                            if ($docType === 'guardian') { echo ' style="background-color: #BDE0F2;"'; }
                            echo '>My child (son/daughter) or minor under my guardianship</a>';

                            echo'<a href="request_for.php?request='.$id.'&others=senior"';
                            if ($docType === 'senior') { echo ' style="background-color: #BDE0F2;"'; }
                            echo '>My parent or grandparent who is a senior citizen (60 and above)</a>';

                            echo '<a href="request_for.php?request='.$id.'&others=relative"';
                            if ($docType === 'relative') { echo ' style="background-color: #BDE0F2;"'; }
                            echo '>My first- or second-degree relative</a>';
                            echo '</div>

                            <h1 class="top-spacer bottom-space">Enter the details of the person you are requesting for</h1>

                            <label for="surname">Surname</label>
                            <input type="text" id="surname" name="surname" required placeholder="Surname">

                            <label for="givenname">Given Name</label>
                            <input type="text" id="givenname" name="givenname" required placeholder="Given Name">

                            <label for="middlename">Middle Name</label>
                            <input type="text" id="middlename" name="middlename" required placeholder="Middle Name">

                            <label for="birthdate">Birthdate</label>
                            <input type="date" id="birthdate" name="birthdate" required>

                            <div class="bottom doc-btns">
                                <a href="document_details.php?view='.$id.'" class="back-btn">Previous</a>
                                <button type="submit" class="request-btn">Next</button>
                            </div>
                        </form>';
            }
            ?>
        </div>
    </div>


</body>
</html>
