<?php
include "../includes/db.php";

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


if (isset($_POST['add_route'])) {
    $route_name = mysqli_real_escape_string($conn, $_POST['route_name']);
    $sub_stops = mysqli_real_escape_string($conn, $_POST['sub_stops']);
    $cost = intval($_POST['cost']);

    $query = "INSERT INTO bus_routes (route_name, sub_stops, cost) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssi", $route_name, $sub_stops, $cost);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: manage_bus_routes.php");
    exit;
}


if (isset($_POST['edit_route'])) {
    $id = intval($_POST['route_id']);
    $route_name = mysqli_real_escape_string($conn, $_POST['route_name']);
    $sub_stops = mysqli_real_escape_string($conn, $_POST['sub_stops']);
    $cost = intval($_POST['cost']);

    $query = "UPDATE bus_routes SET route_name=?, sub_stops=?, cost=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssii", $route_name, $sub_stops, $cost, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: manage_bus_routes.php");
    exit;
}


if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $query = "DELETE FROM bus_routes WHERE id=?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: manage_bus_routes.php");
    exit;
}


$query = "SELECT * FROM bus_routes ORDER BY id";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <title>Admin - Manage Bus Routes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color:  #e0f7fa;
        }

        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: white;
        }

        th,
        td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color:rgb(157, 113, 178);
            color: white;
        }

        .btn {
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 5px;
        }

        .btn-edit {
            background-color: #ff9800;
            color: white;
        }

        .btn-delete {
            background-color: #e53946;
            color: white;
        }

        form {
            width: 50%;
            margin: 20px auto;
            padding: 20px;
            background: white;
        }

        input,
        textarea,
        button {
            display: block;
            width: 100%;
            margin-top: 10px;
        }
        
        
    </style>
</head>

<body>
    <h2 style="text-align:center;">Manage Bus Routes</h2>
    <form method="POST" action="manage_bus_routes.php">


        <h3>Add/Edit Route</h3>
        <input type="hidden" name="route_id" id="route_id">
        <input type="text" name="route_name" id="route_name" placeholder="Route Name" required>
        <textarea name="sub_stops" id="sub_stops" placeholder="Sub Stops (comma separated)" required></textarea>
        <input type="number" name="cost" id="cost" placeholder="Cost" required>
        <button type="submit" name="add_route">Add Route</button>
        <button type="submit" name="edit_route" style="display:none;">Update Route</button>
    </form>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Route Name</th>
                <th>Sub Stops</th>
                <th>Cost</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['route_name']) ?></td>
                    <td><?= htmlspecialchars($row['sub_stops']) ?></td>
                    <td>₹<?= $row['cost'] ?></td>
                    <td>
                        <a href="#" class="btn btn-edit" onclick="editRoute('<?= $row['id'] ?>', '<?= $row['route_name'] ?>', '<?= $row['sub_stops'] ?>', '<?= $row['cost'] ?>')">Edit</a>
                        <a href="manage_bus_routes.php?delete_id=<?= $row['id'] ?>" class="btn btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <script>
        function editRoute(id, name, stops, cost) {
            document.getElementById('route_id').value = id;
            document.getElementById('route_name').value = name;
            document.getElementById('sub_stops').value = stops;
            document.getElementById('cost').value = cost;

            document.querySelector('[name=add_route]').style.display = 'none';
            let editButton = document.querySelector('[name=edit_route]');
            editButton.style.display = 'block';

        
            document.querySelector('form').action = "manage_bus_routes.php";
        }
    </script>
    <a href="admin1.php" class="back-home-btn">Back to Home</a>
</body>

</html>