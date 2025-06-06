<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json"); // Set the response content type to JSON

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "blood_glucose";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

$sql = "SELECT * FROM healthdata";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $data = array();

    // Fetch data and add to the $data array
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    // Output the data as JSON
    echo json_encode($data);
} else {
    echo json_encode(["message" => "No data found"]);
}

$conn->close();
?>
