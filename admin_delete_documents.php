
<?php 
    include "db.php";
    session_start(); 

    
    if (!isset($_GET['id'])) {
        header("Location: manage_documents.php");
        exit();
    }

    $doc_id = $_GET['id'];
    $id_delete = $doc_id; //for delete logic

    // Fetch document details from the database
    $sql = "SELECT * FROM manage_documents WHERE doc_id = ?";
    
    $stmt = $conn->prepare($sql);

    $stmt->bind_param("i", $doc_id); // i means integer iinsert
    $stmt->execute();
    $result = $stmt->get_result();
    
    $row = $result->fetch_assoc();
    
    $id = $row['doc_id'];
    $name = $row['doc_name'];
    $description = $row['description'];
    $price = number_format((float) $row['price'], 2, '.', '');
    $shipping_fee = number_format((float) $row['shipping_fee'], 2, '.', '');
    $status = $row['status'];

    $stmt->close();

    // Deletion logic
    if (isset($_POST['delete'])) {
        $conn->begin_transaction();

        try{
            //delete the requirements associated with the document
            $sql_req_delete = "DELETE FROM Doc_Type_Requirements WHERE doc_id= ?";
            $stmt_req_delete = $conn->prepare($sql_req_delete);
            $stmt_req_delete->bind_param("i", $id_delete);
            $stmt_req_delete->execute();
            $stmt_req_delete->close();

            //delete the document
            $sql_delete = "DELETE FROM Document_Types WHERE doc_id= ?";
            $stmt_delete = $conn->prepare($sql_delete);
            $stmt_delete->bind_param("i", $id_delete);
            $stmt_delete->execute();
            $stmt_delete->close();

            $conn->commit();
            header("Location: manage_documents.php");
            exit();
        } catch (mysqli_sql_exception $e){ {
            $conn->rollback();
            echo "Error deleting record: " . $e->getMessage();
        }
        }
    }

//    $conn->close();
    
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
        <h2><a href="manage_documents.php"><b>&lt;-</b></a></h2>
        <h2>Official Document Catalog - Delete Document</h2>
        </div>

        <div class="info-container shipping-box top-space">
                <h3 class="confirm">Are you sure, you want to delete this document?</h3>
                <div class="order-summary"> 
                    <table class="order-table">
                        <tr>
                            <td>Document ID</td>
                            <td><b class="text-blue"><?php echo $id; ?></b></td>
                        </tr>
                        <tr>
                            <td>Document Name</td>
                            <td><b class="text-blue"><?php echo $name; ?></b></td>
                        </tr>
                        <tr>
                            <td>Description</td>
                            <td><b class="text-blue"><?php echo $description; ?></b></td>
                        </tr>
                        <tr>
                            <td>Price</td>
                            <td><b class="text-blue">₱<?php echo $price; ?></b></td>
                        </tr>
                        <tr>
                            <td>Shipping Fee</td>
                            <td><b class="text-blue">₱<?php echo $shipping_fee; ?></b></td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td><b class="text-blue"><?php echo $status; ?></b></td>
                        </tr>
                    </table>

                    <div class="item-summary">
                        <table class="item-table">

                            <?php
                            //get the requirement/s foc that document
                            $sql_req = "SELECT req_name FROM document_requirements WHERE doc_id = ?";
                            $stmt = $conn->prepare($sql_req);
                            $stmt->bind_param("i", $doc_id);
                            $stmt->execute();
                            $result_req = $stmt->get_result();

                            $req_number = $result_req->num_rows;

                      
                            echo "<tr>";
                            echo "<td>Total Requirements</td>";
                            echo "<th class='items-title'>" . $req_number . "</th>";
                            echo "</tr>";

                            $i = 1;
                            while($row_req = mysqli_fetch_array($result_req)){
                                
                                $requirement_name = $row_req['req_name'];

                                echo "<tr>";
                                echo "<td>" . $i . ".</td>";
                                echo "<td><b class='text-blue'>" . $requirement_name . "</b></td>";
                                echo "</tr>";

                                $i++;
                             }
                             $stmt->close();
                             $conn->close();

                            ?>

                        </table>
                    </div>
                    
                </div> 
        
        </div>

        <form method="POST" action="admin_delete_documents.php?id=<?php echo $id; ?>" class="bottom doc-btns">
            <a href="admin_documents.php?id=<?php echo $id;?>" class="back-btn">Cancel</a>
            <button type="submit" name="delete" class="delete-btn">Delete Permanently</button>
        </form>

    </div>

</body>
</html>