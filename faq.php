<?php 
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Townsville Barangay System - FAQ</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
</head>
<body>
    <header class="main-header">
        <div class="logo">
            <img src="./images/logo.png" alt="Townsville Barangay Logo">
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
            <h1>Frequently Asked Questions (FAQs)</h1>

            <div class="faq-item top-space">
                <h3>1. How do I create an account?</h3>
                <p>To create an account, you need to provide the following details: your legal name, sex, birthdate, phone number, email address, and residential address. You will also need to upload a government-issued ID (e.g., Voter’s ID, Driver’s License, or Passport) showing your residential address.</p>
            </div>

            <div class="faq-item top-space">
                <h3>2. What documents can I request?</h3>
                <p>You can request the following documents from the system (Depending on Availability):
                    <ul style="margin-left:40px;">
                        <li>Barangay Clearance</li>
                        <li>Certificate of Residency</li>
                        <li>Certificate of Good Moral Character</li>
                        <li>Certificate for Business</li>
                    </ul>
                </p>
            </div>

            <div class="faq-item top-space">
                <h3>3. How many documents can I request per day?</h3>
                <p>You are allowed to request up to three documents per day.</p>
            </div>

            <div class="faq-item top-space">
                <h3>4. How do I track the status of my request?</h3>
                <p>You can track the status of your document request via the system. Status updates will be available at different stages, such as Pending, Approved, Denied, Processing, Shipping, Ready for Pick-up, and Released.</p>
            </div>

            <div class="faq-item top-space">
                <h3>5. Can I request documents on behalf of someone else?</h3>
                <p>Yes, you can request documents on behalf of minors (under 18), senior citizens (60 and above), or first- and second-degree relatives. To do so, you must provide the following:
                    <ul style="margin-left:40px;">
                        <li>Letter of Authorization</li>
                        <li>Valid IDs of both the requester and the person being represented</li>
                        <li>Proof of relationship (e.g., birth certificate, marriage certificate, or barangay certification)</li>
                    </ul>
                </p>
            </div>

            <div class="faq-item top-space">
                <h3>6. How do I pay for the documents?</h3>
                <p>You can pay for your requested documents using either your Wallet (which can be topped up) or Cash on Delivery (COD). If the payment is insufficient or incorrect, the admin will deny your request.</p>
            </div>

            <div class="faq-item top-space">
                <h3>7. Can I cancel my document request?</h3>
                <p>Yes, you can cancel your document request at any time while you are still filling out the form. Once submitted and processed, cancellation may not be possible.</p>
            </div>

            <div class="faq-item top-space">
                <h3>8. What happens if I exceed the document request limit?</h3>
                <p>If you reach the limit of three document requests for the day, the system will disable further requests for that day. You will still be able to view document details, but you won’t be able to proceed to the next step until the limit resets.</p>
            </div>

            <div class="faq-item top-space">
                <h3>9. How will I be notified about the status of my document request?</h3>
                <p>The system will notify you via your user dashboard. Updates will be provided whenever the status of your document changes. You will be informed when your document is ready for pick-up or if further action is required from you.</p>
            </div>

            <div class="faq-item top-space">
                <h3>10. Can I request documents if I reside outside the barangay?</h3>
                <p>No, the system only allows document requests from residents within the barangay’s jurisdiction. If your address is outside the barangay, your registration and request will be denied by the admin.</p>
            </div>
        </div>
    </div>
</body>
</html>
