<?php
// Assuming your MySQL server is running locally with default credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "blood_glucose";  

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$Age = isset($_POST['Age']) ? $_POST['Age'] : null;
$Height = isset($_POST['Height']) ? $_POST['Height'] : null;
$Weight = isset($_POST['Weight']) ? $_POST['Weight'] : null;
$BMI = null;
if ($Height != 0 && $Weight != 0) {
    //Convert to meters
    $heightInMeters = $Height / 100;
    $BMI = $Weight / ($heightInMeters * $heightInMeters);
}
$Sp02 = isset($_POST['Sp02']) ? $_POST['Sp02'] : null;
$Heart_Rate = isset($_POST['Heart_Rate']) ? $_POST['Heart_Rate'] : null;
$Motion = isset($_POST['Motion']) ? $_POST['Motion'] : null;
$Humidity = isset($_POST['Humidity']) ? $_POST['Humidity'] : null;
$Temperature = isset($_POST['Temperature']) ? $_POST['Temperature'] : null;
$EDA = isset($_POST['EDA']) ? $_POST['EDA'] : null;
$predictedValue = isset($_POST['predictedValue']) ? $_POST['predictedValue'] : null;

// Insert data into the database including user ID
$sql = "INSERT INTO healthdata (Age, Height, Weight, BMI, SP02, Heart_Rate, Temperature, Humidity, GSR, Motion, Blood_Glucose)
        VALUES ('$Age','$Height','$Weight','$BMI','$Sp02','$Heart_Rate', '$Temperature', '$Humidity', '$EDA', '$Motion','$predictedValue')";

if ($conn->query($sql) === TRUE) {
    echo "Record inserted successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close connection
$conn->close();
?>
