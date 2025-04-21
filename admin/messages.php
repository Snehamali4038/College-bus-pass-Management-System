<?php
include "../includes/db.php"; 


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    $delete_query = "DELETE FROM contact_messages WHERE id = ?";
    
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        echo "<script>alert('Message deleted successfully!'); window.location.href='messages.php';</script>";
    } else {
        echo "<script>alert('Failed to delete message. Try again!');</script>";
    }

    $stmt->close();
}


$sql = "SELECT * FROM contact_messages ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Contact Messages</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color:  #e0f7fa;
            text-align: center;
            margin: 0;
            padding: 20px;
        }
        .container {
            width: 90%;
            max-width: 600px;
            margin: auto;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .message-card {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            text-align: left;
        }
        .message-card b {
            color: black;
            font-size: 16px;
        }
        .message-card span {
            color: #d52a2a;
            font-weight: bold;
            font-size: 16px;
        }
        .delete-btn {
            width: 100%;
            background: #d52a2a;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
            margin-top: 15px;
            text-transform: uppercase;
            font-weight: bold;
        }
        .delete-btn:hover {
            background: #a91e1e;
        }
        .back-home-btn {
            display: block;
            width: fit-content;
            margin: 20px auto;
            /* Centers the button */
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            font-size: 16px;
           
        }
        .back-home-btn:hover
        {
            text-decoration: underline;
        }
        
    </style>
</head>
<body>
    <h2>📩 Contact Messages</h2>
    <div class="container">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='message-card'>
                        <b>Name :</b> <span>{$row['name']}</span><br>
                        <b>Number :</b> <span>{$row['phone']}</span><br>
                        <b>Email :</b> <span>{$row['email']}</span><br>
                        <b>Message :</b> <span>{$row['message']}</span><br>
                        <form method='POST' action='messages.php'>
                            <input type='hidden' name='delete_id' value='{$row['id']}'>
                            <button type='submit' class='delete-btn'>Delete</button>
                        </form>
                      </div>";
            }
        } else {
            echo "<p>No messages found.</p>";
        }
        ?>
    </div>
    <a href="admin1.php" class="back-home-btn">Back to Home</a>
</body>
</html>

<?php
$conn->close();
?>
