<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Patient Data</title>
    <style>
        body {
            background-color: #F4F9F9;
            text-align: center;
            margin: 0;
            padding: 0;
        }

        nav {
            background-color: #333;
            overflow: hidden;
            margin: 0;
            padding: 0;
        }

        nav ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center; /* Center the navigation links */
        }

        nav ul li {
            display: inline;
        }

        nav ul li a {
            display: block;
            color: white;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
        }

        nav ul li a:hover {
            background-color: #ddd;
            color: black;
        }

        .table-container {
            max-height: 400px; /* Adjust the height as needed */
            overflow-y: auto; /* Add a scrollbar if content exceeds height */
            margin-left: auto;
            margin-right: auto;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid #AAAAAA;
            text-align: left;
            padding: 8px;
            background-color: #CCF2F4;
        }

        canvas {
            margin-top: 20px;
        }

        .lineGraph {
            background-color: #E0E1DD;
        }

        .dropdown {
            margin-top: 20px;
            text-align: center;
        }

        .dropdown select {
            padding: 8px;
            margin-right: 10px;
        }

        .button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <nav>
        <ul>
            <li><a href="view_data.php">View Data</a></li>
            <li><a href="profile.html">Profile</a></li>
            <li><a href="help.html">Help</a></li>
        </ul>
    </nav>
    <h2>View Patient Data</h2>

    <?php
    // Assuming your MySQL server is running locally with default credentials
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "blood_glucose";  // Replace with your actual database name

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch data from the database and sort by ID in descending order
    $sql = "SELECT * FROM healthdata ORDER BY Id DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Output data in a table
        echo "<div class='table-container'>";
        echo "<table>";
        echo "<tr><th>ID</th><th>datetime_column</th><th>Age</th><th>Height</th><th>Weight</th><th>BMI</th><th>Sp02</th><th>Heart Rate</th><th>Temperature</th><th>Humidity</th><th>GSR</th><th>Motion</th><th>Blood Glucose</th></tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["Id"] . "</td>";
            echo "<td>" . $row["DateTime"] . "</td>";
            echo "<td class='age'>" . $row["Age"] . "</td>";
            echo "<td class='height'>" . $row["Height"] . "</td>";
            echo "<td class='weight'>" . $row["Weight"] . "</td>";
            echo "<td class='bmi'>" . $row["BMI"] . "</td>";
            echo "<td class='sp02'>" . $row["SP02"] . "</td>";
            echo "<td class='heart rate'>" . $row["Heart_Rate"] . "</td>";
            echo "<td class= 'temperature'>" . $row["Temperature"] . "</td>";
            echo "<td class= 'humidity'>" . $row["Humidity"] . "</td>";
            echo "<td class= 'gsr'>" . $row["GSR"] . "</td>";
            echo "<td class= 'motion'>" . $row["Motion"] . "</td>";
            echo "<td class= 'blood glucose'>" . $row["Blood_Glucose"] . "</td>";
            echo "</tr>";
        }

        echo "</table>";
        echo "</div>";
    } else {
        echo "No records found";
    }

    // Close connection
    $conn->close();
    ?>

    <div class="dropdown">
        <label for="columnSelector">Select Column:</label>
        <select id="columnSelector">
            <option value="SP02">SP02</option>
            <option value="Heart Rate">Heart Rate</option>
            <option value="Temperature">Temperature</option>
            <option value="Humidity">Humidity</option>
            <option value="GSR">GSR</option>
            <option value="Motion">Motion</option>
            <option value="Blood Glucose">Blood Glucose</option>
        </select>
        <button class="button" onclick="updateChart()">Update Graph</button>
    </div>

    <canvas id="lineGraph" class="lineGraph"></canvas>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        var columnSelector = document.getElementById("columnSelector");
        var ctx = document.getElementById('lineGraph').getContext('2d');
        var lineGraph = null;

        function updateChart() {
            var selectedColumn = columnSelector.value;
            var dataValues = [];

            // Retrieve data values based on selected column
            if (selectedColumn.toLowerCase() === 'blood glucose') {
                var values = document.querySelectorAll('.blood.glucose');
                dataValues = Array.from(values).map(value => parseFloat(value.textContent));
            } else if (selectedColumn.toLowerCase() === 'heart rate') {
                var values = document.querySelectorAll('.heart.rate');
                dataValues = Array.from(values).map(value => parseFloat(value.textContent));
            } else {
                // Retrieve data values for other columns
                var values = document.querySelectorAll('.' + selectedColumn.toLowerCase());
                dataValues = Array.from(values).map(value => parseFloat(value.textContent));
            }

            if (lineGraph !== null) {
                lineGraph.destroy();
            }

            lineGraph = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: Array.from({ length: dataValues.length }, (_, i) => i + 1),
                    datasets: [{
                        label: selectedColumn,
                        data: dataValues,
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }
                }
            });
        }
    </script>
</body>
</html>
