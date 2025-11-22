
<?php

session_start();

include "db.php";  
if (isset($_POST['submit'])) {
    
    $email = $_POST['email'];
    $password = $_POST['password'];

    // check if email and password match to a user
    $sql = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";

    
    $result = $conn->query($sql);

    // Check if a matching user is found
    if ($result->num_rows > 0) {
        
        $user = mysqli_fetch_array($result); // Fetch a single row as an indexed array
        // Store user data in session
        $_SESSION['id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['givenname'] = $user['givenname'];
        $_SESSION['type'] = $user['type'];             
        $_SESSION['is_verified'] = $user['is_verified'];
         $_SESSION['comment']= $user['comment'];

        if($user['is_verified'] == 0)
        {
            header("Location: under_verification.php");
            exit();
        }

        if($user['type'] == 'admin')
        {
            header("Location: admin_dashboard.php");
            exit();
        }
        else{
            // Redirect to the dashboard
        header("Location: dashboard.php");
        exit();
        }
        
    } else {
        // If login fails, show error
        header("Location: index.php?error");
        exit();
    }
}   

// Close the database connection
$conn->close();


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

        <div class="message-container <?php if (isset($_GET['error'])) { echo 'visible'; }?>">
                <img src="./images/warning.png">
                <p>Username or password is incorrect</p>
        </div>
        <div class="form-box login-box">
                
                    <div class="brgy-logo">
                        <img src="./images/logo.png">
                    </div> 
                    
                        <h1>BRGY. Townsville</h1>
                        <form action="index.php" method="POST">
                            <label>Email</label><br>
                            <input type="email" name="email" placeholder="Email" required><br>
                            <label>Password</label><br>
                            <input type="password" name="password" placeholder="Password" required><br>
                            <button type="submit" name="submit">Login</button>
                            <p><b>Not a member? <a href="register.php">Sign up now</a></b></p>
                        </form>
                   
        </div>


    </div>
</body>
</html>