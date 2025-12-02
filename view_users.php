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
       <div class="table-container">
            <table class="approval user-col" >
                <tr class="top-table" >
               
                <th class="spacer-id">ID</th>
                <th class="spacer-name">Full Name</th>
                <th class="spacer-email">Email</th>
                <th class="spacer-phone">Phone</th>
                <th class="spacer-status">Status</th>
                <th class="spacer-actions">Actions</th>

                </tr>

                <?php

                include "db.php";
                
                $sql = "SELECT * FROM users";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                   
                    while ($row = mysqli_fetch_row($result)) {
                        
                        

                            if ($row[11]=='user')
                            {
                        $id = $row[0];
                        $givenname = $row[1];  
                        $surname = $row[2];
                        $middlename = $row[3];
                        $email = $row[4];
                        $phone = $row[6];
                        $status = (($row[10] == 1)? 'Verified' : 'Unverified');
                    
                        

                            // Display the row 
                            echo "<tr>";
                            echo "<td>" . $id. "</td>";
                            echo "<td>" . $surname . " , " . $givenname . " " . $middlename."</td>";
                            echo "<td>" . $email . "</td>";
                            echo "<td>" . $phone . "</td>";
                            echo "<td>" . $status . "</td>";
                            
                            echo "<td >
                                <a href='edit_id.php?view=" . $id . "' class='text-blue'>Edit</a>   
                                </td>";
                            echo "</tr>";
                        }
                        
                        
                    }
                } else {
                    echo "<tr><td colspan='7'>No unverified accounts found</td></tr>";
                }

                mysqli_close($conn);
                ?>
            </table>
        </div>
        
</body>
</html>