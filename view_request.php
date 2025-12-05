
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
// --- HANDLE APPROVE / DENY ACTIONS ---
$message = '';

if (isset($_GET['approve']) || isset($_GET['deny'])) {
    $request_id = isset($_GET['approve']) ? (int) $_GET['approve'] : (int) $_GET['deny'];
    $new_status = isset($_GET['approve']) ? 'Approved' : 'Denied';

    $stmt = $conn->prepare("UPDATE Requests SET status = ? WHERE request_id = ?");
    $stmt->bind_param("si", $new_status, $request_id);

    if ($stmt->execute()) {
        
        $_SESSION['message'] = "Request #{$request_id} has been {$new_status}.";
        header("Location: document.php"); // Redirect to document.php
        $stmt->close();
        exit();
    } else {
        $_SESSION['message'] = "Error updating request status.";
        header("Location: document.php"); 
        $stmt->close();
        exit();
    }

    
}


  if (isset($_GET['view'])) {
    $id = $_GET['view'];             
                $sql = "
    SELECT 
        r.request_id,
        r.request_date,
        r.status,
        r.delivery_mode,
        r.payment_mode,
        r.payment_status,
        r.copies,
        r.is_on_behalf,
        r.represented_surname,
        r.represented_givenname,
        r.represented_middlename,
        r.represented_birthdate,
        r.represented_relationship,
        d.doc_name,
        u.surname,
        u.givenname,
        u.middlename,
        u.address,
        u.sex,
        u.email,
        u.phone,
        u.birthdate,
        u.upload_id,
        ud.file_path,
        r.payment_status,
        r.shipping_date,
        r.arrival_date
    FROM Requests r
    JOIN Users u ON r.user_id = u.id
    JOIN Document_Types d ON r.doc_id = d.doc_id
    LEFT JOIN Uploaded_Documents ud ON r.request_id = ud.request_id
    WHERE r.request_id = $id
";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                        $row = mysqli_fetch_array($result);
                        $documentType = strtoupper($row['doc_name']);

                        if ($row['is_on_behalf'] == 1)
                        {
                            $on_behalf = '1';
                            if ($row['represented_relationship']==='senior')
                            {
                                $requestType = 'SENIOR CITIZEN';
                            }else if ($row['represented_relationship']==='relative')
                            {
                                $requestType = 'FIRST / SECOND RELATIVE';
                            }else{
                                 $requestType = 'MINOR UNDER THEIR GUARDIANSHIP';
                            }
                        } else{
                            $on_behalf = '0';
                            $requestType = 'PERSONAL';
                        }
                        
                        $givenname = strtoupper($row['givenname']);  
                        $surname = strtoupper($row['surname']);
                        $middlename = strtoupper($row['middlename']);
                        $email= $row['email'];
                        $phone=$row['phone'];
                        $address=strtoupper($row['address']);
                        $sex=strtoupper($row['sex']);
                        $birthdate=date("m/d/Y", strtotime($row['birthdate']));
                        $uploadid=$row['upload_id'];
                           
                        $represented_surname = strtoupper($row['represented_surname']);
                        $represented_givenname = strtoupper($row['represented_givenname']);
                        $represented_middlename = strtoupper($row['represented_middlename']);
                        $represented_birthdate = date("m/d/Y", strtotime($row['represented_birthdate']));
                        
                    }        
                

        $sql_files = "
            SELECT ud.file_path, r.req_name
            FROM Uploaded_Documents ud
            JOIN Requirements r ON ud.req_id = r.req_id
            WHERE ud.request_id = $id
        ";
        $result_files = $conn->query($sql_files);

    
    mysqli_close($conn);
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
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
    </header>

    <div class="main-dashboard">
        <div class="page-container">
            <h2><a href="document.php"><b><-</b></a></h2>
            <h2>Request Approval</h2>
            
        </div>

        

        
        <div class="info-container">
        <h4>User Details</h4>
    <table class="info-table request">
        <tr>
            <td><b>Last Name</b></td>
            <td class="blue"><?php echo $surname; ?></td>
            <td><b>Sex</b></td>
            <td class="blue"><?php echo $sex; ?></td>
            <td><b>Mobile Number</b></td>
            <td class="blue"><?php echo $phone; ?></td>
        </tr>
        <tr>
            <td><b>Given Name</b></td>
            <td class="blue"><?php echo $givenname; ?></td>
            <td><b>Birthdate</b></td>
            <td class="blue"><?php echo $birthdate; ?></td>
            <td><b>Email</b></td>
            <td class="blue"><?php echo $email; ?></td>
        </tr>
        <tr>
            <td><b>Home Address</b></td>
            <td class="blue" colspan="5"><?php echo $address; ?></td>
        </tr>
        
</table>
        <?php 
        
        if($on_behalf=='1'){

            echo'
            <h4 class="top-spacer">On Behalf Details</h4>
            <table class="info-table request">
            <tr>
            <td ><b>Document Type</b></td>
            <td class="blue" colspan="3">'.$documentType.'</td>
            <td ><b>For</b></td>
            <td class="blue" colspan="3">'.$requestType.'</td>
            </tr>
            <tr>
            <td><b>Full Name</b></td>
            <td class="blue" colspan="3">'.$represented_surname.', '.$represented_givenname.' '.$represented_middlename.'</td>
            <td><b>Birthdate</b></td>
            <td class="blue">'.$represented_birthdate.'</td>
            </tr>
            </table>
            ';
        }else{
            echo'
            
            <table class="info-table request">
            <tr>
            <td ><b>Document Type</b></td>
            <td class="blue" colspan="3">'.$documentType.'</td>
            <td ><b>For</b></td>
            <td class="blue" >'.$requestType.'</td>
            </tr>
            
            </table>
            ';
        }
        
        ?>
    


            <h4>Government ID</h4>
            <?php echo '<img  src="./uploads/'.$uploadid.'">'?>
            
            
            <?php if ($result_files->num_rows > 0): ?>
                <h4>Uploaded Files</h4>
                
                    <?php while ($file = mysqli_fetch_array($result_files)): ?>
                        
                        <h3><?php echo $file['req_name']; ?></h3>
                        <img src="./<?php echo $file['file_path']; ?>">
                    <?php endwhile; ?>
                
            <?php else: ?>
                
            <?php endif; ?>
            
            
            <div class="bottom doc-btns">

            <a href="view_request.php?deny=<?php echo $id; ?>" class="deny-btn">
                                Deny
                            </a>

                <a href="view_request.php?approve=<?php echo $id; ?>" class="approve-btn">
                                Approve
                            </a>
                           
                            
            </div>
            </form>
        </div>

</body>
</html>