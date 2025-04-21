<?php
session_start();

include "../includes/db.php";

$email = $_SESSION['email'] ?? '';

if (empty($email)) {
    echo "User not logged in.";
    exit();
}

$success = isset($_GET['success']) && $_GET['success'] == 1;


$query = "SELECT * FROM applications WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$appResult = $stmt->get_result();


if ($appResult->num_rows === 0) {
    $noAppMessage = true;
} else {
    $appData = $appResult->fetch_assoc();
    $status = $appData['status'];
    $paymentStatus = $appData['payment_status'];

    $selectedStop = trim($appData['bus_route']); // moved inside the else block

    // Get cost based on bus_route
    $costQuery = "SELECT cost, route_name FROM bus_routes WHERE sub_stops LIKE CONCAT('%', ?, '%')";
    $stmt = $conn->prepare($costQuery);
    $stmt->bind_param("s", $selectedStop);
    $stmt->execute();
    $costResult = $stmt->get_result();

    if ($costResult->num_rows === 0) {
        echo "No fee structure found for the selected route: " . htmlspecialchars($selectedStop);
        exit();
    }

    $costData = $costResult->fetch_assoc();
    $amount = $costData['cost'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Payment & Renewal</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e0f7fa;
            padding: 20px;
        }

        .container {
            width: 80%;
            margin: auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .status,
        .payment-history,
        .alert {
            margin: 15px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }

        button,
        a:not(.home-link) {
            padding: 8px 15px;
            margin: 5px;
            background-color:rgb(157, 113, 178);;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover,
        a:not(.home-link):hover {
            background-color:rgb(157, 113, 178);;
        }

        .home-link:hover {
            text-decoration: underline;
        }

        .alert {
            color: #e74c3c;
            font-weight: bold;
        }

        .success {
            color: green;
            font-weight: bold;
        }

        .home-link {
            display: inline-block;
            text-decoration: none;
           text-align: center;
            color:blueviolet;
            padding-top: 10px;
        }

        .disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

        .status-message {
            font-size: 16px;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
            font-weight: bold;
        }

        .status-message.pending {
    background-color: #fff3cd !important;
    color: #856404 !important;
    border: 1px solid #ffeeba !important;
}

.status-message.rejected {
    background-color: #f8d7da !important;
    color: #721c24 !important;
    border: 1px solid #f5c6cb !important;
}

.status-message.unexpected {
    background-color: #f1f1f1 !important;
    color: #6c757d !important;
    border: 1px solid #d6d8db !important;
}
.status-message.no-application {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
    padding: 12px;
    border-radius: 5px;
    margin: 20px 0;
    text-align: center;
    font-weight: bold;
}
.container{
    text-align: center;
}

    </style>
</head>

<body>
    <div class="container">
        <h2>Payment</h2>
        <?php if (!empty($noAppMessage)): ?>
            <div class="status-message no-application">You have not submitted an application yet.</div>
            <div>
                <a class="home-link" href="user.php">Back to Home</a>
            </div>
            </div></body></html>
            <?php exit(); ?>
        <?php endif; ?>

        
        <?php if ($status === 'pending'): ?>
            <div class="status-message pending">Your application is still under review by the admin.</div>
            <div>
                <a class="home-link" href="user.php">Back to Home</a>
            </div>
            </div></body></html>
            <?php exit(); ?>
        <?php elseif ($status === 'rejected'): ?>
            <div class="status-message rejected">Your application was rejected. Please contact the admin.</div>
            <div>
                <a class="home-link" href="user.php">Back to Home</a>
            </div>
            </div></body></html>
            <?php exit(); ?>
        <?php elseif ($status !== 'approved'): ?>
            <div class="status-message unexpected">Unexpected application status.</div>
            <div>
                <a class="home-link" href="user.php">Back to Home</a>
            </div>
            </div></body></html>
            <?php exit(); ?>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert success">Payment successful! A receipt has been sent to your email.</div>
        <?php endif; ?>

        <div class="status">
            <p>Payment Status: <strong><?php echo htmlspecialchars($paymentStatus); ?></strong></p>

            <?php if ($paymentStatus !== 'completed'): ?>
                <p class="alert">Admin has approved your payment. Please complete the payment.</p>
            <?php else: ?>
                <p class="success">Your payment is already completed. Thank you!</p>
            <?php endif; ?>

            <button id="renew-btn" <?php echo ($paymentStatus === 'completed') ? 'disabled class="disabled"' : ''; ?>>Renew Pass</button>

            <?php if ($paymentStatus === 'completed'): ?>
                <a href="receipt.php" target="_blank">Download Receipt</a>
            <?php else: ?>
                <a href="#" class="disabled" style="pointer-events: none;">Download Receipt</a>
            <?php endif; ?>
        </div>

        <div class="payment-methods">
            <h3>Choose Payment Method:</h3>
            <div id="payment-forms">
                <?php
                $methods = ["UPI", "Credit/Debit Card", "Net Banking", "Wallet"];
                $disabledAttr = ($paymentStatus === 'completed') ? 'disabled' : ''; 

                foreach ($methods as $method) {
                    
                    $buttonClass = ($paymentStatus === 'completed') ? 'payment-btn disabled' : 'payment-btn';

                    
                    echo '<form method="POST" action="complete_payment.php" style="display:inline-block;">
            <input type="hidden" name="email" value="' . htmlspecialchars($email) . '">
            <input type="hidden" name="method" value="' . htmlspecialchars($method) . '">
            <button class="' . $buttonClass . '" type="submit" ' . $disabledAttr . '>' . $method . ' (₹' . $amount . ')</button>
          </form>';
                }
                ?>

            </div>
        </div>

        <div class="payment-history">
            <h3>Payment History</h3>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $historyQuery = "SELECT ph.* FROM payment_history ph 
                    INNER JOIN applications a ON ph.application_id = a.id 
                    WHERE a.email = ? ORDER BY ph.date DESC";

                    $stmt = $conn->prepare($historyQuery);
                    $stmt->bind_param("s", $email);
                    $stmt->execute();
                    $historyResult = $stmt->get_result();

                    if ($historyResult->num_rows > 0) {
                        while ($row = $historyResult->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$row['date']}</td>
                                    <td>₹{$row['amount']}</td>
                                    <td>{$row['method']}</td>
                                    <td>{$row['status']}</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No payment history available.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div>
            <a class="home-link" href="user.php">Back to Home</a>
        </div>
    </div>
</body>

</html>