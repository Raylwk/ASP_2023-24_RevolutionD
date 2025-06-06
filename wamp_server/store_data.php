<?php
header("Access-Control-Allow-Origin: *"); // Allow cross-origin requests

// Retrieve JSON data from the Flask server
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

// Connect to your SQL Server (replace with your actual database connection details)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "blood_glucose";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql = "INSERT INTO healthdata (Age, Height, Weight, BMI, SP02, Heart_Rate, Temperature, Humidity, GSR, Motion, Blood_Glucose)
        VALUES (?,?,?,?,?,?,?,?,?,?,?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ddddddddddd",$data['Age'],$data['Height'],$data['Weight'],$data['BMI'], $data['SP02'], $data['HeartRate'], $data['Temperature_Ambient'], $data['Humidity_Ambient'],
                                    $data['EDA'], $data['Motion'], $data['Glucose']);

if ($stmt->execute()) {
    echo "Data inserted successfully";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
