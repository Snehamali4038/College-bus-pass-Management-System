<?php
session_start();
require_once '../includes/db.php';


if (!isset($_POST['email']) || !isset($_POST['method'])) {
    echo "Invalid payment request.";
    exit();
}

$email = $_POST['email'];


$checkPaymentQuery = "SELECT payment_status FROM applications WHERE email = ?";
$stmt = $conn->prepare($checkPaymentQuery);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$app = $result->fetch_assoc();

if ($app && $app['payment_status'] === 'completed') {
    echo "Payment already completed. Duplicate payment not allowed.";
    exit();
}

$method = $_POST['method'];
$date = date("Y-m-d");


$appQuery = "SELECT * FROM applications WHERE email = ? AND status = 'approved'";
$stmt = $conn->prepare($appQuery);
$stmt->bind_param("s", $email);
$stmt->execute();
$appResult = $stmt->get_result();

if ($appResult->num_rows === 0) {
    echo "Admin approval required before payment.";
    exit();
}

$appData = $appResult->fetch_assoc();
$application_id = $appData['id'];
$selectedStop = trim($appData['bus_route']);


$routeQuery = "SELECT cost FROM bus_routes WHERE sub_stops LIKE CONCAT('%', ?, '%')";
$stmt = $conn->prepare($routeQuery);
$stmt->bind_param("s", $selectedStop);
$stmt->execute();
$routeResult = $stmt->get_result();

if ($routeResult->num_rows === 0) {
    echo "Route pricing not found.";
    exit();
}

$routeData = $routeResult->fetch_assoc();
$amount = $routeData['cost'];


$insertQuery = "INSERT INTO payment_history (application_id, method, amount, status, date) VALUES (?, ?, ?, 'paid', ?)";
$stmt = $conn->prepare($insertQuery);
$stmt->bind_param("isis", $application_id, $method, $amount, $date);
$stmt->execute();


$updateQuery = "UPDATE applications SET payment_status = 'completed', amount_paid = ? WHERE email = ?";
$stmt = $conn->prepare($updateQuery);
$stmt->bind_param("ds", $amount, $email);
$stmt->execute();


$to = $email;
$subject = "Your Bus Pass Payment Receipt";
$message = "Dear Student,\n\nYour payment of ₹$amount via $method on $date has been successfully received.\n\nStop: $selectedStop\nStatus: Paid\n\nThank you,\nTransport Department";
$headers = "From: noreply@yourcollege.com";

mail($to, $subject, $message, $headers);


header("Location: payment.php?success=1");
exit();
?>
