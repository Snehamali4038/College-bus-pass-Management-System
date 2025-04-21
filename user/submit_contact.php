<?php
include "../includes/db.php"; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

   
    $query = "INSERT INTO contact_messages (name, email, phone, message) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $phone, $message);
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Message sent successfully!'); window.location.href='user.php';</script>";
    } else {
        echo "<script>alert('Failed to send message. Try again!'); window.location.href='user.php';</script>";
    }
    mysqli_stmt_close($stmt);
}
?>
