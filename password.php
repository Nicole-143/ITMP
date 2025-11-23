
<?php 
include "db.php";

    session_start(); 
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    // checks if it matches
    if ($password !== $confirm_password) {
        header("Location: password.php?error=mismatch");
        exit;
    }
    // Password requirements min 8 chars, uppercase, lowercase, number, special char
    $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z0-9]).{8,}$/';
    if (!preg_match($pattern, $password)) {
        header("Location: password.php?error=invalid");
        exit;
    }
    // Retrieving session data
    $givenname = $_SESSION['givenname'];
    $middlename = $_SESSION['middlename'];
    $surname = $_SESSION['surname'];
    $email = $_SESSION['email'];
    $address = $_SESSION['address'];
    $phone = $_SESSION['phone'];
    $sex = $_SESSION['sex'];
    $birthdate = $_SESSION['birthdate'];
    $fileupload = $_SESSION['fileupload'];
    $password = $_POST['password'];

    // SQL query to insert the user data into the database
    $insert = "INSERT INTO users (givenname, surname, middlename, email, password, phone, address, sex, birthdate, is_verified, type, upload_id ) 
           VALUES ('$givenname', '$surname', '$middlename', '$email', '$password', '$phone', '$address', '$sex', '$birthdate', 0, 'user','$fileupload')";

    
    if (mysqli_query($conn, $insert)) {

        $user_id = mysqli_insert_id($conn);
        $wallet_sql = "INSERT INTO wallet (user_id, balance) VALUES ('$user_id', '0.00')";
        mysqli_query($conn, $wallet_sql);

        // After insertion, maybe announce muna na registration successful or login ba muna
        header("Location: index.php"); 
    }

}

mysqli_close($conn);
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
            <li><a href="register.php" onclick="">Register</a></li>
            <li><a href="index.php" onclick="">Login</a></li>
        </ul>
    </nav>
    </header>

    <div class="main-page">
       <div class="form-box valid-pass">
                
                <h1>Registration - Create Password</h1>
                <p><i class="tip">TIP: Avoid using your name or birthdate, use a mix of words, numbers, and symbols</i></p>
                <form action="password.php" method="POST">
                <div class="top">
                    <div class="left">

                    <h2>Password Requirements</h2>
                    <ul>
                        <li>Be at least <b>8 characters long</b></li>
                        <li>Include<b> uppercase (A-Z) and lowercase letters (a-z)</b></li>
                        <li>Contain at least <b>one number (0-9)</b></li>
                        <li>Include at least one special character (e.g. ! @ # $ % ^ & *_ )</li>  
                    </ul>

                    </div>

                    <div class="right">
                    <label>Password</label><br>
                    <input type="password" placeholder="New Password" name="password" required><br>
                    
                    <div class="spacer">
                        <p>spacer</p>
                    </div>

                    <label>Confirm Password</label><br>
                    <input type="password" placeholder="Confirm Password" name="confirm_password"required><br>

                    

                    <div class="message-container <?php if (isset($_GET['error'])) { echo 'visible'; }?>">
                        <img src="./images/warning.png">
                        <p>
                            <?php
                                if (isset($_GET['error']) && $_GET['error'] == 'mismatch') {
                                    echo '<p>Passwords do not match.</p>';
                                } elseif (isset($_GET['error']) && $_GET['error'] == 'invalid') {
                                    echo '<p>Invalid password, please follow the password requirements.</p>';
                                }
                            ?>
                        </p>
                    </div>

                    </div>

                </div>

                <div class="bottom">
                    <button type="submit">Submit</button>
                </div>

            </form>
        </div>


    </div>
</body>
</html>