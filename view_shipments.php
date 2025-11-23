
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
            <h2>View Shipments</h2>
        </div>
       
        <div class="view-table">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Requested By</th>
                    <th>Order</th>
                    <th>Request Date</th>
                    <th>Shipping Date</th>
                    <th>Arrival Date</th>
                    <th>Action</th>
                </tr>
            

            <?php
            include "db.php";
            if($conn->connect_error){
                die("Connection failed: " . $conn->connect_error);
            }
            
            $sql = "SELECT * FROM manage_shipments";
            $result = $conn->query($sql);
            
            while($row = mysqli_fetch_array($result)){
            
                $id = $row['request_id'];
                $surname = $row['surname'];
                $givenname = $row['givenname'];
                $middlename = $row['middlename'];
                
                $order = $row['status'];
                $r_date = $row['request_date'];
                $s_date = $row['shipping_date'];
                $a_date = $row['arrival_date'];
            
                // Get the middle initial
                $middleinitial = strtoupper($middlename[0]);
            
                // Format the dates
                $registerdate = date("m/d/Y", strtotime($r_date));
                $shipping_date = $s_date ? date("m/d/Y", strtotime($s_date)) : 'N/A';
                $arrival_date = $a_date ? date("m/d/Y", strtotime($a_date)) : 'N/A';
                                        
                echo "<tr>";
                echo "<td>" . $id. "</td>";
                echo "<td>" . $surname . " , " . $givenname . " " . $middleinitial . ". "."</td>";
                echo "<td>" . $order . "</td>";
                echo "<td>" . $registerdate . "</td>";
                echo "<td>" . $shipping_date . "</td>";
                echo "<td>" . $arrival_date . "</td>";
                echo "<td>
                <a class='action' href='admin_shipments.php?id=" . $id . "'>View Details</a>
                      </td>";
                echo "</tr>";
            }
            $conn->close();
            ?>

        </table>
        </div>
</body>
</html>