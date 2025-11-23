
<?php 
session_start();

include "db.php";

if (!isset($_SESSION['email'])) {
    // Redirect to the login page if not logged in
    header("Location: index.php");
    exit();
}

if ($_SESSION['type'] == 'user') {
    header("Location: dashboard.php"); 
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
            <li>Contact Us</li>
            <li><a href="admin_dashboard.php">Home</a></li> 
            <li><a href="logout.php" onclick="">Logout</a></li>
        </ul>
    </nav>
    </header>

    <div class="main-dashboard">
       <div class="page-container">
            <h2><a href="admin_dashboard.php"><b><-</b></a></h2>
            <h2>Account Registration Approval</h2>
            
        </div>
       <div class="table-container">
            <table class="approval">
                <tr class="top-table">
                    <th>Requested By</th>
                    <th>Email</th>
                    <th>Register Date</th>
                    <th>Action</th>
                </tr>

                <?php
               
                $sql = "SELECT * FROM users";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                   
                    while ($row = mysqli_fetch_row($result)) {
                        // Check if the user is unverified (row[10] is the is_verified)
                        if ($row[10] == 0) {
                            
                            $givenname = $row[1];  
                            $surname = $row[2];
                            $middlename = $row[3];
                            $date = $row[13];  
                            $email = $row[4]; 
                            $id=$row[0];
                            // Format the registration date
                            $registerdate = date("m/d/Y", strtotime($date));
                            
                            // Get the middle initial
                            $middleinitial = strtoupper($middlename[0]);

                            // Display the row 
                            echo "<tr>";
                            echo "<td>" . $surname . " , " . $givenname . " " . $middleinitial . ". "."</td>";
                            echo "<td>" . $email . "</td>";
                            echo "<td>" . $registerdate . "</td>";
                            echo "<td >
                                <a href='view_id.php?view=" . $id . "' class='action-btn'>View Details</a>   
                                </td>";
                            echo "</tr>";
                            
                            

                        }
                    }
                } else {
                    echo "<tr><td colspan='4'>No unverified accounts found</td></tr>";
                }

                mysqli_close($conn);
                ?>
            </table>
        </div>

</body>
</html>