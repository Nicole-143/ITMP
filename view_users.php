<?php
session_start();
include "db.php";

if (!isset($_SESSION['email']) || $_SESSION['type'] == 'user') {
    header("Location: index.php");
    exit();
}

// Fetch all users
$sql = "SELECT id, givenname, surname, middlename, email, phone, is_verified, type 
        FROM Users ORDER BY id ASC";
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

    <style>
        /* Fix button layout */
        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .action-btn {
            padding: 6px 12px;
            background-color: #004aad;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
        }

        .delete-btn {
            background-color: #b30000;
        }

        .action-btn:hover {
            opacity: 0.8;
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
    <div class="page-container">
        <h2><a href="admin_dashboard.php"><b><-</b></a></h2>
        <h2>View Users</h2>
    </div>

    <table>
        <tr>
            <th>ID</th>
            <th>Fullname</th>
            <th>User Type</th>
            <th>Status</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Actions</th>
        </tr>

        <?php
        if ($result && $result->num_rows > 0):
            while ($row = $result->fetch_assoc()):
                $fullname = $row["givenname"] . " " . $row["middlename"] . " " . $row["surname"];

                $status = $row['is_verified'] == 1 ? "Verified" : "Unverified";
                $userType = ($row['type'] == "admin") ? "Admin" : "Resident";
        ?>

        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($fullname) ?></td>
            <td><?= $userType ?></td>
            <td><?= $status ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['phone']) ?></td>
            <td>
                <div class="action-buttons">

                    <a href="view_user_details.php?id=<?= $row['id'] ?>" class="action-btn">
                        View
                    </a>

                    <?php if ($row['type'] != 'admin'): ?>
                    <!-- Only allow deleting RESIDENT accounts -->
                    <a href="delete_user.php?id=<?= $row['id'] ?>"
                       class="action-btn delete-btn"
                       onclick="return confirm('Are you sure you want to delete this user? This cannot be undone.');">
                        Delete
                    </a>
                    <?php endif; ?>

                </div>
            </td>
        </tr>

        <?php
            endwhile;
        else:
            echo "<tr><td colspan='7'>No users found.</td></tr>";
        endif;

        $conn->close();
        ?>

    </table>
</div>

</body>
</html>
