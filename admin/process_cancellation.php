<?php
session_start();
include "../includes/db.php";

if (!isset($_SESSION["user"]) || $_SESSION["user_type"] !== "admin") {
    die("You need to log in as admin to process cancellation requests.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $request_id = $_POST['cancel_id'];
    $action = $_POST['action'];

    
    $sql = "SELECT c.id, c.application_id, c.requested_on, c.status, a.email, ph.amount, ph.date AS payment_date
            FROM cancellation_requests c
            JOIN applications a ON a.id = c.application_id
            JOIN payment_history ph ON ph.application_id = a.id
            WHERE c.id = ?
            ORDER BY ph.date DESC LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    $requested_on = new DateTime($data['requested_on']);
    $payment_date = new DateTime($data['payment_date']);
    $interval = $payment_date->diff($requested_on)->days;

    $amount_paid = $data['amount'];

    if ($action == 'approve') {
        if ($interval <= 7) {
            $refund_amount = $amount_paid / 2;
        } else {
            $refund_amount = 0;
        }

        $update = $conn->prepare("UPDATE cancellation_requests SET status = 'approved', refund_amount = ? WHERE id = ?");
        $update->bind_param("di", $refund_amount, $request_id);
        $update->execute();

        echo "Cancellation request approved successfully.";
    } elseif ($action == 'reject') {
        $update = $conn->prepare("UPDATE cancellation_requests SET status = 'rejected', refund_amount = 0 WHERE id = ?");
        $update->bind_param("i", $request_id);
        $update->execute();

        echo "Cancellation request rejected successfully.";
    } else {
        die("Invalid action.");
    }

    $stmt->close();
    $conn->close();
}
?>
