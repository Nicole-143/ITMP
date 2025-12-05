<?php 
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Townsville Barangay System - About</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
</head>
<body>
    <header class="main-header">
        <div class="logo">
            <img src="./images/logo.png" alt="Townsville Barangay Logo">
            <div>
                <h4>BRGY. Townsville</h4>
                <h4>Document Request</h4>
            </div>
        </div>
        <nav class="navigation-menu">
            <ul>
                <li><a href="about.php">About</a></li>
                <li><a href="faq.php">FAQS</a></li></li>
                <li><a href="contact.php">Contact Us</a></li>
                <li><a href="register.php" >Register</a></li>
                <li><a href="index.php" >Login</a></li>
            </ul>
        </nav>
    </header>

    <div class="main-page">
        <div class="form-box register-box">
            <h1>About the Townsville Barangay System</h1>

            <p>Welcome to the official Townsville Barangay Document Request System! Our platform is designed to help residents efficiently request important documents like Barangay Clearances, Certificates of Residency, and Certificates of Good Moral Character. Whether you need documentation for personal, legal, or business purposes, we are here to help.</p>

            <h1 class="top-space">Our Mission</h1>
            <p>We aim to provide seamless, digital access to all the necessary documents for the residents of Townsville. Our goal is to streamline the process, making it quicker and easier to request, track, and receive official documents. By doing so, we empower our community to stay connected and compliant with government requirements.</p>

            <h1 class="top-space">How It Works</h1>
            <ol style="margin-left:20px;">
                <li><strong class="text-blue">Create an account:</strong> Register with your personal details, including a government-issued ID.</li>
                <li style="margin-top:10px;"><strong class="text-blue">Request documents:</strong> Choose from a variety of documents available for request.</li>
                <li style="margin-top:10px;"><strong class="text-blue">Track your request:</strong> Stay updated on the status of your request through the tracking system.</li>
                <li style="margin-top:10px;"><strong class="text-blue">Pick up or delivery:</strong> Pick up your documents at the Barangay Hall, or choose delivery to your home.</li>
            </ol>

            <h1 class="top-space">Disclaimer</h1>
            <p>This system is designed to serve the residents of the Townsville Barangay only. If you live outside the jurisdiction, your request may be denied.</p>

            
        </div>
    </div>

</body>
</html>
