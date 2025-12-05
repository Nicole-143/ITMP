<?php 
session_start();

include "db.php";

if (!isset($_SESSION['email'])) {
    // Redirect to the login page if not logged in
    header("Location: index.php");
    exit();
}

if ($_SESSION['type'] == 'user') {
    header("Location: dashboard.php"); 
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $month = $_POST['month']; 
    $year = $_POST['year'];
    $report_type = $_POST['report_type'];

    $monthNames = [
        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June',
        7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
    ];

    $monthName = $monthNames[$month];

    if ($report_type == "payments") {
       
        generatePaymentsReport($month, $monthName, $year);
    } elseif ($report_type == "document_requests") {
       
        generateDocumentRequestsReport($month, $monthName, $year);
    }
}



function generatePaymentsReport($month, $monthName,$year) {
    
    include "db.php";
   

    $sql = "SELECT p.payment_id, p.amount, p.payment_date, r.request_id
            FROM payments p
            LEFT JOIN requests r ON p.request_id = r.request_id
            WHERE MONTH(p.payment_date) = '$month'AND YEAR(p.payment_date) = '$year'";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($result) > 0) {


        $xml = new DOMDocument("1.0", "UTF-8");
        $xml->formatOutput = true; 

        // Root <payments> element
        $payments = $xml->createElement("payments");
        $xml->appendChild($payments);

        $monthElement = $xml->createElement('month', $monthName);
        $payments->appendChild($monthElement);

        $yearElement = $xml->createElement('year', $year);
        $payments->appendChild($yearElement);

        $total_amount = 0;
        $payment_count = 0;

       
        $details = $xml->createElement('details');
        $payments->appendChild($details);

        
        
        while ($row = mysqli_fetch_array($result)) {
            $payment = $xml->createElement('payment');
            $details->appendChild($payment);

            
            $payment->appendChild($xml->createElement('request_id', $row['request_id']));
            $payment->appendChild($xml->createElement('amount', "₱" .$row['amount']));
            $payment->appendChild($xml->createElement('date', $row['payment_date']));

            
            $total_amount += $row['amount'];
            $payment_count++;

        }

        $format_total = "₱" . number_format($total_amount, 2, '.', ',');
        $payments->appendChild($xml->createElement('total_amount',$format_total));
        $payments->appendChild($xml->createElement('payment_count', $payment_count));

        header('Content-Type: application/xml');
        header('Content-Disposition: attachment; filename="'.$monthName .'_'.$year.'_Payments_Report' . '.xml"');
        echo $xml->saveXML();
        mysqli_close($conn);
        exit();
        
    } else {
        header("Location: reports.php?none=$monthName&year=$year");
        exit();
    }

    
}


function generateDocumentRequestsReport($month, $monthName, $year) {
    include "db.php";
    
    $sql = "
        SELECT 
            dt.doc_name,
            COUNT(r.request_id) AS requests,  -- Alias for total requests
            SUM(CASE WHEN r.status IN ('Approved', 'Released', 'Ready for Pick-up') THEN 1 ELSE 0 END) AS successfully_requested,
            COUNT(CASE WHEN r.is_on_behalf = 1 THEN 1 ELSE NULL END) AS requests_on_behalf, 
            COUNT(CASE WHEN r.delivery_mode = 'Pick-up' THEN 1 ELSE NULL END) AS pick_up_requests, 
            COUNT(CASE WHEN r.delivery_mode = 'Delivery' THEN 1 ELSE NULL END) AS delivery_requests,
            SUM(r.copies) AS total_documents_processed  
        FROM 
            requests r
        LEFT JOIN 
            document_types dt ON r.doc_id = dt.doc_id
        WHERE 
            MONTH(r.request_date) = '$month' AND YEAR(r.request_date) = '$year'
        GROUP BY 
            dt.doc_name;
    ";

    $result = mysqli_query($conn, $sql);

    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($result) > 0) {
        $xml = new DOMDocument("1.0", "UTF-8");
        $xml->formatOutput = true; 

        // Root <document_requests> element
        $docRequests = $xml->createElement("document_requests");
        $xml->appendChild($docRequests);

       
        $monthElement = $xml->createElement('month', $monthName);
        $docRequests->appendChild($monthElement);
        
        $yearElement = $xml->createElement('year', $year);
        $docRequests->appendChild($yearElement);
       
        $summary = $xml->createElement('summary');
        $docRequests->appendChild($summary);

       
        $requests = 0;
        $documents_processed = 0;
        $successfully_released = 0;
        $requests_on_behalf = 0;
        $pick_up_requests = 0;
        $delivery_requests = 0;
        
        $rows = [];
        
        while ($row = mysqli_fetch_array($result)) {
            
            $rows[] = $row;
            
            $requests += $row['requests'];
            $documents_processed += $row['total_documents_processed'];
            $successfully_released += $row['successfully_requested'];
            $requests_on_behalf += $row['requests_on_behalf'];
            $pick_up_requests += $row['pick_up_requests'];
            $delivery_requests += $row['delivery_requests'];
        }

        $summary->appendChild($xml->createElement('total_requests', $requests));
        $summary->appendChild($xml->createElement('total_documents_processed', $documents_processed));
        $summary->appendChild($xml->createElement('total_successfully_released', $successfully_released));
        $summary->appendChild($xml->createElement('total_requests_on_behalf', $requests_on_behalf));
        $summary->appendChild($xml->createElement('total_pick_up_requests', $pick_up_requests));
        $summary->appendChild($xml->createElement('total_delivery_requests', $delivery_requests));

        $details = $xml->createElement('details');
        $docRequests->appendChild($details);

  
        foreach ($rows as $row) {
            $document = $xml->createElement('document-type');
            $details->appendChild($document);

            $document->appendChild($xml->createElement('doc_name', $row['doc_name']));
            $document->appendChild($xml->createElement('total_requests', $row['requests']));
            $document->appendChild($xml->createElement('successful_requests', $row['successfully_requested']));
            $document->appendChild($xml->createElement('released_or_picked_up', $row['successfully_requested'])); 
            $document->appendChild($xml->createElement('requests_on_behalf', $row['requests_on_behalf']));
            $document->appendChild($xml->createElement('pick_up_requests', $row['pick_up_requests']));
            $document->appendChild($xml->createElement('delivery_requests', $row['delivery_requests']));
            $document->appendChild($xml->createElement('total_documents_processed', $row['total_documents_processed']));
        }

       
        header('Content-Type: application/xml');
        header('Content-Disposition: attachment; filename="'.$monthName.'_'.$year.'_Document_Requests_Report.xml"');
        echo $xml->saveXML();

        mysqli_close($conn);
        exit();
    } else {
       
        header("Location: reports.php?none=$monthName&year=$year");
        exit();
    }
}



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
            <li><a href="logout.php" onclick="">Logout</a></li>
        </ul>
    </nav>
    </header>

    <div class="main-dashboard">

        

       <div class="page-container">
            <h2><a href="admin_dashboard.php"><b><-</b></a></h2>
            <h2>Generate Reports</h2>
            
        </div>

        <?php if  (isset($_GET['none'])&& isset($_GET['year'])): ?>
            <p class="bottom-spacer head4">No data found for <?php echo htmlspecialchars($_GET['none']); ?> <?php echo htmlspecialchars($_GET['year']); ?>.</p>
        <?php endif; ?>

        <h3>Select Month:</h3>

        <form action="reports.php" method="POST">
            <div>
               
                <select name="month" id="month">
                    <option value="1">January</option>
                    <option value="2">February</option>
                    <option value="3">March</option>
                    <option value="4">April</option>
                    <option value="5">May</option>
                    <option value="6">June</option>
                    <option value="7">July</option>
                    <option value="8">August</option>
                    <option value="9">September</option>
                    <option value="10">October</option>
                    <option value="11">November</option>
                    <option value="12">December</option>
                </select>

                
            </div>
            <h3>Select Year:</h3>
            <div>
                <select name="year" id="year">
                <?php 
                    
                    $currentYear = date('Y');
                    for ($year = 2020; $year <= $currentYear; $year++) {
                        echo "<option value='$year'>$year</option>";
                    }
                ?>
            </select>
            </div>

            
            <h3>Select Report Type:</h3>

            <div class="bottom doc-btns">
            
            <button type="submit" name="report_type" value="payments" class="report-btn">Generate Payments Report</button>

           
            <button type="submit" name="report_type" value="document_requests" class="report-btn">Generate Document Requests Report</button>
            </div>
        </form>



</body>
</html>