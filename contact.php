<?php 
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Townsville Barangay System - Contact Us</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
</head>
<body>

<header class="main-header">
    <div class="logo">
        <img src="./images/logo.png" alt="Townsville Logo">
        <div>
            <h4>BRGY. Townsville</h4>
            <h4>Document Request System</h4>
        </div>
    </div>
    <nav class="navigation-menu">
        <ul>
            <li><a href="about.php">About</a></li>
            <li><a href="faq.php">FAQS</a></li></li>
            <li><a href="contact.php">Contact Us</a></li>
            <li><a href="register.php" >Register</a></li>
            <li><a href="index.php">Login</a></li>
        </ul>
    </nav>
</header>

<div class="main-page">
    <div class="form-box register-box">
        
         <!-- CONTACT INFO -->
          
            <h1>Barangay Office Information</h1>
            <p class="contact-intro">
            Have questions or need assistance? You can reach Barangay Townsville using the details below.
        </p><br>
        <div class="contact-section">
           
            <div class="contact-item">
                <h3>Barangay Address</h3>
                <p style="margin:5px 0px 0px 20px;">3-B Power Puff Street, Barangay Hall<br>Townsville City, Megaville City</p>
            </div>

            <div class="contact-item top-space">
                <h3>Office Operating Hours</h3>
                <p style="margin:5px 0px 0px 20px;">Monday – Friday: 8:00 AM – 5:00 PM<br>Saturday: 9:00 AM – 12:00 PM<br>Sunday: Closed</p>
            </div>

            <div class="contact-item top-space">
                <h3>General Inquiries</h3>
                <p style="margin:5px 0px 0px 20px;"><b>Email:</b> townsville.brgy.office@gmail.com<br>
                    <b>Phone:</b> (02) 3456-7890<br>
                    <b>Mobile:</b> 0912-345-6781</p>
            </div>

            <div class="contact-item top-space">
                <h3>Social Media</h3>
                <p style="margin:5px 0px 0px 20px;">
                    <b>Facebook: </b>facebook.com/townsvillebarangay<br>
                <b>Twitter: </b>@TownsvilleBrgy<br>
                <b>Instagram: </b>@brgy.townsville<br>
          </p>
            </div>

        </div>
    

        
    </div>
</div>

</body>
</html>
