<?php
session_start();
include "../includes/db.php";

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Notifications</title>
    <style>
        :root {
            --bg-col: #f8f9fa;
            --box-col: #ffffff;
            --mov-col1: #8093f1;
            --mov-col2: #ee6055;
            --form-col: #ffffff;
            --blk-col: #212529;
            --whi-col: #ffffff;
            --inp-col: #6c757d;
            --error-col: #dc3545;
            --success-col: #28a745;
            --warning-col: #ffc107;
        }

        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background-color: #e0f7fa;
            margin: 0;
            padding: 20px;
        }

        .container {
            width: 90%;
            max-width: 800px;
            margin: 20px auto;
            background-color: var(--box-col);
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            color: var(--blk-col);
        }

        h2 {
            text-align: center;
            color: var(--blk-col);
            margin-bottom: 30px;
        }

        .notification {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 5px solid;
            position: relative;
        }

        .notification-title {
            font-weight: bold;
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
        }

        .notification-date {
            font-size: 0.8em;
            color: var(--inp-col);
        }

        .notification-message {
            font-size: 15px;
        }

        .success {
            background-color: rgba(40, 167, 69, 0.1);
            border-color: var(--success-col);
        }

        .error {
            background-color: rgba(220, 53, 69, 0.1);
            border-color: var(--error-col);
        }

        .warning {
            background-color: rgba(255, 193, 7, 0.1);
            border-color: var(--warning-col);
        }

        .info {
            background-color: rgba(128, 147, 241, 0.1);
            border-color: var(--mov-col1);
        }

        .neutral {
            background-color: rgba(33, 37, 41, 0.05);
            border-color: #ccc;
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

        .section-title {
            font-size: 18px;
            font-weight: bold;
            margin: 25px 0 15px 0;
            color: var(--blk-col);
            border-bottom: 1px solid #eee;
            padding-bottom: 8px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Your Notifications</h2>

        <?php
        // Get user's application ID
        $appQuery = "SELECT id FROM applications WHERE email = ?";
        $stmt = $conn->prepare($appQuery);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $appResult = $stmt->get_result();

        $hasNotifications = false;

        if ($appResult->num_rows > 0) {
            $appRow = $appResult->fetch_assoc();
            $applicationId = $appRow['id'];

            $announcementQuery = "SELECT title, message, created_at FROM announcements ORDER BY created_at DESC";
            $announcementResult = $conn->query($announcementQuery);

            if ($announcementResult->num_rows > 0) {
                $hasNotifications = true;
                echo "<div class='section-title'>Admin Announcements</div>";

                while ($row = $announcementResult->fetch_assoc()) {
                    echo "<div class='notification neutral'>
                            <div class='notification-title'>
                                <span>" . htmlspecialchars($row['title']) . "</span>
                                <span class='notification-date'>" . date('d M Y', strtotime($row['created_at'])) . "</span>
                            </div>
                            <div class='notification-message'>" . nl2br(htmlspecialchars($row['message'])) . "</div>
                          </div>";
                }
            }

            // 1. Application Status Notifications
            $statusQuery = "
            SELECT a.status, a.applied_on 
            FROM applications a
            WHERE a.id = ?
            ";
            $stmt = $conn->prepare($statusQuery);
            $stmt->bind_param("i", $applicationId);
            $stmt->execute();
            $statusResult = $stmt->get_result();
            $statusRow = $statusResult->fetch_assoc();

            if ($statusRow) {
                $hasNotifications = true;
                echo "<div class='section-title'>Application Status</div>";

                if ($statusRow['status'] == 'approved') {
                    echo "<div class='notification success'>
                            <div class='notification-title'>
                                <span>Application Approved</span>
                                <span class='notification-date'>" . date('d M Y', strtotime($statusRow['applied_on'])) . "</span>
                            </div>
                            <div class='notification-message'>Your bus pass application has been approved. You can now make the payment.</div>
                          </div>";
                } elseif ($statusRow['status'] == 'rejected') {
                    echo "<div class='notification error'>
                            <div class='notification-title'>
                                <span>Application Rejected</span>
                                <span class='notification-date'>" . date('d M Y', strtotime($statusRow['applied_on'])) . "</span>
                            </div>
                            <div class='notification-message'>Your bus pass application has been rejected. Please contact admin for more details.</div>
                          </div>";
                } else {
                    echo "<div class='notification info'>
                            <div class='notification-title'>
                                <span>Application Pending</span>
                                <span class='notification-date'>" . date('d M Y', strtotime($statusRow['applied_on'])) . "</span>
                            </div>
                            <div class='notification-message'>Your application is under review. Please check back later.</div>
                          </div>";
                }
            }

            // 2. Cancellation Request Notifications
            $cancelQuery = "
            SELECT c.status, c.requested_on, c.processed_on
            FROM cancellation_requests c
            WHERE c.application_id = ?
            ORDER BY c.requested_on DESC
            ";
            $stmt = $conn->prepare($cancelQuery);
            $stmt->bind_param("i", $applicationId);
            $stmt->execute();
            $cancelResult = $stmt->get_result();

            if ($cancelResult->num_rows > 0) {
                $hasNotifications = true;
                echo "<div class='section-title'>Cancellation Requests</div>";

                while ($cancelRow = $cancelResult->fetch_assoc()) {
                    $status = $cancelRow['status'];
                    $requestDate = $cancelRow['requested_on'];
                    $processedDate = $cancelRow['processed_on'] ?? $requestDate;

                    if ($status == 'approved') {
                        
                        $paymentQuery = "SELECT date, amount FROM payment_history WHERE application_id = ? ORDER BY date DESC LIMIT 1";
                        $stmt2 = $conn->prepare($paymentQuery);
                        $stmt2->bind_param("i", $applicationId);
                        $stmt2->execute();
                        $paymentResult = $stmt2->get_result();

                        $message = "Your cancellation request has been approved.";

                        if ($paymentResult->num_rows > 0) {
                            $paymentRow = $paymentResult->fetch_assoc();
                            $daysSincePayment = (strtotime($requestDate) - strtotime($paymentRow['date'])) / (60 * 60 * 24);

                            if ($daysSincePayment <= 7) {
                                $refundAmount = $paymentRow['amount'] / 2;
                                $message .= " ₹" . number_format($refundAmount, 2) . " will be refunded to your account.";
                            } else {
                                $message .= " No refund is applicable as it's beyond 7 days from payment.";
                            }
                        }

                        echo "<div class='notification success'>
                                <div class='notification-title'>
                                    <span>Cancellation Approved</span>
                                    <span class='notification-date'>" . date('d M Y', strtotime($processedDate)) . "</span>
                                </div>
                                <div class='notification-message'>$message</div>
                              </div>";
                    } elseif ($status == 'rejected') {
                        echo "<div class='notification error'>
                                <div class='notification-title'>
                                    <span>Cancellation Rejected</span>
                                    <span class='notification-date'>" . date('d M Y', strtotime($processedDate)) . "</span>
                                </div>
                                <div class='notification-message'>Your cancellation request has been rejected.</div>
                              </div>";
                    } else {
                        echo "<div class='notification info'>
                                <div class='notification-title'>
                                    <span>Cancellation Pending</span>
                                    <span class='notification-date'>" . date('d M Y', strtotime($requestDate)) . "</span>
                                </div>
                                <div class='notification-message'>Your cancellation request is under review.</div>
                              </div>";
                    }
                }
            }

            // 3. Payment Notifications
            $paymentQuery = "
            SELECT p.date, p.amount, p.method
            FROM payment_history p
            WHERE p.application_id = ?
            ORDER BY p.date DESC
            ";
            $stmt = $conn->prepare($paymentQuery);
            $stmt->bind_param("i", $applicationId);
            $stmt->execute();
            $paymentResult = $stmt->get_result();

            if ($paymentResult->num_rows > 0) {
                $hasNotifications = true;
                echo "<div class='section-title'>Payment Notifications</div>";

                while ($paymentRow = $paymentResult->fetch_assoc()) {
                    echo "<div class='notification success'>
                            <div class='notification-title'>
                                <span>Payment Received</span>
                                <span class='notification-date'>" . date('d M Y', strtotime($paymentRow['date'])) . "</span>
                            </div>
                            <div class='notification-message'>
                                Payment of ₹" . number_format($paymentRow['amount'], 2) . " via " . htmlspecialchars($paymentRow['method']) . " was successfully processed.
                            </div>
                          </div>";
                }
            }

        }

        


        if (!$hasNotifications) {
            echo "<p>No new notifications at the moment.</p>";
        }
        ?>

        <a href="user.php" class="home-link">Back to home</a>
    </div>
</body>

</html>