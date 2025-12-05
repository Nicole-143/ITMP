
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

$user_id = $_SESSION['id'];

$sql_count = "
    SELECT COUNT(*) AS request_count
    FROM Requests
    WHERE user_id = $user_id
      AND DATE(request_date) = CURDATE()
      AND status IN ('Pending', 'Approved', 'Processing', 'Ready for Shipping', 'Shipping', 'Ready for Pick-up', 'Released')
";

$result_count = mysqli_query($conn, $sql_count);

$request_count = 0;
if ($result_count) {
    $row_count = mysqli_fetch_assoc($result_count);
    $request_count = $row_count['request_count'];
}

if ($request_count >= 3) {
    $_SESSION['request_limit_reached'] = true; // track the limit status
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
            <li><a href="dashboard.php">Home</a></li>
            <li><a href="track_request.php">Track Requests</a></li> 
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
    </header>


    <div class="main-page">

       <div class="form-box register-box ">
                <div class="stretch-page">
                <div class="top-list">
                
                    
                        <?php
                            
                            include "db.php"; 

                            if (isset($_GET['view'])) {
                                $id = $_GET['view'];     
                                
                            $sql_doc = "SELECT doc_name FROM dashboard_documents WHERE doc_id = $id";
                            $doc_result = mysqli_query($conn, $sql_doc);
                            if($doc_row = mysqli_fetch_row($doc_result)){
                                        $doc_name = $doc_row[0];
                            }

                            echo'<h1>'.$doc_name.'</h1>';
                            echo'<h2 class="htop">Eligibility</h2>
                            <ul>
                                <li>Resident of the Philippines</li>
                                <li>18 years old and above (For minors, application must be assisted by a parent or legal guardian)</li>
                                <li>Individuals who have earned income for at least 30 consecutive working days</li>
                                <li>Those who own real estate or collective assets with a total value of PHP 1,000 and above within the city</li>
                                <li>Those who are required by the law to file income tax returns</li>
                            </ul>';
                            echo'<h2 class="htop">Requirements</h2>
                            <ul>';
                            $sql = "SELECT req_name 
                                    FROM document_requirements
                                    WHERE doc_id = $id;";

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
                </div>

                <div class="bottom doc-btns">
                    <a href="dashboard.php" class="back-btn">Back to Home</a>
                    <?php if ($request_count < 3): ?>
                        <a href="request_for.php?request=<?php echo $id; ?>" class="request-btn">Request to Document</a>
                    <?php else: 
                        $_SESSION['request_limit_reached'] = true;
                        ?>
                        <button class="request-btn disabled" disabled>
                            Daily Document Requests Limit Reached
                        </button>
                    <?php endif?>   
                </div>
        </div>
</div>

    </div>
</body>
</html>