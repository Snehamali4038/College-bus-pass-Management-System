<?php
session_start();
include "../includes/db.php";

// Check if admin is logged in
if (!isset($_SESSION["user"]) || $_SESSION["user_type"] !== "admin") {
    die("You need to log in as admin to manage cancellation requests.");
}

// Fetch all pending cancellation requests with student info
$sql = "SELECT c.id AS cancel_id, a.email, a.amount_paid, c.refund_amount, c.requested_on, c.reason 
        FROM cancellation_requests c
        JOIN applications a ON a.id = c.application_id
        WHERE c.status = 'pending'
        ORDER BY c.requested_on DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Manage Cancellation Requests</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 20px;
            background-color: #e0f7fa;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        th,
        td {
            padding: 12px 15px;
            border: 1px solid #ccc;
            text-align: center;
        }
        th {
            background-color:rgb(157, 113, 178);
            color: white;
        }
        

        tr:hover {
            background-color: #f1f7ff;
        }

        button {
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        button[name="action"][value="approve"] {
            background-color: #4CAF50;
            color: white;
        }

        button[name="action"][value="reject"] {
            background-color: #f44336;
            color: white;
        }
        

        @media (max-width: 768px) {

            table,
            thead,
            tbody,
            th,
            td,
            tr {
                display: block;
            }

            thead {
                display: none;
            }

            tr {
                margin-bottom: 15px;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
                padding: 10px;
                background-color: #fff;
            }

            td {
                text-align: right;
                position: relative;
                padding-left: 50%;
            }

            td::before {
                content: attr(data-label);
                position: absolute;
                left: 15px;
                width: 45%;
                padding-right: 10px;
                white-space: nowrap;
                text-align: left;
                font-weight: bold;
            }

            
        }
    </style>
</head>

<body>
    <h2>Pending Cancellation Requests</h2>

    <table>
        <thead>
            <tr>
                <th>Email</th>
                <th>Amount Paid</th>
                <th>Refund Amount</th>
                <th>Requested On</th>
                <th>Reason</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td data-label="Email"><?= $row['email'] ?></td>
                        <td data-label="Amount Paid">₹<?= $row['amount_paid'] ?></td>
                        <td data-label="Refund Amount">₹<?= $row['refund_amount'] ?></td>
                        <td data-label="Requested On"><?= $row['requested_on'] ?></td>
                        <td data-label="Reason"><?= $row['reason'] ?></td>
                        <td data-label="Action">
                            <form action="process_cancellation.php" method="POST" style="display:inline;">
                                <input type="hidden" name="cancel_id" value="<?= $row['cancel_id'] ?>">
                                <button name="action" value="approve">✅ Approve</button>
                                <button name="action" value="reject">❌ Reject</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No pending requests.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <a href="admin1.php" class="back-home-btn">Back to Home</a>
</body>

</html>

<?php $conn->close(); ?>