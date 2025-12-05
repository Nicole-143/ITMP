
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

if (!isset($_SESSION['last_request_date']) || $_SESSION['last_request_date'] != date('Y-m-d')) {
    // Reset document limit for a new day
    $_SESSION['request_limit_reached'] = false;
    $_SESSION['last_request_date'] = date('Y-m-d');
}

$givenname = $_SESSION['givenname']; 

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
            <li><a href="view_wallet.php">Wallet</a></li> 
            <li><a href="track_request.php">Track Requests</a></li> 
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
    </header>



    <div class="main-dashboard">
        <div class="user-container">

        <h2>Welcome <?php echo htmlspecialchars($givenname); ?>!</h2>
        <p class="description">Your one-stop platform for easy and fast barangay document requests.</p>
         
        <?php
        include "db.php"; 


        $sql = "SELECT * FROM dashboard_documents"; 
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            
            echo '<table class="document-btns">';

            //row counter
            $rowCounter = 0;

            //first row of the table
            echo '<tr>';

            
            while ($row = mysqli_fetch_row($result)) {
                
                $id = $row[0];
                $name = $row[1];
                $description = $row[2];  
                
                echo '<td>';
                echo '<a href="document_details.php?view=' . $id . '"><b>' .htmlspecialchars($name) .'</b><p>'. htmlspecialchars($description).'</p></a><br>';
                echo '</td>';

               
                $rowCounter++;

             
                if ($rowCounter % 3 == 0) {
                    echo '</tr><tr>'; 
                }
            }

            // end at last column
            if ($rowCounter % 3 != 0) {
                echo '</tr>';
            }

            echo '</table>';
        } else {
            echo "No data available.";
        }

        mysqli_close($conn);
        
        
        ?>
        </div>
    </div>
</body>
</html>