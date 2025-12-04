<?php
session_start();
include "db.php";

if (!isset($_SESSION['email']) || $_SESSION['type'] == 'user') {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid user ID.");
}

$user_id = intval($_GET['id']);

$sql = "SELECT id, givenname, surname, middlename, email, phone, address,
               sex, birthdate, is_verified, type, upload_id, registerdate, comment
        FROM Users
        WHERE id = $user_id";

$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) === 0) {
    die("User not found.");
}

$user = mysqli_fetch_assoc($result);
mysqli_close($conn);

// Helper values
$fullname   = $user['givenname'] . ' ' . $user['middlename'] . ' ' . $user['surname'];
$status     = $user['is_verified'] == 1 ? "Verified" : "Unverified";
$userType   = ($user['type'] == 'admin') ? "Admin" : "Resident";
$birthdate  = $user['birthdate'] ? date("M d, Y", strtotime($user['birthdate'])) : "—";
$registered = $user['registerdate'] ? date("M d, Y h:i A", strtotime($user['registerdate'])) : "—";
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
            <h2><a href="view_users.php"><b><-</b></a></h2>
            <h2>Back To Users</h2>
        </div>

        <div class="approval user">
            <table class="info-table user">
                <tr class="top-table user">
                    <th colspan="2">User Details</th>
                </tr>

                <tr>
                    <td class="detail"><b>Full Name</b></td>
                    <td><?= htmlspecialchars($fullname); ?></td>

                </tr>
                <tr>
                    <td><b>User Type</b></td>
                    <td><?= $userType; ?></td>
                </tr>
                <tr>
                    <td><b>Status</b></td>
                    <td><?= $status; ?></td>
                </tr>
                <tr>
                    <td><b>Email</b></td>
                    <td><?= htmlspecialchars($user['email']); ?></td>
                </tr> 
                <tr>
                    <td><b>Phone</b></td>
                    <td><?= htmlspecialchars($user['phone']); ?></td>
                </tr> 
                <tr>
                    <td><b>Home Address</b></td>
                    <td><?= htmlspecialchars($user['address']); ?></td>
                </tr>  
                <tr>
                    <td><b>Sex</b></td>
                    <td><?= htmlspecialchars($user['sex']); ?></td>
                </tr> 
                <tr>
                    <td><b>Birthdate</b></td>
                    <td><?= $birthdate; ?></td>
                </tr>
                <tr>
                    <td><b>Registered On</b></td>
                    <td><?= $registered; ?></td>
                </tr>
                <tr>
                    <td><b>Admin Comment</b></td>
                    <td><?= $user['comment'] ? htmlspecialchars($user['comment']) : '—'; ?></td>
                </tr>
                <tr>
                    <td><b>Uploaded ID</b></td>
                    <td>
                        <?php if (!empty($user['upload_id'])): ?>
                            <div class="img-container">

                            <img src="./uploads/<?= urlencode($user['upload_id']); ?>" />
                            </div>
                            <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                </tr>
            </table>

        </div>
    </div>

</body>
</html>