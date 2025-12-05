
<?php 
include "db.php";
    session_start(); 

    
    if (!isset($_GET['id'])) {
        header("Location: view_shipments.php");
        exit();
    }

    $request_id = $_GET['id'];

    // to handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_action'])) {
    // if checkbox is not checked it will just stay on the same page
    if (!isset($_POST['confirm']) || $_POST['confirm'] !== 'confirm') {
        $_SESSION['error_message'] = "Please check the confirmation box before proceeding.";
        header("Location: admin_shipments.php?id=" . $request_id);
        exit();
    }
    
    $action_type = $_POST['action_type'];
    $id = $_POST['request_id'];
    
    // Redirect based on action type
    switch($action_type) {
        case 'ship':
            header("Location: update_user_shipment.php?id=" . $id);
            exit();
        case 'arrival':
            header("Location: update_user_arrival.php?id=" . $id);
            exit();
        default:
            header("Location: view_shipments.php");
            exit();
    }
}

    // Fetch shipment details from the database
    $sql = "SELECT * FROM admin_shipments WHERE request_id = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("i", $request_id); // i means integer iinsert
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows == 0){
        echo "No shipment found with the given ID.";
        exit();
    }

    $row = $result->fetch_assoc();
    
    $id = $row['request_id'];

    $surname = $row['surname'];
    $givenname = $row['givenname'];
    $middlename = $row['middlename'];
    $requested_by = $surname . ", " . $givenname . " " . strtoupper($middlename[0]) . ".";

    $mobile_number = $row['phone'];
    $email = $row['email'];
    $address = $row['address'];

    $order = $row['status'];
      
    $s_date = $row['shipping_date'];
    $a_date = $row['arrival_date'];
    $shipping_date = $s_date ? date("m/d/Y h:i A", strtotime($s_date)) : 'N/A';
    $arrival_date = $a_date ? date("m/d/Y h:i A", strtotime($a_date)) : 'N/A';
    
    $payment_mode = $row['payment_mode'];
    $payment_status = $row['payment_status'];

    $document_name = $row['doc_name'];
    $quantity = $row['copies'];
    $price = $row['price'];
    $shipping_fee = $row['shipping_fee'];

    $stmt->close();
    $conn->close();


    //For the action buttons and message
    $message = "Please confirm that shipment has been initiated.";
    $button_text = "Ship Request";
    $action_page = "update_user_shipment.php";
    $action_type = "ship";
    $show_button = true;

    if ($order === 'Shipping') {
        $message = "Please confirm that the shipment has arrived.";
        $button_text = "Shipment Has Arrived";
        $action_page = "update_user_arrival.php";
        $action_type = "arrival";
        $show_button = true;
    } elseif ($order === 'Released') {
        $message = "Order Complete.";
        $button_text = "Completed";
        $action_page = "#";
        $action_type = "";
        $show_button = false; // no button
    }

    $error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
    unset($_SESSION['error_message']);
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
        <h2><a href="view_shipments.php"><b>&lt;-</b></a></h2>
        <h2>Document Requests - View Shipments</h2>
        </div>

        <div class="info-container shipping-box top-space">
                <h1>Order Summary</h1>
                <div class="order-summary"> 
                    <table class="order-table">
                        <tr>
                            <td>Request ID</td>
                            <td><b class="text-blue"><?php echo $id; ?></b></td>
                        </tr>
                        <tr>
                            <td>Requested By</td>
                            <td><b class="text-blue"><?php echo $requested_by; ?></b></td>
                        </tr>
                        <tr>
                            <td>Mobile Number</td>
                            <td><b class="text-blue"><?php echo $mobile_number; ?></b></td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td><b class="text-blue"><?php echo $email; ?></b></td>
                        </tr>
                        <tr>
                            <td>Address</td>
                            <td><b class="text-blue"><?php echo $address; ?></b></td>
                        </tr>
                    </table>

                    <div class="item-summary">
                        <table class="item-table">
                            <tr>
                                <th class="items-title">Items</th>
                            <tr>
                                <td><b class="text-blue"><?php echo $document_name; ?></b></td>
                                <td><b class="text-blue">x <?php echo $quantity; ?></b></td>
                                <td class="to-right"><b class="text-blue">₱ <?php echo number_format($price,2); ?></b></td>
                            </tr>

                            <tr >
                                <td ><b class="text-blue">Shipping Fee<b></td>
                                <td></td>
                                <td class="to-right"><b class="text-blue">₱ <?php echo number_format($shipping_fee,2); ?></b></td>
                            </tr>
                            <tr class="total-row">
                                <td>Total</td>
                                <td></td>
                                <td class="the-right"><b class="text-blue">₱ <?php 
                                $total = (float) $shipping_fee + ($price * $quantity);
                                echo number_format($total, 2, '.', ''); ?></b></td>
                        </table>
                    </div>

                </div> 
                <div class="details-container"> 
                    <table class="order-table ">
                        <tr>
                            <td>Order Status</td>
                            <td><b class="text-blue"><?php echo $order; ?></b></td>
                            <td >Shipping Date</td>
                            <td><b class="text-blue"><?php echo $shipping_date; ?></b></td>
                            <td>Arrival Date</td>
                            <td><b class="text-blue"><?php echo $arrival_date; ?></b></td>
                        </tr>
                        <tr>
                            <td>Payment Mode</td>
                            <td><b class="text-blue"><?php echo $payment_mode; ?></b></td>
                            <td>Payment Status</td>
                            <td><b class="text-blue"><?php echo $payment_status; ?></b></td>
                        </tr>
                        
                    </table>            
                </div> 

                <?php if ($show_button): ?>
                    <p class="confirmation-message confirm"><?php echo $message; ?></p>

                    <form method="POST" action="<?php echo $action_page; ?>">
                    <input type="hidden" name="request_id" value="<?php echo $id; ?>">
                    <input type="hidden" name="action_type" value="<?php echo $action_type; ?>">
                    <input type="hidden" name="confirm_action" value="1">
                    
                    <div class="checkbox-container">
                        <input type="checkbox" id="confirm" name="confirm" value="confirm">
                        <label for="confirm" class="confirm-text">I confirm.</label>
                    </div>
                    
                    
                    <div class="bottom doc-btns">
                        <a href="view_shipments.php" class="back-btn">Back to View Shipments</a>
                        <button type="submit" class="action-btn"><?php echo $button_text; ?></button>
                    </div>
                    </form>

                <?php else: ?>
                    <p class="confirmation-message confirm-complete"><?php echo $message; ?></p>

                    <div class="bottom doc-btns">
                        <a href="view_shipments.php" class="back-btn">Back to View Shipments</a>
                        <?php if ($show_button): ?>
                            <a href="<?php echo $action_page; ?>?id=<?php echo $id; ?>" class="action-btn"><?php echo $button_text; ?></a>

                        <?php endif; ?>
                    </div>
                <?php endif; ?>
        </div>

    </div>
    
    
    

</body>
</html>