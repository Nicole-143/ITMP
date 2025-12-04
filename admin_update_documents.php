<?php 
include "db.php";
session_start(); 

$error_message = '';
$max_requirements = 4;

if(isset($_GET['id'])){
    $id = mysqli_real_escape_string($conn, $_GET['id']);
}

//UPDATE LOGIC
if (isset($_POST['update'])) {
    $id = mysqli_real_escape_string($conn, $_POST['doc_id']);
    $name = mysqli_real_escape_string($conn, $_POST['doc_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $shipping_fee = mysqli_real_escape_string($conn, $_POST['shipping_fee']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    if(empty($name) || empty($description) || $price === '' || $shipping_fee === '' || $price < 0 || $shipping_fee < 0) {
        $error_message = "Please fill in all required fields and ensure price/shipping are non-negative.";
    } else {

        //validate the requirements selection
        $submitted_requirements = [];
        $has_duplicates = false;

        for($i = 1; $i <= $max_requirements; $i++){
            $req_field = 'requirement_' . $i;

            if(isset($_POST[$req_field]) && !empty($_POST[$req_field])){
                $req_name_check = trim($_POST[$req_field]);

                if(in_array($req_name_check, $submitted_requirements)){
                    $has_duplicates = true;
                    $error_message = "Duplicate requirements selected. Please ensure each requirement is unique.";
                    break; 
                }
                $submitted_requirements[] = $req_name_check;
            }
        }
    }

    // Update only if no errors
    if(empty($error_message)){
        $sql_update_doc = "UPDATE Document_Types SET 
        doc_name='$name', description='$description', price='$price', shipping_fee='$shipping_fee', status='$status' WHERE doc_id=$id";

        if(mysqli_query($conn, $sql_update_doc)){

            // Using document_requirements based on previous code usage
            $sql_reqs_delete = "DELETE FROM Doc_Type_Requirements WHERE doc_id=$id"; 
            mysqli_query($conn, $sql_reqs_delete);

            $reqs_insert_success = true;

            foreach($submitted_requirements as $req_name_to_insert){
                $req_name_to_insert_safe = mysqli_real_escape_string($conn, $req_name_to_insert);
                $req_id = 0;
                
                switch($req_name_to_insert){ 
                    case 'Government-issued ID':
                        $req_id = 1;
                        break;
                    case 'Community Tax Certificate (Cedula)':
                        $req_id = 2;
                        break;
                    case 'Proof of Residency (e.g., utility bill or lease agreement)':
                        $req_id = 3;
                        break;
                    case 'Proof of income':
                        $req_id = 7;
                        break;
                    default: 
                        continue 2; // Skip unknown requirements
                }
                    
                if ($req_id > 0) {
                    $sql_reqs_insert = "INSERT INTO Doc_Type_Requirements (doc_id, req_id) VALUES ($id, $req_id)";
                    
                    if (!mysqli_query($conn, $sql_reqs_insert)) {
                        $reqs_insert_success = false;
                        $error_message = "Error inserting requirement: " . mysqli_error($conn);
                        break;
                    }
                }
            }
                
            if ($reqs_insert_success) {
                // If success go to the mismong docu page
                header("Location: admin_documents.php?id=$id"); 
                exit();
            }
        } else {
            $error_message = "Error updating document details: " . mysqli_error($conn);
        }
    }
}


if (!isset($_GET['id'])) {
    header("Location: admin_documents.php?id=$id"); 
    exit();
}

//document details
$result = mysqli_query($conn, "SELECT * FROM manage_documents WHERE doc_id = $id");
$row = mysqli_fetch_array($result);

if (!$row) {
    header("Location: admin_documents.php?id=$id");
    exit();
}

//document requirements
$result_req = mysqli_query($conn, "SELECT req_name FROM document_requirements WHERE doc_id = $id");
$requirements_data = [];
while ($req_row = mysqli_fetch_array($result_req)) {
    $requirements_data[] = $req_row['req_name']; 
}
$req_number = count($requirements_data); 

//all requirements for the select dropdowns
$result_all_req = mysqli_query($conn, "SELECT DISTINCT req_name FROM document_requirements");
$all_requirements = [];
while ($all_req_row = mysqli_fetch_array($result_all_req)) {
    $all_requirements[] = $all_req_row['req_name'];
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
        <h2>Official Document Catalog - Edit Document</h2>
        </div>

        <?php if (!empty($error_message)): ?>
            <div class="confirmation-message confirm">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">

        <div class="info-container shipping-box top-space">
                <h1>Official Document Catalog</h1>
                <div class="order-summary"> 
                    <table class="order-table">
                        <tr>
                            <td>Document ID</td>
                            <td>
                                <input type="text" name="doc_id" value="<?php echo $row['doc_id']; ?>" placeholder="Ex. Community Tax Certificate (Cedula)" readonly> 
                            </td>
                        </tr>
                        <tr>
                            <td>Document Name</td>
                            <td>
                                <input type="text" name="doc_name" value="<?php echo htmlspecialchars($row['doc_name']); ?>" placeholder="Ex. Proof of tax payment and identity, often required for official transactions." required>
                            </td>
                        </tr>
                        <tr>
                            <td>Description</td>
                            <td>
                                <input type="text" name="description" value="<?php echo htmlspecialchars($row['description']); ?>" placeholder="Ex. 20.00" min="0" step="0.01" required>
                            </td>
                        </tr>
                        <tr>
                            <td>Price</td>
                            <td>
                                <input type="number" name="price" value="<?php echo $row['price']; ?>" placeholder="Ex. 20.00" min="0" step="0.01" required>
                            </td>
                        </tr>
                        <tr>
                            <td>Shipping Fee</td>
                            <td>
                                <input type="number" name="shipping_fee" value="<?php echo $row['shipping_fee']; ?>" placeholder="Ex. 20.00" min="0" step="0.01" required>
                            </td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>
                                <select name="status" required>
                                    <option value="Active" <?php if($row['status'] == 'Active') echo 'selected'; ?>>Active</option>
                                    <option value="Inactive" <?php if($row['status'] == 'Inactive') echo 'selected'; ?>>Inactive</option>
                                </select>
                            </td>
                        </tr>
                    </table>

                    <div class="item-summary">
                        <table class="item-table">
                            <tr>
                                <th>Total Requirements</th>
                                <th class="items-title">
                                    </th>
                            </tr>
                            <?php
                            $used_requirements = []; //Array for tracking used requirements
                            
                            for($i = 0; $i < $max_requirements; $i++){
                                $current_req_name = isset($requirements_data[$i]) ? $requirements_data[$i] : ''; 
                                $display_index = $i + 1;

                                echo "<tr>";
                                echo "<td>" . $display_index . ".</td>";
                                echo "<td>";
                                echo "<select name='requirement_" . $display_index . "'>";
                                echo "<option value=''>-- Select Requirement --</option>";
                                
                                foreach($all_requirements as $requirement){
                                    $selected = ($requirement == $current_req_name) ? 'selected' : '';

                                    echo "<option value='" . htmlspecialchars($requirement) . "' $selected>" . htmlspecialchars($requirement) . "</option>";
                                }
                                
                                echo "</select>";
                                echo "</td>";
                                echo "</tr>";
                                
                                // Track used requirements to prevent duplicates
                                if (!empty($current_req_name)) {
                                    $used_requirements[] = $current_req_name;
                                }
                            }
                            ?>

                        </table>
                    </div>
                    
                </div> 
        
        </div>

        <div class="bottom doc-btns">
            <a href="admin_documents.php?id=<?php echo $row['doc_id'];?>" class="back-btn">Cancel</a>
            <button type="submit" name="update" class="action-btn">Update Document</button>
        </div>

    </div>
    
</body>
</html>