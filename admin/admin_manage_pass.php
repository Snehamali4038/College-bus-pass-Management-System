<?php


include "../includes/db.php";

$query = "SELECT * FROM applications WHERE status IN ('pending', 'approved')";
$result = mysqli_query($conn, $query);

$errorMsg = $errorMsg ?? ''; 

if (!$result || mysqli_num_rows($result) == 0) {
    echo "<tr><td colspan='9' style='text-align:center; color:red;'>No applications found.</td></tr>";
}

if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    $action = $_GET['action'];

    if ($action == 'approve') {
        $updateQuery = "UPDATE applications SET status='approved', payment_status='pending' WHERE id=?";
    } elseif ($action == 'reject') {
        $updateQuery = "UPDATE applications SET status='rejected', payment_status=NULL WHERE id=?";
    } elseif ($action == 'reset') {
        $updateQuery = "UPDATE applications SET status='pending', payment_status=NULL WHERE id=?";
    } else {
        die("Invalid action.");
    }

    $stmt = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($stmt, "i", $id);
    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if ($success) {
        echo "<script>alert('Application updated!'); window.location.href='admin_manage_pass.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error updating application.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <title>Admin Manage Passes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color:  #e0f7fa;
            margin: 0;
            padding: 0;
        }

        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
        }

        th, td {
            padding: 15px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color:rgb(157, 113, 178);
            color: white;
        }

        td {
            background-color: white;
        }

        .action-btn {
            padding: 6px 12px;
            background-color: #00796b;
            color: white;
            border: none;
            cursor: pointer;
            margin: 5px;
            border-radius: 5px;
            text-decoration: none;
        }

        .action-btn:hover {
            background-color: #004d40;
        }

        .reject-btn {
            background-color: #e53946;
        }

        .reset-btn {
            background-color: #ff9800;
        }

        .error {
            color: red;
            text-align: center;
        }

        
    </style>
</head>
<body>

<div class="container">
    <h2 style="text-align: center; margin-top: 20px;">Manage Bus Pass Applications</h2>

    <?php if (isset($errorMsg)) {
        echo "<p class='error'>$errorMsg</p>";
    } ?>

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>City</th>
            <th>Bus Route</th>
            <th>Status</th>
            <th>Payment Status</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($result): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['student_name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['phone']) ?></td>
                    <td><?= htmlspecialchars($row['city']) ?></td>
                    <td><?= htmlspecialchars($row['bus_route']) ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                    <td>
                        <?php 
                        if ($row['payment_status'] == 'completed') {
                            echo "<a href='view_pass.php?email=" . urlencode($row['email']) . "' class='action-btn'>View Pass</a>";
                        } else {
                            echo htmlspecialchars($row['payment_status'] ?? 'N/A');
                        }
                        ?>
                    </td>
                    <td>
                        <?php if ($row['status'] == 'pending'): ?>
                            <a href="admin_manage_pass.php?action=approve&id=<?= $row['id'] ?>" class="action-btn">Approve</a>
                            <a href="admin_manage_pass.php?action=reject&id=<?= $row['id'] ?>" class="action-btn reject-btn">Reject</a>
                        <?php elseif ($row['status'] == 'approved' || $row['status'] == 'rejected'): ?>
                            <a href="admin_manage_pass.php?action=reset&id=<?= $row['id'] ?>" class="action-btn reset-btn">Reset</a>
                        <?php else: ?>
                            <span style="color: grey;">No Action</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php endif; ?>
        </tbody>
    </table>

    <a href="admin1.php" class="back-home-btn">Back to Home</a>
</div>

</body>
</html>
