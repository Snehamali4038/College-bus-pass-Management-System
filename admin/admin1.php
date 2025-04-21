<?php
session_start();

$name = $_SESSION["user"] ?? '';
$email = $_SESSION['email'] ?? '';
$isLoggedIn = !empty($email);
?>
<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user_type"] !== "admin") {
    header("Location: ../../user/login.php"); 
    // Redirect non-admins to login page
    exit();
}


include "../includes/db.php"; 

$total_applications = mysqli_query($conn, "SELECT COUNT(*) FROM applications");
$total_applications_count = mysqli_fetch_row($total_applications)[0];

// Query to get pending applications count
$pending_apps = mysqli_query($conn, "SELECT COUNT(*) FROM applications WHERE status = 'pending'");
$pending_apps_count = mysqli_fetch_row($pending_apps)[0];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    
    <link rel="stylesheet" href="../assets/css/admin1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"> <!-- Link to CSS for styling -->
</head>

<body>
    <div class="container">
        <!-- Navbar -->
        <nav class="navbar">
            <div class="navbar-left">
                <a href="admin-dashboard.php" class="navbar-item">
                    <span class="admin-text">Admin</span>
                    <span class="dashboard-text">Dashboard</span>
                </a>
            </div>
            <div class="navbar-right">
                <li>

                    <div class="profile-container">
                        <i class="fas fa-user profile-icon" onclick="toggleProfileCard()"></i>
                        <div class="profile-card" id="profileCard">
                            <?php if ($isLoggedIn): ?>
                                <p><strong>Username:</strong> <?= htmlspecialchars($name) ?></p>
                                <p><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
                                <form method="POST" action="logout.php">
                                    <button type="submit" class="logout-button">Logout</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>

                </li>

            </div>
        </nav>

        <!-- Sidebar -->
        <aside class="sidebar">
            <h2>Menu</h2>
            <ul>
                <li><a href="admin_manage_pass.php">Manage Passes</a></li>
                <li><a href="admin_manage_cancellation.php">Payment & Refund Management</a></li>
                <li><a href="manage_bus_routes.php">Bus Route Management</a></li>
                <li><a href="notifications&alerts.php">Notifications & Alerts</a></li>
                <li><a href="messages.php">Messages</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="dashboard-cards">
                <div class="card">
                    <h2><?php echo $total_applications_count; ?></h2>
                    <p>Total Applications</p>
                </div>
                <div class="card">
                    <h2><?php echo $pending_apps_count; ?></h2>
                    <p>Pending Requests</p>
                </div>
                <div class="card">
                    <h2><?php echo $total_applications_count; ?></h2>
                    <p>Payment Completed</p>
                </div>
            </div>
        </main>
    </div>
    <script src="../assets/js/admin1.js"></script>

</body>

</html>