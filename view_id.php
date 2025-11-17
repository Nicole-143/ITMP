
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


$givenname = $_SESSION['givenname']; 


  if (isset($_GET['view'])) {
    $id = $_GET['view'];             
                $sql = "SELECT * FROM users WHERE id = $id";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                        $row = mysqli_fetch_row($result);
                        
                        $givenname = strtoupper($row[1]);  
                        $surname = strtoupper($row[2]);
                        $middlename = strtoupper($row[3]);
                        $email= $row[4];
                        $phone=$row[6];
                        $address=strtoupper($row[7]);
                        $sex=strtoupper($row[8]);
                        $birthdate=date("m/d/Y", strtotime($row[9]));
                        $is_verified = $row[10];
                        $uploadid=$row[12];
                        $comment=$row[14];
                    }        
                

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        
        // Get comment from textarea
        $comment = $_POST['comment'];
        $verified = 0;
        
        if (isset($_POST['approve'])) {
            $verified = 1;  // Mark as approved
        } 

        // Update the database with the new verification status and comment
        $updateSql = "UPDATE users SET is_verified = $verified, comment = '$comment' WHERE id = $id";
        
        if(mysqli_query($conn, $updateSql )) {
            // Redirect back to the admin dashboard 
            header("Location: approval.php"); 
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }

    mysqli_close($conn);
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
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
    </header>

    <div class="main-dashboard">
        <div class="page-container">
            <h2><a href="approval.php"><b><-</b></a></h2>
            <h2>Account Registration Approval</h2>
            
        </div>
        
        <div class="info-container">

            <h4>User Registration Approval</h4>

            <table class="info-table">
                <tr>
                    <td><b>Last Name</b></td>
                    <?php echo '<td class="blue">'.$surname.'<td>'?>

                    <td><b>Sex</b></td>
                    <?php echo '<td class="blue">'.$sex.'<td>'?>

                    <td><b>Mobile Number</b></td>
                    <?php echo '<td class="blue">'.$phone.'<td>'?>
                </tr>
                <tr>
                    <td><b>Given Name</b></td>
                    <?php echo '<td class="blue">'.$givenname.'<td>'?>
                    <td><b>Birthdate</b></td>
                    <?php echo '<td class="blue">'.$birthdate.'<td>'?>
                    <td><b>Email</b></td>
                    <?php echo '<td class="blue">'.$email.'<td>'?>
                </tr>
                <tr>
                    <td><b>Home Address</b></td>
                    <?php echo '<td class="blue">'.$address.'<td>'?>
                </tr>  
            </table>

            <h4>Government ID</h4>
            <?php echo '<img  src="./uploads/'.$uploadid.'">'?>
            
            <h4>Add Comment</h4>
            <form action="view_id.php?view=<?php echo $id; ?>" method="POST">
            <textarea id="comment" name="comment"><?php echo htmlspecialchars($comment); ?></textarea>   
            
            <div class="action-btns">
                <button type="submit" name= "approve" class="approve-btn">Approve</button>
                <button type="submit" name= "deny" class="deny-btn">Deny</button>
            </div>
            </form>
        </div>

</body>
</html>