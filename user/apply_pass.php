<?php
session_start(); 

include "../includes/db.php"; 

$success_msg = "";
$error_msg = "";


if (!isset($_SESSION['email'])) {
    header("Location: login.php"); 
    exit();
}

$logged_in_email = $_SESSION['email']; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = $logged_in_email; 
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $bus_route = mysqli_real_escape_string($conn, $_POST['bus_route']);

    
    $check_query = "SELECT * FROM applications WHERE email = '$email'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $error_msg = "You have already applied!";
    } else {
        
        $upload_folder = "uploads/";
        if (!is_dir($upload_folder)) {
            mkdir($upload_folder, 0777, true);
        }

        $file_name = $_FILES['id_proof']['name'];
        $file_tmp = $_FILES['id_proof']['tmp_name'];
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $new_file_name = time() . "_" . uniqid() . "." . $file_ext;
        $file_path = $upload_folder . $new_file_name;

        if (move_uploaded_file($file_tmp, $file_path)) {
            $sql = "INSERT INTO applications (student_name, email, phone, city, bus_route, id_proof, status, applied_on, payment_status) 
                    VALUES ('$name', '$email', '$phone', '$city', '$bus_route', '$file_path', 'pending', NOW(), 'pending')";

            if (mysqli_query($conn, $sql)) {
                $success_msg = "Application submitted successfully!";
            } else {
                $error_msg = "Error: " . mysqli_error($conn);
            }
        } else {
            $error_msg = "File upload failed!";
        }
    }
}

mysqli_close($conn);
?>

<?php
include "../includes/db.php";
$stops_query = "SELECT sub_stops FROM bus_routes";
$stops_result = mysqli_query($conn, $stops_query);
$all_stops = [];

while ($row = mysqli_fetch_assoc($stops_result)) {
    $stops = explode(",", $row['sub_stops']);
    foreach ($stops as $stop) {
        $trimmed_stop = trim($stop);
        if (!in_array($trimmed_stop, $all_stops)) {
            $all_stops[] = $trimmed_stop;
        }
    }
}
sort($all_stops);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Student Information for Bus Pass</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/user1.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e0f7fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            width: 100%;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: blueviolet;
        }

        label {
            font-weight: bold;
            margin-top: 10px;
            display: block;
        }

        input,
        select,
        button {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        button {
            background-color: rgb(157, 113, 178);
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: rgb(157, 113, 178);
            ;
        }

        input:focus,
        select:focus {
            border-color: #00796b;
            outline: none;
        }


        /* Message styling */
        .success-message,
        .error-message {
            position: relative;
            margin-top: 15px;
            padding: 10px 35px 10px 10px;
            border-radius: 5px;
            text-align: center;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .close-btn {
            position: absolute;
            right: 10px;
            top: 5px;
            font-size: 18px;
            font-weight: bold;
            color: inherit;
            cursor: pointer;
        }

        a {
            color: blueviolet;
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
            text-align: center;
            width: 100%;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Student Information for Bus Pass</h2>

        <form action="apply_pass.php" method="POST" enctype="multipart/form-data">
            <label for="name">Full Name:</label>
            <input type="text" id="name" name="name" required>

         

            <label for="phone">Phone Number:</label>
            <input type="text" id="phone" name="phone" required>

            <label for="city">City:</label>
            <input type="text" id="city" name="city" required>

            <label for="bus_route">Bus Stop:</label>
            <select id="bus_route" name="bus_route" required>
                <option value="">Select a Bus Stop</option>
                <?php
                foreach ($all_stops as $stop) {
                    echo "<option value='" . htmlspecialchars($stop, ENT_QUOTES) . "'>" . htmlspecialchars($stop, ENT_QUOTES) . "</option>";
                }
                ?>
            </select>

            <label for="id_proof">Student ID Proof:</label>
            <input type="file" id="id_proof" name="id_proof" accept="image/*,application/pdf" required>

            <button type="submit">Apply</button>
        </form>

       
        <div id="message-container">
            <?php if (!empty($success_msg)): ?>
                <div class="success-message" id="success-message">
                    <?php echo $success_msg; ?>
                    <span class="close-btn" onclick="document.getElementById('success-message').style.display='none'">&times;</span>
                </div>
            <?php endif; ?>

            <?php if (!empty($error_msg)): ?>
                <div class="error-message" id="error-message">
                    <?php echo $error_msg; ?>
                    <span class="close-btn" onclick="document.getElementById('error-message').style.display='none'">&times;</span>
                </div>
            <?php endif; ?>
        </div>

        <div style="text-align: center; margin-top: 15px;">
            <a href="user.php">Back to Home</a>
        </div>
    </div>
</body>

</html>