<?php
session_start();
include "../includes/db.php";

if (isset($_POST["submit"])) {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $cpassword = $_POST["cpassword"];
    $user_type = $_POST["user_type"];

    if (empty($name) || empty($email) || empty($password)) {
        $_SESSION['error'] = "All fields are required!";
        header("Location: register.php");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format!";
        header("Location: register.php");
        exit();
    }

    if ($password !== $cpassword) {
        $_SESSION['error'] = "Passwords do not match!";
        header("Location: register.php");
        exit();
    }

    if (strlen($password) < 8) {
        $_SESSION['error'] = "Password must be at least 8 characters!";
        header("Location: register.php");
        exit();
    }

    $check_email = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $result = $check_email->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Email already registered!";
        header("Location: register.php");
        exit();
    }

    $user_type = ($user_type !== "admin" && $user_type !== "user") ? "user" : $user_type;
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO users (name, email, password, user_type) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $hashed_password, $user_type);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Registration successful! Please login.";
        header("Location: login.php");
        exit();
    } else {
        $_SESSION['error'] = "Registration failed! Please try again.";
        header("Location: register.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/login.css">
    <title>Register</title>
    <style>
        .alert {
            position: relative;
            padding: 15px 35px 15px 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: inherit;
            padding: 0 10px;
        }
    </style>
</head>
<body>
    <div class="box">
        <span class="borderline"></span> 
        <form action="register.php" method="post">
            <h2>Register</h2>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <?= $_SESSION['error'] ?>
                    <button class="close-btn" onclick="this.parentElement.style.display='none'">&times;</button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?= $_SESSION['success'] ?>
                    <button class="close-btn" onclick="this.parentElement.style.display='none'">&times;</button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            <div class="inputbox">
                <input type="text" name="name" required>
                <span>Name</span>
                <i></i>
            </div>
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
            <div class="inputbox">
                <input type="password" name="cpassword" required>
                <span>Confirm Password</span>
                <i></i>
            </div>
            <div class="inputbox">
                <select name="user_type">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
                <i></i>
            </div>
            <div class="links">
                <a href="login.php">Already have an account? Login</a>
            </div>
            <input type="submit" value="Register Now" name="submit">
        </form>
    </div>
</body>
</html>