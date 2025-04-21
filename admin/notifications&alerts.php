<?php

include "../includes/db.php";


if (isset($_GET['dismiss_msg'])) {
    $_SESSION['dismissed_msg'] = $_GET['dismiss_msg'];
}


if (isset($_POST['submit_create'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    $query = "INSERT INTO announcements (title, message) VALUES ('$title', '$message')";
    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Announcement created successfully!";
    } else {
        $_SESSION['error_message'] = "Error: " . mysqli_error($conn);
    }
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}


if (isset($_POST['submit_edit'])) {
    $id = $_POST['id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    $query = "UPDATE announcements SET title = '$title', message = '$message' WHERE id = $id";
    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Announcement updated successfully!";
    } else {
        $_SESSION['error_message'] = "Error: " . mysqli_error($conn);
    }
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}


if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $query = "DELETE FROM announcements WHERE id = $id";
    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Announcement deleted successfully!";
    } else {
        $_SESSION['error_message'] = "Error: " . mysqli_error($conn);
    }
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}


$query = "SELECT * FROM announcements ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/admin.css"> 
    <title>Admin - Notifications</title>
    <style>
        :root {
    --primary-color: #6a11cb;
    --secondary-color: #2575fc;
    --success-color: #28a745;
    --danger-color: #dc3545;
    --warning-color: #ffc107;
    --info-color: #17a2b8;
    --light-color: #f8f9fa;
    --dark-color: #343a40;
    --white: #ffffff;
    --gray: #6c757d;
}

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

body {
    background-color: #f5f5f5;
    color: #333;
    line-height: 1.6;
    padding: 20px;
}

.container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 20px;
    background-color: var(--white);
    border-radius: 8px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
}


.message {
    position: relative;
    padding: 10px 20px;
    margin-bottom: 15px;
    border-radius: 4px;
    font-weight: 500;
}

.success-message {
    background-color: rgba(40, 167, 69, 0.2);
    color: var(--success-color);
    border-left: 4px solid var(--success-color);
}

.error-message {
    background-color: rgba(220, 53, 69, 0.2);
    color: var(--danger-color);
    border-left: 4px solid var(--danger-color);
}

.close-btn {
    position: absolute;
    top: 50%;
    right: 10px;
    transform: translateY(-50%);
    background: none;
    border: none;
    font-size: 16px;
    cursor: pointer;
    color: inherit;
    opacity: 0.7;
    transition: opacity 0.3s;
}

.close-btn:hover {
    opacity: 1;
}

h1, h2, h3 {
    color: var(--primary-color);
    margin-bottom: 15px;
}

h1 {
    text-align: center;
    padding-bottom: 15px;
    border-bottom: 2px solid #eee;
}

h2 {
    color: var(--secondary-color);
    padding-bottom: 8px;
    border-bottom: 1px solid #eee;
}

form {
    background-color: var(--light-color);
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
}

label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    color: var(--dark-color);
}

input[type="text"],
textarea {
    width: 100%;
    padding: 10px;
    margin-bottom: 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    transition: border 0.3s;
}

input[type="text"]:focus,
textarea:focus {
    border-color: var(--primary-color);
    outline: none;
    box-shadow: 0 0 5px rgba(106, 17, 203, 0.3);
}

textarea {
    min-height: 120px;
    resize: vertical;
}

button {
    background-color: var(--primary-color);
    color: white;
    border: none;
    padding: 10px 18px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    transition: background-color 0.3s;
}

button:hover {
    background-color: #5a0cb0;
}

.announcement {
    background-color: var(--white);
    padding: 15px;
    margin-bottom: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    position: relative;
}

.announcement p {
    margin: 8px 0;
    color: var(--dark-color);
}

.announcement small {
    color: var(--gray);
    font-size: 13px;
}

.action-links {
    margin-top: 10px;
}

.action-links a {
    display: inline-block;
    padding: 6px 12px;
    margin-right: 8px;
    border-radius: 4px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s;
}

.edit-link {
    background-color: var(--warning-color);
    color: var(--dark-color);
}

.edit-link:hover {
    background-color: #e0a800;
}

.delete-link {
    background-color: var(--danger-color);
    color: white;
}

.delete-link:hover {
    background-color: #c82333;
}


/* Responsive Design */
@media (max-width: 768px) {
    .container {
        padding: 15px;
    }

    h1 {
        font-size: 20px;
        padding-bottom: 12px;
    }

    h2 {
        font-size: 18px;
    }

    form {
        padding: 12px;
    }

    input[type="text"],
    textarea {
        padding: 8px;
        font-size: 12px;
    }

    button {
        padding: 8px 14px;
        font-size: 12px;
        width: 100%;
    }

    .announcement {
        padding: 12px;
    }

    .action-links a {
        display: block;
        width: 100%;
        margin-bottom: 8px;
        text-align: center;
    }
}

@media (max-width: 480px) {
    body {
        padding: 10px;
    }

    .container {
        padding: 10px;
    }

    h1 {
        font-size: 18px;
    }

    h2 {
        font-size: 16px;
    }

    textarea {
        min-height: 100px;
    }
}

    </style>
</head>
<body>
    <div class="container">
        <h1>Admin - Manage Announcements</h1>

        <?php if (isset($_SESSION['success_message']) && (!isset($_SESSION['dismissed_msg']) || $_SESSION['dismissed_msg'] != md5($_SESSION['success_message']))): ?>
            <div class="message success-message">
                <?php echo $_SESSION['success_message']; ?>
                <button class="close-btn" onclick="window.location.href='?dismiss_msg=<?php echo md5($_SESSION['success_message']); ?>'">×</button>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message']) && (!isset($_SESSION['dismissed_msg']) || $_SESSION['dismissed_msg'] != md5($_SESSION['error_message']))): ?>
            <div class="message error-message">
                <?php echo $_SESSION['error_message']; ?>
                <button class="close-btn" onclick="window.location.href='?dismiss_msg=<?php echo md5($_SESSION['error_message']); ?>'">×</button>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        
        <h2>Create New Announcement</h2>
        <form method="POST" action="">
            <label for="title">Title:</label>
            <input type="text" name="title" required>

            <label for="message">Message:</label>
            <textarea name="message" required></textarea>

            <button type="submit" name="submit_create">Create Announcement</button>
        </form>

        
        <h2>All Announcements</h2>
        <?php
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<div class='announcement'>";
                echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
                echo "<p>" . nl2br(htmlspecialchars($row['message'])) . "</p>";
                echo "<small>Posted on: " . $row['created_at'] . "</small>";
                echo "<div class='action-links'>";
                echo "<a href='?edit_id=" . $row['id'] . "' class='edit-link'>Edit</a>";
                echo "<a href='?delete_id=" . $row['id'] . "' class='delete-link' onclick='return confirm(\"Are you sure you want to delete this announcement?\");'>Delete</a>";
                echo "</div>";
                echo "</div>";
            }
        } else {
            echo "<p>No announcements found.</p>";
        }

        
        if (isset($_GET['edit_id'])) {
            $id = $_GET['edit_id'];
            $query_edit = "SELECT * FROM announcements WHERE id = $id";
            $result_edit = mysqli_query($conn, $query_edit);
            $row_edit = mysqli_fetch_assoc($result_edit);
        ?>

       
        <h2>Edit Announcement</h2>
        <form method="POST" action="">
            <input type="hidden" name="id" value="<?php echo $row_edit['id']; ?>">
            <label for="title">Title:</label>
            <input type="text" name="title" value="<?php echo htmlspecialchars($row_edit['title']); ?>" required>

            <label for="message">Message:</label>
            <textarea name="message" required><?php echo htmlspecialchars($row_edit['message']); ?></textarea>

            <button type="submit" name="submit_edit">Update Announcement</button>
        </form>
        <?php } ?>
    </div>
    <a href="admin1.php" class="back-home-btn">Back to Home</a>
</body>
</html>

<?php

mysqli_close($conn);
?>