 <?php
session_start();

include "../includes/db.php";

$success = false;
$error = '';

if (!isset($_SESSION['email'])) {
    echo "You need to log in first!";
    exit();
}

$email = $_SESSION['email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reason = isset($_POST['reason']) ? $_POST['reason'] : 'No reason provided';

    
    $query = "SELECT id, amount_paid FROM applications WHERE email = ? AND payment_status = 'completed'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $error = "You don't have an active bus pass or the payment is not completed.";
    } else {
        $app = $result->fetch_assoc();
        $application_id = $app['id'];
        $refund_amount = $app['amount_paid'];

       
        $check = $conn->prepare("SELECT * FROM cancellation_requests WHERE application_id = ? AND status = 'pending'");
        $check->bind_param("i", $application_id);
        $check->execute();
        $existing = $check->get_result();

        if ($existing->num_rows > 0) {
            $error = "You already have a pending cancellation request.";
        } else {
            
            $insert = $conn->prepare("INSERT INTO cancellation_requests (application_id, refund_amount, reason) VALUES (?, ?, ?)");
            $insert->bind_param("ids", $application_id, $refund_amount, $reason);
            $insert->execute();

            $success = true;
        }
    }
}
?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Refund & Cancellation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #e0f7fa;
        }

        .container {
            width: 80%;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1, h3 {
            text-align: center;
            color: #333;
        }

        p, li {
            font-size: 16px;
            color: #555;
            line-height: 1.6;
        }

        .note, .alert {
            padding: 10px;
            margin: 20px 0;
        }

        .note {
            background-color: #f8f9fa;
            border-left: 4px solid #007BFF;
        }

        .alert {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
        }

        .card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            background-color: #f9f9f9;
            margin-top: 20px;
        }

        .btn {
            background-color: rgb(157, 113, 178);;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            text-decoration: none;
            cursor: pointer;
            margin-top: 15px;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        textarea {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        a {
            color:blueviolet;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .success-box {
            text-align: center;
            padding: 20px;
        }

        .success-box p {
            color: #28a745;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Refund & Cancellation Policy</h1>

        <?php if ($success): ?>
            <div class="success-box">
                <h2>Cancellation Request Submitted</h2>
                <p>Your request has been submitted and is under review by the admin.</p>
                <p>Refund eligibility is subject to admin approval and processing time (up to 7 working days).</p>
                <a href="user.php" class="btn">Back to Home</a>
            </div>
        <?php else: ?>
            <?php if (!empty($error)): ?>
                <div class="alert"><?= $error ?></div>
            <?php endif; ?>

            <div class="card">
                <h3>Eligibility & Conditions</h3>
                <ul>
                    <li><strong>Refunds available only within 7 days</strong> of payment/activation.</li>
                    <li><strong>50% refund</strong> will be issued if eligible.</li>
                    <li><strong>No refund</strong> after 7 days.</li>
                    <li><strong>Reason is optional</strong> but recommended.</li>
                </ul>
            </div>

            <div class="card">
                <h3>Submit Cancellation</h3>
                <form method="POST">
                    <label for="reason"><strong>Optional Reason for Cancellation:</strong></label><br>
                    <textarea name="reason" rows="4" placeholder="Write your reason here (optional)..."></textarea><br>
                    <button type="submit" class="btn">Submit Cancellation Request</button>
                </form>
            </div>

            <div style="text-align: center; margin-top: 15px;">
                <a href="user.php">Back to Home</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
 
