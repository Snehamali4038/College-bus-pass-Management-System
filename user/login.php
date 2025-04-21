<?php
session_start();
include "../includes/db.php";

if (isset($_POST["submit"])) {
    $email = $_POST["email"];
    $password = $_POST["password"];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format!";
        header("Location: login.php");
        exit();
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row["password"])) {
            session_regenerate_id(true);
            $_SESSION["user"] = $row["name"];
            $_SESSION["user_type"] = $row["user_type"];
            $_SESSION["email"] = $row["email"];

            header("Location: " . ($row["user_type"] === "admin" ? "../admin/admin1.php" : "user.php"));
            exit();
        } else {
            $_SESSION['error'] = "Invalid password!";
        }
    } else {
        $_SESSION['error'] = "User not found!";
    }
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/login.css">
    <title>Login</title>

</head>

<body>
    <div class="box login_box">
        <span class="borderline"></span>
        <form action="login.php" method="post">
            <h2>Login</h2>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <?= $_SESSION['error'] ?>
                    <button class="close-btn" onclick="this.parentElement.remove()">&times;</button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <div class="inputbox">
                <input type="email" name="email" required>
                <span>Email</span>
                <i></i>
            </div>
            <div class="inputbox">
                <input type="password" name="password" required>
                <span>Password</span>
                <i></i>
            </div>
            <div class="links">
                <a href="#">Forgot Password</a>
                <a href="register.php">Sign Up</a>
            </div>
            <input type="submit" value="Login" name="submit">
        </form>
    </div>
</body>

</html>