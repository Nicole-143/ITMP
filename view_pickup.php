<?php
session_start();
include "db.php";

if (!isset($_SESSION['email']) || $_SESSION['type'] == 'user') {
    header("Location: index.php");
    exit();
}

/* ------------------------------
   HANDLE UPDATE PICKUP STATUS
--------------------------------*/
if (isset($_POST['update_pickup'])) {
    $request_id = intval($_POST['request_id']);
    $pickup_status = $_POST['pickup_status'];
    $pickup_date = !empty($_POST['pickup_date']) ? "'" . $_POST['pickup_date'] . "'" : "NULL";

    // If picked up → also set status to Released
    if ($pickup_status == "Picked Up") {
        $sql = "
            UPDATE Requests 
            SET pickup_status = 'Picked Up',
                pickup_date = $pickup_date,
                status = 'Released',
                payment_status = 'Paid'
            WHERE request_id = $request_id
        ";
    } else {
        // If changed back to Not Picked Up
        $sql = "
            UPDATE Requests 
            SET pickup_status = 'Not Picked Up',
                pickup_date = NULL
            WHERE request_id = $request_id
        ";
    }

    mysqli_query($conn, $sql);

    header("Location: view_pickup.php?updated=1");
    exit();
}

/* ------------------------------
   FETCH NOT PICKED UP REQUESTS
--------------------------------*/
$sql_not = "
    SELECT r.request_id, r.pickup_status, r.request_date, r.copies, 
           r.pickup_date, u.givenname, u.surname,u.middlename, dt.doc_name
    FROM Requests r
    LEFT JOIN Users u ON u.id = r.user_id
    LEFT JOIN Document_Types dt ON dt.doc_id = r.doc_id
    WHERE r.delivery_mode = 'Pick-up'
    AND r.pickup_status = 'Not Picked Up'
    AND (r.status = 'Approved' OR r.status = 'Ready for Pick-up')
    ORDER BY r.request_date DESC
";

$not_picked = mysqli_query($conn, $sql_not);

/* ------------------------------
   FETCH PICKED UP REQUESTS
--------------------------------*/
$sql_yes = "
    SELECT r.request_id, r.pickup_status, r.request_date, r.copies, 
           r.pickup_date, u.givenname, u.surname, u.middlename, dt.doc_name
    FROM Requests r
    LEFT JOIN Users u ON u.id = r.user_id
    LEFT JOIN Document_Types dt ON dt.doc_id = r.doc_id
    WHERE r.delivery_mode = 'Pick-up'
    AND r.pickup_status = 'Picked Up'
    ORDER BY r.pickup_date DESC
";

$picked = mysqli_query($conn, $sql_yes);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pickup Documents - Admin</title>
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
            <li><a href="admin_dashboard.php">Home</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
</header>

<div class="main-admin-dashboard user">
    <div class="admin-container">

        <div class="page-container">
            <h2><a href="admin_dashboard.php"><b><-</b></a></h2>
            <h2>Pick-up Document Requests</h2>
            
        </div>

        <?php if (isset($_GET['updated'])): ?>
            <p class="success-message" style="color: green;">Pickup status updated successfully.</p>
        <?php endif; ?>

        <!-- ============================
             TABLE 1: NOT PICKED UP
        ============================= -->
        <h3>Not Picked Up</h3>
        
        <table class="approval user">
            <thead>
                <tr class="top-table">
                    <th class="spacer-rid">Request ID</th>
                    <th class="spacer-name">User Name</th>
                    <th >Document</th>
                    <th class="spacer-id">Copies</th>
                    <th>Request Date</th>
                    <th>Pickup Date</th>
                    <th>Pickup Status</th>
                    <th class="spacer-action">Action</th>
                </tr>
            </thead>

            <tbody>
                <?php if (mysqli_num_rows($not_picked) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($not_picked)): 
                        
                        
                        $middlename = $row['middlename'];
                        $middleinitial = !empty($middlename) ? strtoupper($middlename[0]) : '';

                        ?>
                        <tr>
                            <td><?= $row['request_id'] ?></td>

                            
                            <td><?= $row['surname'].", ".$row['givenname']." ".$middleinitial."." ?></td>
                            <td><?= $row['doc_name'] ?></td>
                            <td><?= $row['copies'] ?></td>
                            <td><?= date("M d, Y h:i A", strtotime($row['request_date'])) ?></td>

                            <form action="view_pickup.php" method="POST">
                                <td>
                                    <div class="pick-option">
                                    <input type="datetime-local" 
                                           name="pickup_date" 
                                           value="<?= $row['pickup_date'] ? date('Y-m-d\TH:i', strtotime($row['pickup_date'])) : '' ?>">
                                    </div>
                                </td>

                                <td>
                                    <div class="pick-option">
                                    <input type="hidden" name="request_id" value="<?= $row['request_id'] ?>">
                                    <select name="pickup_status" class="display-amount">
                                        <option value="Not Picked Up" selected>Not Picked Up</option>
                                        <option value="Picked Up">Picked Up</option>
                                    </select>
                                    </div>
                                </td>

                                <td>
                                    <div class="request-container">
                                    <button type="submit" name="update_pickup" class="request-btn user">Update</button>
                                    </div>
                                </td>
                            </form>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="8">No documents awaiting pickup.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <!-- ============================
             TABLE 2: PICKED UP
        ============================= -->
        <h3 style="margin-top: 40px;">Picked Up Documents</h3>

        <table class="approval user">
            <thead>
                <tr class="top-table">
                    <th class="spacer-rid">Request ID</th>
                    <th class="spacer-name">User Name</th>
                    <th>Document</th>
                    <th class="spacer-id">Copies</th>
                    <th>Request Date</th>
                    <th>Pickup Date</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                <?php if (mysqli_num_rows($picked) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($picked)): 
                        
                        $middlename = $row['middlename'];
                        $middleinitial = !empty($middlename) ? strtoupper($middlename[0]) : '';

                        ?>
                        <tr>
                            <td ><?= $row['request_id'] ?></td>
                            <td><?= $row['surname'].", ".$row['givenname']." ".$middleinitial."." ?></td>
                            <td><?= $row['doc_name'] ?></td>
                            <td><?= $row['copies'] ?></td>
                            <td>
                            
                            <?= date("M d, Y h:i A", strtotime($row['request_date'])) ?>
                            </td>
                            <td><?= date("M d, Y h:i A", strtotime($row['pickup_date'])) ?></td>
                            <td><b>Picked Up</b></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7">No picked up documents yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

    </div>
</div>

</body>
</html>
