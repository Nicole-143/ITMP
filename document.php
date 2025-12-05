<?php
session_start();
include "db.php";

// Require login
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

// Only admins can access
if ($_SESSION['type'] == 'user') {
    header("Location: dashboard.php");
    exit();
}

$givenname = $_SESSION['givenname'] ?? '';

$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    // Clear the message after displaying it
    unset($_SESSION['message']);
}

// --- FETCH PENDING REQUESTS ---
$sql = "
    SELECT 
        r.request_id,
        r.request_date,
        r.status,
        r.delivery_mode,
        r.payment_mode,
        r.payment_status,
        r.status,
        d.doc_name,
        u.surname,
        u.givenname,
        u.middlename
    FROM Requests r
    JOIN Users u ON r.user_id = u.id
    JOIN Document_Types d ON r.doc_id = d.doc_id
    WHERE r.status = 'Pending' OR r.status = 'Processing'
    ORDER BY r.request_date DESC
";

$result = $conn->query($sql);
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
        <h2>Document Request Approval</h2>
    </div>

    <div class="table-container">
        <?php if (!empty($message)): ?>
            <p class="confirmation-message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <table class="approval">
            <tr class="top-table">
                <th>Requested By</th>
                <th>Document Type</th>
                <th>Delivery Mode</th>
                <th>Payment Mode</th>
                <th>Payment Status</th>
                <th>Order Status</th>
                <th>Request Date</th>
                <th>Action</th>
            </tr>

            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $request_id     = $row['request_id'];
                    $request_date   = date("m/d/Y", strtotime($row['request_date']));
                    $status         = $row['status'];
                    $delivery_mode  = $row['delivery_mode'];
                    if ($row['payment_mode']== 'Cash_On_Delivery'){
                            $payment_mode = 'Cash On Delivery';
                        }else if ($row['payment_mode']== 'Wallet'){
                            $payment_mode = 'Wallet';
                        }
                        else{
                                $payment_mode = 'Cash'; // If payment_mode is NULL, show Cash
                        }
                    $payment_status = $row['payment_status'];
                    $order_status = $row['status'];
                    $doc_name       = $row['doc_name'];

                    $surname    = $row['surname'];
                    $given      = $row['givenname'];
                    $middle     = $row['middlename'];
                    $middleinit = strtoupper(substr($middle, 0, 1));

                    $requested_by = "{$surname}, {$given} {$middleinit}.";
                    
                    $link_page = 'view_request.php';
                    if ($order_status == 'Processing') {
                        $link_page = 'document_processing.php';
                    }
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($requested_by); ?></td>
                        <td><?php echo htmlspecialchars($doc_name); ?></td>
                        <td><?php echo htmlspecialchars($delivery_mode); ?></td>
                        <td><?php echo htmlspecialchars($payment_mode); ?></td>
                        <td><?php echo htmlspecialchars($payment_status); ?></td>
                        <td><?php echo htmlspecialchars($order_status); ?></td>
                        <td><?php echo htmlspecialchars($request_date); ?></td>
                        <td>
                            <a href='<?php echo $link_page; ?>?view=<?php echo $request_id ?>' class='text-blue'>View Details</a>   
                        </td>
                    </tr>
                    <?php
                }
            } else {
                echo '<tr><td colspan="7"><b>No pending document requests found</b></td></tr>';
            }

            $conn->close();
            ?>
        </table>
    </div>
</div>
</body>
</html>
