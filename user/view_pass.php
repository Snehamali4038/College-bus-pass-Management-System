<?php
session_start(); 

include "../includes/db.php";

// Check if email is passed via GET
// This will print the session variables for debugging

if (!isset($_SESSION['email']) || empty($_SESSION['email'])) {
    echo "You are not logged in.";
    exit();
}

$email = $_SESSION['email'];

// Step 1: Get application info
$appQuery = "SELECT * FROM applications WHERE email = ? ORDER BY applied_on DESC LIMIT 1";
$stmt = $conn->prepare($appQuery);
$stmt->bind_param("s", $email);
$stmt->execute();
$appResult = $stmt->get_result();

if ($appResult->num_rows === 0) {
    $errorMsg = "No bus pass application found for this email.";
} else {
    $row = $appResult->fetch_assoc();

    $status = $row['status'];
    $payment_status = $row['payment_status'];

    if ($status !== 'approved') {
        $errorMsg = "Your application is not approved yet.";
    } elseif ($payment_status !== 'completed') {
        $errorMsg = "Please complete the payment to view your bus pass.";
    } else {
        // Everything okay, extract values
        $studentId = $row['id'];
        $studentName = $row['student_name'];
        $phone = $row['phone'];
        $city = $row['city'];
        $busRoute = $row['bus_route'];
        $driverName = "Ramesh Patil";
        $driverContact = "9876543211";

        $appliedOnDate = $row['applied_on'];
        $validityStartDate = new DateTime($appliedOnDate);
        $validityEndDate = clone $validityStartDate;
        $validityEndDate->add(new DateInterval('P1M'));
        $validity = $validityStartDate->format('d M Y') . ' - ' . $validityEndDate->format('d M Y');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Bus Pass</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e0f7fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 450px;
            width: 90%;
        }
        .bus-pass {
            border: 1px solid #ccc;
            padding: 15px;
            margin-top: 15px;
            background-color: #fafafa;
            border-radius: 5px;
        }
        .print-btn {
            margin-top: 15px;
            padding: 8px 16px;
            background-color:rgb(157, 113, 178);;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        
        .home-link {
            text-decoration: none;
            color: blue;
            margin-top: 10px;
            display: inline-block;
            color: blueviolet;
          }
          .home-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Bus Pass</h2>
        <div id="status">
            <?php
            if (isset($errorMsg)) {
                echo "<p>$errorMsg</p>";
            } else {
                echo '<p>Your bus pass has been issued and payment is completed.</p>';
            }
            ?>
        </div>

        <?php if (!isset($errorMsg)) { ?>
        <div class="bus-pass" id="pass-details">
            <p><strong>Student ID:</strong> <?php echo htmlspecialchars($studentId); ?></p>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($studentName); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($phone); ?></p>
            <p><strong>City:</strong> <?php echo htmlspecialchars($city); ?></p>
            <p><strong>Bus Route:</strong> <?php echo htmlspecialchars($busRoute); ?></p>
            <p><strong>Driver Name:</strong> <?php echo htmlspecialchars($driverName); ?></p>
            <p><strong>Driver Contact:</strong> <?php echo htmlspecialchars($driverContact); ?></p>
            <p><strong>Pass Validity:</strong> <?php echo htmlspecialchars($validity); ?></p>
            <!-- <img src="qrcode.png" alt="QR Code" width="100"> -->
        </div>
        <button class="print-btn" onclick="printPass()">Print</button>
        <?php } ?>
        <div>
            <a class="home-link" href="user.php">Back to Home</a>
        </div>
    </div>
    
    <script>
        function printPass() {
            window.print();
        }
    </script>
</body>
</html>
