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
            <h2>View Users</h2>
        </div>
       
        <table>
            <tr>
                <th>ID</th>
                <th>Fullname</th>
                <th>Status</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Actions</th>
            </tr>


            <?php
            include "db.php";
            if($conn->connect_error){
                die("Connection failed: " . $conn->connect_error);
            }
            
            $sql = "SELECT * FROM users";
            $result = $conn->query($sql);
            
            
            $conn->close();
            ?>

        </table>
</body>
</html>