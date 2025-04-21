<?php
session_start();

$name = $_SESSION["user"] ?? '';
$email = $_SESSION['email'] ?? '';
$isLoggedIn = !empty($email);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <title>Student Dashboard</title>
    
    
    <link rel="stylesheet" href="../assets/css/user.css">
</head>

<body>
    <nav class="navbar">
        <h1>Student Dashboard</h1>
        <ul>
            <li><a href="user.php" class="nav-link" data-target="home-content">Home</a></li>
            <li><a href="#" class="nav-link" data-target="about-content">About</a></li>
            <li><a href="#" class="nav-link" data-target="contact-content">Contact</a></li>
            <li><a href="login.php" class="nav-link">Login</a> | <a href="register.php" class="nav-link">Register</a>
            </li>

            <li>
                <div class="profile-container">
                    <i class="fa fa-user profile-icon" onclick="toggleProfileCard()"></i>
                    <div class="profile-card" id="profileCard">
                        <?php if ($isLoggedIn): ?>
                            <p><strong>Username:</strong> <?= htmlspecialchars($name) ?></p>
                            <p><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
                            <form method="POST" action="logout.php">
                                <button type="submit" class="logout-button">Logout</button>
                            </form>
                        <?php else: ?>
                            <p><a href="login.php">Login</a> or <a href="register.php">Register</a></p>
                        <?php endif; ?>
                    </div>
                </div>

            </li>
        </ul>
    </nav>

    <div class="container">
        <div class="sidebar">
            <h2>Menu</h2>
            <ul>
                <li><a href="apply_pass.php" class="menu-link">Apply for Pass</a></li>
                <li><a href="view_pass.php" class="menu-link">View Pass</a></li>
                <li><a href="payment.php" class="menu-link">Payment</a></li>
                <li><a href="notifications.php" class="menu-link">Notifications</a></li>
                <li><a href="refund_cancel.php" class="menu-link">Refund & Cancellation</a></li>
            </ul>




        </div>

        
        <div class="main-content">
            <div id="content">
                <h2>Bus Routes and Fees</h2>
                <table id="busRoutesTable" border="1">
                    <thead>
                        <tr>
                            
                            <th>Sub Stops</th>
                            <th>Cost</th>
                        </tr>
                    </thead>
                    <tbody>
                       
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    <script src="../assets/js/user.js"></script>
</body>

</html>