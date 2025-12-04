
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
            <h2>Official Document Catalog</h2>
        </div>
       
        
         <div class="table-container documents">
            <table class="approval">
                <tr class="top-table">
           
                    <th>ID</th>
                    <th class="doc">Documents Offered</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            

                <?php
                include "db.php";
                if($conn->connect_error){
                    die("Connection failed: " . $conn->connect_error);
                }
                
                $sql = "SELECT * FROM manage_documents";
                $result = $conn->query($sql);
                
                while($row = mysqli_fetch_array($result)){
                
                    $id = $row['doc_id'];
                    $name = $row['doc_name'];
                    $price = number_format((float) $row['price'], 2, '.', '');
                    $status = $row['status'];
                
                    echo "<tr>";
                    echo "<td>" . $id. "</td>";
                    echo "<td>" . $name."</td>";
                    echo "<td>₱" . $price . "</td>";
                    echo "<td>" . $status . "</td>";
                    echo "<td>
                    <a class='text-blue' href='admin_documents.php?id=" . $id . "'>View Details</a>
                        </td>";
                    echo "</tr>";
                }
                $conn->close();
                ?>

            </table>
        </div>

        <div class="bottom doc-btns">
            <a href="admin_create_documents.php?id=<?php echo $id;?>" class="action-btn">Create a New Document</a>
        </div>

    </div>

</body>
</html>