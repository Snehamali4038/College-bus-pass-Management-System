<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    session_unset();
    session_destroy();

    
    header("Location: ../user/login.php");
    exit();
} else {
    
    header("Location: ../user/login.php");
    exit();
}
?>
