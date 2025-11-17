
<?php 
include "db.php";


    
    session_start(); // Start session to hold user data between steps

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $birthdate = $_POST['birthdate'];

    // Calculate the user's age
    $age = calculateAge($birthdate);

    if ($age < 18) {
        // If the age is less than 18, show error
        header("Location: register.php?error");

    }

    else{

    // Save user data temporarily in session
    $_SESSION['givenname'] = $_POST['givenname'];
    $_SESSION['middlename'] = $_POST['middlename'];
    $_SESSION['surname'] = $_POST['surname'];
    $_SESSION['email'] = $_POST['email'];
    $_SESSION['address'] = $_POST['address'];
    $_SESSION['phone'] = $_POST['phone'];
    $_SESSION['sex'] = $_POST['sex'];
    $_SESSION['birthdate'] = $_POST['birthdate'];

    header('Location: upload_id.php'); // Redirect to upload id page
    exit;
    }
    }

    function calculateAge($birthdate) {
    $birthDate = new DateTime($birthdate);
    $currentDate = new DateTime();
    $age = $currentDate->diff($birthDate)->y; // difference in years
    return $age;
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
       <div class="form-box register-box">
                
                <h1>Registration - Personal Information</h1>
          
                <form action="register.php" method="POST">
                <div class="top">
                    <div class="left">

                    <label>Surname</label><br>
                    <input type="text" class="surname" name="surname" placeholder="Surname" required><br>

                    <label>Middle Name</label><br>
                    <input type="text" class="middlename" name="middlename" placeholder="Middle Name"><br>
                    
                    <label>Email</label><br>
                    <input type="email" class="email" name="email" placeholder="Email" required><br>

                    <label>Phone Number</label><br>
                    <input type="number" class="phone" name="phone"placeholder="Phone Number" required><br>
                    
                    <label>Residential Address</label><br>
                    <input type="text" class="address" name="address"placeholder="Residential Address" required><br>
                    </div>

                    <div class="right">
                    
                    <div class="inner-top">
                        <label>Given Name</label><br>
                        <input type="text" class="given" name="givenname" placeholder="Given Name" required><br>

                        
                    </div>
                    
                    
                    <div class="top-bottom">
                            <div class="message-container <?php if (isset($_GET['error'])) { echo 'visible'; }?>">
                        <img src="./images/warning.png">
                        <p>
                            <?php
                                
                                    echo '<p>You must be at least 18 years old to register.</p>';
        
                            ?>
                        </p>
                        </div>
                    
                    <div class="b-bottom">    
                    <div class="inner-bottom">

                    
                        <div class="left">
                            <label>Sex</label>
                            <select name="sex" class="selection">
                            <option value="" disabled selected>Sex</option>
                            <option value="female">Female</option>    
                            <option value="male">Male</option> 
                            <option value="other">Other</option>      
                            </select>
                        </div>

                        <div class="right">
                            <label>Birthdate</label>
                            <input type="date" class="birthdate" name="birthdate" >

                        </div>
                    </div>
                    
                    </div>
                </div>
                </div>
                </div>

                <div class="bottom">
                    <button type="submit">Next</button>
                </div>

            </form>
        </div>


    </div>
</body>
</html>