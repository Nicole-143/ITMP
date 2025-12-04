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
    <title>User Details - Townsville Barangay System</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

    <style>
        .details-card {
            max-width: 700px;
            margin: 20px auto;
            background: #ffffff;
            padding: 24px 28px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .details-card h2 {
            margin-top: 0;
            margin-bottom: 10px;
        }

        .details-grid {
            width: 100%;
            border-collapse: collapse;
        }

        .details-grid td {
            padding: 8px 4px;
            vertical-align: top;
        }

        .details-grid td.label {
            font-weight: 600;
            width: 160px;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 16px;
            text-decoration: none;
            font-weight: 600;
        }

        .id-link {
            color: #004aad;
            text-decoration: underline;
        }
    </style>
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
    <div class="details-card">
        <a href="view_users.php" class="back-link">&larr; Back to Users</a>

        <h2>User Details</h2>
        <p><strong>User ID:</strong> <?= $user['id']; ?></p>

        <table class="details-grid">
            <tr>
                <td class="label">Full Name</td>
                <td><?= htmlspecialchars($fullname); ?></td>
            </tr>
            <tr>
                <td class="label">User Type</td>
                <td><?= $userType; ?></td>
            </tr>
            <tr>
                <td class="label">Status</td>
                <td><?= $status; ?></td>
            </tr>
            <tr>
                <td class="label">Email</td>
                <td><?= htmlspecialchars($user['email']); ?></td>
            </tr>
            <tr>
                <td class="label">Phone</td>
                <td><?= htmlspecialchars($user['phone']); ?></td>
            </tr>
            <tr>
                <td class="label">Address</td>
                <td><?= htmlspecialchars($user['address']); ?></td>
            </tr>
            <tr>
                <td class="label">Sex</td>
                <td><?= htmlspecialchars($user['sex']); ?></td>
            </tr>
            <tr>
                <td class="label">Birthdate</td>
                <td><?= $birthdate; ?></td>
            </tr>
            <tr>
                <td class="label">Registered On</td>
                <td><?= $registered; ?></td>
            </tr>
            <tr>
                <td class="label">Admin Comment</td>
                <td><?= $user['comment'] ? htmlspecialchars($user['comment']) : '—'; ?></td>
            </tr>
            <tr>
                <td class="label">Uploaded ID</td>
                <td>
                    <?php if (!empty($user['upload_id'])): ?>
                        <a href="uploads/<?= urlencode($user['upload_id']); ?>" target="_blank" class="id-link">
                            View Uploaded ID
                        </a>
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
