<?php
$servername = "localhost";  
$username = "root";         
$password = "";             
$dbname = "college_bus_system";  


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$query = "SELECT route_name, sub_stops, cost FROM bus_routes ORDER BY route_name";
$result = $conn->query($query);

$routes = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $routes[] = $row;
    }
}

echo json_encode($routes);
$conn->close();
?>
